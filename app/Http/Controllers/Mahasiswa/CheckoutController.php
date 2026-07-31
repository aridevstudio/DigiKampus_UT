<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Bootcamp;
use App\Models\DosenNotification;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionItem;
use App\Models\PlatformSetting;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Services\MidtransSnapService;
use App\Services\EventCapacityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly MidtransSnapService $midtransSnapService,
        private readonly EventCapacityService $eventCapacityService,
    ) {
    }

    /**
     * Show checkout/cart page.
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();
        Voucher::releaseExpiredReservations();

        $cartItems = $this->getCartItems($user->id);
        $subtotal = $this->calculateSubtotal($cartItems);
        $serviceFee = $cartItems->isNotEmpty() ? $this->courseServiceFee() : 0;
        $total = $subtotal + $serviceFee;
        $vouchers = Voucher::available()->orderBy('code')->get();

        return view('pages.mahasiswa.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'vouchers' => $vouchers,
            'paymentMethods' => $this->checkoutPaymentMethods(),
        ]);
    }

    /**
     * Add item to cart.
     */
    public function addToCart()
    {
        $user = Auth::guard('mahasiswa')->user();
        $courseId = (int) request('course_id');

        $course = \App\Models\Course::find($courseId);
        $isBootcamp = $course && $course->isBootcamp();
        $entityLabel = $isBootcamp ? 'Bootcamp' : 'Kursus';
        $entityLabelLower = $isBootcamp ? 'bootcamp' : 'kursus';

        $exists = Cart::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($exists) {
            return back()->with('error', $entityLabel . ' sudah ada di keranjang');
        }

        $enrolled = Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($enrolled) {
            return back()->with('error', 'Anda sudah terdaftar di ' . $entityLabelLower . ' ini');
        }

        $bootcampCheck = $this->validateBootcampTicketAvailability($courseId);
        if (!$bootcampCheck['ok']) {
            return back()->with('error', $bootcampCheck['message']);
        }

        Cart::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);

        return redirect()->route('mahasiswa.checkout')->with('success', $entityLabel . ' berhasil ditambahkan ke keranjang');
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($id)
    {
        $user = Auth::guard('mahasiswa')->user();

        Cart::where('id_mahasiswa', $user->id)
            ->where('id_cart', $id)
            ->delete();

        return back()->with('success', 'Item berhasil dihapus dari keranjang');
    }

    /**
     * Build payment page and create Midtrans transaction.
     */
    public function payment(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        $preferredPaymentMethod = trim((string) $request->query('payment', $this->checkoutPaymentMethods()[0]['id']));
        $paymentMethods = collect($this->checkoutPaymentMethods());
        $selectedPayment = $paymentMethods->firstWhere('id', $preferredPaymentMethod) ?? $paymentMethods->first();

        if (!$selectedPayment) {
            return redirect()->route('mahasiswa.checkout')->with('error', 'Metode pembayaran tidak tersedia.');
        }

        $preferredPaymentMethod = (string) $selectedPayment['id'];

        $cartItems = $this->getCartItems($user->id);
        if ($cartItems->isEmpty()) {
            return redirect()->route('mahasiswa.checkout')->with('error', 'Keranjang Anda kosong');
        }

        foreach ($cartItems as $cartItem) {
            $bootcampCheck = $this->validateBootcampTicketAvailability((int) $cartItem->id_course);
            if (!$bootcampCheck['ok']) {
                return redirect()->route('mahasiswa.checkout')->with('error', $bootcampCheck['message']);
            }
        }

        $voucher = null;
        if ($request->filled('voucher')) {
            $voucher = $this->resolveVoucher(trim((string) $request->query('voucher')));
            if (!$voucher['success']) {
                return redirect()->route('mahasiswa.checkout')->with('error', $voucher['message']);
            }
            $voucher = $voucher['voucher'];
        }

        $subtotal = $this->calculateSubtotal($cartItems);
        $serviceFee = $this->courseServiceFee();
        if ($voucher && $subtotal < (float) $voucher->min_subtotal) {
            return redirect()->route('mahasiswa.checkout')
                ->with('error', 'Voucher membutuhkan minimal belanja Rp ' . number_format($voucher->min_subtotal, 0, ',', '.') . '.');
        }
        $discountAmount = $this->calculateVoucherDiscount($voucher, $subtotal);
        $grossAmount = max(0, $subtotal + $serviceFee - $discountAmount);
        $orderId = $this->generateOrderId();
        $transaction = null;

        try {
            $transaction = DB::transaction(function () use (
                $user,
                $cartItems,
                $voucher,
                $preferredPaymentMethod,
                $subtotal,
                $serviceFee,
                $discountAmount,
                $grossAmount,
                $orderId
            ) {
                $paymentTransaction = PaymentTransaction::create([
                    'order_id' => $orderId,
                    'id_mahasiswa' => $user->id,
                    'id_voucher' => $voucher?->id_voucher,
                    'preferred_payment_method' => $preferredPaymentMethod,
                    'subtotal_amount' => $subtotal,
                    'service_fee' => $serviceFee,
                    'discount_amount' => $discountAmount,
                    'gross_amount' => $grossAmount,
                    'transaction_status' => 'pending',
                ]);

                foreach ($cartItems as $item) {
                    PaymentTransactionItem::create([
                        'id_payment_transaction' => $paymentTransaction->id_payment_transaction,
                        'id_course' => $item->id_course,
                        'course_name' => $item->course->nama_course ?? 'Kursus',
                        'price' => $item->course->harga ?? 0,
                    ]);

                    $this->eventCapacityService->reserveForPayment(
                        (int) $item->id_course,
                        (int) $user->id,
                        (int) $paymentTransaction->id_payment_transaction,
                    );
                }

                if ($voucher && $discountAmount > 0) {
                    $this->reserveVoucherUsage($voucher, $paymentTransaction);
                }

                return $paymentTransaction->load('items.course');
            });

            $midtransPayload = $this->buildMidtransPayload(
                $transaction,
                $user,
                $voucher,
                $serviceFee,
                $discountAmount
            );

            $midtransResponse = $this->midtransSnapService->createTransaction($midtransPayload);

            $transaction->update([
                'snap_token' => $midtransResponse['token'] ?? null,
                'snap_redirect_url' => $midtransResponse['redirect_url'] ?? null,
                'payload' => [
                    'request' => $midtransPayload,
                    'response' => $midtransResponse,
                ],
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'orderId' => $transaction->order_id,
                    'snapToken' => $transaction->snap_token,
                    'redirectUrl' => $transaction->snap_redirect_url,
                    'finishUrl' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                    'pendingUrl' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                    'errorUrl' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                    'closeUrl' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                    'detailUrl' => route('mahasiswa.transaction-detail', ['id' => $transaction->id_payment_transaction]),
                    'paymentMethod' => $selectedPayment,
                ]);
            }

            return view('pages.mahasiswa.payment', [
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'serviceFee' => $serviceFee,
                'discountAmount' => $discountAmount,
                'total' => $grossAmount,
                'selectedPayment' => $selectedPayment,
                'paymentTransaction' => $transaction->fresh(['items.course', 'voucher']),
                'snapRedirectUrl' => $midtransResponse['redirect_url'] ?? null,
                'snapToken' => $midtransResponse['token'] ?? null,
            ]);
        } catch (ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => collect($e->errors())->flatten()->first() ?: 'Voucher tidak dapat digunakan.',
                ], 422);
            }

            return redirect()->route('mahasiswa.checkout')
                ->withErrors($e->errors())
                ->with('error', collect($e->errors())->flatten()->first() ?: 'Voucher tidak dapat digunakan.');
        } catch (\Throwable $e) {
            if ($transaction instanceof PaymentTransaction) {
                $this->eventCapacityService->releaseForTransaction((int) $transaction->id_payment_transaction);
            }
            report($e);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi pembayaran Midtrans. Periksa konfigurasi env dan coba lagi.',
                ], 500);
            }

            return redirect()->route('mahasiswa.checkout')
                ->with('error', 'Gagal membuat transaksi pembayaran Midtrans. Periksa konfigurasi env dan coba lagi.');
        }
    }

    /**
     * Midtrans finish landing page.
     */
    public function success(Request $request)
    {
        $user = Auth::guard('mahasiswa')->user();
        $orderId = trim((string) $request->query('order_id'));

        if ($orderId === '') {
            return redirect()->route('mahasiswa.finance')->with('info', 'Transaksi tidak ditemukan.');
        }

        $transaction = PaymentTransaction::with(['items.course', 'voucher'])
            ->where('id_mahasiswa', $user->id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        $incomingStatus = trim((string) $request->query('transaction_status'));
        if ($incomingStatus !== '') {
            $this->syncTransactionStatus($transaction, [
                'transaction_status' => $incomingStatus,
                'payment_type' => $request->query('payment_type'),
                'fraud_status' => $request->query('fraud_status'),
                'status_code' => $request->query('status_code'),
            ]);
        }

        return view('pages.mahasiswa.payment-success', [
            'paymentTransaction' => $transaction->fresh(['items.course', 'voucher']),
        ]);
    }

    /**
     * Midtrans notification endpoint.
     */
    public function midtransNotification(Request $request)
    {
        $orderId = trim((string) $request->input('order_id'));
        if ($orderId === '') {
            return response()->json(['message' => 'order_id wajib diisi'], 422);
        }

        $transaction = PaymentTransaction::with(['items.course', 'voucher'])
            ->where('order_id', $orderId)
            ->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        if (!$this->hasValidMidtransSignature($request, $transaction)) {
            return response()->json(['message' => 'Signature Midtrans tidak valid'], 403);
        }

        $this->syncTransactionStatus($transaction, $request->all());

        return response()->json(['message' => 'ok']);
    }

    /**
     * Finance page backed by payment transactions.
     */
    public function finance()
    {
        $user = Auth::guard('mahasiswa')->user();
        $search = trim((string) request('search'));
        $status = trim((string) request('status'));
        $date = trim((string) request('date'));

        $query = PaymentTransaction::with(['items'])
            ->where('id_mahasiswa', $user->id)
            ->latest();

        if ($search !== '') {
            $query->where(function ($inner) use ($search) {
                $inner->where('order_id', 'like', '%' . $search . '%')
                    ->orWhereHas('items', function ($itemQuery) use ($search) {
                        $itemQuery->where('course_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status !== '' && $status !== 'semua') {
            $pendingCutoff = PaymentTransaction::pendingExpiryCutoff();

            if ($status === 'aktif') {
                $query->whereIn('transaction_status', ['settlement', 'capture']);
            } elseif ($status === 'pending') {
                $query->where('transaction_status', 'pending')
                    ->where('created_at', '>=', $pendingCutoff);
            } elseif ($status === 'gagal') {
                $query->where(function ($failedQuery) use ($pendingCutoff) {
                    $failedQuery->whereIn('transaction_status', ['deny', 'cancel', 'expire', 'failure'])
                        ->orWhere(function ($expiredPendingQuery) use ($pendingCutoff) {
                            $expiredPendingQuery->where('transaction_status', 'pending')
                                ->where('created_at', '<', $pendingCutoff);
                        });
                });
            } else {
                $query->where('transaction_status', $status);
            }
        }

        if ($date === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($date === 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($date === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $transactions = $query->paginate(10);

        $allTransactions = PaymentTransaction::where('id_mahasiswa', $user->id)->get();
        $totalPayment = (float) $allTransactions->whereIn('transaction_status', ['settlement', 'capture'])->sum('gross_amount');
        $successCount = $allTransactions->whereIn('transaction_status', ['settlement', 'capture'])->count();
        $pendingCount = $allTransactions->filter(fn ($transaction) => $transaction->effective_transaction_status === 'pending')->count();
        $failedCount = $allTransactions->filter(fn ($transaction) => in_array($transaction->effective_transaction_status, ['deny', 'cancel', 'expire', 'failure'], true))->count();

        return view('pages.mahasiswa.finance', [
            'transactions' => $transactions,
            'totalPayment' => $totalPayment,
            'successCount' => $successCount,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
            'selectedStatus' => $status !== '' ? $status : 'semua',
            'searchQuery' => $search,
        ]);
    }

    /**
     * Transaction detail page.
     */
    public function transactionDetail($id)
    {
        $user = Auth::guard('mahasiswa')->user();

        $transaction = PaymentTransaction::with(['items.course', 'voucher'])
            ->where('id_mahasiswa', $user->id)
            ->where('id_payment_transaction', $id)
            ->firstOrFail();

        return view('pages.mahasiswa.transaction-detail', [
            'paymentTransaction' => $transaction,
        ]);
    }

    private function getCartItems(int $mahasiswaId)
    {
        return Cart::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $mahasiswaId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function validateBootcampTicketAvailability(int $courseId): array
    {
        $course = \App\Models\Course::find($courseId);
        if ($course && $this->eventCapacityService->isCapacityOnlyEvent($course)) {
            $availability = $this->eventCapacityService->availabilityFor($course);
            if ($availability['is_full']) {
                return ['ok' => false, 'message' => 'Slot ' . ucfirst((string) $course->tipe_event) . ' sudah penuh.'];
            }
        }

        if (!Schema::hasColumn('bootcamps', 'linked_course_id')) {
            return ['ok' => true, 'message' => null];
        }

        $bootcamp = Bootcamp::query()
            ->where('linked_course_id', $courseId)
            ->first();

        if (!$bootcamp) {
            return ['ok' => true, 'message' => null];
        }

        if (!in_array($bootcamp->status, ['open_registration', 'published'], true)) {
            return ['ok' => false, 'message' => 'Penjualan bootcamp/tiket ini belum dibuka atau sudah ditutup.'];
        }

        $capacity = (int) ($bootcamp->seat_capacity ?? 0);
        if ($capacity <= 0 && preg_match('/\d+\s*\/\s*(\d+)/', (string) $bootcamp->seats_label, $matches)) {
            $capacity = (int) $matches[1];
        }

        if ($capacity <= 0) {
            return ['ok' => false, 'message' => 'Kuota bootcamp/tiket belum dikonfigurasi.'];
        }

        $filled = PaymentTransactionItem::query()
            ->join('payment_transactions as pt', 'payment_transaction_items.id_payment_transaction', '=', 'pt.id_payment_transaction')
            ->where('payment_transaction_items.id_course', $courseId)
            ->whereIn('pt.transaction_status', ['settlement', 'capture'])
            ->where(function ($query) {
                $query->whereNull('pt.fraud_status')
                    ->orWhere('pt.fraud_status', '!=', 'challenge');
            })
            ->count();

        if ($filled >= $capacity) {
            return ['ok' => false, 'message' => 'Kuota bootcamp/tiket sudah penuh.'];
        }

        return ['ok' => true, 'message' => null];
    }

    private function calculateSubtotal($cartItems): float
    {
        return (float) $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
    }

    private function courseServiceFee(): int
    {
        return PlatformSetting::getCourseServiceFee();
    }

    private function resolveVoucher(string $code): array
    {
        Voucher::releaseExpiredReservations();

        $voucher = Voucher::whereRaw('LOWER(code) = ?', [Str::lower($code)])->first();
        if (!$voucher) {
            return ['success' => false, 'message' => 'Kode voucher tidak dikenali.'];
        }

        if (!$voucher->is_active) {
            return ['success' => false, 'message' => 'Voucher tidak aktif.'];
        }

        if ($voucher->isExpired()) {
            return ['success' => false, 'message' => 'Voucher sudah melewati masa berlaku.'];
        }

        if (!$voucher->hasUsageRemaining()) {
            return ['success' => false, 'message' => 'Kuota voucher sudah habis.'];
        }

        if (Schema::hasTable('voucher_usages')) {
            $user = Auth::guard('mahasiswa')->user();
            $alreadyUsedByUser = VoucherUsage::where('id_voucher', $voucher->id_voucher)
                ->where('id_user', $user?->id)
                ->whereNull('released_at')
                ->whereIn('status', ['reserved', 'confirmed'])
                ->exists();

            if ($alreadyUsedByUser) {
                return ['success' => false, 'message' => 'Voucher ini sudah pernah Anda gunakan.'];
            }
        }

        return ['success' => true, 'voucher' => $voucher];
    }

    private function calculateVoucherDiscount(?Voucher $voucher, float $subtotal): float
    {
        if (!$voucher) {
            return 0;
        }

        if ($subtotal < (float) $voucher->min_subtotal) {
            return 0;
        }

        if ($voucher->type === 'percent') {
            return round($subtotal * (((float) $voucher->value) / 100), 2);
        }

        return min((float) $voucher->value, $subtotal);
    }

    private function generateOrderId(): string
    {
        return 'MID-UT-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
    }

    private function buildMidtransPayload(
        PaymentTransaction $transaction,
        $user,
        ?Voucher $voucher,
        float $serviceFee,
        float $discountAmount
    ): array {
        $itemDetails = $transaction->items->map(function (PaymentTransactionItem $item) {
            return [
                'id' => 'COURSE-' . $item->id_course,
                'price' => (int) round((float) $item->price),
                'quantity' => 1,
                'name' => Str::limit($item->course_name, 50, ''),
            ];
        })->values()->all();

        if ($serviceFee > 0) {
            $itemDetails[] = [
                'id' => 'SERVICE-FEE',
                'price' => (int) round($serviceFee),
                'quantity' => 1,
                'name' => 'Biaya Layanan',
            ];
        }

        if ($discountAmount > 0) {
            $itemDetails[] = [
                'id' => 'VOUCHER-' . ($voucher?->id_voucher ?? 'NA'),
                'price' => (int) round($discountAmount * -1),
                'quantity' => 1,
                'name' => 'Diskon Voucher ' . ($voucher?->code ?? ''),
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) round((float) $transaction->gross_amount),
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->profile->no_hp ?? null,
            ],
            'callbacks' => [
                'finish' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                'unfinish' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
                'error' => route('mahasiswa.payment-success', ['order_id' => $transaction->order_id]),
            ],
        ];

        $enabledPayments = $this->resolveMidtransEnabledPayments((string) $transaction->preferred_payment_method);
        if (!empty($enabledPayments)) {
            $payload['enabled_payments'] = $enabledPayments;
        }

        return $payload;
    }

    private function syncTransactionStatus(PaymentTransaction $transaction, array $payload): void
    {
        $status = (string) ($payload['transaction_status'] ?? $transaction->transaction_status);
        $paymentMethod = (string) ($payload['payment_type'] ?? $transaction->payment_method);
        $fraudStatus = $payload['fraud_status'] ?? $transaction->fraud_status;

        $transaction->update([
            'transaction_status' => $status,
            'payment_method' => $paymentMethod !== '' ? $paymentMethod : $transaction->payment_method,
            'fraud_status' => $fraudStatus,
            'paid_at' => in_array($status, ['settlement', 'capture'], true) ? ($transaction->paid_at ?? now()) : $transaction->paid_at,
            'payload' => array_merge($transaction->payload ?? [], ['latest_notification' => $payload]),
        ]);

        if (in_array($status, ['settlement', 'capture'], true)) {
            $this->finalizeSuccessfulTransaction($transaction->fresh(['items.course', 'voucher']));
        } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure'], true)) {
            $this->releaseVoucherUsage($transaction);
            $this->eventCapacityService->releaseForTransaction((int) $transaction->id_payment_transaction);
        }
    }

    private function finalizeSuccessfulTransaction(PaymentTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $this->confirmVoucherUsage($transaction);

            foreach ($transaction->items as $item) {
                $exists = Enrollment::where('id_mahasiswa', $transaction->id_mahasiswa)
                    ->where('id_course', $item->id_course)
                    ->exists();

                if (!$exists) {
                    Enrollment::create([
                        'id_mahasiswa' => $transaction->id_mahasiswa,
                        'id_course' => $item->id_course,
                        'status' => 'aktif',
                        'progress' => 0,
                        'tanggal_daftar' => now(),
                    ]);

                    $this->eventCapacityService->confirmForEnrollment(
                        (int) $item->id_course,
                        (int) $transaction->id_mahasiswa,
                        (int) $transaction->id_payment_transaction,
                    );

                    \App\Models\Agenda::create([
                        'id_mahasiswa' => $transaction->id_mahasiswa,
                        'id_dosen' => null,
                        'id_course' => $item->id_course,
                        'judul' => 'Mulai Belajar: ' . ($item->course->nama_course ?? 'Kursus'),
                        'deskripsi' => 'Anda mulai terdaftar di kursus ini.',
                        'tanggal' => now(),
                        'waktu_mulai' => now()->format('H:i'),
                        'tipe' => 'workshop', // Warna kuning
                        'warna' => \App\Models\Agenda::getColorByType('workshop'),
                    ]);

                    if ($item->course && $item->course->id_dosen) {
                        $mahasiswa = $transaction->mahasiswa;
                        DosenNotification::notifyDosen(
                            $item->course->id_dosen,
                            'Mahasiswa Baru Mendaftar',
                            ($mahasiswa->name ?? 'Mahasiswa') . ' mendaftar di kursus ' . ($item->course->nama_course ?? 'Kursus'),
                            'enrollment',
                            'enrollment',
                            '/dosen/kursus/' . $item->id_course
                        );
                    }
                }
            }

            Cart::where('id_mahasiswa', $transaction->id_mahasiswa)->delete();
        });

        Notification::notifyMahasiswa(
            $transaction->id_mahasiswa,
            'Pembayaran berhasil',
            'Pembayaran order ' . $transaction->order_id . ' berhasil. Kursus Anda sudah aktif.',
            'umum',
            'payment',
            '#10B981'
        );
    }

    private function reserveVoucherUsage(Voucher $voucher, PaymentTransaction $transaction): void
    {
        if (!Schema::hasTable('voucher_usages') || !Schema::hasColumn('vouchers', 'used_count')) {
            $voucher->forceFill([
                'used_by_user_id' => $transaction->id_mahasiswa,
                'used_payment_transaction_id' => $transaction->id_payment_transaction,
                'used_at' => now(),
            ])->save();

            return;
        }

        $lockedVoucher = Voucher::whereKey($voucher->id_voucher)->lockForUpdate()->first();

        if (!$lockedVoucher || !$lockedVoucher->isAvailableForCheckout()) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher tidak tersedia, kuota habis, atau sudah expired.',
            ]);
        }

        $alreadyUsedByUser = VoucherUsage::where('id_voucher', $lockedVoucher->id_voucher)
            ->where('id_user', $transaction->id_mahasiswa)
            ->whereNull('released_at')
            ->whereIn('status', ['reserved', 'confirmed'])
            ->exists();

        if ($alreadyUsedByUser) {
            throw ValidationException::withMessages([
                'voucher' => 'Voucher ini sudah pernah digunakan oleh akun Anda.',
            ]);
        }

        VoucherUsage::create([
            'id_voucher' => $lockedVoucher->id_voucher,
            'id_payment_transaction' => $transaction->id_payment_transaction,
            'id_user' => $transaction->id_mahasiswa,
            'discount_amount' => $transaction->discount_amount,
            'status' => 'reserved',
            'used_at' => now(),
        ]);

        $lockedVoucher->forceFill([
            'used_count' => (int) $lockedVoucher->used_count + 1,
            'used_by_user_id' => $transaction->id_mahasiswa,
            'used_payment_transaction_id' => $transaction->id_payment_transaction,
            'used_at' => now(),
        ])->save();
    }

    private function confirmVoucherUsage(PaymentTransaction $transaction): void
    {
        if (!$transaction->id_voucher || (float) $transaction->discount_amount <= 0) {
            return;
        }

        if (!Schema::hasTable('voucher_usages') || !Schema::hasColumn('vouchers', 'used_count')) {
            return;
        }

        $voucher = Voucher::whereKey($transaction->id_voucher)->lockForUpdate()->first();
        if (!$voucher) {
            return;
        }

        $usage = VoucherUsage::where('id_payment_transaction', $transaction->id_payment_transaction)->first();

        if (!$usage) {
            $usage = VoucherUsage::create([
                'id_voucher' => $voucher->id_voucher,
                'id_payment_transaction' => $transaction->id_payment_transaction,
                'id_user' => $transaction->id_mahasiswa,
                'discount_amount' => $transaction->discount_amount,
                'status' => 'confirmed',
                'used_at' => now(),
                'confirmed_at' => now(),
            ]);

            $voucher->forceFill(['used_count' => (int) $voucher->used_count + 1])->save();
        } elseif ($usage->released_at !== null) {
            $usage->update([
                'status' => 'confirmed',
                'released_at' => null,
                'confirmed_at' => now(),
                'used_at' => $usage->used_at ?? now(),
            ]);

            $voucher->forceFill(['used_count' => (int) $voucher->used_count + 1])->save();
        } elseif ($usage->status !== 'confirmed') {
            $usage->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }

        $voucher->forceFill([
            'used_by_user_id' => $transaction->id_mahasiswa,
            'used_payment_transaction_id' => $transaction->id_payment_transaction,
            'used_at' => $usage->used_at ?? now(),
        ])->save();
    }

    private function releaseVoucherUsage(PaymentTransaction $transaction): void
    {
        if (!$transaction->id_voucher) {
            return;
        }

        if (!Schema::hasTable('voucher_usages') || !Schema::hasColumn('vouchers', 'used_count')) {
            return;
        }

        DB::transaction(function () use ($transaction) {
            $usage = VoucherUsage::where('id_payment_transaction', $transaction->id_payment_transaction)
                ->whereNull('released_at')
                ->lockForUpdate()
                ->first();

            if (!$usage || $usage->status === 'confirmed') {
                return;
            }

            $usage->update([
                'status' => 'released',
                'released_at' => now(),
            ]);

            $voucher = Voucher::whereKey($usage->id_voucher)->lockForUpdate()->first();
            if ($voucher) {
                $voucher->forceFill([
                    'used_count' => max(0, (int) $voucher->used_count - 1),
                ])->save();
            }
        });
    }

    private function hasValidMidtransSignature(Request $request, PaymentTransaction $transaction): bool
    {
        $signature = (string) $request->input('signature_key');
        if ($signature === '') {
            return false;
        }

        $serverKey = (string) config('services.midtrans.server_key');
        if ($serverKey === '') {
            return false;
        }

        $expected = hash(
            'sha512',
            $transaction->order_id .
            (string) $request->input('status_code', '') .
            number_format((float) $transaction->gross_amount, 2, '.', '') .
            $serverKey
        );

        return hash_equals($expected, $signature);
    }

    private function checkoutPaymentMethods(): array
    {
        return [
            [
                'id' => 'bca_va',
                'name' => 'BCA Virtual Account',
                'type' => 'ATM / m-BCA / KlikBCA',
                'icon' => 'BCA',
                'accent' => 'from-blue-600 to-sky-500',
            ],
            [
                'id' => 'bni_va',
                'name' => 'BNI Virtual Account',
                'type' => 'ATM / BNI Mobile',
                'icon' => 'BNI',
                'accent' => 'from-emerald-600 to-teal-500',
            ],
            [
                'id' => 'bri_va',
                'name' => 'BRI Virtual Account',
                'type' => 'ATM / BRImo',
                'icon' => 'BRI',
                'accent' => 'from-cyan-600 to-blue-500',
            ],
            [
                'id' => 'echannel',
                'name' => 'Mandiri Bill',
                'type' => 'Livin / ATM Mandiri',
                'icon' => 'MD',
                'accent' => 'from-yellow-500 to-amber-400',
            ],
            [
                'id' => 'gopay',
                'name' => 'GoPay',
                'type' => 'E-wallet',
                'icon' => 'GP',
                'accent' => 'from-sky-500 to-cyan-400',
            ],
            [
                'id' => 'qris',
                'name' => 'QRIS',
                'type' => 'Scan semua e-wallet',
                'icon' => 'QR',
                'accent' => 'from-slate-700 to-slate-500',
            ],
        ];
    }

    private function resolveMidtransEnabledPayments(string $preferredPaymentMethod): array
    {
        return match ($preferredPaymentMethod) {
            'bca_va' => ['bca_va'],
            'bni_va' => ['bni_va'],
            'bri_va' => ['bri_va'],
            'echannel' => ['echannel'],
            'gopay' => ['gopay'],
            'qris' => ['qris'],
            default => [],
        };
    }
}

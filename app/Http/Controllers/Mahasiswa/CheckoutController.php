<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DosenNotification;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionItem;
use App\Models\Voucher;
use App\Services\MidtransSnapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly MidtransSnapService $midtransSnapService
    ) {
    }

    /**
     * Show checkout/cart page.
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();

        $cartItems = $this->getCartItems($user->id);
        $subtotal = $this->calculateSubtotal($cartItems);
        $serviceFee = $cartItems->isNotEmpty() ? 5000 : 0;
        $total = $subtotal + $serviceFee;
        $vouchers = Voucher::available()->orderBy('code')->get();

        return view('pages.mahasiswa.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'vouchers' => $vouchers,
            'paymentMethods' => [
                ['id' => 'midtrans', 'name' => 'Midtrans', 'icon' => 'midtrans'],
            ],
        ]);
    }

    /**
     * Add item to cart.
     */
    public function addToCart()
    {
        $user = Auth::guard('mahasiswa')->user();
        $courseId = (int) request('course_id');

        $exists = Cart::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kursus sudah ada di keranjang');
        }

        $enrolled = Enrollment::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();

        if ($enrolled) {
            return back()->with('error', 'Anda sudah terdaftar di kursus ini');
        }

        Cart::create([
            'id_mahasiswa' => $user->id,
            'id_course' => $courseId,
        ]);

        return redirect()->route('mahasiswa.checkout')->with('success', 'Kursus berhasil ditambahkan ke keranjang');
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
        $preferredPaymentMethod = trim((string) $request->query('payment', 'midtrans'));

        $cartItems = $this->getCartItems($user->id);
        if ($cartItems->isEmpty()) {
            return redirect()->route('mahasiswa.checkout')->with('error', 'Keranjang Anda kosong');
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
        $serviceFee = 5000;
        if ($voucher && $subtotal < (float) $voucher->min_subtotal) {
            return redirect()->route('mahasiswa.checkout')
                ->with('error', 'Voucher membutuhkan minimal belanja Rp ' . number_format($voucher->min_subtotal, 0, ',', '.') . '.');
        }
        $discountAmount = $this->calculateVoucherDiscount($voucher, $subtotal);
        $grossAmount = max(0, $subtotal + $serviceFee - $discountAmount);
        $orderId = $this->generateOrderId();

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

            if ($voucher) {
                $voucher->update([
                    'used_by_user_id' => $user->id,
                    'used_payment_transaction_id' => $transaction->id_payment_transaction,
                    'used_at' => now(),
                ]);
            }

            return view('pages.mahasiswa.payment', [
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'serviceFee' => $serviceFee,
                'discountAmount' => $discountAmount,
                'total' => $grossAmount,
                'selectedPayment' => [
                    'id' => 'midtrans',
                    'name' => 'Midtrans Payment Gateway',
                    'type' => 'Secure Checkout',
                    'color' => 'bg-sky-600',
                ],
                'paymentTransaction' => $transaction->fresh(['items.course', 'voucher']),
                'snapRedirectUrl' => $midtransResponse['redirect_url'] ?? null,
            ]);
        } catch (\Throwable $e) {
            report($e);

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
            $mappedStatus = match ($status) {
                'aktif' => ['settlement', 'capture'],
                'pending' => ['pending'],
                'gagal' => ['deny', 'cancel', 'expire', 'failure'],
                default => [$status],
            };
            $query->whereIn('transaction_status', $mappedStatus);
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
        $pendingCount = $allTransactions->where('transaction_status', 'pending')->count();
        $failedCount = $allTransactions->whereIn('transaction_status', ['deny', 'cancel', 'expire', 'failure'])->count();

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

    private function calculateSubtotal($cartItems): float
    {
        return (float) $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
    }

    private function resolveVoucher(string $code): array
    {
        $voucher = Voucher::whereRaw('LOWER(code) = ?', [Str::lower($code)])->first();
        if (!$voucher) {
            return ['success' => false, 'message' => 'Kode voucher tidak dikenali.'];
        }

        if (!$voucher->is_active) {
            return ['success' => false, 'message' => 'Voucher tidak aktif.'];
        }

        if ($voucher->used_at !== null) {
            return ['success' => false, 'message' => 'Voucher ini sudah dipakai pada transaksi lain.'];
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

        return [
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
        }
    }

    private function finalizeSuccessfulTransaction(PaymentTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
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
}

<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DosenNotification;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Show checkout/cart page
     */
    public function index()
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Get cart items from database
        $cartItems = Cart::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
        $serviceFee = $cartItems->isNotEmpty() ? 5000 : 0;
        $total = $subtotal + $serviceFee;
        
        // Payment methods
        $paymentMethods = [
            ['id' => 'bca', 'name' => 'Transfer Bank BCA', 'icon' => 'bank'],
            ['id' => 'ovo', 'name' => 'OVO', 'icon' => 'ovo'],
            ['id' => 'gopay', 'name' => 'GoPay', 'icon' => 'gopay'],
        ];
        
        return view('pages.mahasiswa.checkout', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart()
    {
        $user = Auth::guard('mahasiswa')->user();
        $courseId = request('course_id');
        
        // Check if already in cart
        $exists = Cart::where('id_mahasiswa', $user->id)
            ->where('id_course', $courseId)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'Kursus sudah ada di keranjang');
        }
        
        // Check if already enrolled
        $enrolled = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
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
     * Remove item from cart
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
     * Show payment confirmation page
     */
    public function payment()
    {
        $user = Auth::guard('mahasiswa')->user();
        $paymentMethod = request('payment', 'bca');
        
        // Get cart items from database
        $cartItems = Cart::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('mahasiswa.checkout')
                ->with('error', 'Keranjang Anda kosong');
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
        $serviceFee = 5000;
        $total = $subtotal + $serviceFee;
        
        // Payment methods config
        $paymentMethods = [
            'bca' => ['name' => 'Bank BCA', 'type' => 'Virtual Account', 'color' => 'bg-blue-600'],
            'ovo' => ['name' => 'OVO', 'type' => 'E-Wallet', 'color' => 'bg-purple-600'],
            'gopay' => ['name' => 'GoPay', 'type' => 'E-Wallet', 'color' => 'bg-green-500'],
        ];
        
        $selectedPayment = $paymentMethods[$paymentMethod] ?? $paymentMethods['bca'];
        $selectedPayment['id'] = $paymentMethod;
        $selectedPayment['va_number'] = '890' . str_pad($user->id, 10, '0', STR_PAD_LEFT) . rand(100, 999);
        
        return view('pages.mahasiswa.payment', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'selectedPayment' => $selectedPayment,
        ]);
    }

    /**
     * Process payment and show success page
     */
    public function success()
    {
        $user = Auth::guard('mahasiswa')->user();
        $paymentMethod = request('payment', 'bca');
        
        // Get cart items from database
        $cartItems = Cart::with(['course'])
            ->where('id_mahasiswa', $user->id)
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('mahasiswa.courses')
                ->with('success', 'Kursus Anda sudah aktif!');
        }
        
        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
        $serviceFee = 5000;
        $total = $subtotal + $serviceFee;
        
        // Generate transaction ID
        $transactionId = 'TRX-UT-' . date('ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        // Payment method labels
        $paymentLabels = [
            'bca' => 'BCA Virtual Account',
            'ovo' => 'OVO',
            'gopay' => 'GoPay',
        ];
        
        // Create enrollments for each cart item
        $courseNames = [];
        foreach ($cartItems as $item) {
            // Check if not already enrolled
            $exists = \App\Models\Enrollment::where('id_mahasiswa', $user->id)
                ->where('id_course', $item->id_course)
                ->exists();
            
            if (!$exists) {
                \App\Models\Enrollment::create([
                    'id_mahasiswa' => $user->id,
                    'id_course' => $item->id_course,
                    'status' => 'aktif',
                    'progress' => 0,
                ]);

                // Notify dosen about new enrollment
                if ($item->course && $item->course->id_dosen) {
                    DosenNotification::notifyDosen(
                        $item->course->id_dosen,
                        'Mahasiswa Baru Mendaftar',
                        ($user->name ?? 'Mahasiswa') . ' mendaftar di kursus ' . ($item->course->nama_course ?? 'Kursus'),
                        'enrollment',
                        'enrollment',
                        '/dosen/kursus/' . $item->id_course
                    );
                }
            }
            
            $courseNames[] = $item->course->nama_course;
        }
        
        // Clear cart after successful payment
        Cart::where('id_mahasiswa', $user->id)->delete();
        
        // Prepare transaction data with Indonesian date format
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $dateFormatted = now()->format('d') . ' ' . $months[(int)now()->format('n')] . ' ' . now()->format('Y, H:i');
        
        $transactionData = [
            'id' => $transactionId,
            'course_names' => $courseNames,
            'course_count' => count($courseNames),
            'date' => $dateFormatted,
            'payment_method' => $paymentLabels[$paymentMethod] ?? 'Unknown',
            'total' => $total,
        ];
        
        return view('pages.mahasiswa.payment-success', [
            'transaction' => $transactionData,
        ]);
    }
    
    /**
     * Show finance/transaction history page
     */
    public function finance()
    {
        $user = Auth::guard('mahasiswa')->user();
        $search = request('search');
        $status = request('status');
        $date = request('date');
        
        // Get enrollments as transactions (simulating payment history)
        $query = \App\Models\Enrollment::with(['course'])
            ->where('id_mahasiswa', $user->id)
            ->orderBy('created_at', 'desc');
        
        // Apply search filter
        if ($search) {
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('nama_course', 'like', '%' . $search . '%');
            });
        }
        
        // Apply status filter
        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }
        
        $transactions = $query->paginate(10);
        
        // Calculate stats
        $allEnrollments = \App\Models\Enrollment::with(['course'])
            ->where('id_mahasiswa', $user->id)
            ->get();
        
        $totalPayment = $allEnrollments->sum(function ($item) {
            return $item->course->harga ?? 0;
        });
        
        $successCount = $allEnrollments->where('status', 'aktif')->count();
        $pendingCount = $allEnrollments->where('status', 'pending')->count();
        $failedCount = $allEnrollments->where('status', 'gagal')->count();
        
        return view('pages.mahasiswa.finance', [
            'transactions' => $transactions,
            'totalPayment' => $totalPayment,
            'successCount' => $successCount,
            'pendingCount' => $pendingCount,
            'failedCount' => $failedCount,
            'selectedStatus' => $status ?? 'semua',
            'searchQuery' => $search,
        ]);
    }
    
    /**
     * Show transaction detail page
     */
    public function transactionDetail($id)
    {
        $user = Auth::guard('mahasiswa')->user();
        
        // Get enrollment by ID
        $enrollment = \App\Models\Enrollment::with(['course', 'course.dosen'])
            ->where('id_mahasiswa', $user->id)
            ->where('id_enroll', $id)
            ->firstOrFail();
        
        // Payment method (simulated)
        $paymentMethods = [
            'va_bca' => ['name' => 'Bank Central Asia (BCA)', 'type' => 'Virtual Account BCA'],
            'va_bni' => ['name' => 'Bank Negara Indonesia (BNI)', 'type' => 'Virtual Account BNI'],
            'ewallet' => ['name' => 'E-Wallet', 'type' => 'E-Wallet'],
        ];
        $selectedMethod = array_rand($paymentMethods);
        
        // Build transaction data
        $transaction = [
            'id' => 'TRX-' . date('Y', strtotime($enrollment->created_at)) . '-' . str_pad($enrollment->id_enroll, 6, '0', STR_PAD_LEFT),
            'date' => $enrollment->created_at,
            'course_name' => $enrollment->course->nama_course ?? 'Unknown',
            'course_id' => $enrollment->id_course,
            'method' => $paymentMethods[$selectedMethod]['type'],
            'bank_name' => $paymentMethods[$selectedMethod]['name'],
            'va_number' => '1234567890' . str_pad($enrollment->id_enroll, 6, '0', STR_PAD_LEFT),
            'amount' => $enrollment->course->harga ?? 0,
            'admin_fee' => 1000,
            'total' => ($enrollment->course->harga ?? 0) + 1000,
            'status' => $enrollment->status,
            'deadline' => $enrollment->created_at->addDays(1),
            'enrollment_id' => $enrollment->id_enroll,
        ];
        
        return view('pages.mahasiswa.transaction-detail', [
            'transaction' => $transaction,
        ]);
    }
}

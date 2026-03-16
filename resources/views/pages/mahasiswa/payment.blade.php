<x-layouts.dashboard :active="'get-courses'">
@php
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=100&h=100&fit=crop';
@endphp

<div class="mb-8 flex items-center justify-center gap-2 sm:gap-4 overflow-x-auto">
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">Keranjang</span>
    </div>
    <div class="h-0.5 w-8 flex-shrink-0 bg-blue-500 sm:w-16"></div>
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">2</div>
        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 sm:text-sm">Pembayaran</span>
    </div>
    <div class="h-0.5 w-8 flex-shrink-0 bg-gray-300 dark:bg-gray-600 sm:w-16"></div>
    <div class="flex flex-shrink-0 items-center gap-1.5 sm:gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-300 text-sm font-bold text-gray-500 dark:bg-gray-600 dark:text-gray-400">3</div>
        <span class="text-xs text-gray-500 dark:text-gray-400 sm:text-sm">Selesai</span>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-gray-100">Ringkasan Order</h2>

            @foreach($cartItems as $item)
            @php
                $course = $item->course;
                $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
            @endphp
            <div class="mb-4 flex items-center gap-4 rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
                <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="h-12 w-12 rounded-lg object-cover">
                <div class="min-w-0 flex-1">
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $course->nama_course }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $course->kode_course }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($course->harga ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach

            <div class="space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700/50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Harga Kursus</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if(($discountAmount ?? 0) > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-green-600 dark:text-green-400">Diskon Voucher</span>
                    <span class="text-green-600 dark:text-green-400">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Biaya Layanan</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                </div>
                <div class="mt-3 flex items-center justify-between border-t border-gray-200 pt-3 dark:border-gray-700/50">
                    <span class="font-medium text-gray-800 dark:text-gray-100">Total Pembayaran</span>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-gray-100">Transaksi Midtrans</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-500 dark:text-gray-400">Order ID</span>
                    <span class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $paymentTransaction->order_id }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-500 dark:text-gray-400">Status</span>
                    <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ ucfirst($paymentTransaction->transaction_status) }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-500 dark:text-gray-400">Gateway</span>
                    <span class="font-semibold text-gray-800 dark:text-gray-200">Midtrans</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            <a href="{{ $snapRedirectUrl }}" target="_blank" rel="noopener noreferrer" class="flex-1 rounded-xl bg-blue-500 py-3 text-center font-medium text-white transition hover:bg-blue-600">
                Lanjutkan Bayar di Midtrans
            </a>
            <a href="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}" class="flex-1 rounded-xl border border-gray-300 py-3 text-center font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700/50">
                Cek Status Transaksi
            </a>
        </div>
    </div>

    <div class="space-y-4 lg:col-span-1">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/20">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">Transaksi Aman</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Midtrans Secure Checkout</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Setelah pembayaran berhasil, kursus akan otomatis diaktifkan pada akun Anda.</p>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <div class="mb-3 flex items-center gap-2">
                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="font-medium text-gray-800 dark:text-gray-100">Butuh Bantuan?</h3>
            </div>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Cek FAQ lebih dulu. Jika belum terjawab, kirim pertanyaan ke admin support.</p>
            <a href="{{ route('mahasiswa.support') }}" class="block w-full rounded-xl bg-blue-500 py-2.5 text-center text-sm font-medium text-white transition hover:bg-blue-600">
                Hubungi Support
            </a>
        </div>
    </div>
</div>

</x-layouts.dashboard>

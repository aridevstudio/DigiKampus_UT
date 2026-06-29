<x-layouts.dashboard :active="'get-courses'">

@php
    $status = $paymentTransaction->effective_transaction_status;
    $isSuccess = in_array($status, ['settlement', 'capture'], true);
    $isPending = $status === 'pending';

    // Detect if all items in the transaction are bootcamps/tiket
    $bootcampCount = $paymentTransaction->items->filter(fn ($item) =>
        $item->course && strtolower((string) ($item->course->kategori ?? '')) === 'tiket'
    )->count();
    $isAllBootcamp = $paymentTransaction->items->count() > 0 && $bootcampCount === $paymentTransaction->items->count();
    $labelEntity = $isAllBootcamp ? 'Bootcamp' : 'Kursus';
    $labelEntityLower = $isAllBootcamp ? 'bootcamp' : 'kursus';
    $successMessage = $isSuccess
        ? 'Terima kasih, ' . $labelEntityLower . ' Anda sudah aktif dan siap dipelajari.'
        : null;
@endphp

<div class="mb-8 flex items-center justify-center gap-4">
    <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Keranjang</span>
    </div>
    <div class="h-0.5 w-16 bg-blue-500"></div>
    <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Pembayaran</span>
    </div>
    <div class="h-0.5 w-16 {{ $isSuccess ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
    <div class="flex items-center gap-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-full {{ $isSuccess ? 'bg-green-500 text-white' : ($isPending ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') }} text-sm font-bold">3</div>
        <span class="text-sm font-medium {{ $isSuccess ? 'text-green-600 dark:text-green-400' : ($isPending ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
            {{ $isSuccess ? 'Selesai' : ($isPending ? 'Pending' : 'Gagal') }}
        </span>
    </div>
</div>

<div class="mb-6 text-center">
    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full {{ $isSuccess ? 'bg-green-500' : ($isPending ? 'bg-yellow-500' : 'bg-red-500') }}">
        @if($isSuccess)
        <svg class="h-10 w-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        @elseif($isPending)
        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        @else
        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        @endif
    </div>
    <h1 class="mb-2 text-2xl font-bold text-gray-800 dark:text-gray-100">
        {{ $isSuccess ? 'Pembayaran Berhasil!' : ($isPending ? 'Pembayaran Masih Pending' : 'Pembayaran Belum Berhasil') }}
    </h1>
    <p class="text-gray-600 dark:text-gray-400">
        @if($isSuccess)
            Terima kasih, {{ $labelEntityLower }} Anda sudah aktif dan siap dipelajari.
        @elseif($isPending)
            Silakan selesaikan pembayaran melalui Midtrans atau cek kembali status transaksi.
        @else
            Transaksi tidak berhasil atau batas waktu pembayaran sudah habis. Anda bisa mencoba pembayaran ulang dari detail transaksi.
        @endif
    </p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h2 class="mb-6 text-lg font-bold text-gray-800 dark:text-gray-100">Ringkasan Transaksi</h2>

            <div class="space-y-4">
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">{{ $labelEntity }}</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100 text-right">
                        @if($paymentTransaction->items->count() > 1)
                            {{ $paymentTransaction->items->count() }} {{ $labelEntityLower }}
                        @else
                            {{ $paymentTransaction->items->first()?->course_name ?? $labelEntity }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Order ID</span>
                    <span class="font-mono font-medium text-gray-800 dark:text-gray-100">{{ $paymentTransaction->order_id }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Tanggal</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $paymentTransaction->created_at->format('d F Y, H:i') }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ strtoupper($paymentTransaction->payment_method ?: 'midtrans') }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600 dark:text-gray-400">Total Pembayaran</span>
                    <span class="text-xl font-bold {{ $isSuccess ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">Rp {{ number_format($paymentTransaction->gross_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row">
            @if($isSuccess)
            <a href="{{ route($isAllBootcamp ? 'mahasiswa.bootcamp-saya' : 'mahasiswa.courses') }}" class="flex-1 rounded-xl bg-blue-500 py-3 text-center font-medium text-white transition hover:bg-blue-600">
                Mulai Belajar Sekarang
            </a>
            @elseif($paymentTransaction->snap_token)
            <button
                type="button"
                data-midtrans-snap-trigger
                data-snap-token="{{ $paymentTransaction->snap_token }}"
                data-finish-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
                data-pending-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
                data-error-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
                data-fallback-url="{{ $paymentTransaction->snap_redirect_url }}"
                class="flex-1 rounded-xl bg-blue-500 py-3 text-center font-medium text-white transition hover:bg-blue-600">
                Lanjutkan Bayar
            </button>
            @endif
            <a href="{{ route('mahasiswa.transaction-detail', ['id' => $paymentTransaction->id_payment_transaction]) }}" class="flex-1 rounded-xl border border-gray-300 py-3 text-center font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700/50">
                Detail Transaksi
            </a>
            <a href="{{ route('mahasiswa.finance') }}" class="flex-1 rounded-xl border border-gray-300 py-3 text-center font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700/50">
                Lihat Finance
            </a>
        </div>
    </div>

    <div class="space-y-4 lg:col-span-1">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
            <h3 class="mb-3 font-bold text-gray-800 dark:text-gray-100">Catatan</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @if($isSuccess)
                    {{ $labelEntity }} aktif otomatis setelah Midtrans mengirim status berhasil ke sistem.
                @elseif($isPending)
                    Jika status belum berubah, cek lagi beberapa saat atau buka detail transaksi untuk bayar ulang.
                @else
                    Anda bisa membuat transaksi baru dari checkout atau menghubungi support jika pembayaran seharusnya berhasil.
                @endif
            </p>
        </div>
    </div>
</div>

@push('scripts')
@include('pages.mahasiswa.partials.midtrans-snap-handler')
@endpush

</x-layouts.dashboard>

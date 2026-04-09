<x-layouts.dashboard :active="'finance'">

@php
    $status = $paymentTransaction->effective_transaction_status;
    $isSuccess = in_array($status, ['settlement', 'capture'], true);
    $isPending = $status === 'pending';
@endphp

<div class="mb-6 animate-fade-in-up">
    <div class="mb-2 flex items-center gap-3">
        <a href="{{ route('mahasiswa.finance') }}" class="rounded-lg p-2 transition hover:bg-gray-100 dark:hover:bg-gray-700/50">
            <svg class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detail Transaksi</h1>
            <p class="text-gray-500 dark:text-gray-400">Informasi lengkap pembayaran kursus</p>
        </div>
    </div>

    <div class="flex justify-end">
        @if($isSuccess)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-4 py-2 text-sm font-medium text-green-700 dark:bg-green-500/20 dark:text-green-400">
            <span class="h-2 w-2 rounded-full bg-green-500"></span>
            Pembayaran Berhasil
        </span>
        @elseif($isPending)
        <span class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-4 py-2 text-sm font-medium text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400">
            <span class="h-2 w-2 rounded-full bg-yellow-500"></span>
            Menunggu Pembayaran
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-4 py-2 text-sm font-medium text-red-700 dark:bg-red-500/20 dark:text-red-400">
            <span class="h-2 w-2 rounded-full bg-red-500"></span>
            Gagal
        </span>
        @endif
    </div>
</div>

<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <h2 class="mb-6 text-lg font-bold text-gray-800 dark:text-gray-100">Informasi Transaksi</h2>
    <div class="space-y-4">
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Order ID</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $paymentTransaction->order_id }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Tanggal</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $paymentTransaction->created_at->format('d F Y, H:i') }} WIB</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Metode</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ strtoupper($paymentTransaction->payment_method ?: 'midtrans') }}</span>
        </div>
        @if($paymentTransaction->voucher)
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Voucher</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ strtoupper($paymentTransaction->voucher->code) }}</span>
        </div>
        @endif
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($paymentTransaction->subtotal_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Biaya Layanan</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($paymentTransaction->service_fee, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between border-b border-gray-100 py-3 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Diskon</span>
            <span class="font-semibold text-green-600 dark:text-green-400">- Rp {{ number_format($paymentTransaction->discount_amount, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between py-4">
            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Dibayarkan</span>
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($paymentTransaction->gross_amount, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700/50 dark:bg-[#1f2937]">
    <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-gray-100">Item Transaksi</h2>
    <div class="space-y-3">
        @foreach($paymentTransaction->items as $item)
        <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3 dark:bg-gray-800/50">
            <div>
                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $item->course_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">ID Course: {{ $item->id_course }}</p>
            </div>
            <span class="font-semibold text-gray-800 dark:text-gray-100">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>
</div>

@if($isPending && $paymentTransaction->snap_token)
<div class="flex justify-end">
    <button
        type="button"
        data-midtrans-snap-trigger
        data-snap-token="{{ $paymentTransaction->snap_token }}"
        data-finish-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
        data-pending-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
        data-error-url="{{ route('mahasiswa.payment-success', ['order_id' => $paymentTransaction->order_id]) }}"
        data-fallback-url="{{ $paymentTransaction->snap_redirect_url }}"
        class="rounded-xl bg-blue-500 px-6 py-3 font-medium text-white transition hover:bg-blue-600">
        Lanjutkan Bayar
    </button>
</div>
@endif

@push('scripts')
@include('pages.mahasiswa.partials.midtrans-snap-handler')
@endpush

</x-layouts.dashboard>

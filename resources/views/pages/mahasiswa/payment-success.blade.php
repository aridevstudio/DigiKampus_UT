<x-layouts.dashboard :active="'get-courses'">
@php
    $order = [
        'id' => 'TRX-UT-240930-001',
        'course' => [
            'title' => 'Analisis Data Dengan Python',
            'code' => 'STIN4113',
        ],
        'date' => '30 Sep 2025, 19:33',
        'payment_method' => 'BCA Virtual Account',
        'total' => 204000,
    ];
@endphp

{{-- Step Indicator --}}
<div class="flex items-center justify-center gap-4 mb-8 animate-fade-in-up">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Keranjang</span>
    </div>
    <div class="w-16 h-0.5 bg-blue-500"></div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Pembayaran</span>
    </div>
    <div class="w-16 h-0.5 bg-blue-500"></div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold">3</div>
        <span class="text-sm font-medium text-green-600 dark:text-green-400">Selesai</span>
    </div>
</div>

{{-- Success Icon --}}
<div class="text-center mb-6 animate-fade-in-up delay-100">
    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-500 flex items-center justify-center animate-bounce-in">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Pembayaran Berhasil!</h1>
    <p class="text-gray-600 dark:text-gray-400">Terima kasih, kursus Anda sudah aktif dan siap dipelajari.</p>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Transaction Summary --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Ringkasan Transaksi</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Nama Kursus</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $order['course']['title'] }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Kode Transaksi</span>
                    <span class="font-mono font-medium text-gray-800 dark:text-gray-100">{{ $order['id'] }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Tanggal & Waktu</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $order['date'] }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100 dark:border-gray-700/50">
                    <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                    <span class="font-medium text-gray-800 dark:text-gray-100">{{ $order['payment_method'] }}</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600 dark:text-gray-400">Total Pembayaran</span>
                    <span class="font-bold text-xl text-green-600 dark:text-green-400">Rp {{ number_format($order['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 animate-fade-in-up delay-300">
            <a href="{{ route('mahasiswa.get-courses') }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <div class="text-center">
                    <div class="font-medium">Mulai</div>
                    <div class="font-medium">Belajar</div>
                    <div class="font-medium">Sekarang</div>
                </div>
            </a>
            <a href="{{ route('mahasiswa.dashboard') }}" class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <div class="text-center">
                    <div>Lihat Kursus</div>
                    <div>Saya</div>
                </div>
            </a>
            <button class="flex-1 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh Invoice</span>
            </button>
        </div>
    </div>
    
    {{-- Right: Sidebar --}}
    <div class="lg:col-span-1 space-y-4">
        {{-- Tips Penggunaan --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-100">Tips Penggunaan</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                Selamat! Anda resmi terdaftar di kursus ini. Selesaikan modul minggu pertama agar progres belajar lebih cepat.
            </p>
        </div>
        
        {{-- Satisfaction Rating --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 text-center animate-fade-in-up delay-300">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-green-500 flex items-center justify-center">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">97%</p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">mahasiswa merasa puas dengan kursus ini</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes bounce-in {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.5s ease-out;
    }
</style>
@endpush

</x-layouts.dashboard>

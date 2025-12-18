<x-layouts.dashboard :active="'get-courses'">
@php
    // Dummy cart items (in real app, this would come from session/database)
    $cartItems = [
        [
            'id' => 1,
            'code' => 'EKMA4159',
            'title' => 'Matematika Ekonomi dan Bisnis',
            'instructor' => 'Dr. Siti Nurhaliza, M.Pd',
            'price' => 450000,
            'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=100&h=100&fit=crop',
            'type' => 'Kursus',
        ],
        [
            'id' => 4,
            'code' => 'STIN4113',
            'title' => 'Analisis Data Dengan Python',
            'instructor' => 'Prof. Ahmad Dahlan',
            'price' => 199000,
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop',
            'type' => 'Kursus',
        ],
    ];
    
    $subtotal = collect($cartItems)->sum('price');
    $serviceFee = 5000;
    $total = $subtotal + $serviceFee;
    
    $paymentMethods = [
        ['id' => 'bca', 'name' => 'Transfer Bank BCA', 'icon' => 'bank'],
        ['id' => 'ovo', 'name' => 'OVO', 'icon' => 'ovo'],
        ['id' => 'gopay', 'name' => 'GoPay', 'icon' => 'gopay'],
    ];
@endphp

{{-- Step Indicator --}}
<div class="flex items-center justify-center gap-4 mb-8 animate-fade-in-up">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold">1</div>
        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Keranjang</span>
    </div>
    <div class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 flex items-center justify-center text-sm font-bold">2</div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Pembayaran</span>
    </div>
    <div class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Selesai</span>
    </div>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Cart Items --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Cart Header --}}
        <div class="flex items-center gap-3 animate-fade-in-up delay-100">
            <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Keranjang Anda</h1>
        </div>
        
        {{-- Cart Items --}}
        <div class="space-y-4 animate-fade-in-up delay-200">
            @foreach($cartItems as $item)
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4">
                <div class="flex gap-4">
                    {{-- Course Image --}}
                    <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
                    
                    {{-- Course Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $item['title'] }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">Kode: {{ $item['code'] }}</p>
                        <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $item['instructor'] ?? '' }}</p>
                        
                        {{-- Price --}}
                        <p class="text-blue-600 dark:text-blue-400 font-bold text-lg mt-2">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </p>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex flex-col items-end justify-between">
                        <div class="flex items-center gap-3">
                            <button class="flex items-center gap-1 text-rose-500 hover:text-rose-600 text-sm transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                </svg>
                                Simpan
                            </button>
                            <button class="flex items-center gap-1 text-gray-400 hover:text-red-500 text-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        {{-- Voucher Input --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 animate-fade-in-up delay-300">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <input 
                    type="text" 
                    placeholder="Masukkan kode voucher..." 
                    class="flex-1 px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-[#111827] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                >
                <button class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition text-sm">
                    Gunakan
                </button>
            </div>
        </div>
    </div>
    
    {{-- Right: Order Summary --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 sticky top-24 animate-fade-in-up delay-200">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Ringkasan Pesanan</h2>
            
            {{-- Summary Items --}}
            <div class="space-y-3 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700/50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal ({{ count($cartItems) }} kursus)</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Biaya Layanan</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                </div>
            </div>
            
            {{-- Total --}}
            <div class="flex justify-between items-center mb-6">
                <span class="font-medium text-gray-800 dark:text-gray-100">Total Pembayaran</span>
                <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp. {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            
            {{-- Payment Methods --}}
            <div class="mb-6">
                <h3 class="font-medium text-gray-800 dark:text-gray-100 mb-3">Metode Pembayaran</h3>
                <div class="space-y-2">
                    {{-- BCA --}}
                    <label class="flex items-center gap-3 p-3 border border-blue-500 bg-blue-50 dark:bg-blue-500/10 rounded-xl cursor-pointer">
                        <input type="radio" name="payment" value="bca" checked class="w-4 h-4 text-blue-500">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">BCA</span>
                        </div>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">Transfer Bank BCA</span>
                    </label>
                    
                    {{-- OVO --}}
                    <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition">
                        <input type="radio" name="payment" value="ovo" class="w-4 h-4 text-blue-500">
                        <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">OVO</span>
                        </div>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">OVO</span>
                    </label>
                    
                    {{-- GoPay --}}
                    <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-gray-300 dark:hover:border-gray-600 transition">
                        <input type="radio" name="payment" value="gopay" class="w-4 h-4 text-blue-500">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">GP</span>
                        </div>
                        <span class="text-sm font-medium text-gray-800 dark:text-gray-100">GoPay</span>
                    </label>
                </div>
            </div>
            
            {{-- Pay Button --}}
            <a href="{{ route('mahasiswa.payment') }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Bayar Sekarang
            </a>
            
            {{-- Security Info --}}
            <div class="flex items-center justify-center gap-2 text-gray-500 dark:text-gray-400 text-xs mb-4">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Pembayaran Anda aman dan terenkripsi
            </div>
            
            {{-- Guarantee --}}
            <div class="bg-green-50 dark:bg-green-500/10 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center gap-2 text-green-600 dark:text-green-400 font-medium text-sm mb-1">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Garansi Uang Kembali 100%
                </div>
                <p class="text-green-600 dark:text-green-400 text-xs">Dalam 7 hari setelah pembelian</p>
            </div>
        </div>
    </div>
</div>

</x-layouts.dashboard>

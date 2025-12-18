<x-layouts.dashboard :active="'get-courses'">
@php
    // Dummy order data (in real app, this would come from session/database)
    $order = [
        'id' => 'ORD-2024-0001',
        'course' => [
            'id' => 4,
            'code' => 'STIN4113',
            'title' => 'Analisis Data Dengan Python',
            'items' => 1,
            'price' => 199000,
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=100&h=100&fit=crop',
        ],
        'harga_kursus' => 199000,
        'biaya_layanan' => 5000,
        'total' => 204000,
        'payment_method' => [
            'id' => 'bca',
            'name' => 'Bank BCA',
            'type' => 'Virtual Account',
            'va_number' => '8901234567890123',
        ],
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
        <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold">2</div>
        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Pembayaran</span>
    </div>
    <div class="w-16 h-0.5 bg-gray-300 dark:bg-gray-600"></div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 flex items-center justify-center text-sm font-bold">3</div>
        <span class="text-sm text-gray-500 dark:text-gray-400">Selesai</span>
    </div>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Order Details --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Ringkasan Order --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-100">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Ringkasan Order</h2>
            
            {{-- Course Item --}}
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl mb-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $order['course']['title'] }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $order['course']['code'] }}</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs">{{ $order['course']['items'] }} item</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($order['course']['price'], 0, ',', '.') }}</p>
                </div>
            </div>
            
            {{-- Price Breakdown --}}
            <div class="space-y-2 pt-4 border-t border-gray-200 dark:border-gray-700/50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Harga Kursus</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($order['harga_kursus'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Biaya Layanan</span>
                    <span class="text-gray-800 dark:text-gray-200">Rp {{ number_format($order['biaya_layanan'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 mt-3 border-t border-gray-200 dark:border-gray-700/50">
                    <span class="font-medium text-gray-800 dark:text-gray-100">Total Pembayaran</span>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($order['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        
        {{-- Metode Pembayaran --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Metode Pembayaran yang Dipilih</h2>
            
            <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                <div class="w-14 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">{{ strtoupper(substr($order['payment_method']['id'], 0, 3)) }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">{{ $order['payment_method']['name'] }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $order['payment_method']['type'] }}</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                        Nomor Virtual Account: 
                        <span class="font-mono font-medium text-gray-800 dark:text-gray-200">{{ $order['payment_method']['va_number'] }}</span>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 text-xs rounded-full">
                        Akan diarahkan ke halaman pembayaran resmi
                    </span>
                </div>
            </div>
        </div>
        
        {{-- Instruksi Pembayaran --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-300">
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Instruksi Pembayaran</h2>
            
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Pastikan detail pembayaran sudah sesuai dengan pesanan Anda.</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        Klik tombol <span class="font-semibold text-gray-800 dark:text-gray-100">Lanjutkan Bayar</span> untuk diarahkan ke halaman pembayaran resmi BCA.
                    </p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Selesaikan pembayaran sesuai instruksi dari bank yang dipilih.</p>
                </div>
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 animate-fade-in-up delay-400">
            <a href="{{ route('mahasiswa.payment-success') }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                Lanjutkan Bayar
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
            <a href="{{ route('mahasiswa.checkout') }}" class="px-8 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition text-center">
                Batalkan
            </a>
        </div>
    </div>
    
    {{-- Right: Sidebar --}}
    <div class="lg:col-span-1 space-y-4">
        
        {{-- Transaksi Aman --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 dark:text-gray-100">Transaksi Aman</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Enkripsi 256-bit</p>
                </div>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Transaksi ini dijamin aman dengan enkripsi 256-bit dan dilindungi oleh sistem keamanan terdepan.</p>
        </div>
        
        {{-- Unduh Ringkasan --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-300">
            <h3 class="font-medium text-gray-800 dark:text-gray-100 mb-4">Unduh Ringkasan</h3>
            <button class="w-full flex items-center justify-center gap-2 p-3 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">Unduh Ringkasan Order (PDF)</span>
            </button>
        </div>
        
        {{-- Butuh Bantuan --}}
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-fade-in-up delay-400">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="font-medium text-gray-800 dark:text-gray-100">Butuh Bantuan?</h3>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Tim support kami siap membantu Anda 24/7</p>
            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2.5 rounded-xl font-medium transition text-sm">
                Hubungi Support
            </button>
        </div>
    </div>
</div>

</x-layouts.dashboard>

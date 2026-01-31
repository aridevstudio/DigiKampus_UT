<x-layouts.dashboard :active="'finance'">

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('mahasiswa.finance') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Detail Transaksi</h1>
            <p class="text-gray-500 dark:text-gray-400">Informasi lengkap pembayaran kursus</p>
        </div>
    </div>
    
    {{-- Status Badge --}}
    <div class="flex justify-end">
        @if($transaction['status'] == 'aktif')
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400">
            <span class="w-2 h-2 rounded-full bg-green-500"></span>
            Pembayaran Berhasil
        </span>
        @elseif($transaction['status'] == 'pending')
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400">
            <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
            Menunggu Pembayaran
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400">
            <span class="w-2 h-2 rounded-full bg-red-500"></span>
            Pembayaran Gagal
        </span>
        @endif
    </div>
</div>

{{-- Transaction Info Card --}}
<div class="bg-white dark:bg-[#1f2937] rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm p-6 mb-6 animate-fade-in-up" style="animation-delay: 100ms">
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Informasi Transaksi</h2>
    
    <div class="space-y-4">
        {{-- ID Transaksi --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">ID Transaksi</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['id'] }}</span>
        </div>
        
        {{-- Tanggal --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Tanggal & Waktu Transaksi</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['date']->format('d F Y, H:i') }} WIB</span>
        </div>
        
        {{-- Nama Kursus --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Nama Kursus</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['course_name'] }}</span>
        </div>
        
        {{-- Metode Pembayaran --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Metode Pembayaran</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['method'] }}</span>
        </div>
        
        {{-- Status --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Status Pembayaran</span>
            @if($transaction['status'] == 'aktif')
            <span class="font-semibold text-green-600 dark:text-green-400">Berhasil</span>
            @elseif($transaction['status'] == 'pending')
            <span class="font-semibold text-yellow-600 dark:text-yellow-400">Menunggu Pembayaran</span>
            @else
            <span class="font-semibold text-red-600 dark:text-red-400">Gagal</span>
            @endif
        </div>
        
        {{-- Nominal --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Nominal Pembayaran</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($transaction['amount'], 0, ',', '.') }}</span>
        </div>
        
        {{-- Biaya Admin --}}
        <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-700/50">
            <span class="text-gray-500 dark:text-gray-400">Biaya Admin</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">Rp {{ number_format($transaction['admin_fee'], 0, ',', '.') }}</span>
        </div>
        
        {{-- Total --}}
        <div class="flex justify-between items-center py-4 bg-blue-50 dark:bg-blue-500/10 -mx-6 px-6 rounded-b-xl mt-4">
            <span class="font-semibold text-gray-700 dark:text-gray-300">Total Dibayarkan</span>
            <span class="text-xl font-bold text-blue-600 dark:text-blue-400">Rp {{ number_format($transaction['total'], 0, ',', '.') }}</span>
        </div>
    </div>
</div>

{{-- Payment Instructions (only for pending) --}}
@if($transaction['status'] == 'pending')
<div class="bg-white dark:bg-[#1f2937] rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm p-6 mb-6 animate-fade-in-up" style="animation-delay: 200ms">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Instruksi Pembayaran</h2>
    </div>
    
    <div class="space-y-4">
        {{-- Bank Name --}}
        <div class="flex justify-between items-center py-3">
            <span class="text-blue-600 dark:text-blue-400">Nama Bank</span>
            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['bank_name'] }}</span>
        </div>
        
        {{-- VA Number --}}
        <div class="flex justify-between items-center py-3">
            <span class="text-blue-600 dark:text-blue-400">Nomor Virtual Account</span>
            <div class="flex items-center gap-3">
                <span id="va-number" class="font-mono font-semibold text-gray-800 dark:text-gray-200">{{ $transaction['va_number'] }}</span>
                <button onclick="copyVA()" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-lg flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                    </svg>
                    Salin
                </button>
            </div>
        </div>
        
        {{-- Deadline --}}
        <div class="flex justify-between items-center py-3">
            <span class="text-blue-600 dark:text-blue-400">Batas Waktu Pembayaran</span>
            <span class="font-semibold text-red-600 dark:text-red-400">{{ $transaction['deadline']->format('d F Y, H:i') }} WIB</span>
        </div>
    </div>
    
    {{-- Warning --}}
    <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-500/10 rounded-xl flex items-start gap-3">
        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <p class="text-sm text-yellow-700 dark:text-yellow-300">Lakukan pembayaran sebelum batas waktu berakhir agar transaksi tidak dibatalkan.</p>
    </div>
</div>

{{-- Upload Section --}}
<div class="bg-white dark:bg-[#1f2937] rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm p-6 mb-6 animate-fade-in-up" style="animation-delay: 300ms">
    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-6">Bukti & Invoice</h2>
    
    <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="text-gray-700 dark:text-gray-300 font-medium mb-2">Unggah Bukti Pembayaran</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Format yang didukung: JPG, PNG, PDF (Maks. 5MB)</p>
        
        <label class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl cursor-pointer transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
            </svg>
            Pilih File
            <input type="file" class="hidden" accept=".jpg,.jpeg,.png,.pdf">
        </label>
    </div>
</div>
@endif

{{-- Action Buttons --}}
<div class="flex items-center justify-between animate-fade-in-up" style="animation-delay: 400ms">
    <a href="{{ route('mahasiswa.finance') }}" class="inline-flex items-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Finance
    </a>
    
    @if($transaction['status'] == 'pending')
    <button class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Bayar Sekarang
    </button>
    @endif
</div>

<script>
function copyVA() {
    const vaNumber = document.getElementById('va-number').textContent;
    navigator.clipboard.writeText(vaNumber).then(() => {
        alert('Nomor VA berhasil disalin!');
    });
}
</script>

</x-layouts.dashboard>

<x-layouts.dosen title="Segera Hadir" active="">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center p-6">
        <div class="w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Fitur Sedang Dalam Pengembangan</h1>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">Maaf, fitur ini belum tersedia saat ini. Kami sedang bekerja keras untuk menghadirkan pengalaman terbaik bagi Anda.</p>
        
        <a href="{{ route('dosen.dashboard') }}" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition shadow-lg shadow-blue-500/30">
            Kembali ke Dashboard
        </a>
    </div>
</x-layouts.dosen>

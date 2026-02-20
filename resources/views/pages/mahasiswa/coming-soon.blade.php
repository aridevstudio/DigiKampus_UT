<x-layouts.dashboard :active="$active ?? 'coming-soon'">
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        {{-- Icon --}}
        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-6 animate-bounce">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        
        {{-- Title --}}
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Segera Hadir! 🚀</h1>
        
        {{-- Description --}}
        <p class="text-gray-500 dark:text-gray-400 max-w-md mb-8">
            Fitur <span class="font-semibold text-blue-500">{{ $title ?? 'ini' }}</span> sedang dalam tahap pengembangan. 
            Kami bekerja keras untuk menghadirkan pengalaman terbaik untukmu!
        </p>
        
        {{-- Progress Bar --}}
        <div class="w-full max-w-xs mb-8">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-gray-500 dark:text-gray-400">Progress Pengembangan</span>
                <span class="text-blue-500 font-medium">75%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500 rounded-full" style="width: 75%"></div>
            </div>
        </div>
        
        {{-- Features Preview --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Performa Cepat</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Mudah Digunakan</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Aman & Terpercaya</p>
            </div>
        </div>
        
        {{-- Back Button --}}
        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
    </div>
</x-layouts.dashboard>

<x-layouts.dashboard :active="'finance'">

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Finance</h1>
    <p class="text-gray-500 dark:text-gray-400">Riwayat pembayaran dan status transaksi kursus Anda</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 animate-fade-in-up" style="animation-delay: 100ms">
    {{-- Total Pembayaran --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pembayaran</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    
    {{-- Pembayaran Berhasil --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran Berhasil</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $successCount }}</p>
            </div>
        </div>
    </div>
    
    {{-- Pembayaran Menunggu --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran Menunggu</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>
    
    {{-- Pembayaran Gagal --}}
    <div class="bg-white dark:bg-[#1f2937] rounded-2xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pembayaran Gagal</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $failedCount }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-[#1f2937] rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50 shadow-sm mb-6 animate-fade-in-up" style="animation-delay: 200ms">
    <form action="{{ route('mahasiswa.finance') }}" method="GET" class="flex flex-col md:flex-row gap-4">
        {{-- Search --}}
        <div class="flex-1">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Cari transaksi atau kursus..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        {{-- Date Filter --}}
        <select name="date" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Tanggal</option>
            <option value="today">Hari Ini</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
        </select>
        
        {{-- Status Filter --}}
        <select name="status" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-[#111827] text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="semua" {{ $selectedStatus == 'semua' ? 'selected' : '' }}>Semua Status</option>
            <option value="aktif" {{ $selectedStatus == 'aktif' ? 'selected' : '' }}>Berhasil</option>
            <option value="pending" {{ $selectedStatus == 'pending' ? 'selected' : '' }}>Menunggu</option>
            <option value="gagal" {{ $selectedStatus == 'gagal' ? 'selected' : '' }}>Gagal</option>
        </select>
        
        {{-- Buttons --}}
        <button type="submit" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-medium transition">
            Filter
        </button>
        <a href="{{ route('mahasiswa.finance') }}" class="px-4 py-2.5 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reset Filter
        </a>
    </form>
</div>

{{-- Transactions Table --}}
<div class="bg-white dark:bg-[#1f2937] rounded-2xl border border-gray-100 dark:border-gray-700/50 shadow-sm overflow-hidden animate-fade-in-up" style="animation-delay: 300ms">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/50">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Tanggal</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">ID Transaksi</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Kursus</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Metode</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Jumlah</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                @forelse($transactions as $transaction)
                @php $paymentMethods = ['Virtual Account', 'E-Wallet', 'Transfer Bank']; @endphp
                <tr onclick="window.location='{{ route('mahasiswa.transaction-detail', ['id' => $transaction->id_enroll]) }}'" class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition cursor-pointer">
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $transaction->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-blue-600 dark:text-blue-400">
                        #TRX{{ str_pad($transaction->id_enroll, 6, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        {{ $transaction->course->nama_course ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $paymentMethods[array_rand($paymentMethods)] }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-gray-200">
                        Rp {{ number_format($transaction->course->harga ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->status == 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Berhasil
                        </span>
                        @elseif($transaction->status == 'pending')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                            Menunggu
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            Gagal
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400 dark:text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p>Belum ada transaksi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} transaksi
        </p>
        <div class="flex items-center gap-2">
            @if($transactions->onFirstPage())
            <span class="px-3 py-1 text-sm text-gray-400 dark:text-gray-600">Sebelumnya</span>
            @else
            <a href="{{ $transactions->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-500 transition">Sebelumnya</a>
            @endif
            
            @foreach($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                @if($page == $transactions->currentPage())
                <span class="px-3 py-1 text-sm bg-blue-500 text-white rounded-lg">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">{{ $page }}</a>
                @endif
            @endforeach
            
            @if($transactions->hasMorePages())
            <a href="{{ $transactions->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-500 transition">Selanjutnya</a>
            @else
            <span class="px-3 py-1 text-sm text-gray-400 dark:text-gray-600">Selanjutnya</span>
            @endif
        </div>
    </div>
    @endif
</div>

</x-layouts.dashboard>

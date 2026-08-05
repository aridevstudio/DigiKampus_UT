<x-layouts.admin title="System Log Viewer">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">System Log Viewer</h1>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Pantau log aplikasi Laravel (<code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono text-[11px]">storage/logs/laravel.log</code>) secara real-time langsung dari dashboard admin.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.system-logs.download') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Unduh Log
                </a>
                <form action="{{ route('admin.system-logs.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan berkas laravel.log? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:hover:bg-rose-900/50 dark:text-rose-400 text-xs font-semibold transition border border-rose-200/50 dark:border-rose-800/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Bersihkan Log
                    </button>
                </form>
            </div>
        </div>

        {{-- Status Notification --}}
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300 text-xs font-medium flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Metrics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Ukuran Log File</span>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $fileSizeFormatted }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Error & Critical</span>
                    <p class="text-lg font-bold text-rose-600 dark:text-rose-400 mt-1">{{ $totalErrors }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total Warning</span>
                    <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $totalWarnings }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Terakhir Diperbarui</span>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $lastModifiedFormatted }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <form method="GET" action="{{ route('admin.system-logs') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari pesan log, kata kunci, atau stack trace..." class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                {{-- Level Select --}}
                <div class="w-full sm:w-48">
                    <select name="level" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        <option value="all" {{ $levelFilter === 'all' ? 'selected' : '' }}>Semua Level</option>
                        <option value="error" {{ $levelFilter === 'error' ? 'selected' : '' }}>Error & Critical</option>
                        <option value="warning" {{ $levelFilter === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="info" {{ $levelFilter === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="debug" {{ $levelFilter === 'debug' ? 'selected' : '' }}>Debug</option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shadow-sm">
                        Filter
                    </button>
                    @if($search !== '' || $levelFilter !== 'all')
                        <a href="{{ route('admin.system-logs') }}" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs font-medium transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Log Entries List --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>Daftar Log Terkini</span>
                    <span class="px-2 py-0.5 text-[11px] rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 font-semibold">
                        {{ $totalEntries }} entri
                    </span>
                </h2>
                <span class="text-xs text-gray-400">Menampilkan hingga 300 entri terbaru</span>
            </div>

            @if(empty($logs))
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Tidak Ada Log Ditemukan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                        @if($search !== '' || $levelFilter !== 'all')
                            Tidak ada log yang cocok dengan kriteria pencarian/filter yang Anda pilih.
                        @else
                            Berkas <code class="font-mono">laravel.log</code> kosong atau belum pernah mencatat error.
                        @endif
                    </p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @foreach($logs as $index => $log)
                        @php
                            $lvl = strtolower($log['level']);
                            $badgeClass = match (true) {
                                in_array($lvl, ['error', 'critical', 'emergency', 'alert']) => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400 dark:border-rose-800/50',
                                $lvl === 'warning' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50',
                                $lvl === 'notice' || $lvl === 'info' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50',
                                default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                            };
                        @endphp
                        <div x-data="{ open: false }" class="p-4 hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition">
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 cursor-pointer" @click="open = !open">
                                <div class="space-y-1.5 min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold rounded-md border {{ $badgeClass }}">
                                            {{ $log['level'] }}
                                        </span>
                                        <span class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                            {{ $log['timestamp'] }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 font-mono">
                                            [{{ $log['environment'] }}]
                                        </span>
                                    </div>

                                    <p class="text-xs font-mono text-gray-900 dark:text-gray-100 font-semibold break-words leading-relaxed">
                                        {{ $log['message'] }}
                                    </p>
                                </div>

                                @if(!empty($log['context']))
                                    <button type="button" class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 dark:text-blue-400 hover:underline flex-shrink-0 self-start sm:self-center">
                                        <span x-text="open ? 'Sembunyikan Trace' : 'Lihat Trace'">Lihat Trace</span>
                                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            @if(!empty($log['context']))
                                <div x-show="open" x-collapse class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                                    <div class="bg-gray-900 text-gray-100 p-4 rounded-xl font-mono text-[11px] leading-relaxed overflow-x-auto max-h-96 whitespace-pre-wrap select-all">
                                        {{ $log['context'] }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>

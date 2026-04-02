<x-layouts.admin title="Kelola Kursus" active="kursus">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kursus</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data kursus, tambah kursus baru, dan atur status publikasi.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.kursus') }}" class="admin-toolbar-responsive admin-toolbar-kursus bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        <div class="admin-toolbar-shell admin-toolbar-kursus-row">
            {{-- Buttons --}}
            <div class="admin-toolbar-actions admin-toolbar-kursus-buttons flex flex-wrap gap-1.5">
                <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-blue-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kursus
                </button>
                <button type="button" onclick="openAddWebinarModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-purple-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Tambah Webinar
                </button>
            </div>

            <div class="admin-toolbar-divider admin-toolbar-kursus-divider w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Filters --}}
            <div class="admin-toolbar-filters admin-toolbar-kursus-filters grid grid-cols-2 gap-1.5">
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2.5 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ ($statusFilter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="ditolak" {{ ($statusFilter ?? '') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="relative">
                    <select name="tipe" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2.5 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($tipeFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Harga</option>
                        <option value="gratis" {{ ($tipeFilter ?? '') === 'gratis' ? 'selected' : '' }}>Gratis</option>
                        <option value="berbayar" {{ ($tipeFilter ?? '') === 'berbayar' ? 'selected' : '' }}>Berbayar</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="relative col-span-2 sm:col-span-1">
                    <select name="kategori" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2.5 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($kategoriFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="webinar" {{ ($kategoriFilter ?? '') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                        <option value="tiket" {{ ($kategoriFilter ?? '') === 'tiket' ? 'selected' : '' }}>Tiket</option>
                        <option value="kursus" {{ ($kategoriFilter ?? '') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Search --}}
            <div class="admin-toolbar-search admin-toolbar-kursus-search flex gap-1.5">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kursus..." class="w-full px-3 py-1.5 pl-9 sm:px-4 sm:py-2.5 sm:pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 absolute left-2.5 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 sm:px-4 sm:py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium rounded-xl transition flex-shrink-0">
                    Cari
                </button>
            </div>
        </div>
    </form>

    {{-- Summary Stats --}}
    @if($totalKursus > 0)
    <div class="responsive-grid-stats mb-6">
        @php
            $kategoriStats = collect($kursusList)->groupBy('kategori')->map->count();
            $statItems = [
                ['label' => 'Total Kursus', 'count' => $totalKursus, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'blue'],
                ['label' => 'Webinar', 'count' => $kategoriStats->get('webinar', 0), 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'purple'],
                ['label' => 'Kursus', 'count' => $kategoriStats->get('kursus', 0), 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'indigo'],
                ['label' => 'Tiket', 'count' => $kategoriStats->get('tiket', 0), 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'color' => 'orange'],
            ];
            $colorMap = [
                'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'icon' => 'text-blue-500 dark:text-blue-400', 'text' => 'text-blue-700 dark:text-blue-300'],
                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'icon' => 'text-purple-500 dark:text-purple-400', 'text' => 'text-purple-700 dark:text-purple-300'],
                'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'icon' => 'text-indigo-500 dark:text-indigo-400', 'text' => 'text-indigo-700 dark:text-indigo-300'],
                'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'icon' => 'text-orange-500 dark:text-orange-400', 'text' => 'text-orange-700 dark:text-orange-300'],
            ];
        @endphp
        @foreach($statItems as $stat)
        <div class="flex items-center gap-3 p-3.5 rounded-xl {{ $colorMap[$stat['color']]['bg'] }} border border-{{ $stat['color'] }}-100 dark:border-{{ $stat['color'] }}-800/30">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center">
                <svg class="w-5 h-5 {{ $colorMap[$stat['color']]['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold {{ $colorMap[$stat['color']]['text'] }}">{{ $stat['count'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 -mt-0.5">{{ $stat['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table admin-desktop-table admin-mobile-list">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kursus</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengajar</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peserta</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($kursusList as $index => $kursus)
                    <tr class="group hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors duration-150">
                        {{-- No --}}
                        <td class="px-5 py-4">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ ($kursusPaginated->currentPage() - 1) * $kursusPaginated->perPage() + $index + 1 }}</span>
                        </td>

                        {{-- Kursus (Thumbnail + Nama + Kode) --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3.5">
                                <div class="flex-shrink-0 relative">
                                    @if($kursus['video_thumbnail'] ?? null)
                                        <img src="{{ $kursus['video_thumbnail'] }}" alt="{{ $kursus['nama'] }}" class="w-14 h-14 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-600 shadow-sm">
                                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800 shadow-sm" title="Video Preview">
                                            <svg class="w-3 h-3 text-white ml-px" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </span>
                                    @elseif($kursus['thumbnail'])
                                        <img src="{{ asset('storage/' . $kursus['thumbnail']) }}" alt="{{ $kursus['nama'] }}" class="w-14 h-14 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-600 shadow-sm">
                                    @else
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 ring-1 ring-blue-200/50 dark:ring-blue-700/30 flex items-center justify-center shadow-sm">
                                            <svg class="w-6 h-6 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if($kursus['has_youtube'] && !($kursus['video_thumbnail'] ?? null))
                                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800 shadow-sm" title="YouTube Playlist">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[120px] sm:max-w-[180px] lg:max-w-[240px] group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $kursus['nama'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-mono">{{ $kursus['kode'] }}</p>
                                    @if(($kursus['module_count'] ?? 0) > 0)
                                    <p class="text-[11px] sm:text-xs text-blue-500 dark:text-blue-400 mt-0.5 font-medium">{{ $kursus['module_count'] }} modul</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Pengajar --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center shadow-sm">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ strtoupper(substr($kursus['dosen'] ?? 'N', 0, 1)) }}</span>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[140px]">{{ $kursus['dosen'] ?? '-' }}</span>
                            </div>
                        </td>

                        {{-- Peserta --}}
                        <td class="px-5 py-4 text-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ number_format($kursus['enrollments_count']) }}</span>
                            </div>
                        </td>

                        {{-- Kategori --}}
                        <td class="px-5 py-4 text-center">
                            @php
                                $kategoriConfig = match($kursus['kategori'] ?? 'kursus') {
                                    'webinar' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'text' => 'text-purple-700 dark:text-purple-400', 'border' => 'border-purple-200 dark:border-purple-700/40', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'label' => 'Webinar'],
                                    'tiket' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-700/40', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'label' => 'Tiket'],
                                    'kursus' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'text' => 'text-indigo-700 dark:text-indigo-400', 'border' => 'border-indigo-200 dark:border-indigo-700/40', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Kursus'],
                                    default => ['bg' => 'bg-gray-50 dark:bg-gray-700/30', 'text' => 'text-gray-600 dark:text-gray-400', 'border' => 'border-gray-200 dark:border-gray-600/40', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => ucfirst($kursus['kategori'] ?? 'kursus')],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $kategoriConfig['bg'] }} {{ $kategoriConfig['text'] }} {{ $kategoriConfig['border'] }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $kategoriConfig['icon'] }}" />
                                </svg>
                                {{ $kategoriConfig['label'] }}
                            </span>
                        </td>

                        {{-- Harga --}}
                        <td class="px-5 py-4 text-center">
                            @if(($kursus['harga'] ?? 0) > 0)
                                @if(($kursus['diskon'] ?? 0) > 0)
                                    @php
                                        $hargaDiskon = $kursus['harga'] - ($kursus['harga'] * $kursus['diskon'] / 100);
                                    @endphp
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] font-medium text-red-500 uppercase tracking-wider bg-red-50 dark:bg-red-500/10 px-1.5 py-0.5 rounded">Diskon {{ $kursus['diskon'] }}%</span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 line-through">Rp {{ number_format($kursus['harga'], 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($kursus['harga'], 0, ',', '.') }}</span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700/40">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Gratis
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4 text-center">
                            @php
                                $displayStatus = match (true) {
                                    ($kursus['approval_status'] ?? null) === 'pending' => 'pending',
                                    ($kursus['approval_status'] ?? null) === 'ditolak' => 'ditolak',
                                    default => $kursus['status'],
                                };
                                $statusConfig = [
                                    'aktif' => ['dot' => 'bg-green-500', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-700 dark:text-green-400', 'border' => 'border-green-200 dark:border-green-700/40'],
                                    'draft' => ['dot' => 'bg-yellow-500', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-700 dark:text-yellow-400', 'border' => 'border-yellow-200 dark:border-yellow-700/40'],
                                    'pending' => ['dot' => 'bg-blue-500', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-700/40'],
                                    'ditolak' => ['dot' => 'bg-rose-500', 'bg' => 'bg-rose-50 dark:bg-rose-900/20', 'text' => 'text-rose-700 dark:text-rose-400', 'border' => 'border-rose-200 dark:border-rose-700/40'],
                                    'nonaktif' => ['dot' => 'bg-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'border' => 'border-red-200 dark:border-red-700/40'],
                                ];
                                $sc = $statusConfig[$displayStatus] ?? $statusConfig['draft'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} animate-pulse"></span>
                                {{ $displayStatus === 'pending' ? 'Menunggu Persetujuan' : ($displayStatus === 'ditolak' ? 'Ditolak' : ucfirst($displayStatus)) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-4 text-center">
                            <div class="inline-flex flex-wrap items-center justify-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-0.5">
                                @if(($kursus['kategori'] ?? '') === 'webinar' && ($kursus['approval_status'] ?? '') === 'pending')
                                <button onclick="approveWebinar({{ $kursus['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Setujui Webinar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setujui
                                </button>
                                <button onclick="rejectWebinar({{ $kursus['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Tolak Webinar">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </button>
                                <div class="w-px h-4 bg-gray-200 dark:bg-gray-600"></div>
                                @endif
                                <button onclick="openEditModal({{ $kursus['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Edit Kursus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <div class="w-px h-4 bg-gray-200 dark:bg-gray-600"></div>
                                <button onclick="confirmDelete({{ $kursus['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Hapus Kursus">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-500 dark:text-gray-400">Belum ada data kursus</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Kursus" untuk menambahkan kursus baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($totalKursus > 0)
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-900/20 admin-responsive-pagination">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $kursusPaginated->firstItem() ?? 0 }}</span>-<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $kursusPaginated->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $totalKursus }}</span> kursus
            </p>
            <div class="flex flex-wrap items-center gap-1 admin-responsive-actions">
                @if($kursusPaginated->onFirstPage())
                <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                @else
                <a href="{{ $kursusPaginated->previousPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif
                
                @for($i = 1; $i <= $kursusPaginated->lastPage(); $i++)
                    @if($i <= 4 || $i === $kursusPaginated->lastPage())
                    <a href="{{ $kursusPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-semibold rounded-lg transition {{ $i === $currentPage ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-sm' }}">
                        {{ $i }}
                    </a>
                    @elseif($i === 5 && $kursusPaginated->lastPage() > 5)
                    <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor
                
                @if($kursusPaginated->hasMorePages())
                <a href="{{ $kursusPaginated->nextPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Add Kursus Modal --}}
    <div id="addKursusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form action="{{ route('admin.kursus.store') }}" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    
                    {{-- Modal Header --}}
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Kursus Baru</h3>
                        <p class="text-sm text-blue-500">Lengkapi informasi berikut untuk membuat kursus baru.</p>
                    </div>
                    
                    <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-1">
                        {{-- 1. Informasi Dasar Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center">1</span>
                                Informasi Dasar Kursus
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Judul Kursus <span class="text-red-400">*</span></label>
                                        <input type="text" name="nama_course" id="add_nama_course" required placeholder="Masukkan judul kursus" value="{{ old('_modal') === 'add' ? old('nama_course') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus <span class="text-gray-300 dark:text-gray-600">(otomatis)</span></label>
                                        <input type="text" name="kode_course" id="add_kode_course" readonly value="{{ old('_modal') === 'add' ? old('kode_course') : ($nextKursusCode ?? 'KRS01') }}" class="w-full px-3 py-2.5 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white cursor-not-allowed">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Kursus</label>
                                    <textarea name="deskripsi" id="add_deskripsi" rows="3" placeholder="Jelaskan tentang kursus ini..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('_modal') === 'add' ? old('deskripsi') : '' }}</textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Persyaratan Kursus (Opsional)</label>
                                    <textarea name="persyaratan" id="add_persyaratan" rows="3" placeholder="Contoh:&#10;STIN4101 - Pengantar Teknologi Informasi&#10;Memiliki laptop dan koneksi internet stabil" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('_modal') === 'add' ? old('persyaratan') : '' }}</textarea>
                                    <p class="text-xs text-gray-400 mt-1">Tulis satu persyaratan per baris.</p>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div x-data="dosenSearch('add')" class="relative">
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Dosen Pengampu <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <input type="hidden" name="id_dosen" :value="selectedId" id="add_id_dosen">
                                        <div class="relative">
                                            <input type="text" x-model="search" @focus="open = true" @click="open = true" @input="open = true" placeholder="Cari dosen..." autocomplete="off" class="w-full px-3 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <button type="button" x-show="selectedId" @click="clear()" class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div x-show="open && filteredItems().length > 0" @click.outside="open = false" x-transition class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto" style="display:none">
                                            <template x-for="item in filteredItems()" :key="item.id">
                                                <button type="button" @click="select(item)" class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-900 dark:text-white flex items-center gap-2 transition">
                                                    <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0" x-text="item.name.charAt(0).toUpperCase()"></span>
                                                    <span x-text="item.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <div class="relative">
                                            <select name="id_jurusan" id="add_id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Pilih Prodi</option>
                                                @foreach($jurusanList as $jurusan)
                                                <option value="{{ $jurusan->id_jurusan }}" {{ old('_modal') === 'add' && old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tingkat Kesulitan <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <div class="relative">
                                            <select name="level" id="add_level" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Pilih Tingkat</option>
                                                <option value="Pemula" {{ old('_modal') === 'add' && old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                                <option value="Menengah" {{ old('_modal') === 'add' && old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                                <option value="Mahir" {{ old('_modal') === 'add' && old('level') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                            </select>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Estimasi Waktu</label>
                                            <input type="number" name="estimasi_waktu" id="add_estimasi_waktu" min="0" placeholder="20" value="{{ old('_modal') === 'add' ? old('estimasi_waktu', 20) : 20 }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Satuan</label>
                                            <div class="relative">
                                                <select name="durasi_satuan" id="add_durasi_satuan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="Jam" {{ old('_modal') === 'add' && old('durasi_satuan', 'Jam') === 'Jam' ? 'selected' : '' }}>Jam</option>
                                                    <option value="Minggu" {{ old('_modal') === 'add' && old('durasi_satuan') === 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                                </select>
                                                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Thumbnail --}}
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Thumbnail Kursus</label>
                                    <div class="flex items-center gap-4">
                                        <div id="thumbnailPreview" class="w-20 h-14 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Upload Thumbnail
                                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="5" class="hidden" onchange="previewThumbnail(this)">
                                            </label>
                                            <p class="text-xs text-gray-400 mt-1">Maksimal 5MB, JPG/PNG/WebP</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 2. Modul Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold flex items-center justify-center">2</span>
                                Modul Kursus
                            </h4>
                            <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 p-4 rounded-lg text-sm flex items-start gap-3">
                                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>Pengelolaan modul dan materi kursus (seperti video, bacaan, dan kuis) dapat dilakukan di halaman <strong>Kelola Modul</strong> setelah kursus berhasil dibuat.</p>
                            </div>
                        </div>
                        
                        {{-- 3. Pengaturan Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold flex items-center justify-center">3</span>
                                Pengaturan Kursus
                            </h4>
                            
                            <div class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    {{-- Status Kursus Toggle --}}
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">Status Kursus</h5>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Aktif atau simpan draft</p>
                                        </div>
                                        <input type="hidden" name="status" id="add_status_input" value="{{ old('_modal') === 'add' ? old('status', 'draft') : 'draft' }}">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="add_status_toggle" class="sr-only peer" {{ old('_modal') === 'add' && old('status') === 'aktif' ? 'checked' : '' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                    </div>

                                    {{-- Akses Publik Toggle --}}
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">Akses Publik</h5>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Tampil untuk semua</p>
                                        </div>
                                        <input type="hidden" name="akses_publik" value="0">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="akses_publik" id="add_akses_publik" value="1" class="sr-only peer" {{ old('_modal') === 'add' ? (old('akses_publik') ? 'checked' : '') : 'checked' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Sertifikat Toggle --}}
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white text-xs">Sertifikat Penyelesaian</h5>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Berikan sertifikat setelah selesai</p>
                                    </div>
                                    <input type="hidden" name="sertifikat" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="sertifikat" id="add_sertifikat" value="1" class="sr-only peer" {{ old('_modal') === 'add' && old('sertifikat') ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 4. Pricing & Akses Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center justify-center">4</span>
                                Pricing & Akses Kursus
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kategori Kursus</label>
                                    <div class="relative">
                                        <select name="kategori" id="add_kategori" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="kursus" {{ old('_modal') === 'add' && old('kategori') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                            <option value="webinar" {{ old('_modal') === 'add' && old('kategori') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                                            <option value="tiket" {{ old('_modal') === 'add' && old('kategori') === 'tiket' ? 'selected' : '' }}>Tiket</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Playlist YouTube <span class="text-gray-300 dark:text-gray-600">(opsional, khusus kursus)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 4h4a2 2 0 002-2V8a2 2 0 00-2-2H9a2 2 0 00-2 2v8a2 2 0 002 2zM5 8v8" /></svg>
                                        <input type="url" name="youtube_playlist" id="add_youtube_playlist" placeholder="https://www.youtube.com/playlist?list=..." value="{{ old('_modal') === 'add' ? old('youtube_playlist') : '' }}" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Saat kursus disimpan, semua video playlist akan diimpor jadi materi video otomatis.</p>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                        <input type="number" name="harga" id="add_harga" min="0" placeholder="0" value="{{ old('_modal') === 'add' ? old('harga', 0) : 0 }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Diskon (%)</label>
                                        <input type="number" name="diskon" id="add_diskon" min="0" max="100" placeholder="0" value="{{ old('_modal') === 'add' ? old('diskon', 0) : 0 }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="flex items-center gap-2 py-2.5">
                                        <input type="hidden" name="tipe" id="add_tipe_input" value="{{ old('_modal') === 'add' ? old('tipe', 'berbayar') : 'berbayar' }}">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="add_gratis_toggle" class="sr-only peer" {{ old('_modal') === 'add' && old('tipe') === 'gratis' ? 'checked' : '' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Gratis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" name="add_status_btn" value="draft" class="px-4 py-2 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 hover:bg-blue-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Simpan Draft
                        </button>
                        <button type="submit" name="add_status_btn" value="aktif" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-blue-500/25">
                            Buat Kursus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Webinar Modal --}}
    <div id="addWebinarModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddWebinarModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                <button onclick="closeAddWebinarModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form action="{{ route('admin.kursus.store') }}" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="kategori" value="webinar">
                    <input type="hidden" name="_modal" value="add_webinar">

                    {{-- Modal Header --}}
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Webinar Baru</h3>
                        <p class="text-sm text-purple-500">Lengkapi informasi webinar yang akan dilaksanakan.</p>
                    </div>

                    <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-1">
                        {{-- 1. Informasi Dasar Webinar --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">1</span>
                                Informasi Dasar Webinar
                            </h4>

                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Judul Webinar <span class="text-red-400">*</span></label>
                                        <input type="text" name="nama_course" id="webinar_nama_course" required placeholder="Masukkan judul webinar" value="{{ old('_modal') === 'add_webinar' ? old('nama_course') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                        @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Webinar <span class="text-gray-300 dark:text-gray-600">(otomatis)</span></label>
                                        <input type="text" name="kode_course" id="webinar_kode_course" readonly value="{{ old('_modal') === 'add_webinar' ? old('kode_course') : ($nextWebinarCode ?? 'WEB01') }}" class="w-full px-3 py-2.5 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white cursor-not-allowed">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Webinar <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                    <textarea name="deskripsi" id="webinar_deskripsi" rows="3" placeholder="Jelaskan topik dan manfaat webinar ini..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none">{{ old('_modal') === 'add_webinar' ? old('deskripsi') : '' }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div x-data="dosenSearch('webinar')" class="relative">
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Pembicara <span class="text-gray-300 dark:text-gray-600">(dosen/admin, opsional)</span></label>
                                        <input type="hidden" name="id_dosen" :value="selectedId" id="webinar_id_dosen">
                                        <div class="relative">
                                            <input type="text" x-model="search" @focus="open = true" @click="open = true" @input="open = true" placeholder="Cari dosen atau admin..." autocomplete="off" class="w-full px-3 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <button type="button" x-show="selectedId" @click="clear()" class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div x-show="open && filteredItems().length > 0" @click.outside="open = false" x-transition class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto" style="display:none">
                                            <template x-for="item in filteredItems()" :key="item.id">
                                                <button type="button" @click="select(item)" class="w-full text-left px-3 py-2 text-sm hover:bg-purple-50 dark:hover:bg-purple-900/20 text-gray-900 dark:text-white flex items-center gap-2 transition">
                                                    <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0" x-text="item.name.charAt(0).toUpperCase()"></span>
                                                    <div class="min-w-0">
                                                        <span class="block truncate" x-text="item.name"></span>
                                                        <span class="block text-[10px] text-gray-400" x-text="item.role_label"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <div class="relative">
                                            <select name="id_jurusan" id="webinar_id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                                <option value="">Pilih Prodi</option>
                                                @foreach($jurusanList as $jurusan)
                                                <option value="{{ $jurusan->id_jurusan }}" {{ old('_modal') === 'add_webinar' && old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Thumbnail --}}
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Thumbnail Webinar <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                    <div class="flex items-center gap-4">
                                        <div id="webinarThumbnailPreview" class="w-20 h-14 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="thumbnail" id="webinarThumbnailInput" accept="image/*" data-max-size-mb="5" onchange="previewWebinarThumbnail(event)" class="hidden">
                                            <label for="webinarThumbnailInput" class="inline-flex items-center gap-2 px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg cursor-pointer transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                Unggah Gambar
                                            </label>
                                            <p class="text-[11px] text-gray-400 mt-1">JPG, PNG, maks 5MB. Rasio 16:9 disarankan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Jadwal Pelaksanaan --}}
                        <div class="border border-purple-200 dark:border-purple-800/50 rounded-xl p-5 bg-purple-50/30 dark:bg-purple-900/10">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">2</span>
                                Jadwal Pelaksanaan
                            </h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tanggal Webinar <span class="text-red-400">*</span></label>
                                        <input type="date" name="tanggal_webinar" value="{{ old('_modal') === 'add_webinar' ? old('tanggal_webinar') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Mulai <span class="text-red-400">*</span></label>
                                        <input type="time" name="jam_mulai_webinar" value="{{ old('_modal') === 'add_webinar' ? old('jam_mulai_webinar') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Selesai <span class="text-red-400">*</span></label>
                                        <input type="time" name="jam_selesai_webinar" value="{{ old('_modal') === 'add_webinar' ? old('jam_selesai_webinar') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kuota Peserta <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                    <input type="number" name="kuota_peserta" min="1" placeholder="Kosongkan jika tidak dibatasi" value="{{ old('_modal') === 'add_webinar' ? old('kuota_peserta') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Link Meeting <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                    <div class="relative">
                                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <input type="url" name="youtube_playlist" placeholder="https://zoom.us/j/... atau https://meet.google.com/..." value="{{ old('_modal') === 'add_webinar' ? old('youtube_playlist') : '' }}" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Link Zoom / Google Meet akan dibagikan ke peserta yang terdaftar.</p>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Pengaturan Webinar --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold flex items-center justify-center">3</span>
                                Pengaturan Webinar
                            </h4>
                            <div class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">Status</h5>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Aktifkan langsung atau simpan draft</p>
                                        </div>
                                        <input type="hidden" name="status" id="webinar_status_input" value="{{ old('_modal') === 'add_webinar' ? old('status', 'draft') : 'draft' }}">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="webinar_status_toggle" class="sr-only peer" {{ old('_modal') === 'add_webinar' && old('status') === 'aktif' ? 'checked' : '' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-500"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">Akses Publik</h5>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Tampil untuk semua</p>
                                        </div>
                                        <input type="hidden" name="akses_publik" value="0">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="akses_publik" value="1" class="sr-only peer" {{ old('_modal') === 'add_webinar' ? (old('akses_publik') ? 'checked' : '') : 'checked' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-500"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                    <div>
                                        <h5 class="font-medium text-gray-900 dark:text-white text-xs">Sertifikat Kehadiran</h5>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400">Berikan sertifikat setelah selesai</p>
                                    </div>
                                    <input type="hidden" name="sertifikat" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="sertifikat" value="1" class="sr-only peer" {{ old('_modal') === 'add_webinar' && old('sertifikat') ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Harga & Akses --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center justify-center">4</span>
                                Harga & Akses
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                    <input type="number" name="harga" id="webinar_harga" min="0" placeholder="0" value="{{ old('_modal') === 'add_webinar' ? old('harga', 0) : 0 }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Diskon (%)</label>
                                    <input type="number" name="diskon" id="webinar_diskon" min="0" max="100" placeholder="0" value="{{ old('_modal') === 'add_webinar' ? old('diskon', 0) : 0 }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-2 py-2.5">
                                    <input type="hidden" name="tipe" id="webinar_tipe_input" value="{{ old('_modal') === 'add_webinar' ? old('tipe', 'berbayar') : 'berbayar' }}">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="webinar_gratis_toggle" class="sr-only peer" {{ old('_modal') === 'add_webinar' && old('tipe') === 'gratis' ? 'checked' : '' }}>
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-purple-500"></div>
                                    </label>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Gratis</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeAddWebinarModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" name="add_status_btn" value="draft" class="px-4 py-2 bg-white dark:bg-gray-700 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/30 hover:bg-purple-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Simpan Draft
                        </button>
                        <button type="submit" name="add_status_btn" value="aktif" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-purple-500/25">
                            Publikasikan Webinar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Kursus Modal --}}
    <div id="editKursusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition z-10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form id="editKursusForm" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_id" id="edit_kursus_id" value="{{ old('_id') }}">
                    
                    {{-- Modal Header --}}
                    <div class="mb-4">
                        <h3 id="edit_modal_title" class="text-lg font-bold text-gray-900 dark:text-white">Edit Kursus</h3>
                        <p id="edit_modal_subtitle" class="text-sm text-blue-500">Ubah informasi kursus yang ada.</p>
                    </div>
                    
                    <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-1">
                        {{-- 1. Informasi Dasar Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center">1</span>
                                <span id="edit_basic_section_title">Informasi Dasar Kursus</span>
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label id="edit_nama_course_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Judul Kursus <span class="text-red-400">*</span></label>
                                        <input type="text" name="nama_course" id="edit_nama_course" required placeholder="Masukkan judul kursus" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label id="edit_kode_course_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus <span class="text-red-400">*</span></label>
                                        <input type="text" name="kode_course" id="edit_kode_course" required placeholder="Contoh: CS101" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                
                                <div>
                                    <label id="edit_deskripsi_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Kursus</label>
                                    <textarea name="deskripsi" id="edit_deskripsi" rows="3" placeholder="Jelaskan tentang kursus ini..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                                </div>
                                
                                <div>
                                    <label id="edit_persyaratan_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Persyaratan Kursus (Opsional)</label>
                                    <textarea name="persyaratan" id="edit_persyaratan" rows="3" placeholder="Contoh:&#10;STIN4101 - Pengantar Teknologi Informasi&#10;Memiliki laptop dan koneksi internet stabil" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                                    <p class="text-xs text-gray-400 mt-1">Tulis satu persyaratan per baris.</p>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div x-data="dosenSearch('edit')" x-ref="editDosenWrap" class="relative">
                                        <label id="edit_dosen_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Dosen Pengampu <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <input type="hidden" name="id_dosen" :value="selectedId" id="edit_id_dosen">
                                        <div class="relative">
                                            <input type="text" x-model="search" @focus="open = true" @click="open = true" @input="open = true" placeholder="Cari dosen..." autocomplete="off" class="w-full px-3 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <button type="button" x-show="selectedId" @click="clear()" class="absolute right-8 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div x-show="open && filteredItems().length > 0" @click.outside="open = false" x-transition class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto" style="display:none">
                                            <template x-for="item in filteredItems()" :key="item.id">
                                                <button type="button" @click="select(item)" class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/20 text-gray-900 dark:text-white flex items-center gap-2 transition">
                                                    <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0" x-text="item.name.charAt(0).toUpperCase()"></span>
                                                    <span x-text="item.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <div class="relative">
                                            <select name="id_jurusan" id="edit_id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Pilih Prodi</option>
                                                @foreach($jurusanList as $jurusan)
                                                <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                                                @endforeach
                                            </select>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tingkat Kesulitan <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                        <div class="relative">
                                            <select name="level" id="edit_level" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="">Pilih Tingkat</option>
                                                <option value="Pemula">Pemula</option>
                                                <option value="Menengah">Menengah</option>
                                                <option value="Mahir">Mahir</option>
                                            </select>
                                            <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>
                                    <div id="editDurationFields" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Estimasi Waktu</label>
                                            <input type="number" name="estimasi_waktu" id="edit_estimasi_waktu" min="0" placeholder="20" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Satuan</label>
                                            <div class="relative">
                                                <select name="durasi_satuan" id="edit_durasi_satuan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="Jam">Jam</option>
                                                    <option value="Minggu">Minggu</option>
                                                </select>
                                                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Thumbnail --}}
                                <div>
                                    <label id="edit_thumbnail_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Thumbnail Kursus</label>
                                    <div class="flex items-center gap-4">
                                        <div id="editThumbnailPreview" class="w-20 h-14 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                            <svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Upload Thumbnail
                                                <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="5" class="hidden" onchange="previewEditThumbnail(this)">
                                            </label>
                                            <p class="text-xs text-gray-400 mt-1">Maksimal 5MB, JPG/PNG/WebP</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 2. Modul Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-bold flex items-center justify-center">2</span>
                                <span id="edit_module_section_title">Modul Kursus</span>
                            </h4>
                            <div>
                                <p id="edit_module_section_desc" class="text-sm text-gray-500 dark:text-gray-400 mb-4">Kelola struktur modul, video pembelajaran, bahan bacaan, kuis, dan tugas untuk kursus ini.</p>
                                <a href="#" id="edit_modul_btn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 font-medium rounded-lg transition border border-blue-200 dark:border-blue-800 w-full justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span id="edit_module_button_text">Kelola Modul Kursus</span>
                                </a>
                            </div>
                        </div>
                        
                        {{-- 3. Pengaturan Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold flex items-center justify-center">3</span>
                                <span id="edit_settings_section_title">Pengaturan Kursus</span>
                            </h4>
                            
                            <div class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    {{-- Status Kursus Toggle --}}
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 id="edit_status_title" class="font-medium text-gray-900 dark:text-white text-xs">Status Kursus</h5>
                                            <p id="edit_status_desc" class="text-[10px] text-gray-500 dark:text-gray-400">Aktif atau simpan draft</p>
                                        </div>
                                        <input type="hidden" name="status" id="edit_status_input" value="draft">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="edit_status_toggle" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                    </div>

                                    {{-- Akses Publik Toggle --}}
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">Akses Publik</h5>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-400">Tampil untuk semua</p>
                                        </div>
                                        <input type="hidden" name="akses_publik" value="0">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="edit_akses_publik" name="akses_publik" value="1" class="sr-only peer" checked>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Sertifikat Toggle --}}
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                    <div>
                                        <h5 id="edit_certificate_title" class="font-medium text-gray-900 dark:text-white text-xs">Sertifikat Penyelesaian</h5>
                                        <p id="edit_certificate_desc" class="text-[10px] text-gray-500 dark:text-gray-400">Berikan sertifikat setelah selesai</p>
                                    </div>
                                    <input type="hidden" name="sertifikat" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="edit_sertifikat" name="sertifikat" value="1" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        {{-- 4. Pricing & Akses Kursus --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center justify-center">4</span>
                                <span id="edit_pricing_section_title">Pricing & Akses Kursus</span>
                            </h4>
                            
                            <div class="space-y-4">
                                <div>
                                    <label id="edit_category_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kategori Kursus</label>
                                    <div class="relative">
                                        <select name="kategori" id="edit_kategori" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="kursus">Kursus</option>
                                            <option value="webinar">Webinar</option>
                                            <option value="tiket">Tiket</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>

                                <div id="edit_category_meta_panel" class="rounded-xl border border-purple-100 dark:border-purple-800/40 bg-purple-50/40 dark:bg-purple-900/10 p-4 space-y-4">
                                    <div>
                                        <h5 id="edit_category_meta_title" class="font-medium text-gray-900 dark:text-white text-xs">Jadwal Webinar</h5>
                                        <p id="edit_category_meta_desc" class="text-[10px] text-gray-500 dark:text-gray-400">Field ini digunakan saat kategori webinar.</p>
                                    </div>
                                    <div id="edit_webinar_datetime_group" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tanggal Webinar</label>
                                            <input type="date" name="tanggal_webinar" id="edit_tanggal_webinar" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Mulai</label>
                                            <input type="time" name="jam_mulai_webinar" id="edit_jam_mulai_webinar" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Selesai</label>
                                            <input type="time" name="jam_selesai_webinar" id="edit_jam_selesai_webinar" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div id="edit_category_meta_fields" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div id="edit_kuota_group">
                                            <label id="edit_kuota_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kuota Peserta</label>
                                            <input type="number" name="kuota_peserta" id="edit_kuota_peserta" min="1" placeholder="Kosongkan jika tidak dibatasi" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div id="edit_link_group">
                                            <label id="edit_link_label" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Link Meeting / Playlist</label>
                                            <input type="url" name="youtube_playlist" id="edit_youtube_playlist" placeholder="https://zoom.us/j/... atau https://meet.google.com/..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div id="editPlaylistPanel" class="rounded-xl border border-blue-100 dark:border-blue-800/40 bg-blue-50/40 dark:bg-blue-900/10 p-4 space-y-3 hidden">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <h5 class="font-medium text-gray-900 dark:text-white text-xs">Video Playlist Kursus</h5>
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400">Preview video yang sudah tersinkron ke database dan materi course.</p>
                                            </div>
                                            <button type="button" id="edit_sync_playlist_btn" onclick="syncPlaylistFromEditModal()" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-blue-600">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m14.836 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-14.837-2M20 15h-4.999" /></svg>
                                                Sinkronkan Playlist
                                            </button>
                                        </div>
                                        <div id="editPlaylistStatus" class="rounded-lg border border-dashed border-blue-200 bg-white/80 px-3 py-2 text-xs text-blue-700 dark:border-blue-700 dark:bg-gray-800/60 dark:text-blue-300">
                                            Belum ada data playlist yang dimuat.
                                        </div>
                                        <div id="editPlaylistList" class="grid gap-2 max-h-64 overflow-y-auto pr-1"></div>
                                    </div>
                                    <div id="editApprovalNoteBox" class="hidden rounded-lg border border-rose-100 dark:border-rose-800/40 bg-rose-50 dark:bg-rose-900/10 px-3 py-2 text-xs text-rose-700 dark:text-rose-300"></div>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                        <input type="number" name="harga" id="edit_harga" min="0" placeholder="0" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Diskon (%)</label>
                                        <input type="number" name="diskon" id="edit_diskon" min="0" max="100" placeholder="0" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div class="flex items-center gap-2 py-2.5">
                                        <input type="hidden" name="tipe" id="edit_tipe_input" value="berbayar">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" id="edit_gratis_toggle" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                        </label>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Gratis</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-blue-500/25">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteKursusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Kursus?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data kursus akan dihapus permanen dan tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <form id="deleteKursusForm" method="POST" class="inline" x-data="{ isLoading: false }" @submit="isLoading = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="approveWebinarForm" method="POST" class="hidden">
        @csrf
        @method('PUT')
    </form>

    <form id="rejectWebinarForm" method="POST" class="hidden">
        @csrf
        @method('PUT')
        <input type="hidden" name="approval_notes" id="rejectWebinarNotes">
    </form>

    
    @php
        $dosenSearchItems = $dosenList
            ->map(function ($dosen) {
                return [
                    'id' => $dosen->id,
                    'name' => $dosen->name,
                    'role_label' => 'Dosen',
                ];
            })
            ->values();

        $webinarSpeakerSearchItems = ($webinarSpeakerList ?? $dosenList)
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role_label' => ($user->role ?? null) === 'admin' ? 'Admin' : 'Dosen',
                ];
            })
            ->values();
    @endphp

    @push('scripts')
    <script>
        // ============================================================
        // Dosen Search data
        // ============================================================
        const dosenItems = @json($dosenSearchItems);
        const webinarSpeakerItems = @json($webinarSpeakerSearchItems);

        // Alpine component for searchable dosen dropdown
        document.addEventListener('alpine:init', () => {
            Alpine.data('dosenSearch', (prefix) => ({
                search: '',
                selectedId: '',
                open: false,
                items: prefix === 'add' ? dosenItems : webinarSpeakerItems,
                filteredItems() {
                    if (!this.search) return this.items;
                    const q = this.search.toLowerCase();
                    return this.items.filter(i =>
                        i.name.toLowerCase().includes(q) ||
                        i.role_label.toLowerCase().includes(q)
                    );
                },
                select(item) {
                    this.selectedId = item.id;
                    this.search = item.name;
                    this.open = false;
                },
                clear() {
                    this.selectedId = '';
                    this.search = '';
                },
                setById(id) {
                    const found = this.items.find(i => String(i.id) === String(id));
                    if (found) { this.selectedId = found.id; this.search = found.name; }
                    else { this.selectedId = ''; this.search = ''; }
                }
            }));
        });
        // ============================================================
        // Form Data Persistence (sessionStorage)
        // ============================================================
        const ADD_FORM_KEY = 'kursus_add_draft';
        const EDIT_FORM_KEY = 'kursus_edit_draft';

        // IDs of all saveable fields per modal
        const addFieldIds = [
            'add_nama_course', 'add_kode_course', 'add_deskripsi', 'add_persyaratan',
            'add_id_dosen', 'add_id_jurusan', 'add_level', 'add_estimasi_waktu', 'add_durasi_satuan',
            'add_kategori', 'add_youtube_playlist', 'add_harga', 'add_diskon',
            'add_status_input', 'add_tipe_input'
        ];
        const addCheckboxIds = [
            'add_status_toggle', 'add_akses_publik', 'add_sertifikat', 'add_gratis_toggle'
        ];
        const editFieldIds = [
            'edit_nama_course', 'edit_kode_course', 'edit_deskripsi', 'edit_persyaratan',
            'edit_id_dosen', 'edit_id_jurusan', 'edit_level', 'edit_estimasi_waktu', 'edit_durasi_satuan',
            'edit_kategori', 'edit_harga', 'edit_diskon', 'edit_tanggal_webinar', 'edit_jam_mulai_webinar',
            'edit_jam_selesai_webinar', 'edit_kuota_peserta', 'edit_youtube_playlist',
            'edit_status_input', 'edit_tipe_input'
        ];
        const editCheckboxIds = [
            'edit_status_toggle', 'edit_akses_publik', 'edit_sertifikat', 'edit_gratis_toggle'
        ];

        function syncEditCategoryUI(kategori = '') {
            const isWebinar = kategori === 'webinar';
            const isKursus = kategori === 'kursus';
            const isTiket = kategori === 'tiket';
            const normalizedCategory = ['kursus', 'webinar', 'tiket'].includes(kategori) ? kategori : 'kursus';
            const durationFields = document.getElementById('editDurationFields');
            const modulButton = document.getElementById('edit_modul_btn')?.closest('.border');
            const playlistPanel = document.getElementById('editPlaylistPanel');
            const categoryMetaPanel = document.getElementById('edit_category_meta_panel');
            const webinarDatetimeGroup = document.getElementById('edit_webinar_datetime_group');
            const kuotaGroup = document.getElementById('edit_kuota_group');
            const linkLabel = document.getElementById('edit_link_label');
            const linkInput = document.getElementById('edit_youtube_playlist');
            const copyMap = {
                kursus: {
                    modalTitle: 'Edit Kursus',
                    modalSubtitle: 'Ubah informasi kursus yang ada.',
                    basicTitle: 'Informasi Dasar Kursus',
                    nameLabel: 'Judul Kursus <span class="text-red-400">*</span>',
                    codeLabel: 'Kode Kursus <span class="text-red-400">*</span>',
                    descriptionLabel: 'Deskripsi Kursus',
                    requirementsLabel: 'Persyaratan Kursus (Opsional)',
                    teacherLabel: 'Dosen Pengampu <span class="text-gray-300 dark:text-gray-600">(opsional)</span>',
                    thumbnailLabel: 'Thumbnail Kursus',
                    moduleTitle: 'Modul Kursus',
                    moduleDesc: 'Kelola struktur modul, video pembelajaran, bahan bacaan, kuis, dan tugas untuk kursus ini.',
                    moduleButton: 'Kelola Modul Kursus',
                    settingsTitle: 'Pengaturan Kursus',
                    statusTitle: 'Status Kursus',
                    statusDesc: 'Aktif atau simpan draft',
                    certificateTitle: 'Sertifikat Penyelesaian',
                    certificateDesc: 'Berikan sertifikat setelah selesai',
                    pricingTitle: 'Pricing & Akses Kursus',
                    categoryLabel: 'Kategori Kursus',
                    metaTitle: 'Playlist Kursus',
                    metaDesc: 'Masukkan URL playlist YouTube untuk sinkronisasi materi kursus.',
                    linkLabel: 'Link Playlist YouTube',
                    linkPlaceholder: 'https://www.youtube.com/playlist?list=...',
                },
                webinar: {
                    modalTitle: 'Edit Webinar',
                    modalSubtitle: 'Ubah informasi webinar yang ada.',
                    basicTitle: 'Informasi Dasar Webinar',
                    nameLabel: 'Judul Webinar <span class="text-red-400">*</span>',
                    codeLabel: 'Kode Webinar <span class="text-red-400">*</span>',
                    descriptionLabel: 'Deskripsi Webinar',
                    requirementsLabel: 'Catatan Webinar (Opsional)',
                    teacherLabel: 'Pembicara / Dosen <span class="text-gray-300 dark:text-gray-600">(opsional)</span>',
                    thumbnailLabel: 'Thumbnail Webinar',
                    moduleTitle: 'Materi Webinar',
                    moduleDesc: 'Webinar tidak memakai struktur modul kursus. Fokus pengelolaan ada pada jadwal, link meeting, dan akses peserta.',
                    moduleButton: 'Kelola Materi Webinar',
                    settingsTitle: 'Pengaturan Webinar',
                    statusTitle: 'Status Webinar',
                    statusDesc: 'Draft atau siap tayang',
                    certificateTitle: 'Sertifikat Kehadiran',
                    certificateDesc: 'Berikan sertifikat kepada peserta webinar',
                    pricingTitle: 'Pricing & Akses Webinar',
                    categoryLabel: 'Kategori Konten',
                    metaTitle: 'Jadwal Webinar',
                    metaDesc: 'Field ini digunakan saat kategori webinar.',
                    linkLabel: 'Link Meeting / Playlist',
                    linkPlaceholder: 'https://zoom.us/j/... atau https://meet.google.com/...',
                },
                tiket: {
                    modalTitle: 'Edit Tiket',
                    modalSubtitle: 'Ubah informasi tiket acara yang ada.',
                    basicTitle: 'Informasi Dasar Tiket',
                    nameLabel: 'Nama Tiket <span class="text-red-400">*</span>',
                    codeLabel: 'Kode Tiket <span class="text-red-400">*</span>',
                    descriptionLabel: 'Deskripsi Tiket',
                    requirementsLabel: 'Catatan Tiket (Opsional)',
                    teacherLabel: 'PIC / Pengelola <span class="text-gray-300 dark:text-gray-600">(opsional)</span>',
                    thumbnailLabel: 'Thumbnail Tiket',
                    moduleTitle: 'Akses Tiket',
                    moduleDesc: 'Tiket acara tidak memakai struktur modul kursus. Pengelolaan difokuskan pada akses, jadwal, kuota, dan harga.',
                    moduleButton: 'Kelola Akses Tiket',
                    settingsTitle: 'Pengaturan Tiket',
                    statusTitle: 'Status Tiket',
                    statusDesc: 'Draft, aktif, atau nonaktif',
                    certificateTitle: 'Dokumen Kehadiran',
                    certificateDesc: 'Aktifkan jika peserta perlu bukti kehadiran',
                    pricingTitle: 'Pricing & Akses Tiket',
                    categoryLabel: 'Kategori Konten',
                    metaTitle: 'Info Akses Tiket',
                    metaDesc: 'Tambahkan link informasi, landing page, atau halaman akses tiket jika diperlukan.',
                    linkLabel: 'Link Informasi / Landing Page',
                    linkPlaceholder: 'https://example.com/event atau halaman detail tiket',
                },
            };
            const copy = copyMap[normalizedCategory];
            const setHtml = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.innerHTML = value;
            };
            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            setText('edit_modal_title', copy.modalTitle);
            setText('edit_modal_subtitle', copy.modalSubtitle);
            setText('edit_basic_section_title', copy.basicTitle);
            setHtml('edit_nama_course_label', copy.nameLabel);
            setHtml('edit_kode_course_label', copy.codeLabel);
            setText('edit_deskripsi_label', copy.descriptionLabel);
            setText('edit_persyaratan_label', copy.requirementsLabel);
            setHtml('edit_dosen_label', copy.teacherLabel);
            setText('edit_thumbnail_label', copy.thumbnailLabel);
            setText('edit_module_section_title', copy.moduleTitle);
            setText('edit_module_section_desc', copy.moduleDesc);
            setText('edit_module_button_text', copy.moduleButton);
            setText('edit_settings_section_title', copy.settingsTitle);
            setText('edit_status_title', copy.statusTitle);
            setText('edit_status_desc', copy.statusDesc);
            setText('edit_certificate_title', copy.certificateTitle);
            setText('edit_certificate_desc', copy.certificateDesc);
            setText('edit_pricing_section_title', copy.pricingTitle);
            setText('edit_category_label', copy.categoryLabel);
            setText('edit_category_meta_title', copy.metaTitle);
            setText('edit_category_meta_desc', copy.metaDesc);
            setText('edit_link_label', copy.linkLabel);

            if (linkInput) {
                linkInput.placeholder = copy.linkPlaceholder;
            }

            if (durationFields) {
                durationFields.classList.toggle('hidden', isWebinar);
                durationFields.querySelectorAll('input, select').forEach((field) => {
                    field.disabled = isWebinar;
                });
            }

            if (modulButton) {
                modulButton.classList.toggle('hidden', isWebinar);
            }

            if (categoryMetaPanel) {
                categoryMetaPanel.classList.toggle('hidden', false);
            }

            if (webinarDatetimeGroup) {
                webinarDatetimeGroup.classList.toggle('hidden', !isWebinar);
                webinarDatetimeGroup.querySelectorAll('input').forEach((field) => {
                    field.disabled = !isWebinar;
                });
            }

            if (kuotaGroup) {
                kuotaGroup.classList.toggle('hidden', !isWebinar);
                kuotaGroup.querySelectorAll('input').forEach((field) => {
                    field.disabled = !isWebinar;
                });
            }

            if (playlistPanel) {
                playlistPanel.classList.toggle('hidden', !isKursus);
            }
        }

        function renderPlaylistStatus(message, tone = 'info') {
            const box = document.getElementById('editPlaylistStatus');
            if (!box) return;

            const toneClasses = {
                info: 'border-blue-200 bg-white/80 text-blue-700 dark:border-blue-700 dark:bg-gray-800/60 dark:text-blue-300',
                success: 'border-green-200 bg-green-50/80 text-green-700 dark:border-green-700 dark:bg-green-900/20 dark:text-green-300',
                warning: 'border-amber-200 bg-amber-50/80 text-amber-700 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                error: 'border-rose-200 bg-rose-50/80 text-rose-700 dark:border-rose-700 dark:bg-rose-900/20 dark:text-rose-300',
            };

            box.className = 'rounded-lg border px-3 py-2 text-xs ' + (toneClasses[tone] || toneClasses.info);
            box.textContent = message;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderPlaylistVideos(videos = []) {
            const list = document.getElementById('editPlaylistList');
            if (!list) return;

            if (!videos.length) {
                list.innerHTML = '<div class="rounded-lg border border-dashed border-gray-200 px-3 py-4 text-center text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">Belum ada video playlist yang masuk ke database.</div>';
                return;
            }

            list.innerHTML = videos.map((video, index) => `
                <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-3 py-2.5 dark:border-gray-700 dark:bg-gray-800/70">
                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 text-[11px] font-semibold text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">${index + 1}</div>
                    <img src="${escapeHtml(video.thumbnail_url || '')}" alt="${escapeHtml(video.title)}" class="h-10 w-16 rounded-lg object-cover bg-gray-100 dark:bg-gray-700" onerror="this.style.display='none'">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100">${escapeHtml(video.title)}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400">Video YouTube ID: ${escapeHtml(video.youtube_id)}</p>
                    </div>
                    <a href="${escapeHtml(video.watch_url)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-blue-200 px-2.5 py-1.5 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:border-blue-700 dark:text-blue-300 dark:hover:bg-blue-900/20">
                        Putar
                    </a>
                </div>
            `).join('');
        }

        async function loadPlaylistPreview(courseId, kategori) {
            if (!courseId || kategori !== 'kursus') {
                renderPlaylistStatus('Preview playlist hanya ditampilkan untuk kategori kursus.', 'warning');
                renderPlaylistVideos([]);
                return;
            }

            renderPlaylistStatus('Memuat playlist yang sudah tersinkron...', 'info');

            try {
                const response = await fetch('/admin/kursus/' + courseId + '/youtube-videos');
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Gagal memuat video playlist.');
                }

                if (!data.videos.length) {
                    renderPlaylistStatus('Belum ada video playlist di database. Simpan kursus atau klik sinkronkan playlist.', 'warning');
                    renderPlaylistVideos([]);
                    return;
                }

                renderPlaylistStatus(data.count + ' video playlist sudah masuk ke database dan siap jadi materi video.', 'success');
                renderPlaylistVideos(data.videos);
            } catch (error) {
                renderPlaylistStatus(error.message || 'Gagal memuat preview playlist.', 'error');
                renderPlaylistVideos([]);
            }
        }

        async function syncPlaylistFromEditModal() {
            if (!currentEditCourseId) {
                renderPlaylistStatus('Course belum dipilih.', 'error');
                return;
            }

            const playlistUrl = document.getElementById('edit_youtube_playlist')?.value?.trim();
            if (!playlistUrl) {
                renderPlaylistStatus('Isi URL playlist YouTube dulu sebelum sinkronisasi.', 'warning');
                return;
            }

            const syncButton = document.getElementById('edit_sync_playlist_btn');
            if (syncButton) {
                syncButton.disabled = true;
                syncButton.classList.add('opacity-60', 'cursor-not-allowed');
            }

            renderPlaylistStatus('Sinkronisasi playlist sedang berjalan...', 'info');

            try {
                const response = await fetch('/admin/kursus/' + currentEditCourseId + '/sync-playlist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        youtube_playlist: playlistUrl,
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Sinkronisasi playlist gagal.');
                }

                renderPlaylistStatus(data.message || 'Playlist berhasil disinkronkan.', 'success');
                renderPlaylistVideos(data.videos || []);
            } catch (error) {
                renderPlaylistStatus(error.message || 'Sinkronisasi playlist gagal.', 'error');
                renderPlaylistVideos([]);
            } finally {
                if (syncButton) {
                    syncButton.disabled = false;
                    syncButton.classList.remove('opacity-60', 'cursor-not-allowed');
                }
            }
        }

        function saveFormState(fieldIds, checkboxIds, storageKey, extra) {
            const data = extra ? { ...extra } : {};
            fieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) data[id] = el.value;
            });
            checkboxIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) data[id] = el.checked;
            });
            // Save thumbnail preview HTML
            const thumbId = storageKey === ADD_FORM_KEY ? 'thumbnailPreview' : 'editThumbnailPreview';
            const thumbEl = document.getElementById(thumbId);
            if (thumbEl && thumbEl.querySelector('img')) {
                data['_thumbHTML'] = thumbEl.innerHTML;
            }
            sessionStorage.setItem(storageKey, JSON.stringify(data));
        }

        function restoreFormState(fieldIds, checkboxIds, storageKey) {
            const raw = sessionStorage.getItem(storageKey);
            if (!raw) return false;
            try {
                const data = JSON.parse(raw);
                fieldIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && data[id] !== undefined) el.value = data[id];
                });
                checkboxIds.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && data[id] !== undefined) {
                        el.checked = data[id];
                        el.dispatchEvent(new Event('change'));
                    }
                });
                // Restore thumbnail preview
                const thumbId = storageKey === ADD_FORM_KEY ? 'thumbnailPreview' : 'editThumbnailPreview';
                if (data['_thumbHTML']) {
                    document.getElementById(thumbId).innerHTML = data['_thumbHTML'];
                }
                // Restore dosen searchable dropdown via Alpine
                const dosenFieldId = storageKey === ADD_FORM_KEY ? 'add_id_dosen' : 'edit_id_dosen';
                const dosenVal = data[dosenFieldId];
                if (dosenVal) {
                    setTimeout(() => {
                        const hiddenInput = document.getElementById(dosenFieldId);
                        if (hiddenInput) {
                            const wrapper = hiddenInput.closest('[x-data]');
                            if (wrapper) {
                                const alpineData = Alpine.$data(wrapper);
                                if (alpineData && alpineData.setById) alpineData.setById(dosenVal);
                            }
                        }
                    }, 50);
                }
                return true;
            } catch (e) { return false; }
        }

        function clearFormState(storageKey) {
            sessionStorage.removeItem(storageKey);
        }

        // ============================================================
        // Add Modal
        // ============================================================
        function openAddModal() {
            // Restore any previously saved data
            restoreFormState(addFieldIds, addCheckboxIds, ADD_FORM_KEY);
            document.getElementById('addKursusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeAddModal() {
            // Save current form state before hiding
            saveFormState(addFieldIds, addCheckboxIds, ADD_FORM_KEY);
            document.getElementById('addKursusModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function previewThumbnail(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('thumbnailPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ============================================================
        // Toggle helpers
        // ============================================================
        function initAddToggles() {
            const addStatusToggle = document.getElementById('add_status_toggle');
            const addStatusInput = document.getElementById('add_status_input');
            if (addStatusToggle) {
                addStatusToggle.addEventListener('change', function() {
                    addStatusInput.value = this.checked ? 'aktif' : 'draft';
                });
            }

            const addGratisToggle = document.getElementById('add_gratis_toggle');
            const addTipeInput = document.getElementById('add_tipe_input');
            const addHarga = document.getElementById('add_harga');
            const addDiskon = document.getElementById('add_diskon');
            if (addGratisToggle) {
                addGratisToggle.addEventListener('change', function() {
                    if (this.checked) {
                        addTipeInput.value = 'gratis';
                        addHarga.value = 0;
                        addHarga.disabled = true;
                        addDiskon.value = 0;
                        addDiskon.disabled = true;
                        addHarga.classList.add('opacity-50', 'cursor-not-allowed');
                        addDiskon.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        addTipeInput.value = 'berbayar';
                        addHarga.disabled = false;
                        addDiskon.disabled = false;
                        addHarga.classList.remove('opacity-50', 'cursor-not-allowed');
                        addDiskon.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
                if (addGratisToggle.checked) {
                    addGratisToggle.dispatchEvent(new Event('change'));
                }
            }
        }
        
        function initEditToggles() {
            const editStatusToggle = document.getElementById('edit_status_toggle');
            const editStatusInput = document.getElementById('edit_status_input');
            if (editStatusToggle) {
                editStatusToggle.addEventListener('change', function() {
                    editStatusInput.value = this.checked ? 'aktif' : 'draft';
                });
            }

            const editGratisToggle = document.getElementById('edit_gratis_toggle');
            const editTipeInput = document.getElementById('edit_tipe_input');
            const editHarga = document.getElementById('edit_harga');
            const editDiskon = document.getElementById('edit_diskon');
            if (editGratisToggle) {
                editGratisToggle.addEventListener('change', function() {
                    if (this.checked) {
                        editTipeInput.value = 'gratis';
                        editHarga.value = 0;
                        editHarga.disabled = true;
                        editDiskon.value = 0;
                        editDiskon.disabled = true;
                        editHarga.classList.add('opacity-50', 'cursor-not-allowed');
                        editDiskon.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        editTipeInput.value = 'berbayar';
                        editHarga.disabled = false;
                        editDiskon.disabled = false;
                        editHarga.classList.remove('opacity-50', 'cursor-not-allowed');
                        editDiskon.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initAddToggles();
            initEditToggles();

            // Clear add form storage on successful submit
            const addForm = document.querySelector('#addKursusModal form');
            if (addForm) {
                addForm.addEventListener('submit', function() {
                    clearFormState(ADD_FORM_KEY);
                });
            }
            // Clear edit form storage on successful submit
            const editForm = document.getElementById('editKursusForm');
            if (editForm) {
                editForm.addEventListener('submit', function() {
                    clearFormState(EDIT_FORM_KEY);
                });
            }
        });

        // ============================================================
        // Edit Modal
        // ============================================================
        let currentEditCourseId = null;

        function openEditModal(id) {
            currentEditCourseId = id;

            // Check if we have unsaved edits for this exact course
            const saved = sessionStorage.getItem(EDIT_FORM_KEY);
            let hasSavedDraft = false;
            if (saved) {
                try {
                    const parsed = JSON.parse(saved);
                    if (parsed._courseId == id) hasSavedDraft = true;
                } catch (e) {}
            }

            if (hasSavedDraft) {
                // Restore from sessionStorage instead of re-fetching
                document.getElementById('editKursusForm').action = '/admin/kursus/' + id;
                document.getElementById('edit_kursus_id').value = id;
                restoreFormState(editFieldIds, editCheckboxIds, EDIT_FORM_KEY);
                syncEditCategoryUI(document.getElementById('edit_kategori')?.value || 'kursus');
                
                // Update Kelola Modul link
                const btnModul = document.getElementById('edit_modul_btn');
                if (btnModul) {
                    btnModul.href = '/admin/kursus/' + id + '/modul';
                }

                document.getElementById('editKursusModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                // Fetch fresh data from server
                fetch('/admin/kursus/' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editKursusForm').action = '/admin/kursus/' + id;
                        document.getElementById('edit_kursus_id').value = id;
                        document.getElementById('edit_nama_course').value = data.nama_course || '';
                        document.getElementById('edit_kode_course').value = data.kode_course || '';
                        document.getElementById('edit_deskripsi').value = data.deskripsi || '';
                        document.getElementById('edit_persyaratan').value = data.persyaratan || '';
                        
                        // Set dosen via Alpine searchable dropdown
                        const editDosenEl = document.querySelector('[x-ref="editDosenWrap"]');
                        if (editDosenEl && editDosenEl.__x) {
                            editDosenEl.__x.$data.setById(data.id_dosen || '');
                        } else if (editDosenEl) {
                            // Fallback: use Alpine.$data
                            const alpineData = Alpine.$data(editDosenEl);
                            if (alpineData) alpineData.setById(data.id_dosen || '');
                        }
                        
                        document.getElementById('edit_id_jurusan').value = data.id_jurusan || '';
                        document.getElementById('edit_level').value = data.level || '';
                        document.getElementById('edit_estimasi_waktu').value = data.estimasi_waktu || 20;
                        document.getElementById('edit_durasi_satuan').value = data.durasi_satuan || 'Jam';
                        document.getElementById('edit_kategori').value = data.kategori || 'kursus';
                        syncEditCategoryUI(data.kategori || 'kursus');
                        document.getElementById('edit_tanggal_webinar').value = data.tanggal_webinar || '';
                        document.getElementById('edit_jam_mulai_webinar').value = data.jam_mulai_webinar || '';
                        document.getElementById('edit_jam_selesai_webinar').value = data.jam_selesai_webinar || '';
                        document.getElementById('edit_kuota_peserta').value = data.kuota_peserta || '';
                        document.getElementById('edit_youtube_playlist').value = data.youtube_playlist || '';
                        document.getElementById('edit_harga').value = data.harga || 0;
                        document.getElementById('edit_diskon').value = data.diskon || 0;

                        const statusToggle = document.getElementById('edit_status_toggle');
                        const statusInput = document.getElementById('edit_status_input');
                        const status = data.status || 'draft';
                        statusInput.value = status;
                        statusToggle.checked = (status === 'aktif');

                        const gratisToggle = document.getElementById('edit_gratis_toggle');
                        const tipeInput = document.getElementById('edit_tipe_input');
                        const tipe = data.tipe || 'berbayar';
                        tipeInput.value = tipe;
                        gratisToggle.checked = (tipe === 'gratis');
                        gratisToggle.dispatchEvent(new Event('change'));

                        // Set akses_publik & sertifikat checkboxes
                        document.getElementById('edit_akses_publik').checked = data.akses_publik !== false;
                        document.getElementById('edit_sertifikat').checked = !!data.sertifikat;

                        const approvalNoteBox = document.getElementById('editApprovalNoteBox');
                        if (approvalNoteBox) {
                            if (data.approval_status === 'ditolak' && data.approval_notes) {
                                approvalNoteBox.textContent = 'Catatan penolakan: ' + data.approval_notes;
                                approvalNoteBox.classList.remove('hidden');
                            } else if (data.approval_status === 'pending') {
                                approvalNoteBox.textContent = 'Webinar ini sedang menunggu persetujuan admin.';
                                approvalNoteBox.classList.remove('hidden');
                            } else {
                                approvalNoteBox.textContent = '';
                                approvalNoteBox.classList.add('hidden');
                            }
                        }

                        // Update Kelola Modul link
                        const btnModul = document.getElementById('edit_modul_btn');
                        if (btnModul) {
                            btnModul.href = '/admin/kursus/' + id + '/modul';
                        }

                        const preview = document.getElementById('editThumbnailPreview');
                        if (data.thumbnail) {
                            preview.innerHTML = '<img src="/storage/' + data.thumbnail + '" class="w-full h-full object-cover">';
                        } else {
                            preview.innerHTML = '<svg class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>';
                        }

                        document.getElementById('editKursusModal').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                        loadPlaylistPreview(id, data.kategori || 'kursus');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal memuat data kursus');
                    });
            }
        }
        
        function closeEditModal() {
            // Save current form state (keyed by course ID)
            saveFormState(editFieldIds, editCheckboxIds, EDIT_FORM_KEY, { _courseId: currentEditCourseId });
            document.getElementById('editKursusModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('edit_kategori')?.addEventListener('change', (event) => {
            syncEditCategoryUI(event.target.value);
        });
        
        function previewEditThumbnail(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('editThumbnailPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ============================================================
        // Delete Modal
        // ============================================================
        function confirmDelete(id) {
            document.getElementById('deleteKursusForm').action = '/admin/kursus/' + id;
            document.getElementById('deleteKursusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function approveWebinar(id) {
            if (!confirm('Setujui webinar ini dan publikasikan sekarang?')) {
                return;
            }

            const form = document.getElementById('approveWebinarForm');
            form.action = '/admin/kursus/' + id + '/approve-webinar';
            form.submit();
        }

        function rejectWebinar(id) {
            const notes = prompt('Masukkan catatan penolakan untuk dosen:');
            if (notes === null) {
                return;
            }

            const trimmedNotes = notes.trim();
            if (!trimmedNotes) {
                alert('Catatan penolakan wajib diisi.');
                return;
            }

            const form = document.getElementById('rejectWebinarForm');
            document.getElementById('rejectWebinarNotes').value = trimmedNotes;
            form.action = '/admin/kursus/' + id + '/reject-webinar';
            form.submit();
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteKursusModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }


        // Auto-reopen modal on validation error
        @if($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => openAddModal());
        @elseif($errors->any() && old('_modal') === 'edit')
        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editKursusForm');
            const editId = '{{ old('_id') }}';
            if (editId) {
                editForm.action = '/admin/kursus/' + editId;
                document.getElementById('edit_kursus_id').value = editId;
            }
            document.getElementById('editKursusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @elseif($errors->any() && old('_modal') === 'add_webinar')
        document.addEventListener('DOMContentLoaded', () => openAddWebinarModal());
        @endif

        // ============================================================
        // Add Webinar Modal
        // ============================================================
        function openAddWebinarModal() {
            document.getElementById('addWebinarModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddWebinarModal() {
            document.getElementById('addWebinarModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function previewWebinarThumbnail(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('webinarThumbnailPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Webinar Status toggle
        const webinarStatusToggle = document.getElementById('webinar_status_toggle');
        if (webinarStatusToggle) {
            const webinarStatusInput = document.getElementById('webinar_status_input');
            webinarStatusToggle.addEventListener('change', function() {
                webinarStatusInput.value = this.checked ? 'aktif' : 'draft';
            });
        }

        // Webinar Gratis toggle
        const webinarGratisToggle = document.getElementById('webinar_gratis_toggle');
        if (webinarGratisToggle) {
            const webinarTipeInput = document.getElementById('webinar_tipe_input');
            const webinarHargaInput = document.getElementById('webinar_harga');
            const webinarDiskonInput = document.getElementById('webinar_diskon');
            webinarGratisToggle.addEventListener('change', function() {
                if (this.checked) {
                    webinarTipeInput.value = 'gratis';
                    if (webinarHargaInput) { webinarHargaInput.value = 0; webinarHargaInput.disabled = true; }
                    if (webinarDiskonInput) { webinarDiskonInput.value = 0; webinarDiskonInput.disabled = true; }
                } else {
                    webinarTipeInput.value = 'berbayar';
                    if (webinarHargaInput) { webinarHargaInput.disabled = false; }
                    if (webinarDiskonInput) { webinarDiskonInput.disabled = false; }
                }
            });
            // Init state
            if (webinarGratisToggle.checked) {
                if (webinarHargaInput) webinarHargaInput.disabled = true;
                if (webinarDiskonInput) webinarDiskonInput.disabled = true;
            }
        }
    </script>
    @endpush
</x-layouts.admin>

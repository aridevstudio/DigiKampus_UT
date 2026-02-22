<x-layouts.admin title="Kelola Kursus" active="kursus">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kursus</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data kursus, tambah kursus baru, dan atur status publikasi.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.kursus') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium rounded-xl transition shadow-sm shadow-blue-500/25 hover:shadow-md hover:shadow-blue-500/30">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kursus
            </button>
            
            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Status Filter --}}
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="draft" {{ ($statusFilter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            
            {{-- Tipe Filter --}}
            <div class="relative">
                <select name="tipe" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($tipeFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="gratis" {{ ($tipeFilter ?? '') === 'gratis' ? 'selected' : '' }}>Gratis</option>
                    <option value="berbayar" {{ ($tipeFilter ?? '') === 'berbayar' ? 'selected' : '' }}>Berbayar</option>
                    <option value="webinar" {{ ($tipeFilter ?? '') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                    <option value="tiket" {{ ($tipeFilter ?? '') === 'tiket' ? 'selected' : '' }}>Tiket</option>
                    <option value="kursus" {{ ($tipeFilter ?? '') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-sm ml-auto">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kursus..." class="w-full px-4 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm text-gray-700 dark:text-gray-300 font-medium rounded-xl transition">
                Cari
            </button>
        </div>
    </form>

    {{-- Summary Stats --}}
    @if($totalKursus > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
        @php
            $tipeStats = collect($kursusList)->groupBy('tipe')->map->count();
            $statItems = [
                ['label' => 'Total Kursus', 'count' => $totalKursus, 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'color' => 'blue'],
                ['label' => 'Webinar', 'count' => $tipeStats->get('webinar', 0), 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'color' => 'purple'],
                ['label' => 'Kursus', 'count' => $tipeStats->get('kursus', 0), 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'color' => 'indigo'],
                ['label' => 'Tiket', 'count' => $tipeStats->get('tiket', 0), 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'color' => 'orange'],
                ['label' => 'Berbayar', 'count' => $tipeStats->get('berbayar', 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ];
            $colorMap = [
                'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'icon' => 'text-blue-500 dark:text-blue-400', 'text' => 'text-blue-700 dark:text-blue-300'],
                'purple' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'icon' => 'text-purple-500 dark:text-purple-400', 'text' => 'text-purple-700 dark:text-purple-300'],
                'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'icon' => 'text-indigo-500 dark:text-indigo-400', 'text' => 'text-indigo-700 dark:text-indigo-300'],
                'orange' => ['bg' => 'bg-orange-50 dark:bg-orange-900/20', 'icon' => 'text-orange-500 dark:text-orange-400', 'text' => 'text-orange-700 dark:text-orange-300'],
                'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'icon' => 'text-emerald-500 dark:text-emerald-400', 'text' => 'text-emerald-700 dark:text-emerald-300'],
            ];
        @endphp
        @foreach($statItems as $stat)
        <div class="flex items-center gap-3 p-3.5 rounded-xl {{ $colorMap[$stat['color']]['bg'] }} border border-{{ $stat['color'] === 'emerald' ? 'emerald' : $stat['color'] }}-100 dark:border-{{ $stat['color'] === 'emerald' ? 'emerald' : $stat['color'] }}-800/30">
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
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kursus</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengajar</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Peserta</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Harga</th>
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
                                    @if($kursus['thumbnail'])
                                        <img src="{{ asset('storage/' . $kursus['thumbnail']) }}" alt="{{ $kursus['nama'] }}" class="w-14 h-14 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-600 shadow-sm">
                                    @else
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 ring-1 ring-blue-200/50 dark:ring-blue-700/30 flex items-center justify-center shadow-sm">
                                            <svg class="w-6 h-6 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif
                                    @if($kursus['has_youtube'])
                                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center ring-2 ring-white dark:ring-gray-800 shadow-sm" title="YouTube Playlist">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[240px] group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $kursus['nama'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-mono">{{ $kursus['kode'] }}</p>
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

                        {{-- Tipe --}}
                        <td class="px-5 py-4 text-center">
                            @php
                                $tipeConfig = match($kursus['tipe']) {
                                    'gratis' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-200 dark:border-emerald-700/40', 'icon' => 'M5 13l4 4L19 7', 'label' => 'Gratis'],
                                    'berbayar' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'border' => 'border-blue-200 dark:border-blue-700/40', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Berbayar'],
                                    'webinar' => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'text' => 'text-purple-700 dark:text-purple-400', 'border' => 'border-purple-200 dark:border-purple-700/40', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'label' => 'Webinar'],
                                    'tiket' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-200 dark:border-amber-700/40', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'label' => 'Tiket'],
                                    'kursus' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/20', 'text' => 'text-indigo-700 dark:text-indigo-400', 'border' => 'border-indigo-200 dark:border-indigo-700/40', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Kursus'],
                                    default => ['bg' => 'bg-gray-50 dark:bg-gray-700/30', 'text' => 'text-gray-600 dark:text-gray-400', 'border' => 'border-gray-200 dark:border-gray-600/40', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => ucfirst($kursus['tipe'])],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $tipeConfig['bg'] }} {{ $tipeConfig['text'] }} {{ $tipeConfig['border'] }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tipeConfig['icon'] }}" />
                                </svg>
                                {{ $tipeConfig['label'] }}
                            </span>
                        </td>

                        {{-- Harga --}}
                        <td class="px-5 py-4 text-right">
                            @if(($kursus['harga'] ?? 0) > 0)
                                <div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($kursus['harga'], 0, ',', '.') }}</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-300 dark:text-gray-600">&mdash;</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4 text-center">
                            @php
                                $statusConfig = [
                                    'aktif' => ['dot' => 'bg-green-500', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-700 dark:text-green-400', 'border' => 'border-green-200 dark:border-green-700/40'],
                                    'draft' => ['dot' => 'bg-yellow-500', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-700 dark:text-yellow-400', 'border' => 'border-yellow-200 dark:border-yellow-700/40'],
                                    'nonaktif' => ['dot' => 'bg-red-500', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'border' => 'border-red-200 dark:border-red-700/40'],
                                ];
                                $sc = $statusConfig[$kursus['status']] ?? $statusConfig['draft'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border {{ $sc['bg'] }} {{ $sc['text'] }} {{ $sc['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} animate-pulse"></span>
                                {{ ucfirst($kursus['status']) }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-4 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-0.5">
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
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-900/20">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $kursusPaginated->firstItem() ?? 0 }}</span>-<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $kursusPaginated->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $totalKursus }}</span> kursus
            </p>
            <div class="flex items-center gap-1">
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
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form action="{{ route('admin.kursus.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Kursus</h3>
                        <p class="text-sm text-blue-500">Isi informasi kursus baru dengan lengkap.</p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 mb-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
                    </div>
                    
                    {{-- Thumbnail --}}
                    <div class="mb-6">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-3">Thumbnail</label>
                        <div class="flex items-center gap-4">
                            <div id="thumbnailPreview" class="w-24 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Thumbnail
                                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="previewThumbnail(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 5MB, JPG/PNG</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Kursus</h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kursus</label>
                                    <input type="text" name="nama_course" required placeholder="Masukkan nama kursus" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus</label>
                                    <input type="text" name="kode_course" required placeholder="Contoh: CS101" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi</label>
                                <textarea name="deskripsi" rows="3" placeholder="Deskripsi kursus..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Dosen Pengampu</label>
                                    <div class="relative">
                                        <select name="id_dosen" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Dosen</option>
                                            @foreach($dosenList as $dosen)
                                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jurusan</label>
                                    <div class="relative">
                                        <select name="id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach($jurusanList as $jurusan)
                                            <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- YouTube Playlist --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Konten Video</h4>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">YouTube Playlist URL</label>
                            <input type="url" name="youtube_playlist" placeholder="https://www.youtube.com/playlist?list=..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">Opsional. Masukkan URL playlist YouTube untuk kursus ini.</p>
                        </div>
                    </div>
                    
                    {{-- Pengaturan Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Pengaturan Kursus</h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipe Kursus</label>
                                    <div class="relative">
                                        <select name="tipe" id="add_tipe" required onchange="toggleHarga('add')" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="gratis">Gratis</option>
                                            <option value="berbayar">Berbayar</option>
                                            <option value="webinar">Webinar</option>
                                            <option value="tiket">Tiket</option>
                                            <option value="kursus">Kursus</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div id="add_harga_container" class="hidden">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                    <input type="number" name="harga" id="add_harga" min="0" placeholder="0" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status Publikasi</label>
                                <div class="relative">
                                    <select name="status" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        <option value="draft">Draft</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
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
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form id="editKursusForm" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Kursus</h3>
                        <p class="text-sm text-blue-500">Ubah informasi kursus.</p>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 mb-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
                    </div>
                    
                    {{-- Thumbnail --}}
                    <div class="mb-6">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-3">Thumbnail</label>
                        <div class="flex items-center gap-4">
                            <div id="editThumbnailPreview" class="w-24 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Thumbnail
                                    <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="previewEditThumbnail(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 5MB, JPG/PNG</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Kursus</h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kursus</label>
                                    <input type="text" name="nama_course" id="edit_nama_course" required placeholder="Masukkan nama kursus" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus</label>
                                    <input type="text" name="kode_course" id="edit_kode_course" required placeholder="Contoh: CS101" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi</label>
                                <textarea name="deskripsi" id="edit_deskripsi" rows="3" placeholder="Deskripsi kursus..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Dosen Pengampu</label>
                                    <div class="relative">
                                        <select name="id_dosen" id="edit_id_dosen" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Dosen</option>
                                            @foreach($dosenList as $dosen)
                                            <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jurusan</label>
                                    <div class="relative">
                                        <select name="id_jurusan" id="edit_id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach($jurusanList as $jurusan)
                                            <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- YouTube Playlist --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Konten Video</h4>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">YouTube Playlist URL</label>
                            <div class="flex gap-2">
                                <input type="url" name="youtube_playlist" id="edit_youtube_playlist" placeholder="https://www.youtube.com/playlist?list=..." class="flex-1 px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                <button type="button" onclick="syncPlaylist()" id="syncPlaylistBtn" class="px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition flex items-center gap-2 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                    Sync Playlist
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Opsional. Masukkan URL playlist YouTube lalu klik Sync untuk mengambil daftar video.</p>
                            <div id="syncStatus" class="mt-2 hidden"></div>
                        </div>
                        {{-- Video List --}}
                        <div id="videoListContainer" class="mt-4 hidden">
                            <div class="flex items-center justify-between mb-2">
                                <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-300">Video Tersinkronisasi</h5>
                                <span id="videoCount" class="text-xs text-gray-400">0 video</span>
                            </div>
                            <div id="videoList" class="space-y-2 max-h-48 overflow-y-auto"></div>
                        </div>
                    </div>
                    
                    {{-- Pengaturan Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Pengaturan Kursus</h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipe Kursus</label>
                                    <div class="relative">
                                        <select name="tipe" id="edit_tipe" required onchange="toggleHarga('edit')" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            <option value="gratis">Gratis</option>
                                            <option value="berbayar">Berbayar</option>
                                            <option value="webinar">Webinar</option>
                                            <option value="tiket">Tiket</option>
                                            <option value="kursus">Kursus</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <div id="edit_harga_container" class="hidden">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                    <input type="number" name="harga" id="edit_harga" min="0" placeholder="0" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status Publikasi</label>
                                <div class="relative">
                                    <select name="status" id="edit_status" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        <option value="draft">Draft</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
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
                        <form id="deleteKursusForm" method="POST" class="inline">
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

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div id="successAlert" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('successAlert')?.remove(), 5000);</script>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);</script>
    @endif

    @push('scripts')
    <script>
        // Add Modal functions
        function openAddModal() {
            document.getElementById('addKursusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeAddModal() {
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
        
        function toggleHarga(prefix) {
            const tipe = document.getElementById(prefix + '_tipe').value;
            const hargaContainer = document.getElementById(prefix + '_harga_container');
            if (['berbayar', 'tiket', 'webinar'].includes(tipe)) {
                hargaContainer.classList.remove('hidden');
            } else {
                hargaContainer.classList.add('hidden');
            }
        }
        
        // Edit Modal functions
        function openEditModal(id) {
            currentEditCourseId = id;
            fetch('/admin/kursus/' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editKursusForm').action = '/admin/kursus/' + id;
                    document.getElementById('edit_nama_course').value = data.nama_course || '';
                    document.getElementById('edit_kode_course').value = data.kode_course || '';
                    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
                    document.getElementById('edit_id_dosen').value = data.id_dosen || '';
                    document.getElementById('edit_id_jurusan').value = data.id_jurusan || '';
                    document.getElementById('edit_tipe').value = data.tipe || 'kursus';
                    document.getElementById('edit_harga').value = data.harga || 0;
                    document.getElementById('edit_status').value = data.status || 'draft';
                    
                    // Show/hide harga field
                    toggleHarga('edit');
                    
                    document.getElementById('edit_youtube_playlist').value = data.youtube_playlist || '';
                    
                    // Load synced YouTube videos
                    document.getElementById('videoListContainer').classList.add('hidden');
                    document.getElementById('syncStatus').classList.add('hidden');
                    if (data.youtube_playlist) {
                        loadVideoList(id);
                    }
                    
                    // Show existing thumbnail if available
                    const preview = document.getElementById('editThumbnailPreview');
                    if (data.thumbnail) {
                        preview.innerHTML = '<img src="/storage/' + data.thumbnail + '" class="w-full h-full object-cover">';
                    } else {
                        preview.innerHTML = '<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>';
                    }
                    
                    document.getElementById('editKursusModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data kursus');
                });
        }
        
        function closeEditModal() {
            document.getElementById('editKursusModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
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
        
        // Delete Modal functions
        function confirmDelete(id) {
            document.getElementById('deleteKursusForm').action = '/admin/kursus/' + id;
            document.getElementById('deleteKursusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteKursusModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // YouTube Playlist Sync
        let currentEditCourseId = null;

        function syncPlaylist() {
            if (!currentEditCourseId) { alert('Simpan kursus terlebih dahulu.'); return; }
            const url = document.getElementById('edit_youtube_playlist').value.trim();
            if (!url) { alert('Masukkan URL playlist YouTube terlebih dahulu.'); return; }

            const btn = document.getElementById('syncPlaylistBtn');
            const status = document.getElementById('syncStatus');
            btn.disabled = true;
            btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> Menyinkronkan...';
            status.classList.remove('hidden');
            status.innerHTML = '<p class="text-xs text-blue-500">Mengambil data playlist...</p>';

            fetch('/admin/kursus/' + currentEditCourseId + '/sync-playlist', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ youtube_playlist: url })
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    status.innerHTML = '<p class="text-xs text-red-500">' + data.error + '</p>';
                } else {
                    status.innerHTML = '<p class="text-xs text-green-500">' + data.message + '</p>';
                    if (data.videos && data.videos.length > 0) {
                        renderVideoList(data.videos);
                    }
                }
            })
            .catch(err => {
                status.innerHTML = '<p class="text-xs text-red-500">Gagal sinkronisasi: ' + err.message + '</p>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg> Sync Playlist';
            });
        }

        function renderVideoList(videos) {
            const container = document.getElementById('videoListContainer');
            const list = document.getElementById('videoList');
            const count = document.getElementById('videoCount');
            container.classList.remove('hidden');
            count.textContent = videos.length + ' video';
            let html = '';
            videos.forEach((v, i) => {
                html += `<div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <span class="text-xs text-gray-400 w-5 text-center flex-shrink-0">${i + 1}</span>
                    ${v.thumbnail_url ? `<img src="${v.thumbnail_url}" class="w-16 h-10 object-cover rounded flex-shrink-0">` : ''}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-800 dark:text-gray-200 truncate font-medium">${v.title}</p>
                        ${v.formatted_duration ? `<p class="text-[10px] text-gray-400">${v.formatted_duration}</p>` : ''}
                    </div>
                    <a href="https://www.youtube.com/watch?v=${v.youtube_id}" target="_blank" class="text-red-500 hover:text-red-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 19c-4.19 0-6.8-.16-7.83-.44-.9-.25-1.48-.83-1.73-1.73-.13-.47-.22-1.1-.28-1.9-.07-.8-.1-1.49-.1-2.09L2 12c0-2.19.16-3.8.44-4.83.25-.9.83-1.48 1.73-1.73.47-.13 1.33-.22 2.65-.28 1.3-.07 2.49-.1 3.59-.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z"/></svg>
                    </a>
                </div>`;
            });
            list.innerHTML = html;
        }

        function loadVideoList(courseId) {
            fetch('/admin/kursus/' + courseId + '/youtube-videos')
                .then(r => r.json())
                .then(data => {
                    if (data.videos && data.videos.length > 0) {
                        renderVideoList(data.videos);
                    }
                });
        }
    </script>
    @endpush
</x-layouts.admin>

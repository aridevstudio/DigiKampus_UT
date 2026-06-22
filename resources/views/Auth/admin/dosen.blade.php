<x-layouts.admin title="Kelola Dosen" active="dosen">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Dosen</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data dosen dan tambah dosen baru dengan status aktif.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.dosen') }}" class="admin-toolbar-responsive bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        <div class="admin-toolbar-shell">
            {{-- Buttons --}}
            <div class="admin-toolbar-actions flex flex-wrap gap-1.5">
                <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-blue-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dosen
                </button>
                <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-emerald-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.export.excel', 'dosen') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-amber-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </a>
                <a href="{{ route('admin.import.template', 'dosen') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium rounded-xl transition">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Template
                </a>
            </div>

            {{-- Search --}}
            <div class="admin-toolbar-search order-3 lg:order-2 flex gap-1.5">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari dosen..." class="w-full px-3 py-1.5 pl-9 sm:px-4 sm:py-2.5 sm:pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 absolute left-2.5 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="px-3 py-1.5 sm:px-4 sm:py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs sm:text-sm text-gray-700 dark:text-gray-300 font-medium rounded-xl transition flex-shrink-0">
                    Cari
                </button>
            </div>

            <div class="admin-toolbar-divider w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Filters --}}
            <div class="admin-toolbar-filters order-2 lg:order-3 grid grid-cols-2 gap-1.5">
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2.5 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="tidak_aktif" {{ ($statusFilter ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="relative">
                    <select name="jurusan" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2.5 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($jurusanFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Prodi</option>
                        @foreach($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id_jurusan }}" {{ ($jurusanFilter ?? '') == $jurusan->id_jurusan ? 'selected' : '' }}>
                                {{ $jurusan->nama_jurusan }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
        </div>
    </form>

    {{-- Summary Stats --}}
    @if($totalDosen > 0)
    @php
        $dosenStatItems = [
            ['label' => 'Total Dosen', 'count' => $dosenAktifCount + $dosenNonaktifCount + $dosenPendingCount, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'blue'],
            ['label' => 'Aktif', 'count' => $dosenAktifCount, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
            ['label' => 'Menunggu', 'count' => $dosenPendingCount, 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber'],
            ['label' => 'Tidak Aktif', 'count' => $dosenNonaktifCount, 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'color' => 'red'],
        ];
        $dosenColorMap = [
            'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'icon' => 'text-blue-500 dark:text-blue-400', 'text' => 'text-blue-700 dark:text-blue-300', 'border' => 'border-blue-100 dark:border-blue-800/30'],
            'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'icon' => 'text-emerald-500 dark:text-emerald-400', 'text' => 'text-emerald-700 dark:text-emerald-300', 'border' => 'border-emerald-100 dark:border-emerald-800/30'],
            'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'icon' => 'text-amber-500 dark:text-amber-400', 'text' => 'text-amber-700 dark:text-amber-300', 'border' => 'border-amber-100 dark:border-amber-800/30'],
            'red' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'icon' => 'text-red-500 dark:text-red-400', 'text' => 'text-red-700 dark:text-red-300', 'border' => 'border-red-100 dark:border-red-800/30'],
        ];
    @endphp
    <div class="responsive-grid-stats mb-6">
        @foreach($dosenStatItems as $stat)
        <div class="flex items-center gap-3 p-3.5 rounded-xl {{ $dosenColorMap[$stat['color']]['bg'] }} border {{ $dosenColorMap[$stat['color']]['border'] }}">
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center">
                <svg class="w-5 h-5 {{ $dosenColorMap[$stat['color']]['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}" />
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold {{ $dosenColorMap[$stat['color']]['text'] }}">{{ $stat['count'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 -mt-0.5">{{ $stat['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table admin-desktop-table admin-mobile-list admin-lecturer-list">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dosen</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor Induk</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Program Studi</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Telepon</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($dosenList as $index => $dosen)
                    <tr class="group hover:bg-blue-50/40 dark:hover:bg-blue-900/10 transition-colors duration-150">
                        {{-- Row Number --}}
                        <td class="admin-lecturer-no px-5 py-4" data-label="No">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">#{{ ($dosenPaginated->currentPage() - 1) * $dosenPaginated->perPage() + $index + 1 }}</span>
                        </td>
                        {{-- Avatar + Name + Email (combined) --}}
                        <td class="admin-lecturer-main px-5 py-4" data-label="Dosen">
                            @php
                                $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500', 'bg-teal-500'];
                                $colorClass = $colors[$dosen['id'] % count($colors)];
                                $initials = collect(explode(' ', $dosen['nama']))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                                $avatarFallback = '<div class="admin-profile-avatar admin-lecturer-avatar w-10 h-10 rounded-full ' . $colorClass . ' flex items-center justify-center text-white text-sm font-bold ring-2 ring-white dark:ring-gray-700 shadow-sm flex-shrink-0">' . e($initials) . '</div>';
                            @endphp
                            <div class="flex items-center gap-3.5">
                                @if($dosen['foto_url'])
                                    <img src="{{ $dosen['foto_url'] }}" alt="{{ $dosen['nama'] }}" class="admin-profile-avatar admin-lecturer-avatar w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shadow-sm flex-shrink-0" data-fallback="{{ $avatarFallback }}" onerror="this.outerHTML=this.dataset.fallback;">
                                @else
                                    <div class="admin-profile-avatar admin-lecturer-avatar w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center text-white text-sm font-bold ring-2 ring-white dark:ring-gray-700 shadow-sm flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[200px] group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $dosen['nama'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $dosen['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- Nomor Induk --}}
                        <td class="admin-lecturer-id px-5 py-4" data-label="Nomor Induk">
                            @if($dosen['nomor_induk'] && $dosen['nomor_induk'] !== '-')
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $dosen['nomor_induk'] }}</span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        {{-- Program Studi --}}
                        <td class="admin-lecturer-program px-5 py-4" data-label="Program Studi">
                            @if($dosen['program_studi'] && $dosen['program_studi'] !== '-')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-lg bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/40">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $dosen['program_studi'] }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        {{-- No. Telepon --}}
                        <td class="admin-lecturer-phone px-5 py-4" data-label="No. Telepon">
                            @if($dosen['no_telepon'] && $dosen['no_telepon'] !== '-')
                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $dosen['no_telepon'] }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="admin-lecturer-status px-5 py-4 text-center" data-label="Status">
                            @if($dosen['status'] === 'Aktif')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-700/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($dosen['status'] === 'Pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-700/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Persetujuan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-700/40">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="admin-lecturer-action px-5 py-4 text-center" data-label="Aksi">
                            <div class="admin-lecturer-action-group inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-0.5">
                                @if($dosen['status'] === 'Pending')
                                <button onclick="confirmApprove({{ $dosen['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Setujui Dosen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Terima
                                </button>
                                <div class="w-px h-4 bg-gray-200 dark:bg-gray-600"></div>
                                @endif
                                <button onclick="openEditModal({{ $dosen['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Edit Dosen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <div class="w-px h-4 bg-gray-200 dark:bg-gray-600"></div>
                                <button onclick="confirmDelete({{ $dosen['id'] }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-white dark:hover:bg-gray-600 rounded-md transition-all duration-150 shadow-none hover:shadow-sm" title="Hapus Dosen">
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
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="text-base font-semibold text-gray-500 dark:text-gray-400">Belum ada data dosen</p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Dosen" untuk menambahkan dosen baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($totalDosen > 0)
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/50 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50 dark:bg-gray-900/20 admin-responsive-pagination">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $dosenPaginated->firstItem() ?? 0 }}</span>-<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $dosenPaginated->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $totalDosen }}</span> dosen
            </p>
            <div class="flex flex-wrap items-center justify-center gap-1 admin-responsive-actions">
                {{-- Previous --}}
                @if($dosenPaginated->onFirstPage())
                <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                @else
                <a href="{{ $dosenPaginated->previousPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif
                
                {{-- Page Numbers --}}
                @for($i = 1; $i <= $dosenPaginated->lastPage(); $i++)
                    @if($i <= 4 || $i === $dosenPaginated->lastPage())
                    <a href="{{ $dosenPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-semibold rounded-lg transition {{ $i === $currentPage ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-sm' }}">
                        {{ $i }}
                    </a>
                    @elseif($i === 5 && $dosenPaginated->lastPage() > 5)
                    <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor
                
                {{-- Next --}}
                @if($dosenPaginated->hasMorePages())
                <a href="{{ $dosenPaginated->nextPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
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

    {{-- Add Dosen Modal --}}
    <div id="addDosenModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddModal()"></div>
        
        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                {{-- Close Button --}}
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                {{-- Modal Body --}}
                <form action="{{ route('admin.dosen.store') }}" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    
                    {{-- Modal Header --}}
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Dosen</h3>
                        <p class="text-sm text-blue-500">Isi informasi dosen baru dengan lengkap.</p>
                    </div>
                    
                    {{-- Foto Profil --}}
                    <div class="mb-6">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-3">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div id="photoPreview" class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Foto
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
                                @if(old('_modal') === 'add' && $errors->has('foto'))
                                    <p class="text-xs text-red-500 mt-2">{{ $errors->first('foto') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Dosen Box --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Dosen</h4>
                        
                        <div class="space-y-4">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Masukkan nama lengkap" value="{{ old('_modal') === 'add' ? old('name') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            {{-- Nomor Induk --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nomor Induk</label>
                                <input type="text" name="nomor_induk" required placeholder="Masukkan Nomor Induk" value="{{ old('_modal') === 'add' ? old('nomor_induk') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            {{-- Program Studi --}}
                            <div x-data="{ open: false, selectedCount: 0 }" x-init="
                                $watch('open', () => { 
                                    selectedCount = $el.querySelectorAll('input[type=checkbox]:checked').length; 
                                });
                                setTimeout(() => selectedCount = $el.querySelectorAll('input[type=checkbox]:checked').length, 100);
                            ">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-left text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 flex justify-between items-center ring-1 ring-inset ring-gray-200 dark:ring-gray-600 transition-all">
                                        <span x-text="selectedCount > 0 ? selectedCount + ' Program Studi Terpilih' : 'Pilih program studi...'" :class="selectedCount > 0 ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-400'">Pilih program studi...</span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="open" style="display: none;" x-transition class="absolute z-[60] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-xl py-1.5 max-h-56 overflow-y-auto">
                                        @foreach($jurusanList as $jurusan)
                                        <label class="flex items-center gap-3 px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer group transition-colors">
                                            <input type="checkbox" name="id_jurusan[]" value="{{ $jurusan->id_jurusan }}" {{ old('_modal') === 'add' && collect(old('id_jurusan', []))->map(fn ($id) => (string) $id)->contains((string) $jurusan->id_jurusan) ? 'checked' : '' }} @change="selectedCount = $el.closest('[x-data]').querySelectorAll('input[type=checkbox]:checked').length" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 transition-colors">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ $jurusan->nama_jurusan }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @if(old('_modal') === 'add' && ($errors->has('id_jurusan') || $errors->has('id_jurusan.*')))
                                    <p class="text-xs text-red-500 mt-2">{{ $errors->first('id_jurusan') ?: $errors->first('id_jurusan.*') }}</p>
                                @endif
                            </div>
                            
                            {{-- Email --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Email</label>
                                <input type="email" name="email" required placeholder="email@university.ac.id" value="{{ old('_modal') === 'add' ? old('email') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            {{-- Nomor Telepon --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nomor Telepon</label>
                                <input type="tel" name="no_hp" placeholder="+62 812 3456 7890" value="{{ old('_modal') === 'add' ? old('no_hp') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="border border-blue-200 bg-blue-50/80 dark:border-blue-800/40 dark:bg-blue-900/10 rounded-xl p-5 mb-4">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.105.895-2 2-2s2 .895 2 2-.895 2-2 2m-4 4h8m-8-8h.01M7 21h10a2 2 0 002-2V7.414a2 2 0 00-.586-1.414l-3.414-3.414A2 2 0 0013.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-700 dark:text-blue-300">Password Default Dosen</h4>
                                <p class="text-xs text-blue-600/90 dark:text-blue-300/80 mt-1">
                                    Password awal otomatis sama dengan <span class="font-semibold">Nomor Induk</span> yang diisi pada form ini.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border border-emerald-200 bg-emerald-50/80 dark:border-emerald-800/40 dark:bg-emerald-900/10 rounded-xl p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Status Dosen Otomatis Aktif</h4>
                                <p class="text-xs text-emerald-600/90 dark:text-emerald-300/80 mt-1">Setiap data dosen yang disimpan dari form ini akan langsung berstatus aktif.</p>
                            </div>
                        </div>
                    </div>
                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Dosen Modal --}}
    <div id="editDosenModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        
        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                {{-- Close Button --}}
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                {{-- Modal Body --}}
                <form id="editDosenForm" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_id" id="edit_dosen_id" value="{{ old('_id') }}">
                    
                    {{-- Modal Header --}}
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Dosen</h3>
                        <p class="text-sm text-blue-500">Ubah informasi dosen.</p>
                    </div>
                    
                    {{-- Foto Profil --}}
                    <div class="mb-6">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-3">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div id="editPhotoPreview" class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2 px-3 py-1.5 border border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-sm font-medium rounded-lg cursor-pointer transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Foto
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewEditPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
                                @if(old('_modal') === 'edit' && $errors->has('foto'))
                                    <p class="text-xs text-red-500 mt-2">{{ $errors->first('foto') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Dosen Box --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Dosen</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" id="edit_name" required placeholder="Masukkan nama lengkap" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nomor Induk</label>
                                <input type="text" name="nomor_induk" id="edit_nomor_induk" required placeholder="Masukkan Nomor Induk" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div x-data="{ open: false, selectedCount: 0 }" x-init="
                                $watch('open', () => { 
                                    selectedCount = $el.querySelectorAll('input[type=checkbox]:checked').length; 
                                });
                                // also listen for external changes from the script popping open the modal
                                document.addEventListener('DOMContentLoaded', () => {
                                    setInterval(() => {
                                        if(!open) selectedCount = $el.querySelectorAll('input[type=checkbox]:checked').length;
                                    }, 200);
                                });
                            ">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi</label>
                                <div class="relative">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-left text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 flex justify-between items-center ring-1 ring-inset ring-gray-200 dark:ring-gray-600 transition-all">
                                        <span x-text="selectedCount > 0 ? selectedCount + ' Program Studi Terpilih' : 'Pilih program studi...'" :class="selectedCount > 0 ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-400'">Pilih program studi...</span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="open" style="display: none;" x-transition class="absolute z-[60] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-xl py-1.5 max-h-56 overflow-y-auto">
                                        @foreach($jurusanList as $jurusan)
                                        <label class="flex items-center gap-3 px-3.5 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer group transition-colors">
                                            <input type="checkbox" name="id_jurusan[]" value="{{ $jurusan->id_jurusan }}" @change="selectedCount = $el.closest('[x-data]').querySelectorAll('input[type=checkbox]:checked').length" class="edit_id_jurusan_checkbox w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 transition-colors">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ $jurusan->nama_jurusan }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @if(old('_modal') === 'edit' && ($errors->has('id_jurusan') || $errors->has('id_jurusan.*')))
                                    <p class="text-xs text-red-500 mt-2">{{ $errors->first('id_jurusan') ?: $errors->first('id_jurusan.*') }}</p>
                                @endif
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Email</label>
                                <input type="email" name="email" id="edit_email" required placeholder="email@university.ac.id" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nomor Telepon</label>
                                <input type="tel" name="no_hp" id="edit_no_hp" placeholder="+62 812 3456 7890" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Ubah Password</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Kosongkan jika tidak ingin mengubah password dosen.</p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password" id="edit_password" autocomplete="new-password" placeholder="Minimal 8 karakter" class="w-full px-3 py-2.5 pr-11 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="togglePasswordVisibility('edit_password', this)" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" aria-label="Tampilkan password">
                                        <svg class="w-4 h-4 password-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg class="w-4 h-4 password-eye-off hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.956 9.956 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.02 10.02 0 01-4.132 5.411M6.11 6.11A10.02 10.02 0 002.458 12a9.99 9.99 0 005.932 6.265" />
                                        </svg>
                                    </button>
                                </div>
                                @if(old('_modal') === 'edit' && $errors->has('password'))
                                    <p class="text-xs text-red-500 mt-2">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="edit_password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru" class="w-full px-3 py-2.5 pr-11 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                                    <button type="button" onclick="togglePasswordVisibility('edit_password_confirmation', this)" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" aria-label="Tampilkan konfirmasi password">
                                        <svg class="w-4 h-4 password-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg class="w-4 h-4 password-eye-off hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.956 9.956 0 0112 4c4.477 0 8.268 2.943 9.542 7a10.02 10.02 0 01-4.132 5.411M6.11 6.11A10.02 10.02 0 002.458 12a9.99 9.99 0 005.932 6.265" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border border-emerald-200 bg-emerald-50/80 dark:border-emerald-800/40 dark:bg-emerald-900/10 rounded-xl p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-sm">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Status Dosen Akan Tetap Aktif</h4>
                                <p class="text-xs text-emerald-600/90 dark:text-emerald-300/80 mt-1">Perubahan data dosen dari form ini tetap mempertahankan status aktif.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteDosenModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Dosen?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data dosen akan dihapus permanen dan tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <form id="deleteDosenForm" method="POST" class="inline" x-data="{ isLoading: false }" @submit="isLoading = true">
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

    {{-- Import Excel Modal --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeImportModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <button onclick="closeImportModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Import Dosen dari Excel</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload file Excel (.xlsx) untuk mengimport data dosen secara massal.</p>
                </div>

                {{-- Step 1: Upload --}}
                <div id="importStep1">
                    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Kolom yang dibutuhkan:</p>
                        <code class="text-xs text-blue-600 dark:text-blue-400">nama, nomor_induk, email, kode_jurusan, no_hp, status</code>
                        <p class="mt-2 text-xs text-blue-700/90 dark:text-blue-300/90">
                            Gunakan <span class="font-semibold">kode_jurusan</span>. Jika dosen mengajar lebih dari satu prodi, pisahkan dengan koma. Status kosong akan otomatis menjadi aktif.
                        </p>
                        <p class="mt-1 text-xs text-blue-700/90 dark:text-blue-300/90">
                            Password default hasil import = <span class="font-semibold">Nomor Induk</span>. Dosen harus memakai menu forgot password saat pertama kali login.
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('admin.import.template', 'dosen') }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Pilih File Excel</label>
                            <input type="file" id="importFile" accept=".xlsx,.xls,.csv" data-max-size-mb="5" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                        <p class="text-xs text-gray-400 mt-1">Maks 5MB. Format: .xlsx, .xls, .csv | Password default: Nomor Induk</p>
                    </div>
                    <div id="importUploadStatus" class="mb-4 hidden"></div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-3">
                        <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Batal</button>
                        <button type="button" onclick="previewImportFile('dosen')" id="previewBtn" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">Preview</button>
                    </div>
                </div>

                {{-- Step 2: Preview & Confirm --}}
                <div id="importStep2" class="hidden">
                    <div id="importPreviewSummary" class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm"></div>
                    <div id="importPreviewTable" class="mb-4 max-h-64 overflow-auto border border-gray-200 dark:border-gray-700 rounded-lg"></div>
                    <div id="importErrorList" class="mb-4 hidden"></div>
                    <div class="mb-4">
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Strategi Duplikat (Nomor Induk sudah ada):</label>
                        <select id="importStrategy" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                            <option value="skip">Lewati (skip)</option>
                            <option value="update">Perbarui data (update)</option>
                            <option value="stop">Hentikan jika ada duplikat</option>
                        </select>
                    </div>
                    <div class="flex justify-between gap-3">
                        <button type="button" onclick="backToStep1()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Kembali</button>
                        <button type="button" onclick="confirmImportFile('dosen')" id="confirmImportBtn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">Konfirmasi Import</button>
                    </div>
                </div>

                {{-- Step 3: Result --}}
                <div id="importStep3" class="hidden">
                    <div id="importResult" class="mb-4"></div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeImportModal(); location.reload();" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    

    @push('scripts')
    <script>
        const defaultPhotoPreviewSvg = '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';

        function resetPhotoPreview(previewId) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.innerHTML = defaultPhotoPreviewSvg;
            }
        }

        function setPhotoPreviewFromUrl(previewId, photoUrl) {
            const preview = document.getElementById(previewId);
            if (!preview) {
                return;
            }

            if (!photoUrl) {
                resetPhotoPreview(previewId);
                return;
            }

            const img = document.createElement('img');
            img.src = photoUrl;
            img.className = 'w-full h-full object-cover';
            img.onerror = function() {
                resetPhotoPreview(previewId);
            };

            preview.innerHTML = '';
            preview.appendChild(img);
        }

        function validatePhotoBeforePreview(input, previewId) {
            if (!input.files || !input.files[0]) {
                resetPhotoPreview(previewId);
                return false;
            }

            const file = input.files[0];
            const maxSizeBytes = 2 * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                alert(`File "${file.name}" terlalu besar. Maksimal 2MB.`);
                input.value = '';
                resetPhotoPreview(previewId);
                return false;
            }

            return true;
        }

        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            if (!input) {
                return;
            }

            const shouldShow = input.type === 'password';
            input.type = shouldShow ? 'text' : 'password';
            button.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
            button.querySelector('.password-eye')?.classList.toggle('hidden', shouldShow);
            button.querySelector('.password-eye-off')?.classList.toggle('hidden', !shouldShow);
        }

        // Add Modal functions
        function openAddModal() {
            document.getElementById('addDosenModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeAddModal() {
            document.getElementById('addDosenModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function previewPhoto(input) {
            if (!validatePhotoBeforePreview(input, 'photoPreview')) {
                return;
            }

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                resetPhotoPreview('photoPreview');
            }
        }
        
        // Edit Modal functions
        function openEditModal(id) {
            fetch('/admin/dosen/' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editDosenForm').action = '/admin/dosen/' + id;
                    document.getElementById('edit_dosen_id').value = id;
                    document.getElementById('edit_name').value = data.name || '';
                    document.getElementById('edit_email').value = data.email || '';
                    document.getElementById('edit_nomor_induk').value = data.nomor_induk || '';
                    document.getElementById('edit_no_hp').value = data.no_hp || '';
                    document.getElementById('edit_password').value = '';
                    document.getElementById('edit_password_confirmation').value = '';
                    
                    // Uncheck all jurusan checkboxes first
                    document.querySelectorAll('.edit_id_jurusan_checkbox').forEach(cb => cb.checked = false);
                    // Check according to data.id_jurusan. For now assuming single value returned from old logic, but in future it could be an array
                    if (data.id_jurusan) {
                        const jurusans = Array.isArray(data.id_jurusan) ? data.id_jurusan : [data.id_jurusan];
                        jurusans.forEach(j_id => {
                            const cb = document.querySelector(`.edit_id_jurusan_checkbox[value="${j_id}"]`);
                            if (cb) cb.checked = true;
                        });
                    }
                    
                    setPhotoPreviewFromUrl('editPhotoPreview', data.foto_url || null);
                    
                    document.getElementById('editDosenModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data dosen');
                });
        }
        
        function closeEditModal() {
            document.getElementById('editDosenModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function previewEditPhoto(input) {
            if (!validatePhotoBeforePreview(input, 'editPhotoPreview')) {
                return;
            }

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('editPhotoPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                resetPhotoPreview('editPhotoPreview');
            }
        }
        
        // Delete Modal functions
        function confirmDelete(id) {
            document.getElementById('deleteDosenForm').action = '/admin/dosen/' + id;
            document.getElementById('deleteDosenModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteDosenModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Approve Modal functions
        function confirmApprove(id) {
            document.getElementById('approveDosenForm').action = '/admin/dosen/' + id + '/approve';
            document.getElementById('approveDosenModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeApproveModal() {
            document.getElementById('approveDosenModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Import Modal functions
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            backToStep1();
        }
        
        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function backToStep1() {
            document.getElementById('importStep1').classList.remove('hidden');
            document.getElementById('importStep2').classList.add('hidden');
            document.getElementById('importStep3').classList.add('hidden');
            document.getElementById('importUploadStatus').classList.add('hidden');
        }

        function previewImportFile(type) {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files || !fileInput.files[0]) { alert('Pilih file terlebih dahulu.'); return; }
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            const btn = document.getElementById('previewBtn');
            const status = document.getElementById('importUploadStatus');
            btn.disabled = true; btn.textContent = 'Memproses...';
            status.classList.remove('hidden');
            status.innerHTML = '<p class="text-xs text-blue-500">Mengupload dan membaca file...</p>';
            fetch('/admin/import/' + type + '/preview', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.error) { status.innerHTML = '<p class="text-xs text-red-500">' + data.error + '</p>'; return; }
                showPreview(data.preview);
            }).catch(err => {
                status.innerHTML = '<p class="text-xs text-red-500">Gagal: ' + err.message + '</p>';
            }).finally(() => { btn.disabled = false; btn.textContent = 'Preview'; });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showPreview(preview) {
            document.getElementById('importStep1').classList.add('hidden');
            document.getElementById('importStep2').classList.remove('hidden');
            const totalRows = preview.total ?? preview.total_rows ?? 0;
            const validCount = preview.valid_count ?? (preview.valid_rows ? preview.valid_rows.length : 0);
            const invalidCount = preview.invalid_count ?? preview.error_count ?? (preview.invalid_rows ? preview.invalid_rows.length : 0);
            const previewErrors = (preview.invalid_rows || [])
                .flatMap(row => row._errors || [])
                .filter(Boolean);

            document.getElementById('importPreviewSummary').innerHTML = `<div class="flex gap-4 text-center"><div class="flex-1"><p class="text-lg font-bold text-gray-800 dark:text-white">${totalRows}</p><p class="text-xs text-gray-500">Total Baris</p></div><div class="flex-1"><p class="text-lg font-bold text-green-600">${validCount}</p><p class="text-xs text-gray-500">Valid</p></div><div class="flex-1"><p class="text-lg font-bold text-red-600">${invalidCount}</p><p class="text-xs text-gray-500">Error</p></div></div>`;
            const table = document.getElementById('importPreviewTable');
            const validRows = (preview.valid_rows || []).map(row => ({ ...row, _valid: true }));
            const invalidRows = (preview.invalid_rows || []).map(row => ({ ...row, _valid: false }));
            const previewRows = [...validRows, ...invalidRows].sort((a, b) => (a._row || 0) - (b._row || 0));
            const cols = (preview.header || ['nama', 'nomor_induk', 'email', 'kode_jurusan', 'no_hp', 'status'])
                .filter(c => !String(c).startsWith('_'));

            if (previewRows.length > 0) {
                let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-100 dark:bg-gray-700">';
                html += '<th class="px-2 py-1.5 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">baris</th>';
                cols.forEach(c => html += '<th class="px-2 py-1.5 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">' + escapeHtml(c) + '</th>');
                html += '<th class="px-2 py-1.5 text-left text-gray-600 dark:text-gray-300 whitespace-nowrap">validasi</th>';
                html += '</tr></thead><tbody>';
                previewRows.slice(0, 15).forEach(row => {
                    const rowTone = row._valid ? '' : 'bg-red-50/70 dark:bg-red-900/10';
                    html += '<tr class="border-t border-gray-100 dark:border-gray-700 ' + rowTone + '">';
                    html += '<td class="px-2 py-1.5 text-gray-500 dark:text-gray-400 whitespace-nowrap">' + escapeHtml(row._row || '-') + '</td>';
                    cols.forEach(c => html += '<td class="px-2 py-1.5 text-gray-700 dark:text-gray-300 whitespace-nowrap">' + escapeHtml(row[c] || '-') + '</td>');
                    if (row._valid) {
                        html += '<td class="px-2 py-1.5"><span class="inline-flex rounded-full bg-green-50 px-2 py-0.5 text-[11px] font-semibold text-green-700 ring-1 ring-green-200 dark:bg-green-900/20 dark:text-green-300 dark:ring-green-800">Valid</span></td>';
                    } else {
                        html += '<td class="px-2 py-1.5 text-red-600 dark:text-red-300 min-w-[220px]">' + escapeHtml((row._errors || []).join(' ')) + '</td>';
                    }
                    html += '</tr>';
                });
                if (previewRows.length > 15) html += '<tr><td colspan="' + (cols.length + 2) + '" class="px-2 py-1.5 text-gray-400 text-center">...dan ' + (previewRows.length - 15) + ' baris lagi</td></tr>';
                html += '</tbody></table>'; table.innerHTML = html;
            } else { table.innerHTML = '<p class="p-4 text-sm text-gray-400 text-center">Tidak ada data yang bisa dibaca</p>'; }
            const errorList = document.getElementById('importErrorList');
            if (previewErrors.length > 0) {
                errorList.classList.remove('hidden');
                let errHtml = '<div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg"><p class="text-xs text-red-700 dark:text-red-300 font-medium mb-1">Error ditemukan:</p><p class="mb-2 text-xs text-red-600 dark:text-red-400">Baris error tidak akan diimport sampai filenya diperbaiki.</p><ul class="text-xs text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">';
                previewErrors.slice(0, 10).forEach(e => errHtml += '<li>' + escapeHtml(e) + '</li>');
                if (previewErrors.length > 10) errHtml += '<li>...dan ' + (previewErrors.length - 10) + ' error lagi</li>';
                errHtml += '</ul></div>'; errorList.innerHTML = errHtml;
            } else { errorList.classList.add('hidden'); }
            const confirmBtn = document.getElementById('confirmImportBtn');
            confirmBtn.disabled = !validCount;
            confirmBtn.textContent = invalidCount > 0 && validCount > 0
                ? `Import ${validCount} Data Valid Saja`
                : 'Konfirmasi Import';
        }

        function confirmImportFile(type) {
            const strategy = document.getElementById('importStrategy').value;
            const btn = document.getElementById('confirmImportBtn');
            btn.disabled = true; btn.textContent = 'Mengimport...';
            fetch('/admin/import/' + type + '/confirm', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ strategy: strategy })
            }).then(r => r.json()).then(data => {
                document.getElementById('importStep2').classList.add('hidden');
                document.getElementById('importStep3').classList.remove('hidden');
                const result = document.getElementById('importResult');
                if (data.success) {
                    result.innerHTML = '<div class="text-center"><div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center"><svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div><h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Berhasil!</h4><p class="text-sm text-gray-600 dark:text-gray-400">' + data.message + '</p></div>';
                } else {
                    result.innerHTML = '<div class="text-center"><div class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center"><svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div><h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Dihentikan</h4><p class="text-sm text-gray-600 dark:text-gray-400">' + data.message + '</p></div>';
                }
            }).catch(err => {
                document.getElementById('importStep2').classList.add('hidden');
                document.getElementById('importStep3').classList.remove('hidden');
                document.getElementById('importResult').innerHTML = '<p class="text-center text-red-500">Error: ' + err.message + '</p>';
            }).finally(() => { btn.disabled = false; btn.textContent = 'Konfirmasi Import'; });
        }
        
        // Status checkbox now uses hidden input fallback pattern (no JS needed)

        // Auto-reopen modal on validation error
        @if($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => openAddModal());
        @elseif($errors->any() && old('_modal') === 'edit')
        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editDosenForm');
            const editId = '{{ old('_id') }}';
            if (editId) {
                editForm.action = '/admin/dosen/' + editId;
                document.getElementById('edit_name').value = '{{ old('name') }}';
                document.getElementById('edit_nomor_induk').value = '{{ old('nomor_induk') }}';
                document.getElementById('edit_email').value = '{{ old('email') }}';
                document.getElementById('edit_no_hp').value = '{{ old('no_hp') }}';
                
                // Set checked status for jurusan checkboxes
                const oldJurusan = @json(old('id_jurusan', []));
                const jurusans = Array.isArray(oldJurusan) ? oldJurusan : [oldJurusan];
                document.querySelectorAll('.edit_id_jurusan_checkbox').forEach(cb => {
                    cb.checked = jurusans.includes(cb.value) || jurusans.includes(Number(cb.value));
                });
                
            }
            document.getElementById('editDosenModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>

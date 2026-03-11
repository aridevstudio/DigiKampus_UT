<x-layouts.admin title="Kelola Mahasiswa" active="mahasiswa">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Mahasiswa</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Kelola data mahasiswa, tambah mahasiswa baru, dan atur status keaktifan.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="responsive-grid-stats mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAll }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Mahasiswa</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAktif }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalNonaktif }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBaru }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Baru (30 Hari)</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.mahasiswa') }}" class="admin-toolbar-responsive bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        <div class="space-y-2 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:gap-3">
            {{-- Buttons --}}
            <div class="flex flex-wrap gap-1.5 sm:contents">
                <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-blue-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah
                </button>
                <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-green-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Import
                </button>
                <a href="{{ route('admin.export.excel', 'mahasiswa') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm shadow-amber-500/25">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </a>
                <a href="{{ route('admin.import.template', 'mahasiswa') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 transition">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Template
                </a>
            </div>

            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Filters --}}
            <div class="grid grid-cols-2 gap-1.5 sm:contents">
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div class="relative">
                    <select name="prodi" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Semua Prodi</option>
                        @foreach($jurusanList as $jurusan)
                        <option value="{{ $jurusan->id_jurusan }}" {{ ($prodiFilter ?? '') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                        @endforeach
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Search --}}
            <div class="flex gap-1.5 sm:flex-1 sm:min-w-[200px] sm:max-w-xs sm:ml-auto">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama, email, NIM..." class="w-full px-3 py-1.5 pl-9 sm:px-4 sm:py-2 sm:pl-9 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <svg class="w-4 h-4 absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 transition flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table admin-desktop-table admin-mobile-list">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIM</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Program Studi</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kontak</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($mahasiswaList as $mhs)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition group">
                        {{-- Combined Avatar + Name --}}
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                @if($mhs['foto'])
                                    <img src="{{ asset('storage/' . $mhs['foto']) }}" alt="{{ $mhs['nama'] }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shadow-sm">
                                @else
                                    @php
                                        $initials = collect(explode(' ', $mhs['nama']))->take(2)->map(fn($w) => strtoupper(mb_substr($w, 0, 1)))->join('');
                                        $colors = ['bg-blue-500', 'bg-indigo-500', 'bg-violet-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-500', 'bg-teal-500'];
                                        $bgColor = $colors[crc32($mhs['nama']) % count($colors)];
                                    @endphp
                                    <div class="w-9 h-9 rounded-full {{ $bgColor }} flex items-center justify-center text-white text-xs font-bold ring-2 ring-white dark:ring-gray-700 shadow-sm">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $mhs['nama'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $mhs['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- NIM --}}
                        <td class="px-6 py-3.5">
                            @if($mhs['nim'] !== '-')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700/50 text-xs font-mono font-medium text-gray-700 dark:text-gray-300">{{ $mhs['nim'] }}</span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Program Studi --}}
                        <td class="px-6 py-3.5">
                            @if($mhs['program_studi'] !== '-')
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $mhs['program_studi'] }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Contact --}}
                        <td class="px-6 py-3.5">
                            @if($mhs['no_telepon'] !== '-')
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $mhs['no_telepon'] }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        {{-- Status --}}
                        <td class="px-6 py-3.5 text-center">
                            @if($mhs['status'] === 'Aktif')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 ring-1 ring-green-200 dark:ring-green-800/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-800/50">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        {{-- Actions --}}
                        <td class="px-6 py-3.5 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-0.5">
                                <button onclick="openEditModal({{ $mhs['id'] }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition shadow-none hover:shadow-sm" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="confirmDelete({{ $mhs['id'] }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition shadow-none hover:shadow-sm" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada data mahasiswa</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah" untuk menambahkan mahasiswa baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($totalMahasiswa > 0)
        <div class="px-6 py-3.5 bg-gray-50/50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between admin-responsive-pagination">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $mahasiswaPaginated->firstItem() ?? 0 }}-{{ $mahasiswaPaginated->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-300">{{ $totalMahasiswa }}</span> mahasiswa
            </p>
            <div class="flex items-center gap-1 admin-responsive-actions">
                {{-- Previous --}}
                @if($mahasiswaPaginated->onFirstPage())
                <button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                @else
                <a href="{{ $mahasiswaPaginated->previousPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif
                
                {{-- Page Numbers --}}
                @for($i = 1; $i <= $mahasiswaPaginated->lastPage(); $i++)
                    @if($i <= 5 || $i === $mahasiswaPaginated->lastPage())
                    <a href="{{ $mahasiswaPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-medium rounded-lg transition {{ $i === $currentPage ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/25' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 hover:shadow-sm' }}">
                        {{ $i }}
                    </a>
                    @elseif($i === 6)
                    <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor
                
                {{-- Next --}}
                @if($mahasiswaPaginated->hasMorePages())
                <a href="{{ $mahasiswaPaginated->nextPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition shadow-none hover:shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Add Mahasiswa Modal --}}
    <div id="addMahasiswaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAddModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form action="{{ route('admin.mahasiswa.store') }}" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Mahasiswa</h3>
                        <p class="text-sm text-blue-500">Isi informasi mahasiswa baru dengan lengkap.</p>
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
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Mahasiswa Box --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Mahasiswa</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" required placeholder="Masukkan nama lengkap" value="{{ old('_modal') === 'add' ? old('name') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">NIM</label>
                                <input type="text" name="nim" required placeholder="Masukkan NIM" value="{{ old('_modal') === 'add' ? old('nim') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi</label>
                                <div class="relative">
                                    <select name="id_jurusan" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        <option value="" class="text-gray-400">Pilih program studi</option>
                                        @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id_jurusan }}" {{ old('_modal') === 'add' && old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Email</label>
                                <input type="email" name="email" required placeholder="email@university.ac.id" value="{{ old('_modal') === 'add' ? old('email') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nomor Telepon</label>
                                <input type="tel" name="no_hp" placeholder="+62 812 3456 7890" value="{{ old('_modal') === 'add' ? old('no_hp') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kursus Terdaftar --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <div class="flex items-center justify-between cursor-pointer" onclick="toggleAddCourseSection()">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Kursus Terdaftar</h4>
                            <svg id="addCourseSectionIcon" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div id="addCourseSection" class="hidden mt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Pilih kursus yang akan didaftarkan untuk mahasiswa ini (opsional)</p>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($courseList ?? [] as $course)
                                <label class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="courses[]" value="{{ $course->id_course }}" class="w-4 h-4 text-blue-500 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $course->nama_course }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    {{-- Status Keaktifan --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Keaktifan</h4>
                                <p class="text-xs text-blue-500">Tentukan status aktif mahasiswa</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tidak Aktif</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="status" value="nonaktif">
                                    <input type="checkbox" name="status" value="aktif" checked class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                                <span class="text-xs text-blue-500 font-medium">Aktif</span>
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition" :disabled="isLoading">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="isLoading">
                            <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Mahasiswa Modal --}}
    <div id="editMahasiswaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl transform transition-all my-auto">
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form id="editMahasiswaForm" method="POST" enctype="multipart/form-data" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_id" id="edit_mhs_id" value="{{ old('_id') }}">
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Data Mahasiswa</h3>
                        <p class="text-sm text-blue-500">Isi informasi mahasiswa baru dengan lengkap.</p>
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
                            </div>
                        </div>
                    </div>
                    
                    {{-- Informasi Mahasiswa Box --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Informasi Mahasiswa</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Lengkap</label>
                                <input type="text" name="name" id="edit_name" required placeholder="Masukkan nama lengkap" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">NIM</label>
                                <input type="text" name="nim" id="edit_nim" required placeholder="Masukkan NIM" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi</label>
                                <div class="relative">
                                    <select name="id_jurusan" id="edit_id_jurusan" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih program studi</option>
                                        @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id_jurusan }}">{{ $jurusan->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
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
                    
                    {{-- Status Keaktifan --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Keaktifan</h4>
                                <p class="text-xs text-blue-500">Tentukan status aktif mahasiswa</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">Tidak Aktif</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="status" value="nonaktif">
                                    <input type="checkbox" name="status" id="edit_status" value="aktif" class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-300 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-5 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                                <span class="text-xs text-blue-500 font-medium">Aktif</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kursus Terdaftar --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Kursus Terdaftar</h4>
                        <div id="edit_enrolledCourses" class="space-y-3">
                            {{-- Courses will be populated via JavaScript --}}
                            <p class="text-sm text-gray-400 dark:text-gray-500">Memuat data kursus...</p>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition" :disabled="isLoading">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="isLoading">
                            <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteMahasiswaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Mahasiswa?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data mahasiswa akan dihapus permanen dan tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <form id="deleteMahasiswaForm" method="POST" class="inline" x-data="{ isLoading: false }" @submit="isLoading = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed" :disabled="isLoading">
                                <svg x-show="isLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                <span x-text="isLoading ? 'Menghapus...' : 'Hapus'"></span>
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
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Import Mahasiswa dari Excel</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Upload file Excel (.xlsx) untuk mengimport data mahasiswa secara massal.</p>
                </div>

                {{-- Step 1: Upload --}}
                <div id="importStep1">
                    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">Kolom yang dibutuhkan:</p>
                        <code class="text-xs text-blue-600 dark:text-blue-400">nama, nim, email, jurusan, no_hp</code>
                        <div class="mt-2">
                            <a href="{{ route('admin.import.template', 'mahasiswa') }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Pilih File Excel</label>
                            <input type="file" id="importFile" accept=".xlsx,.xls,.csv" data-max-size-mb="5" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                        <p class="text-xs text-gray-400 mt-1">Maks 5MB. Format: .xlsx, .xls, .csv | Password default: password123</p>
                    </div>
                    <div id="importUploadStatus" class="mb-4 hidden"></div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-3">
                        <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Batal</button>
                        <button type="button" onclick="previewImportFile('mahasiswa')" id="previewBtn" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">Preview</button>
                    </div>
                </div>

                {{-- Step 2: Preview & Confirm --}}
                <div id="importStep2" class="hidden">
                    <div id="importPreviewSummary" class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm"></div>
                    <div id="importPreviewTable" class="mb-4 max-h-64 overflow-auto border border-gray-200 dark:border-gray-700 rounded-lg"></div>
                    <div id="importErrorList" class="mb-4 hidden"></div>
                    <div class="mb-4">
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Strategi Duplikat (NIM sudah ada):</label>
                        <select id="importStrategy" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white">
                            <option value="skip">Lewati (skip)</option>
                            <option value="update">Perbarui data (update)</option>
                            <option value="stop">Hentikan jika ada duplikat</option>
                        </select>
                    </div>
                    <div class="flex justify-between gap-3">
                        <button type="button" onclick="backToStep1()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">Kembali</button>
                        <button type="button" onclick="confirmImportFile('mahasiswa')" id="confirmImportBtn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">Konfirmasi Import</button>
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
        // Add Modal functions
        function openAddModal() {
            document.getElementById('addMahasiswaModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeAddModal() {
            document.getElementById('addMahasiswaModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Toggle course section in Add modal
        function toggleAddCourseSection() {
            const section = document.getElementById('addCourseSection');
            const icon = document.getElementById('addCourseSectionIcon');
            section.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
        
        // Edit Modal functions
        function openEditModal(id) {
            fetch('/admin/mahasiswa/' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editMahasiswaForm').action = '/admin/mahasiswa/' + id;
                    document.getElementById('edit_mhs_id').value = id;
                    document.getElementById('edit_name').value = data.name || '';
                    document.getElementById('edit_email').value = data.email || '';
                    document.getElementById('edit_nim').value = data.nim || '';
                    document.getElementById('edit_id_jurusan').value = data.id_jurusan || '';
                    document.getElementById('edit_no_hp').value = data.no_hp || '';
                    document.getElementById('edit_status').checked = data.status === 'aktif';
                    
                    // Show existing photo if available
                    const preview = document.getElementById('editPhotoPreview');
                    if (data.foto) {
                        preview.innerHTML = '<img src="/storage/' + data.foto + '" class="w-full h-full object-cover">';
                    } else {
                        preview.innerHTML = '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
                    }
                    
                    // Populate enrolled courses
                    const coursesContainer = document.getElementById('edit_enrolledCourses');
                    if (data.enrolled_courses && data.enrolled_courses.length > 0) {
                        let coursesHtml = '';
                        data.enrolled_courses.forEach(course => {
                            const progress = course.progress || 0;
                            coursesHtml += `
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-sm text-gray-700 dark:text-gray-300 min-w-0 flex-shrink">${course.name}</span>
                                    <div class="flex items-center gap-3 flex-1 max-w-xs">
                                        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 rounded-full transition-all" style="width: ${progress}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">${progress}% selesai</span>
                                    </div>
                                </div>
                            `;
                        });
                        coursesContainer.innerHTML = coursesHtml;
                    } else {
                        coursesContainer.innerHTML = '<p class="text-sm text-gray-400 dark:text-gray-500">Belum ada kursus terdaftar</p>';
                    }
                    
                    document.getElementById('editMahasiswaModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data mahasiswa');
                });
        }
        
        function closeEditModal() {
            document.getElementById('editMahasiswaModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function previewEditPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('editPhotoPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Delete Modal functions
        function confirmDelete(id) {
            document.getElementById('deleteMahasiswaForm').action = '/admin/mahasiswa/' + id;
            document.getElementById('deleteMahasiswaModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteMahasiswaModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Import Modal functions
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Reset to step 1
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
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Pilih file terlebih dahulu.');
                return;
            }
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            const btn = document.getElementById('previewBtn');
            const status = document.getElementById('importUploadStatus');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
            status.classList.remove('hidden');
            status.innerHTML = '<p class="text-xs text-blue-500">Mengupload dan membaca file...</p>';

            fetch('/admin/import/' + type + '/preview', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    status.innerHTML = '<p class="text-xs text-red-500">' + data.error + '</p>';
                    return;
                }
                showPreview(data.preview);
            })
            .catch(err => {
                status.innerHTML = '<p class="text-xs text-red-500">Gagal: ' + err.message + '</p>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Preview';
            });
        }

        function showPreview(preview) {
            document.getElementById('importStep1').classList.add('hidden');
            document.getElementById('importStep2').classList.remove('hidden');

            // Summary
            const summary = document.getElementById('importPreviewSummary');
            summary.innerHTML = `
                <div class="flex gap-4 text-center">
                    <div class="flex-1"><p class="text-lg font-bold text-gray-800 dark:text-white">${preview.total_rows}</p><p class="text-xs text-gray-500">Total Baris</p></div>
                    <div class="flex-1"><p class="text-lg font-bold text-green-600">${preview.valid_count}</p><p class="text-xs text-gray-500">Valid</p></div>
                    <div class="flex-1"><p class="text-lg font-bold text-red-600">${preview.error_count}</p><p class="text-xs text-gray-500">Error</p></div>
                </div>`;

            // Preview table (first 10 valid rows)
            const table = document.getElementById('importPreviewTable');
            if (preview.valid_rows && preview.valid_rows.length > 0) {
                const cols = Object.keys(preview.valid_rows[0]);
                let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-100 dark:bg-gray-700">';
                cols.forEach(c => html += '<th class="px-2 py-1.5 text-left text-gray-600 dark:text-gray-300">' + c + '</th>');
                html += '</tr></thead><tbody>';
                preview.valid_rows.slice(0, 10).forEach(row => {
                    html += '<tr class="border-t border-gray-100 dark:border-gray-700">';
                    cols.forEach(c => html += '<td class="px-2 py-1.5 text-gray-700 dark:text-gray-300">' + (row[c] || '-') + '</td>');
                    html += '</tr>';
                });
                if (preview.valid_rows.length > 10) html += '<tr><td colspan="' + cols.length + '" class="px-2 py-1.5 text-gray-400 text-center">...dan ' + (preview.valid_rows.length - 10) + ' baris lagi</td></tr>';
                html += '</tbody></table>';
                table.innerHTML = html;
            } else {
                table.innerHTML = '<p class="p-4 text-sm text-gray-400 text-center">Tidak ada data valid</p>';
            }

            // Errors
            const errorList = document.getElementById('importErrorList');
            if (preview.errors && preview.errors.length > 0) {
                errorList.classList.remove('hidden');
                let errHtml = '<div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg"><p class="text-xs text-red-700 dark:text-red-300 font-medium mb-1">Error ditemukan:</p><ul class="text-xs text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">';
                preview.errors.slice(0, 10).forEach(e => errHtml += '<li>' + e + '</li>');
                if (preview.errors.length > 10) errHtml += '<li>...dan ' + (preview.errors.length - 10) + ' error lagi</li>';
                errHtml += '</ul></div>';
                errorList.innerHTML = errHtml;
            } else {
                errorList.classList.add('hidden');
            }

            // Disable confirm if no valid rows
            document.getElementById('confirmImportBtn').disabled = !preview.valid_count;
        }

        function confirmImportFile(type) {
            const strategy = document.getElementById('importStrategy').value;
            const btn = document.getElementById('confirmImportBtn');
            btn.disabled = true;
            btn.textContent = 'Mengimport...';

            fetch('/admin/import/' + type + '/confirm', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ strategy: strategy })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('importStep2').classList.add('hidden');
                document.getElementById('importStep3').classList.remove('hidden');
                const result = document.getElementById('importResult');
                if (data.success) {
                    result.innerHTML = `
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Berhasil!</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${data.message}</p>
                        </div>`;
                } else {
                    result.innerHTML = `
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Dihentikan</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">${data.message}</p>
                        </div>`;
                }
            })
            .catch(err => {
                document.getElementById('importStep2').classList.add('hidden');
                document.getElementById('importStep3').classList.remove('hidden');
                document.getElementById('importResult').innerHTML = '<p class="text-center text-red-500">Error: ' + err.message + '</p>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Konfirmasi Import';
            });
        }
        
        // Auto-reopen modal on validation error
        @if($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => openAddModal());
        @elseif($errors->any() && old('_modal') === 'edit')
        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.getElementById('editMahasiswaForm');
            const editId = '{{ old('_id') }}';
            if (editId) {
                editForm.action = '/admin/mahasiswa/' + editId;
                document.getElementById('edit_name').value = '{{ old('name') }}';
                document.getElementById('edit_nim').value = '{{ old('nim') }}';
                document.getElementById('edit_email').value = '{{ old('email') }}';
                document.getElementById('edit_no_hp').value = '{{ old('no_hp') }}';
                document.getElementById('edit_id_jurusan').value = '{{ old('id_jurusan') }}';
                document.getElementById('edit_status').checked = '{{ old('status') }}' === 'aktif';
            }
            document.getElementById('editMahasiswaModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>


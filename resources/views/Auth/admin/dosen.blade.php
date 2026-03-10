<x-layouts.admin title="Kelola Dosen" active="dosen">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Dosen</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data dosen, tambah dosen baru, dan atur status keaktifan.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.dosen') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        <div class="admin-responsive-toolbar flex flex-wrap items-center gap-3">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-blue-500/25 hover:shadow-md hover:shadow-blue-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Dosen
            </button>
            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-emerald-500/25 hover:shadow-md hover:shadow-emerald-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import Excel
            </button>
            <a href="{{ route('admin.export.excel', 'dosen') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-amber-500/25 hover:shadow-md hover:shadow-amber-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <a href="{{ route('admin.import.template', 'dosen') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm text-gray-700 dark:text-gray-300 font-medium rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Template
            </a>
            
            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Status Filter --}}
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="tidak_aktif" {{ ($statusFilter ?? '') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            
            {{-- Program Studi Filter --}}
            <div class="relative">
                <select name="jurusan" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($jurusanFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Program Studi</option>
                    @foreach($jurusanList as $jurusan)
                        <option value="{{ $jurusan->id_jurusan }}" {{ ($jurusanFilter ?? '') == $jurusan->id_jurusan ? 'selected' : '' }}>
                            {{ $jurusan->nama_jurusan }}
                        </option>
                    @endforeach
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            
            {{-- Search --}}
            <div class="relative w-full sm:flex-1 sm:min-w-[200px] max-w-sm sm:ml-auto">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari dosen..." class="w-full px-4 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 mb-6">
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
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-900/40">
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dosen</th>
                        <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIP</th>
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
                        <td class="px-5 py-4">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500">{{ ($dosenPaginated->currentPage() - 1) * $dosenPaginated->perPage() + $index + 1 }}</span>
                        </td>
                        {{-- Avatar + Name + Email (combined) --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3.5">
                                @if($dosen['foto'])
                                    <img src="{{ asset('storage/' . $dosen['foto']) }}" alt="{{ $dosen['nama'] }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-700 shadow-sm flex-shrink-0">
                                @else
                                    @php
                                        $colors = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-500', 'bg-indigo-500', 'bg-teal-500'];
                                        $colorClass = $colors[$dosen['id'] % count($colors)];
                                        $initials = collect(explode(' ', $dosen['nama']))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
                                    @endphp
                                    <div class="w-10 h-10 rounded-full {{ $colorClass }} flex items-center justify-center text-white text-sm font-bold ring-2 ring-white dark:ring-gray-700 shadow-sm flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[200px] group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $dosen['nama'] }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $dosen['email'] }}</p>
                                </div>
                            </div>
                        </td>
                        {{-- NIP --}}
                        <td class="px-5 py-4">
                            @if($dosen['nip'] && $dosen['nip'] !== '-')
                                <span class="text-sm text-gray-700 dark:text-gray-300 font-mono">{{ $dosen['nip'] }}</span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        {{-- Program Studi --}}
                        <td class="px-5 py-4">
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
                        <td class="px-5 py-4">
                            @if($dosen['no_telepon'] && $dosen['no_telepon'] !== '-')
                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $dosen['no_telepon'] }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300 dark:text-gray-600 italic">Belum diisi</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
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
                        <td class="px-5 py-4 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-0.5">
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
                    
                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mb-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
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
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
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
                            
                            {{-- NIP --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">NIP</label>
                                <input type="text" name="nip" required placeholder="Masukkan NIP" value="{{ old('_modal') === 'add' ? old('nip') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
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
                                            <input type="checkbox" name="id_jurusan[]" value="{{ $jurusan->id_jurusan }}" @change="selectedCount = $el.closest('[x-data]').querySelectorAll('input[type=checkbox]:checked').length" class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 focus:ring-2 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600 transition-colors">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">{{ $jurusan->nama_jurusan }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
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
                    
                    {{-- Status Keaktifan --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Keaktifan</h4>
                                <p class="text-xs text-blue-500">Tentukan status aktif dosen</p>
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
                    
                    {{-- Buttons --}}
                    <div class="flex items-center justify-end gap-2 mb-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                            Simpan
                        </button>
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
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewEditPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
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
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">NIP</label>
                                <input type="text" name="nip" id="edit_nip" required placeholder="Masukkan NIP" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500">
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
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Status Keaktifan</h4>
                                <p class="text-xs text-blue-500">Tentukan status aktif dosen</p>
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
                        <code class="text-xs text-blue-600 dark:text-blue-400">nama, nip, email, jurusan, no_hp</code>
                        <div class="mt-2">
                            <a href="{{ route('admin.import.template', 'dosen') }}" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Pilih File Excel</label>
                        <input type="file" id="importFile" accept=".xlsx,.xls,.csv" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                        <p class="text-xs text-gray-400 mt-1">Maks 5MB. Format: .xlsx, .xls, .csv | Password default: password123</p>
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
                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1.5">Strategi Duplikat (NIP sudah ada):</label>
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
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('photoPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(input.files[0]);
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
                    document.getElementById('edit_nip').value = data.nip || '';
                    document.getElementById('edit_no_hp').value = data.no_hp || '';
                    document.getElementById('edit_status').checked = data.status === 'aktif';
                    
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
                    
                    // Show existing photo if available
                    const preview = document.getElementById('editPhotoPreview');
                    if (data.foto) {
                        preview.innerHTML = '<img src="/storage/' + data.foto + '" class="w-full h-full object-cover">';
                    } else {
                        preview.innerHTML = '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
                    }
                    
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

        function showPreview(preview) {
            document.getElementById('importStep1').classList.add('hidden');
            document.getElementById('importStep2').classList.remove('hidden');
            document.getElementById('importPreviewSummary').innerHTML = `<div class="flex gap-4 text-center"><div class="flex-1"><p class="text-lg font-bold text-gray-800 dark:text-white">${preview.total_rows}</p><p class="text-xs text-gray-500">Total Baris</p></div><div class="flex-1"><p class="text-lg font-bold text-green-600">${preview.valid_count}</p><p class="text-xs text-gray-500">Valid</p></div><div class="flex-1"><p class="text-lg font-bold text-red-600">${preview.error_count}</p><p class="text-xs text-gray-500">Error</p></div></div>`;
            const table = document.getElementById('importPreviewTable');
            if (preview.valid_rows && preview.valid_rows.length > 0) {
                const cols = Object.keys(preview.valid_rows[0]);
                let html = '<table class="w-full text-xs"><thead><tr class="bg-gray-100 dark:bg-gray-700">';
                cols.forEach(c => html += '<th class="px-2 py-1.5 text-left text-gray-600 dark:text-gray-300">' + c + '</th>');
                html += '</tr></thead><tbody>';
                preview.valid_rows.slice(0, 10).forEach(row => { html += '<tr class="border-t border-gray-100 dark:border-gray-700">'; cols.forEach(c => html += '<td class="px-2 py-1.5 text-gray-700 dark:text-gray-300">' + (row[c] || '-') + '</td>'); html += '</tr>'; });
                if (preview.valid_rows.length > 10) html += '<tr><td colspan="' + cols.length + '" class="px-2 py-1.5 text-gray-400 text-center">...dan ' + (preview.valid_rows.length - 10) + ' baris lagi</td></tr>';
                html += '</tbody></table>'; table.innerHTML = html;
            } else { table.innerHTML = '<p class="p-4 text-sm text-gray-400 text-center">Tidak ada data valid</p>'; }
            const errorList = document.getElementById('importErrorList');
            if (preview.errors && preview.errors.length > 0) {
                errorList.classList.remove('hidden');
                let errHtml = '<div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg"><p class="text-xs text-red-700 dark:text-red-300 font-medium mb-1">Error:</p><ul class="text-xs text-red-600 dark:text-red-400 list-disc list-inside space-y-0.5">';
                preview.errors.slice(0, 10).forEach(e => errHtml += '<li>' + e + '</li>');
                errHtml += '</ul></div>'; errorList.innerHTML = errHtml;
            } else { errorList.classList.add('hidden'); }
            document.getElementById('confirmImportBtn').disabled = !preview.valid_count;
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
                document.getElementById('edit_nip').value = '{{ old('nip') }}';
                document.getElementById('edit_email').value = '{{ old('email') }}';
                document.getElementById('edit_no_hp').value = '{{ old('no_hp') }}';
                
                // Set checked status for jurusan checkboxes
                const oldJurusan = @json(old('id_jurusan', []));
                const jurusans = Array.isArray(oldJurusan) ? oldJurusan : [oldJurusan];
                document.querySelectorAll('.edit_id_jurusan_checkbox').forEach(cb => {
                    cb.checked = jurusans.includes(cb.value) || jurusans.includes(Number(cb.value));
                });
                
                document.getElementById('edit_status').checked = '{{ old('status') }}' === 'aktif';
            }
            document.getElementById('editDosenModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>

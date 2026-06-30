<x-layouts.admin title="Kategori Forum" active="forum-kategori">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori Forum Komunitas</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Kelola kategori untuk diskusi umum mahasiswa. Topik akan muncul berdasarkan kategori aktif.</p>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-sm text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <div class="responsive-grid-stats mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAll }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Kategori</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $totalAktif }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Aktif</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-500 dark:text-gray-300">{{ $totalNonaktif }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Nonaktif</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.forum-kategori') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3">
            <div class="w-full sm:w-auto">
                <button type="button" onclick="openAddKategoriModal()" class="w-full justify-center inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs sm:text-sm font-medium rounded-xl transition shadow-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Kategori
                </button>
            </div>
            <div class="w-full sm:w-auto">
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3.5 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <div class="w-full sm:flex-1">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..." class="w-full px-3.5 py-2.5 pl-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
            <div class="w-full sm:w-auto">
                <button type="submit" class="w-full justify-center inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <!-- Desktop Table view -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Slug</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Urutan</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($kategoriPaginated as $item)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex w-8 h-8 rounded-lg items-center justify-center text-white text-xs font-bold" style="background-color: {{ $item->warna ?: '#3B82F6' }}">{{ mb_substr($item->nama, 0, 1) }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $item->nama }}</p>
                                    @if($item->deskripsi)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[280px]">{{ $item->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-xs font-mono text-gray-600 dark:text-gray-300">{{ $item->slug }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item->urutan }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-0.5">
                                <button onclick="openEditKategoriModal({{ $item->id_forum_category }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.forum-kategori.toggle', $item->id_forum_category) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                </form>
                                <button onclick="confirmDeleteKategori({{ $item->id_forum_category }}, '{{ addslashes($item->nama) }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h10v10H7V7zm-4 4h4m10 0h4"/></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada kategori forum</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Kategori" untuk menambahkan kategori diskusi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card List view -->
        <div class="block md:hidden divide-y divide-gray-100 dark:divide-gray-700/50">
            @forelse($kategoriPaginated as $item)
            <div class="p-5 space-y-3 bg-white dark:bg-gray-800">
                <!-- Nama -->
                <div>
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Nama</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex w-7 h-7 rounded-lg items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background-color: {{ $item->warna ?: '#3B82F6' }}">{{ mb_substr($item->nama, 0, 1) }}</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->nama }}</span>
                    </div>
                </div>

                <!-- Deskripsi -->
                @if($item->deskripsi)
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Deskripsi</span>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 leading-normal">{{ $item->deskripsi }}</p>
                </div>
                @endif

                <!-- Slug -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Slug</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-xs font-mono text-gray-600 dark:text-gray-300">{{ $item->slug }}</span>
                    </div>
                </div>

                <!-- Urutan -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Urutan</span>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 font-medium">{{ $item->urutan }}</p>
                </div>

                <!-- Status -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-2.5">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Status</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <!-- Aksi -->
                <div class="border-t border-gray-50 dark:border-gray-700/50 pt-3">
                    <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Aksi</span>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="openEditKategoriModal({{ $item->id_forum_category }})" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-blue-600 bg-gray-50 hover:bg-blue-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form method="POST" action="{{ route('admin.forum-kategori.toggle', $item->id_forum_category) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-amber-600 bg-gray-50 hover:bg-amber-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            </button>
                        </form>
                        <button onclick="confirmDeleteKategori({{ $item->id_forum_category }}, '{{ addslashes($item->nama) }}')" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-red-600 bg-gray-50 hover:bg-red-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 rounded-xl border border-gray-200 dark:border-gray-700/50 transition shadow-sm" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center bg-white dark:bg-gray-800">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h10v10H7V7zm-4 4h4m10 0h4"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada kategori forum</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Kategori" untuk menambahkan kategori diskusi.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination Footer -->
        @if($kategoriPaginated->total() > 0)
        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700/50 flex flex-col gap-3 items-center md:flex-row md:justify-between">
            <p class="text-xs text-gray-500 dark:text-gray-400 text-center md:text-left">
                Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $kategoriPaginated->firstItem() ?? 0 }}-{{ $kategoriPaginated->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-300">{{ $kategoriPaginated->total() }}</span> kategori
            </p>
            <div class="flex items-center gap-1 justify-center flex-wrap">
                @if($kategoriPaginated->onFirstPage())<button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                @else<a href="{{ $kategoriPaginated->previousPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>@endif
                @for($i = 1; $i <= $kategoriPaginated->lastPage(); $i++)
                    @if($i <= 5 || $i === $kategoriPaginated->lastPage())
                        <a href="{{ $kategoriPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-medium rounded-lg transition {{ $i === $kategoriPaginated->currentPage() ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/25' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">{{ $i }}</a>
                    @elseif($i === 6)<span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor
                @if($kategoriPaginated->hasMorePages())<a href="{{ $kategoriPaginated->nextPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                @else<button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>@endif
            </div>
        </div>
        @endif
    </div>

    <div id="addKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddKategoriModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddKategoriModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <form action="{{ route('admin.forum-kategori.store') }}" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Kategori Forum</h3>
                        <p class="text-sm text-blue-500">Buat kategori diskusi umum untuk mahasiswa.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                            <input type="text" name="nama" required placeholder="Contoh: Tanya Jawab Skripsi" value="{{ old('_modal') === 'add' ? old('nama') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi</label>
                            <textarea name="deskripsi" rows="2" maxlength="500" placeholder="Deskripsi singkat kategori" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none">{{ old('_modal') === 'add' ? old('deskripsi') : '' }}</textarea>
                            @error('deskripsi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Warna</label>
                                <input type="color" name="warna" value="{{ old('_modal') === 'add' ? old('warna', '#3B82F6') : '#3B82F6' }}" class="w-full h-10 px-1 py-1 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer">
                                @error('warna')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Urutan</label>
                                <input type="number" name="urutan" min="0" max="9999" value="{{ old('_modal') === 'add' ? old('urutan', $nextUrutan) : $nextUrutan }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                            <select name="is_active" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="1" {{ old('_modal') === 'add' ? (old('is_active', '1') == '1' ? 'selected' : '') : 'selected' }}>Aktif</option>
                                <option value="0" {{ old('_modal') === 'add' && old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddKategoriModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" :disabled="isLoading">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm disabled:opacity-60" :disabled="isLoading">
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditKategoriModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditKategoriModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <form id="editKategoriForm" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_kategori_id" value="{{ old('_modal') === 'edit' ? old('_kategori_id') : '' }}">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Kategori Forum</h3>
                        <p class="text-sm text-blue-500">Perbarui kategori diskusi.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                            <input id="editNama" name="nama" type="text" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            @if(old('_modal') === 'edit')@error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror @endif
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi</label>
                            <textarea id="editDeskripsi" name="deskripsi" rows="2" maxlength="500" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Warna</label>
                                <input id="editWarna" name="warna" type="color" class="w-full h-10 px-1 py-1 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Urutan</label>
                                <input id="editUrutan" name="urutan" type="number" min="0" max="9999" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                            <select id="editStatus" name="is_active" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditKategoriModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" :disabled="isLoading">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm disabled:opacity-60" :disabled="isLoading">
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openAddKategoriModal() {
            document.getElementById('addKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeAddKategoriModal() {
            document.getElementById('addKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        function populateEditKategori(data) {
            document.getElementById('editKategoriForm').action = '/admin/forum/kategori/' + data.id_forum_category;
            document.querySelector('#editKategoriForm input[name="_kategori_id"]').value = data.id_forum_category || '';
            document.getElementById('editNama').value = data.nama || '';
            document.getElementById('editDeskripsi').value = data.deskripsi || '';
            document.getElementById('editWarna').value = data.warna || '#3B82F6';
            document.getElementById('editUrutan').value = data.urutan ?? 0;
            document.getElementById('editStatus').value = data.is_active ? '1' : '0';
        }
        function openEditKategoriModal(id) {
            fetch('/admin/forum/kategori/' + id)
                .then(r => r.json())
                .then(data => {
                    populateEditKategori(data);
                    document.getElementById('editKategoriModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(err => {
                    console.error(err);
                    if (window.Swal) Swal.fire('Error', 'Gagal memuat data kategori.', 'error');
                });
        }
        function closeEditKategoriModal() {
            document.getElementById('editKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        function confirmDeleteKategori(id, nama) {
            const proceed = window.Swal ? window.Swal.fire({
                title: 'Hapus Kategori?',
                html: '<p class="text-gray-500">Kategori <strong>' + nama + '</strong> akan dihapus.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then(r => r.isConfirmed) : Promise.resolve(window.confirm('Hapus ' + nama + '?'));
            proceed.then(ok => {
                if (!ok) return;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/forum/kategori/' + id;
                const csrf = document.createElement('input'); csrf.type='hidden'; csrf.name='_token'; csrf.value='{{ csrf_token() }}'; form.appendChild(csrf);
                const m = document.createElement('input'); m.type='hidden'; m.name='_method'; m.value='DELETE'; form.appendChild(m);
                document.body.appendChild(form);
                form.submit();
            });
        }

        @if($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => openAddKategoriModal());
        @endif
        @if($errors->any() && old('_modal') === 'edit' && old('_kategori_id'))
        document.addEventListener('DOMContentLoaded', () => {
            populateEditKategori({
                id_forum_category: @json(old('_kategori_id')),
                nama: @json(old('nama')),
                deskripsi: @json(old('deskripsi')),
                warna: @json(old('warna', '#3B82F6')),
                urutan: @json(old('urutan', 0)),
                is_active: @json((bool) old('is_active', true)),
            });
            document.getElementById('editKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>

<x-layouts.admin title="Kelola Kategori" active="kategori">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kategori</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Kelola data kategori untuk modul kursus dan pengumuman.</p>
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
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalKursus }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Tipe Kursus</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPengumuman }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Tipe Pengumuman</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.kategori') }}" class="admin-toolbar-responsive bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
        <div class="space-y-2 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:gap-3">
            <div class="admin-toolbar-actions flex flex-wrap gap-1.5">
                <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-medium rounded-xl transition">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kategori
                </button>
            </div>

            <div class="admin-toolbar-filters grid grid-cols-2 gap-1.5">
                <div class="relative">
                    <select name="tipe" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                        <option value="all" {{ $tipeFilter === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                        <option value="kursus" {{ $tipeFilter === 'kursus' ? 'selected' : '' }}>Kursus</option>
                        <option value="pengumuman" {{ $tipeFilter === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" class="w-full appearance-none px-3 py-1.5 pr-8 sm:px-4 sm:py-2 sm:pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $statusFilter === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </div>
            </div>

            <div class="admin-toolbar-search flex gap-1.5 flex-1">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kategori..." class="w-full px-3 py-1.5 pl-9 sm:px-4 sm:py-2 sm:pl-9 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-xs sm:text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400">
                    <svg class="w-4 h-4 absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 text-xs sm:text-sm font-medium rounded-xl border border-gray-200 dark:border-gray-600 transition flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Cari
                </button>
            </div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto responsive-table">
            <table class="w-full responsive-data-table admin-desktop-table admin-mobile-list">
                <thead>
                    <tr class="bg-gray-50/80 dark:bg-gray-700/30">
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Kategori</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($kategoriPaginated as $item)
                    <tr class="hover:bg-blue-50/40 dark:hover:bg-gray-700/30 transition">
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-xs font-mono font-semibold text-blue-600 dark:text-blue-400">{{ $item->kode_kategori }}</span>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->nama_kategori }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item->tipe === 'kursus' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                {{ ucfirst($item->tipe) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full {{ $item->status === 'aktif' ? 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <div class="inline-flex items-center gap-1 bg-gray-50 dark:bg-gray-700/30 rounded-lg p-0.5">
                                <button onclick="openEditModal({{ $item->id_category }})" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button onclick="confirmDelete({{ $item->id_category }}, '{{ addslashes($item->nama_kategori) }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white dark:hover:bg-gray-600 rounded-md transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h10M7 12h10M7 17h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada data kategori</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik "Tambah Kategori" untuk menambahkan kategori baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoriPaginated->total() > 0)
        <div class="px-6 py-3.5 bg-gray-50/50 dark:bg-gray-700/20 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between admin-responsive-pagination">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Menampilkan <span class="font-medium text-gray-700 dark:text-gray-300">{{ $kategoriPaginated->firstItem() ?? 0 }}-{{ $kategoriPaginated->lastItem() ?? 0 }}</span> dari <span class="font-medium text-gray-700 dark:text-gray-300">{{ $kategoriPaginated->total() }}</span> kategori
            </p>
            <div class="flex items-center gap-1 admin-responsive-actions">
                @if($kategoriPaginated->onFirstPage())
                <button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                @else
                <a href="{{ $kategoriPaginated->previousPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </a>
                @endif

                @for($i = 1; $i <= $kategoriPaginated->lastPage(); $i++)
                    @if($i <= 5 || $i === $kategoriPaginated->lastPage())
                    <a href="{{ $kategoriPaginated->url($i) }}" class="w-8 h-8 flex items-center justify-center text-xs font-medium rounded-lg transition {{ $i === $kategoriPaginated->currentPage() ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/25' : 'text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700' }}">
                        {{ $i }}
                    </a>
                    @elseif($i === 6)
                    <span class="w-8 h-8 flex items-center justify-center text-xs text-gray-400">...</span>
                    @endif
                @endfor

                @if($kategoriPaginated->hasMorePages())
                <a href="{{ $kategoriPaginated->nextPageUrl() }}" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                @else
                <button class="p-1.5 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div id="addKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <form action="{{ route('admin.kategori.store') }}" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">

                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tambah Kategori</h3>
                        <p class="text-sm text-blue-500">Isi informasi kategori baru.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kategori <span class="text-red-400">*</span></label>
                                <input type="text" name="kode_kategori" required placeholder="KAT-001" value="{{ old('_modal') === 'add' ? old('kode_kategori') : $nextCategoryCode }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                @error('kode_kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                                <select name="tipe" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                    <option value="">Pilih Tipe</option>
                                    <option value="kursus" {{ old('_modal') === 'add' && old('tipe') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                    <option value="pengumuman" {{ old('_modal') === 'add' && old('tipe') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                </select>
                                @error('tipe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                            <input type="text" name="nama_kategori" required placeholder="Contoh: Webinar Premium" value="{{ old('_modal') === 'add' ? old('nama_kategori') : '' }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            @error('nama_kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status <span class="text-red-400">*</span></label>
                            <select name="status" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="aktif" {{ old('_modal') === 'add' ? (old('status', 'aktif') === 'aktif' ? 'selected' : '') : 'selected' }}>Aktif</option>
                                <option value="nonaktif" {{ old('_modal') === 'add' && old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="admin-responsive-modal-actions flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" :disabled="isLoading">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm disabled:opacity-60" :disabled="isLoading">
                            <span x-text="isLoading ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editKategoriModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <form id="editKategoriForm" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_kategori_id" value="{{ old('_modal') === 'edit' ? old('_kategori_id') : '' }}">

                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Kategori</h3>
                        <p class="text-sm text-blue-500">Perbarui informasi kategori.</p>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kategori <span class="text-red-400">*</span></label>
                                <input id="editKodeKategori" name="kode_kategori" type="text" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                @if(old('_modal') === 'edit')
                                @error('kode_kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                                <select id="editTipeKategori" name="tipe" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                    <option value="kursus">Kursus</option>
                                    <option value="pengumuman">Pengumuman</option>
                                </select>
                                @if(old('_modal') === 'edit')
                                @error('tipe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Nama Kategori <span class="text-red-400">*</span></label>
                            <input id="editNamaKategori" name="nama_kategori" type="text" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                            @if(old('_modal') === 'edit')
                            @error('nama_kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Status <span class="text-red-400">*</span></label>
                            <select id="editStatusKategori" name="status" required class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @if(old('_modal') === 'edit')
                            @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            @endif
                        </div>
                    </div>

                    <div class="admin-responsive-modal-actions flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm" :disabled="isLoading">Batal</button>
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
        function openAddModal() {
            document.getElementById('addKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddModal() {
            document.getElementById('addKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function populateEditForm(data) {
            document.getElementById('editKategoriForm').action = '/admin/kategori/' + data.id_category;
            document.querySelector('#editKategoriForm input[name="_kategori_id"]').value = data.id_category || '';
            document.getElementById('editKodeKategori').value = data.kode_kategori || '';
            document.getElementById('editNamaKategori').value = data.nama_kategori || '';
            document.getElementById('editTipeKategori').value = data.tipe || 'kursus';
            document.getElementById('editStatusKategori').value = data.status || 'aktif';
        }

        function openEditModal(id) {
            fetch('/admin/kategori/' + id)
                .then(response => response.json())
                .then(data => {
                    populateEditForm(data);
                    document.getElementById('editKategoriModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data kategori.', 'error');
                });
        }

        function closeEditModal() {
            document.getElementById('editKategoriModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Kategori?',
                html: '<p class="text-gray-500">Kategori <strong>' + nama + '</strong> akan dihapus.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/kategori/' + id;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            });
        }

        @if($errors->any() && old('_modal') === 'add')
        document.addEventListener('DOMContentLoaded', () => openAddModal());
        @endif

        @if($errors->any() && old('_modal') === 'edit' && old('_kategori_id'))
        document.addEventListener('DOMContentLoaded', () => {
            populateEditForm({
                id_category: @json(old('_kategori_id')),
                kode_kategori: @json(old('kode_kategori')),
                nama_kategori: @json(old('nama_kategori')),
                tipe: @json(old('tipe')),
                status: @json(old('status')),
            });
            document.getElementById('editKategoriModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
        @endif
    </script>
    @endpush
</x-layouts.admin>

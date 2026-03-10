<x-layouts.admin title="Kelola Pengumuman" active="pengumuman">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Pengumuman</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola pengumuman, berita, dan event untuk mahasiswa.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.pengumuman') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        <div class="admin-responsive-toolbar flex flex-wrap items-center gap-3">
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-blue-500/25 hover:shadow-md hover:shadow-blue-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengumuman
            </button>

            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            {{-- Kategori Filter --}}
            <div class="relative">
                <select name="kategori" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($kategoriFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="pengumuman" {{ ($kategoriFilter ?? '') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                    <option value="berita" {{ ($kategoriFilter ?? '') === 'berita' ? 'selected' : '' }}>Berita</option>
                    <option value="event" {{ ($kategoriFilter ?? '') === 'event' ? 'selected' : '' }}>Event</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Status Filter --}}
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            {{-- Search --}}
            <div class="w-full sm:flex-1 sm:min-w-[200px]">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari pengumuman..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            {{-- Stats --}}
            <div class="hidden lg:flex items-center gap-2 text-xs text-gray-400">
                <span class="font-medium">Total: {{ $totalNews }}</span>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100/50 dark:from-gray-700/50 dark:to-gray-700/30">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Publish</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                    @forelse($newsList as $index => $news)
                    <tr class="hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $newsList->firstItem() + $index }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($news->thumbnail)
                                <img src="{{ asset('storage/' . $news->thumbnail) }}" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 dark:text-white truncate max-w-[300px]">{{ $news->judul }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[300px]">{{ Str::limit(strip_tags($news->konten), 60) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $kategoriColors = [
                                    'pengumuman' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                    'berita' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                    'event' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold {{ $kategoriColors[$news->kategori] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($news->kategori) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-600 dark:text-gray-300">{{ $news->tanggal_publish->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($news->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button" onclick="openEditModal({{ $news->id_news }})"
                                    class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-500/10 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="openDeleteModal({{ $news->id_news }}, '{{ addslashes($news->judul) }}')"
                                    class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                            <p class="text-gray-400 dark:text-gray-500 font-medium">Belum ada pengumuman</p>
                            <p class="text-gray-300 dark:text-gray-600 text-sm mt-1">Klik "Tambah Pengumuman" untuk membuat yang baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($newsList->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 admin-responsive-pagination">
            {{ $newsList->links() }}
        </div>
        @endif
    </div>

    {{-- ======================== --}}
    {{-- ADD MODAL --}}
    {{-- ======================== --}}
    <div id="addModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
                {{-- Header --}}
                <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 rounded-t-2xl z-10">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Tambah Pengumuman</h2>
                        <button type="button" onclick="closeAddModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <input type="hidden" name="_modal" value="add">
                    <div class="px-6 py-5 space-y-5">
                        {{-- Judul --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" value="{{ old('_modal') === 'add' ? old('judul') : '' }}" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Judul pengumuman">
                        </div>

                        {{-- Konten --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konten <span class="text-red-500">*</span></label>
                            <textarea name="konten" rows="6" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y"
                                placeholder="Tulis isi pengumuman...">{{ old('_modal') === 'add' ? old('konten') : '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                <select name="kategori" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="pengumuman" {{ old('_modal') === 'add' && old('kategori') === 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                                    <option value="berita" {{ old('_modal') === 'add' && old('kategori') === 'berita' ? 'selected' : '' }}>Berita</option>
                                    <option value="event" {{ old('_modal') === 'add' && old('kategori') === 'event' ? 'selected' : '' }}>Event</option>
                                </select>
                            </div>

                            {{-- Tanggal Publish --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Publish <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="tanggal_publish" required
                                    value="{{ old('_modal') === 'add' ? old('tanggal_publish') : now()->format('Y-m-d\TH:i') }}"
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        {{-- Thumbnail --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Thumbnail</label>
                            <input type="file" name="thumbnail" accept="image/*"
                                class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400 file:font-medium file:cursor-pointer hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 transition">
                            <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP. Maks 2MB.</p>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('_modal') === 'add' ? (old('is_active') ? 'checked' : '') : 'checked' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-gray-500 peer-checked:bg-blue-600"></div>
                            </label>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif (tampilkan ke pengguna)</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 rounded-b-2xl admin-responsive-modal-actions flex justify-end gap-3">
                        <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-500 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl shadow-sm shadow-blue-500/25 transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- EDIT MODAL --}}
    {{-- ======================== --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto relative">
                {{-- Header --}}
                <div class="sticky top-0 bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 rounded-t-2xl z-10">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white">Edit Pengumuman</h2>
                        <button type="button" onclick="closeEditModal()" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form id="editForm" method="POST" enctype="multipart/form-data" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal" value="edit">
                    <input type="hidden" name="_id" id="edit_news_id">
                    <div class="px-6 py-5 space-y-5">
                        {{-- Judul --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="edit_judul" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Judul pengumuman">
                        </div>

                        {{-- Konten --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konten <span class="text-red-500">*</span></label>
                            <textarea name="konten" id="edit_konten" rows="6" required
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y"
                                placeholder="Tulis isi pengumuman..."></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                                <select name="kategori" id="edit_kategori" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="pengumuman">Pengumuman</option>
                                    <option value="berita">Berita</option>
                                    <option value="event">Event</option>
                                </select>
                            </div>

                            {{-- Tanggal Publish --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Publish <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="tanggal_publish" id="edit_tanggal_publish" required
                                    class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>

                        {{-- Current Thumbnail Preview --}}
                        <div id="edit_thumbnail_preview" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Thumbnail Saat Ini</label>
                            <img id="edit_thumbnail_img" src="" alt="" class="w-20 h-20 rounded-lg object-cover border border-gray-200 dark:border-gray-600">
                        </div>

                        {{-- Thumbnail --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ganti Thumbnail</label>
                            <input type="file" name="thumbnail" accept="image/*"
                                class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-500/10 dark:file:text-blue-400 file:font-medium file:cursor-pointer hover:file:bg-blue-100 dark:hover:file:bg-blue-500/20 transition">
                            <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak ingin mengganti. JPG, PNG, WebP. Maks 2MB.</p>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-center gap-3">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-gray-500 peer-checked:bg-blue-600"></div>
                            </label>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktif (tampilkan ke pengguna)</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="sticky bottom-0 bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-100 dark:border-gray-700/50 rounded-b-2xl admin-responsive-modal-actions flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-500 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl shadow-sm shadow-blue-500/25 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- DELETE MODAL --}}
    {{-- ======================== --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md">
                <div class="px-6 py-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-500/20 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Hapus Pengumuman</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin ingin menghapus pengumuman "<span id="delete_title" class="font-semibold text-gray-700 dark:text-gray-200"></span>"? Tindakan ini tidak dapat dibatalkan.</p>
                    <form id="deleteForm" method="POST" class="flex justify-center gap-3" x-data="{ isLoading: false }" @submit="isLoading = true">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-500 transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-xl shadow-sm transition">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Add Modal
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Edit Modal
    function openEditModal(id) {
        fetch(`{{ url('admin/pengumuman') }}/${id}`)
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat data');
                return res.json();
            })
            .then(data => {
                document.getElementById('editForm').action = `{{ url('admin/pengumuman') }}/${id}`;
                document.getElementById('edit_news_id').value = id;
                document.getElementById('edit_judul').value = data.judul;
                document.getElementById('edit_konten').value = data.konten;
                document.getElementById('edit_kategori').value = data.kategori;
                document.getElementById('edit_tanggal_publish').value = data.tanggal_publish;
                document.getElementById('edit_is_active').checked = data.is_active;

                // Thumbnail preview
                const previewDiv = document.getElementById('edit_thumbnail_preview');
                if (data.thumbnail) {
                    document.getElementById('edit_thumbnail_img').src = `{{ asset('storage') }}/${data.thumbnail}`;
                    previewDiv.classList.remove('hidden');
                } else {
                    previewDiv.classList.add('hidden');
                }

                document.getElementById('editModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            })
            .catch(err => {
                alert('Gagal memuat data pengumuman: ' + err.message);
            });
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Delete Modal
    function openDeleteModal(id, title) {
        document.getElementById('deleteForm').action = `{{ url('admin/pengumuman') }}/${id}`;
        document.getElementById('delete_title').textContent = title;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Auto-reopen modal on validation error
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            const oldModal = '{{ old('_modal') }}';
            if (oldModal === 'add') {
                openAddModal();
            } else if (oldModal === 'edit') {
                const oldId = '{{ old('_id') }}';
                if (oldId) {
                    // Re-populate from old values
                    document.getElementById('editForm').action = `{{ url('admin/pengumuman') }}/${oldId}`;
                    document.getElementById('edit_news_id').value = oldId;
                    document.getElementById('edit_judul').value = '{{ old('judul') }}';
                    document.getElementById('edit_konten').value = `{{ old('konten') }}`;
                    document.getElementById('edit_kategori').value = '{{ old('kategori') }}';
                    document.getElementById('edit_tanggal_publish').value = '{{ old('tanggal_publish') }}';
                    document.getElementById('edit_is_active').checked = {{ old('is_active') ? 'true' : 'false' }};
                    document.getElementById('editModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }
        @endif
    });
</script>
@endpush

</x-layouts.admin>

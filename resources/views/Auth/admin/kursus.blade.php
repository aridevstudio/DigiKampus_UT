<x-layouts.admin title="Kelola Kursus" active="kursus">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kursus</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data kursus, tambah kursus baru, dan atur status publikasi.</p>
    </div>

    {{-- Actions Bar --}}
    <form method="GET" action="{{ route('admin.kursus') }}" class="flex flex-wrap items-center gap-4 mb-6">
        <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Kursus
        </button>
        
        {{-- Status Filter --}}
        <div class="relative">
            <select name="status" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="draft" {{ ($statusFilter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        
        {{-- Tipe Filter --}}
        <div class="relative">
            <select name="tipe" onchange="this.form.submit()" class="appearance-none px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all" {{ ($tipeFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                <option value="gratis" {{ ($tipeFilter ?? '') === 'gratis' ? 'selected' : '' }}>Gratis</option>
                <option value="berbayar" {{ ($tipeFilter ?? '') === 'berbayar' ? 'selected' : '' }}>Berbayar</option>
            </select>
            <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        
        {{-- Search --}}
        <div class="relative flex-1 max-w-xs">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari Kursus..." class="w-full px-4 py-2.5 pl-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
            Cari
        </button>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Thumbnail</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Nama Kursus</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Kode</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Dosen</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Harga</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="text-left px-6 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($kursusList as $kursus)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            @if($kursus['thumbnail'])
                                <img src="{{ asset('storage/' . $kursus['thumbnail']) }}" alt="{{ $kursus['nama'] }}" class="w-16 h-10 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-900 dark:text-white font-medium">{{ $kursus['nama'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $kursus['kode'] }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $kursus['dosen'] }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $kursus['tipe'] === 'gratis' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                {{ ucfirst($kursus['tipe']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-300">
                            @if($kursus['tipe'] === 'berbayar')
                                Rp {{ number_format($kursus['harga'], 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'aktif' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'draft' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'nonaktif' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full {{ $statusColors[$kursus['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($kursus['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="openEditModal({{ $kursus['id'] }})" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button onclick="confirmDelete({{ $kursus['id'] }})" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <p class="text-lg font-medium">Belum ada data kursus</p>
                                <p class="text-sm mt-1">Klik "Tambah Kursus" untuk menambahkan kursus baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($totalKursus > 0)
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ $kursusPaginated->firstItem() ?? 0 }}-{{ $kursusPaginated->lastItem() ?? 0 }} dari {{ $totalKursus }}
            </p>
            <div class="flex items-center gap-1">
                @if($kursusPaginated->onFirstPage())
                <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                @else
                <a href="{{ $kursusPaginated->previousPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                @endif
                
                @for($i = 1; $i <= $kursusPaginated->lastPage(); $i++)
                    @if($i <= 4 || $i === $kursusPaginated->lastPage())
                    <a href="{{ $kursusPaginated->url($i) }}" class="w-9 h-9 flex items-center justify-center text-sm font-medium rounded-lg transition {{ $i === $currentPage ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                        {{ $i }}
                    </a>
                    @endif
                @endfor
                
                @if($kursusPaginated->hasMorePages())
                <a href="{{ $kursusPaginated->nextPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                @else
                <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
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
                                <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
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
            if (tipe === 'berbayar') {
                hargaContainer.classList.remove('hidden');
            } else {
                hargaContainer.classList.add('hidden');
            }
        }
        
        // Edit Modal functions
        function openEditModal(id) {
            fetch('/admin/kursus/' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editKursusForm').action = '/admin/kursus/' + id;
                    document.getElementById('edit_nama_course').value = data.nama_course || '';
                    document.getElementById('edit_kode_course').value = data.kode_course || '';
                    document.getElementById('edit_deskripsi').value = data.deskripsi || '';
                    document.getElementById('edit_id_dosen').value = data.id_dosen || '';
                    document.getElementById('edit_id_jurusan').value = data.id_jurusan || '';
                    document.getElementById('edit_tipe').value = data.tipe || 'gratis';
                    document.getElementById('edit_harga').value = data.harga || 0;
                    document.getElementById('edit_status').value = data.status || 'draft';
                    
                    // Show/hide harga field
                    toggleHarga('edit');
                    
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
    </script>
    @endpush
</x-layouts.admin>

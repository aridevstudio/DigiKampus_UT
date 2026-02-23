<x-layouts.dosen title="Edit Kursus" active="kursus-saya">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Kursus</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi dan struktur materi kursus Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[3fr_2fr] gap-10 items-start w-full max-w-6xl mx-auto">
        {{-- Informasi Kursus --}}
        <div class="w-full">
            <div class="bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-xl shadow-sm p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Kursus</h2>
                </div>

                <form action="{{ route('dosen.kursus.update', $course->id_course) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Kursus</label>
                            <input type="text" name="nama_course" value="{{ old('nama_course', $course->nama_course) }}" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Kursus</label>
                            <input type="text" name="kode_course" value="{{ old('kode_course', $course->kode_course) }}" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Kursus</label>
                            <textarea name="deskripsi" rows="4" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('deskripsi', $course->deskripsi) }}</textarea>
                        </div>

                        {{-- Baris 1: Kategori & Tingkat Kesulitan --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                                <select name="id_jurusan" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusans ?? [] as $jurusan)
                                    <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan', $course->id_jurusan) == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('id_jurusan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat Kesulitan</label>
                                <select name="level" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Level</option>
                                    <option value="Pemula" {{ old('level', $course->level) == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                    <option value="Menengah" {{ old('level', $course->level) == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                    <option value="Mahir" {{ old('level', $course->level) == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                </select>
                                @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Baris 2: Estimasi Waktu Belajar & Tipe Harga (sejajar 2 kolom sesuai wireframe) --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimasi Waktu Belajar</label>
                                <div class="flex gap-2">
                                    <input type="number" name="estimasi_waktu" value="{{ old('estimasi_waktu', $course->estimasi_waktu) }}" min="0" placeholder="8" class="w-20 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <select name="durasi_satuan" class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="Minggu" {{ old('durasi_satuan', $course->durasi_satuan) == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                        <option value="Jam" {{ old('durasi_satuan', $course->durasi_satuan) == 'Jam' ? 'selected' : '' }}>Jam</option>
                                    </select>
                                </div>
                                @error('estimasi_waktu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                @error('durasi_satuan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Harga</label>
                                <select name="tipe" id="tipe" required onchange="toggleHarga()" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="gratis" {{ old('tipe', $course->tipe) === 'gratis' ? 'selected' : '' }}>Gratis</option>
                                    <option value="berbayar" {{ old('tipe', $course->tipe) === 'berbayar' ? 'selected' : '' }}>Berbayar</option>
                                </select>
                                @error('tipe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Kategori Kursus (Kursus/Webinar/Tiket) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kursus</label>
                            <select name="kategori" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="kursus" {{ old('kategori', $course->kategori) === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                <option value="webinar" {{ old('kategori', $course->kategori) === 'webinar' ? 'selected' : '' }}>Webinar</option>
                                <option value="tiket" {{ old('kategori', $course->kategori) === 'tiket' ? 'selected' : '' }}>Tiket</option>
                            </select>
                            @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Harga (condisional, muncul kalau Berbayar) --}}
                        <div id="hargaField" class="{{ old('tipe', $course->tipe) === 'berbayar' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga (Rp)</label>
                            <input type="number" name="harga" value="{{ old('harga', $course->harga) }}" min="0" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('harga')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thumbnail Kursus</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 flex items-center gap-4">
                                <div class="shrink-0 relative group">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail" class="w-32 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-32 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-1">Ganti Thumbnail</h4>
                                    <p class="text-xs text-gray-500 mb-3">PNG, JPG hingga 5MB. Rasio 16:9 direkomendasikan</p>
                                    <label class="inline-flex items-center px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 cursor-pointer transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                        Upload Gambar
                                        <input type="file" name="thumbnail" accept="image/*" class="hidden" onchange="previewImage(this)">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Kursus</label>
                             <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="aktif" {{ $course->status === 'aktif' ? 'checked' : '' }} class="form-radio text-green-500 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="draft" {{ $course->status === 'draft' ? 'checked' : '' }} class="form-radio text-yellow-500 focus:ring-yellow-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Draft</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="nonaktif" {{ $course->status === 'nonaktif' ? 'checked' : '' }} class="form-radio text-red-500 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                                </label>
                             </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-8 flex justify-center items-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-base transition shadow-lg shadow-blue-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Perubahan Informasi
                    </button>
                    
                    <div class="mt-4 text-center">
                         <a href="{{ route('dosen.kursus.preview', $course->id_course) }}" target="_blank" class="text-sm text-blue-500 hover:underline">Lihat Pratinjau Kursus &rarr;</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modul Utama --}}
        <div class="w-full">
            <div class="bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-xl shadow-sm p-8 flex flex-col min-h-[400px]">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Modul Utama</h2>
                <div id="modulesList" class="space-y-6 flex-1">
                @forelse($course->modules as $module)
                <div data-module-id="{{ $module->id_module }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    {{-- Module Header --}}
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 rounded-t-2xl group">
                        <div class="flex items-center gap-3">
                            <div class="cursor-move module-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-white dark:hover:bg-gray-600 transition">
                                <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $module->judul_module }}</h3>
                                @if($module->deskripsi)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $module->deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditModuleModal({{ $module->id_module }}, '{{ $module->judul_module }}', '{{ $module->deskripsi }}')" class="p-2 text-gray-400 hover:text-blue-500 transition">
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button onclick="confirmDeleteModule({{ $module->id_module }})" class="p-2 text-gray-400 hover:text-red-500 transition">
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Module Body (Materials) --}}
                    <div class="p-4">
                        <div id="materials-{{ $module->id_module }}" class="space-y-3 materials-list" data-module-id="{{ $module->id_module }}">
                            @if($module->materials->count() === 0)
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-4">
                                    <h4 class="font-semibold text-blue-700 dark:text-blue-300 mb-3 text-base">Tambah Konten Awal</h4>
                                    <form action="{{ route('dosen.material.store', $course->id_course) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id_module" value="{{ $module->id_module }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                                                <input type="text" name="judul_material" required class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                                <select name="tipe" required class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                                    <option value="video">Video</option>
                                                    <option value="bacaan">Bacaan</option>
                                                    <option value="kuis">Kuis</option>
                                                    <option value="tugas">Tugas</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                                            <textarea name="konten" rows="2" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 mb-3">
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                                                <input type="url" name="video_url" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                                                <input type="number" name="durasi" min="0" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                        <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center justify-center gap-2 transition shadow shadow-blue-500/20">Simpan Konten Awal</button>
                                    </form>
                                </div>
                            @endif
                            @foreach($module->materials as $material)
                            <div data-material-id="{{ $material->id_material }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 transition group/material">
                                {{-- Material Handle --}}
                                <div class="cursor-move material-handle text-gray-400 hover:text-gray-600 p-1">
                                    <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                                </div>

                                {{-- Icon --}}
                                <div class="shrink-0">
                                    @php
                                        $colors = [
                                            'video' => 'bg-red-100 text-red-500', 
                                            'bacaan' => 'bg-blue-100 text-blue-500', 
                                            'kuis' => 'bg-green-100 text-green-500', 
                                            'tugas' => 'bg-purple-100 text-purple-500'
                                        ];
                                        $icons = [
                                            'video' => '<path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />',
                                            'bacaan' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />',
                                            'kuis' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />',
                                            'tugas' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />'
                                        ];
                                    @endphp
                                    <div class="w-8 h-8 rounded-lg {{ $colors[$material->tipe] }} flex items-center justify-center">
                                        <svg width="16" height="16" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">{!! $icons[$material->tipe] !!}</svg>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $material->judul_material }}</h4>
                                    <div class="text-xs text-gray-500 flex gap-2">
                                        <span class="capitalize">{{ $material->tipe }}</span>
                                        @if($material->durasi) &bull; {{ $material->durasi }} menit @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center opacity-0 group-hover/material:opacity-100 transition-opacity">
                                    <button onclick="openEditMaterialModal({{ $material->id_material }})" class="p-1.5 text-gray-400 hover:text-blue-500">
                                        <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button onclick="confirmDeleteMaterial({{ $material->id_material }})" class="p-1.5 text-gray-400 hover:text-red-500">
                                        <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button onclick="openAddMaterialModal({{ $module->id_module }})" class="mt-4 w-full flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400 font-medium px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 dark:hover:text-blue-400 bg-white dark:bg-gray-800 transition">
                            <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Konten
                        </button>
                    </div>
                </div>
                @empty
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                         <svg width="24" height="24" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-medium">Belum ada modul</h3>
                    <p class="text-gray-500 text-sm mt-1">Buat modul pertama untuk mulai menyusun materi.</p>
                </div>
                @endforelse
            </div>

            <button onclick="openAddModuleModal()" class="w-full mt-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 text-base transition shadow-lg shadow-blue-500/30">
                <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul Baru
            </button>
        </div>
    </div>

    {{-- Modals --}}

    {{-- Add Module Modal --}}
    <div id="addModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModuleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form action="{{ route('dosen.module.store', $course->id_course) }}" method="POST" class="p-6">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Modul Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_module" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Modul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Module Modal --}}
    <div id="editModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditModuleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="editModuleForm" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Modul</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_module" id="edit_module_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_module_deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Module Modal --}}
    <div id="deleteModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                 <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg width="32" height="32" class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Modul?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Semua materi dalam modul ini juga akan dihapus. Data tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteModuleForm" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus Modul</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Material Modal --}}
    <div id="addMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddMaterialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form action="{{ route('dosen.material.store', $course->id_course) }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="id_module" id="add_material_module_id">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Materi Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                            <input type="text" name="judul_material" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" name="video_url" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Material Modal --}}
    <div id="editMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
             <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditMaterialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="editMaterialForm" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Materi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                            <input type="text" name="judul_material" id="edit_material_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="edit_material_tipe" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" id="edit_material_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" name="video_url" id="edit_material_video_url" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" id="edit_material_durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Material Modal --}}
    <div id="deleteMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                 <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg width="32" height="32" class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Materi?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Materi akan dihapus permanen.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteMaterialForm" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div id="validationAlert" class="fixed top-4 right-4 z-[60] max-w-md bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-sm mb-1">Data gagal disimpan:</p>
                <ul class="text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.closest('#validationAlert').remove()" class="ml-2 shrink-0">&times;</button>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('validationAlert')?.remove(), 8000);</script>
    @endif

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div id="successAlert" class="fixed top-4 right-4 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('successAlert')?.remove(), 5000);</script>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="fixed top-4 right-4 z-[60] bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);</script>
    @endif

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const courseId = {{ $course->id_course }};

        // Toggle Harga Field
        function toggleHarga() {
            const tipe = document.getElementById('tipe').value;
            document.getElementById('hargaField').classList.toggle('hidden', tipe !== 'berbayar');
        }

        // Initialize Sortable for Modules
        var modulesList = document.getElementById('modulesList');
        if(modulesList) {
            Sortable.create(modulesList, {
                handle: '.module-handle',
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: function (evt) {
                    var order = [];
                    document.querySelectorAll('#modulesList > div[data-module-id]').forEach(function(item) {
                        order.push(item.getAttribute('data-module-id'));
                    });
                    
                    fetch(`{{ route('dosen.module.reorder', $course->id_course) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    });
                },
            });
        }

        // Initialize Sortable for Materials (within each module)
        document.querySelectorAll('.materials-list').forEach(function(list) {
            Sortable.create(list, {
                group: 'materials', // Allow dragging between modules if needed (optional)
                handle: '.material-handle',
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: function (evt) {
                    var moduleId = evt.to.getAttribute('data-module-id');
                    var order = [];
                    evt.to.querySelectorAll('[data-material-id]').forEach(function(item) {
                        order.push(item.getAttribute('data-material-id'));
                    });

                    // TODO: Handle move between modules if 'group' is enabled.
                    // For now assuming reorder within module. 
                    // If we support moving between modules, we need an endpoint that accepts module_id too.
                    // Using existing reorder endpoint which just updates urutan.
                    
                    fetch(`{{ route('dosen.material.reorder', $course->id_course) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order }) // Should probably send module_id too?
                    });
                },
            });
        });

        // Module Modal Functions
        function openAddModuleModal() {
            document.getElementById('addModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeAddModuleModal() {
            document.getElementById('addModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModuleModal(id, judul, deskripsi) {
            document.getElementById('editModuleForm').action = `/dosen/kursus/${courseId}/module/${id}`;
            document.getElementById('edit_module_judul').value = judul;
            document.getElementById('edit_module_deskripsi').value = deskripsi || '';
            document.getElementById('editModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModuleModal() {
            document.getElementById('editModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDeleteModule(id) {
            document.getElementById('deleteModuleForm').action = `/dosen/kursus/${courseId}/module/${id}`;
            document.getElementById('deleteModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteModuleModal() {
            document.getElementById('deleteModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Material Modal Functions
        function openAddMaterialModal(moduleId) {
            document.getElementById('add_material_module_id').value = moduleId;
            document.getElementById('addMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeAddMaterialModal() {
            document.getElementById('addMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditMaterialModal(id) {
            fetch(`/dosen/kursus/${courseId}/material/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editMaterialForm').action = `/dosen/kursus/${courseId}/material/${id}`;
                    document.getElementById('edit_material_judul').value = data.judul_material || '';
                    document.getElementById('edit_material_tipe').value = data.tipe || 'video';
                    document.getElementById('edit_material_konten').value = data.konten || '';
                    document.getElementById('edit_material_video_url').value = data.video_url || '';
                    document.getElementById('edit_material_durasi').value = data.durasi || '';
                    document.getElementById('editMaterialModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
        }
        function closeEditMaterialModal() {
            document.getElementById('editMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDeleteMaterial(id) {
            document.getElementById('deleteMaterialForm').action = `/dosen/kursus/${courseId}/material/${id}`;
            document.getElementById('deleteMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteMaterialModal() {
            document.getElementById('deleteMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = document.querySelector('img[alt="Thumbnail"]');
                    var placeholder = document.querySelector('.border-dashed .bg-gray-100'); // Selector for placeholder div

                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        if (placeholder) placeholder.classList.add('hidden'); // Hide placeholder if it exists separate from img
                    } else {
                        // If no image tag exists yet (only placeholder), we might need to swap them or just assume the layout
                        var previewContainer = document.querySelector('.shrink-0.relative.group');
                        if(previewContainer) {
                             previewContainer.innerHTML = `<img src="${e.target.result}" alt="Thumbnail" class="w-32 h-20 object-cover rounded-lg">`;
                        }
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-layouts.dosen>

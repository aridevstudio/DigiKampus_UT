<x-layouts.dosen title="Buat Kursus Baru" active="buat-kursus">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data" id="buatKursusForm" x-data="{ isLoading: false, selectedKategori: '{{ old('kategori', 'kursus') }}' }" @submit="isLoading = true">
            @csrf
            
            {{-- Page Header + Action Buttons --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6 mb-6">
                <div class="mb-4">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                        <span x-show="selectedKategori !== 'webinar'">Buat Kursus Baru</span>
                        <span x-show="selectedKategori === 'webinar'" x-cloak>Buat Webinar Baru</span>
                    </h1>
                    <p class="text-sm text-blue-500">
                        <span x-show="selectedKategori !== 'webinar'">Lengkapi informasi berikut untuk membuat kursus baru.</span>
                        <span x-show="selectedKategori === 'webinar'" x-cloak>Lengkapi informasi berikut untuk mengajukan webinar baru.</span>
                    </p>
                </div>
                
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('dosen.kursus') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" name="status_btn" value="draft" class="px-4 py-2 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 hover:bg-blue-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                        Simpan Draft
                    </button>
                    <button type="submit" name="status_btn" value="aktif" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-blue-500/25">
                        <span x-show="selectedKategori !== 'webinar'">Buat Kursus</span>
                        <span x-show="selectedKategori === 'webinar'" x-cloak>Ajukan Webinar</span>
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6">
                <div class="space-y-4">
                    {{-- 1. Informasi Dasar Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center justify-center">1</span>
                            Informasi Dasar Kursus
                        </h4>
                        
                        <div class="space-y-4">
                            {{-- Judul Kursus (full width) --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Judul Kursus <span class="text-red-400">*</span></label>
                                <input type="text" name="nama_course" id="add_nama_course" required placeholder="Masukkan judul kursus" value="{{ old('nama_course') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Kode Kursus --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus <span class="text-red-400">*</span></label>
                                <input type="text" name="kode_course" id="add_kode_course" required placeholder="Contoh: EKMA4116" value="{{ old('kode_course', 'C-' . strtoupper(substr(md5((string) now()->timestamp), 0, 6))) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-400 mt-1">Gunakan kode unik untuk kursus ini.</p>
                                @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Deskripsi Kursus --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Kursus</label>
                                <textarea name="deskripsi" id="add_deskripsi" rows="3" placeholder="Jelaskan tentang kursus ini..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Persyaratan Kursus --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Persyaratan Kursus (Opsional)</label>
                                <textarea name="persyaratan" id="add_persyaratan" rows="3" placeholder="Contoh:&#10;STIN4101 - Pengantar Teknologi Informasi&#10;Memiliki laptop dan koneksi internet stabil" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('persyaratan') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Tulis satu persyaratan per baris.</p>
                                @error('persyaratan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jurusan/Prodi & Tingkat Kesulitan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jurusan/Prodi</label>
                                    <div class="relative">
                                        <select name="id_jurusan" id="add_id_jurusan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Jurusan</option>
                                            @foreach($jurusans ?? [] as $jurusan)
                                            <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tingkat Kesulitan</label>
                                    <div class="relative">
                                        <select name="level" id="add_level" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Tingkat</option>
                                            <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                            <option value="Menengah" {{ old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                            <option value="Mahir" {{ old('level') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Estimasi Waktu --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Estimasi Waktu Belajar</label>
                                    <input type="number" name="estimasi_waktu" id="add_estimasi_waktu" min="0" placeholder="20" value="{{ old('estimasi_waktu', 20) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('estimasi_waktu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Satuan Durasi</label>
                                    <div class="relative">
                                        <select name="durasi_satuan" id="add_durasi_satuan" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="Jam" {{ old('durasi_satuan', 'Jam') === 'Jam' ? 'selected' : '' }}>Jam</option>
                                            <option value="Minggu" {{ old('durasi_satuan') === 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    @error('durasi_satuan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Playlist YouTube / Link Meeting --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    <span x-show="selectedKategori !== 'webinar'">Link Playlist YouTube (Opsional)</span>
                                    <span x-show="selectedKategori === 'webinar'" x-cloak>Link Meeting (Zoom/Google Meet) <span class="text-red-400">*</span></span>
                                </label>
                                <input type="url" name="youtube_playlist" id="add_youtube_playlist" :placeholder="selectedKategori === 'webinar' ? 'https://zoom.us/j/... atau https://meet.google.com/...' : 'https://www.youtube.com/playlist?list=...'" value="{{ old('youtube_playlist') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('youtube_playlist')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                                            <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewThumbnail(this)">
                                        </label>
                                        <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Jadwal Pelaksanaan (Webinar Only) --}}
                    <div x-show="selectedKategori === 'webinar'" x-transition x-cloak class="border border-indigo-200 dark:border-indigo-700/50 rounded-xl p-5 bg-indigo-50/30 dark:bg-indigo-900/10">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">2</span>
                            Jadwal Pelaksanaan
                        </h4>
                        
                        <div class="space-y-4">
                            {{-- Tanggal Webinar --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tanggal Webinar <span class="text-red-400">*</span></label>
                                <input type="date" name="tanggal_webinar" value="{{ old('tanggal_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('tanggal_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jam Mulai & Jam Selesai --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Mulai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_mulai_webinar" value="{{ old('jam_mulai_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('jam_mulai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Selesai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_selesai_webinar" value="{{ old('jam_selesai_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('jam_selesai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Kuota Peserta --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kuota Peserta (Opsional)</label>
                                <input type="number" name="kuota_peserta" min="0" placeholder="Kosongkan jika tidak dibatasi" value="{{ old('kuota_peserta') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('kuota_peserta')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- 2. Struktur Modul Awal (Hidden for Webinar) --}}
                    <div x-show="selectedKategori !== 'webinar'" x-transition class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">2</span>
                            Struktur Modul Awal
                        </h4>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                                <input type="text" name="modul_judul" value="{{ old('modul_judul') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul modul">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                <select name="modul_tipe" id="modul_tipe" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                    <option value="video" {{ old('modul_tipe') == 'video' ? 'selected' : '' }}>Video</option>
                                    <option value="bacaan" {{ old('modul_tipe') == 'bacaan' ? 'selected' : '' }}>Bacaan</option>
                                    <option value="kuis" {{ old('modul_tipe') == 'kuis' ? 'selected' : '' }}>Kuis</option>
                                    <option value="tugas" {{ old('modul_tipe') == 'tugas' ? 'selected' : '' }}>Tugas Akhir (Opsional)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Jika Judul Modul diisi, setelah kursus dibuat Anda akan diarahkan ke halaman kelola sesuai tipe ini.
                                </p>
                            </div>
                            <div>
                                <label id="modul_konten_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                                <textarea id="modul_konten" name="modul_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Deskripsi modul...">{{ old('modul_konten') }}</textarea>
                            </div>
                            <div id="modul_video_url_group">
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                                <input type="url" id="modul_video_url" name="modul_video_url" value="{{ old('modul_video_url') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div id="modul_durasi_group">
                                <label id="modul_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                                <input type="number" id="modul_durasi" name="modul_durasi" min="0" value="{{ old('modul_durasi') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="0">
                            </div>
                            <p id="modul_type_hint" class="text-xs text-gray-500 dark:text-gray-400"></p>
                            @error('modul_video_url')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
                            @error('modul_konten')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
                            @error('modul_durasi')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
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
                                    <input type="hidden" name="status" id="status_input" value="draft">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="status_toggle" class="sr-only peer">
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
                                        <input type="checkbox" name="akses_publik" value="1" class="sr-only peer" checked>
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
                                    <input type="checkbox" name="sertifikat" value="1" class="sr-only peer">
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
                                    <select name="kategori" id="add_kategori" required x-model="selectedKategori" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="kursus" {{ old('kategori') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                        <option value="webinar" {{ old('kategori') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                                        <option value="tiket" {{ old('kategori') === 'tiket' ? 'selected' : '' }}>Tiket</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                                @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Harga (Rp)</label>
                                    <input type="number" name="harga" id="harga_input" min="0" placeholder="0" value="{{ old('harga', 0) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Diskon (%)</label>
                                    <input type="number" name="diskon" id="diskon_input" min="0" max="100" placeholder="0" value="{{ old('diskon', 0) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="flex items-center gap-2 py-2.5">
                                    <input type="hidden" name="tipe" id="tipe_input" value="berbayar">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="gratis_toggle" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                    </label>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Gratis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Thumbnail Preview (matching admin style)
        function previewThumbnail(input) {
            const file = input.files[0];
            if (file) {
                if (!file.type.startsWith('image/')) {
                    alert('Harap pilih file gambar (PNG, JPG)');
                    input.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('thumbnailPreview');
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusToggle = document.getElementById('status_toggle');
            const statusInput = document.getElementById('status_input');
            const gratisToggle = document.getElementById('gratis_toggle');
            const tipeInput = document.getElementById('tipe_input');
            const hargaInput = document.getElementById('harga_input');
            const diskonInput = document.getElementById('diskon_input');
            const modulTipeSelect = document.getElementById('modul_tipe');
            const modulKontenLabel = document.getElementById('modul_konten_label');
            const modulKontenInput = document.getElementById('modul_konten');
            const modulVideoGroup = document.getElementById('modul_video_url_group');
            const modulVideoInput = document.getElementById('modul_video_url');
            const modulDurasiGroup = document.getElementById('modul_durasi_group');
            const modulDurasiLabel = document.getElementById('modul_durasi_label');
            const modulDurasiInput = document.getElementById('modul_durasi');
            const modulTypeHint = document.getElementById('modul_type_hint');

            // Status Toggle
            if (statusToggle) {
                statusToggle.addEventListener('change', function() {
                    statusInput.value = this.checked ? 'aktif' : 'draft';
                });
            }

            // Gratis Toggle
            if (gratisToggle) {
                gratisToggle.addEventListener('change', function() {
                    if (this.checked) {
                        tipeInput.value = 'gratis';
                        hargaInput.value = 0;
                        hargaInput.disabled = true;
                        diskonInput.value = 0;
                        diskonInput.disabled = true;
                        hargaInput.classList.add('opacity-50', 'cursor-not-allowed');
                        diskonInput.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        tipeInput.value = 'berbayar';
                        hargaInput.disabled = false;
                        diskonInput.disabled = false;
                        hargaInput.classList.remove('opacity-50', 'cursor-not-allowed');
                        diskonInput.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }

            // Struktur Modul Awal adaptif berdasarkan tipe materi
            const syncModuleFieldsByType = () => {
                if (!modulTipeSelect) return;

                const type = modulTipeSelect.value || 'video';

                const config = {
                    video: {
                        kontenLabel: 'Ringkasan Video (opsional)',
                        kontenPlaceholder: 'Ringkas isi video yang akan dipelajari...',
                        showVideoUrl: true,
                        showDurasi: true,
                        durasiLabel: 'Durasi Video (menit)',
                        hint: 'Tipe Video menampilkan URL video dan durasi tayang.',
                    },
                    bacaan: {
                        kontenLabel: 'Konten Bacaan',
                        kontenPlaceholder: 'Tulis isi materi bacaan, rangkuman, atau poin utama...',
                        showVideoUrl: false,
                        showDurasi: true,
                        durasiLabel: 'Estimasi Baca (menit)',
                        hint: 'Tipe Bacaan fokus ke konten teks. URL video disembunyikan.',
                    },
                    kuis: {
                        kontenLabel: 'Instruksi Kuis',
                        kontenPlaceholder: 'Jelaskan aturan, jumlah soal, dan nilai minimum kelulusan...',
                        showVideoUrl: false,
                        showDurasi: true,
                        durasiLabel: 'Durasi Kuis (menit)',
                        hint: 'Tipe Kuis cocok untuk evaluasi belajar dengan batas waktu.',
                    },
                    tugas: {
                        kontenLabel: 'Instruksi Tugas Akhir (Opsional)',
                        kontenPlaceholder: 'Tuliskan instruksi pengerjaan, format pengumpulan, dan kriteria penilaian tugas akhir...',
                        showVideoUrl: false,
                        showDurasi: false,
                        durasiLabel: 'Durasi (menit)',
                        hint: 'Tugas akhir bersifat opsional: dosen bebas menambahkan atau tidak.',
                    },
                };

                const selected = config[type] || config.video;

                if (modulKontenLabel) modulKontenLabel.textContent = selected.kontenLabel;
                if (modulKontenInput) modulKontenInput.placeholder = selected.kontenPlaceholder;
                if (modulDurasiLabel) modulDurasiLabel.textContent = selected.durasiLabel;
                if (modulTypeHint) modulTypeHint.textContent = selected.hint;

                if (modulVideoGroup) {
                    modulVideoGroup.classList.toggle('hidden', !selected.showVideoUrl);
                }
                if (modulDurasiGroup) {
                    modulDurasiGroup.classList.toggle('hidden', !selected.showDurasi);
                }

                // Hindari data lintas tipe ikut terkirim.
                if (!selected.showVideoUrl && modulVideoInput) {
                    modulVideoInput.value = '';
                }
                if (!selected.showDurasi && modulDurasiInput) {
                    modulDurasiInput.value = '';
                }
            };

            if (modulTipeSelect) {
                modulTipeSelect.addEventListener('change', syncModuleFieldsByType);
                syncModuleFieldsByType();
            }

            // Handle submit buttons — set status based on which button was clicked
            const form = document.getElementById('buatKursusForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = e.submitter;
                    if (submitBtn) {
                        const statusVal = submitBtn.value;
                        if (statusVal === 'draft' || statusVal === 'aktif') {
                            statusInput.value = statusVal;
                            if (statusToggle) {
                                statusToggle.checked = (statusVal === 'aktif');
                            }
                        }
                        // Loading spinner
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = `<svg class="animate-spin w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Menyimpan...`;
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.dosen>

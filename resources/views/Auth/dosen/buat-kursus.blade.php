@php
    $defaultKategori = old('kategori', $initialKategori ?? 'kursus');
    $defaultCourseCode = old('kategori') === 'webinar'
        ? 'C-' . strtoupper(substr(md5('course' . (string) now()->timestamp), 0, 6))
        : old('kode_course', 'C-' . strtoupper(substr(md5((string) now()->timestamp), 0, 6)));
    $defaultWebinarCode = old('kategori') === 'webinar'
        ? old('kode_course', 'WEB' . strtoupper(substr(md5('webinar' . (string) now()->timestamp), 0, 6)))
        : 'WEB' . strtoupper(substr(md5('webinar' . (string) now()->timestamp), 0, 6));
    $pageTitles = [
        'kursus' => 'Buat Kursus Baru',
        'webinar' => 'Buat Webinar Baru',
    ];
@endphp

<x-layouts.dosen :title="$pageTitles[$defaultKategori] ?? 'Buat Kursus Baru'" active="buat-kursus">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data" id="buatKursusForm" x-data="{ isLoading: false, selectedKategori: '{{ $defaultKategori }}' }" @submit="isLoading = true">
            @csrf
            
            {{-- Page Header + Action Buttons --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6 mb-6" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/40 shadow-purple-500/5' : ''">
                <div class="mb-4">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">
                        <span x-show="selectedKategori === 'kursus'">Buat Kursus Baru</span>
                        <span x-show="selectedKategori === 'webinar'" x-cloak>Buat Webinar Baru</span>
                    </h1>
                    <p class="text-sm" :class="selectedKategori === 'webinar' ? 'text-purple-500' : 'text-blue-500'">
                        <span x-show="selectedKategori === 'kursus'">Lengkapi informasi berikut untuk membuat kursus baru.</span>
                        <span x-show="selectedKategori === 'webinar'" x-cloak>Lengkapi informasi webinar yang akan dilaksanakan.</span>
                    </p>
                </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6">
                <div class="space-y-4">
                    {{-- 1. Informasi Dasar Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'">1</span>
                            <span x-show="selectedKategori === 'kursus'">Informasi Dasar Kursus</span>
                            <span x-show="selectedKategori === 'webinar'" x-cloak>Informasi Dasar Webinar</span>
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div :class="selectedKategori === 'kursus' ? 'sm:col-span-2' : ''">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                        <span x-show="selectedKategori === 'kursus'">Judul Kursus <span class="text-red-400">*</span></span>
                                        <span x-show="selectedKategori === 'webinar'" x-cloak>Judul Webinar <span class="text-red-400">*</span></span>
                                    </label>
                                    <input type="text" name="nama_course" id="add_nama_course" required :placeholder="selectedKategori === 'webinar' ? 'Masukkan judul webinar' : 'Masukkan judul kursus'" value="{{ old('nama_course') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-transparent" :class="selectedKategori === 'webinar' ? 'focus:ring-2 focus:ring-purple-500' : 'focus:ring-2 focus:ring-blue-500'">
                                    @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                        <span x-show="selectedKategori === 'kursus'">Kode Kursus <span class="text-red-400">*</span></span>
                                        <span x-show="selectedKategori === 'webinar'" x-cloak>Kode Webinar <span class="text-gray-300 dark:text-gray-600">(otomatis)</span></span>
                                    </label>
                                    <input
                                        type="text"
                                        name="kode_course"
                                        id="add_kode_course"
                                        required
                                        data-course-default="{{ $defaultCourseCode }}"
                                        data-webinar-default="{{ $defaultWebinarCode }}"
                                        placeholder="Contoh: EKMA4116"
                                        value="{{ $defaultKategori === 'webinar' ? $defaultWebinarCode : $defaultCourseCode }}"
                                        class="w-full px-3 py-2.5 border rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-transparent"
                                        :class="selectedKategori === 'webinar'
                                            ? 'bg-gray-100 dark:bg-gray-600 border-gray-200 dark:border-gray-600 cursor-not-allowed focus:ring-2 focus:ring-purple-500'
                                            : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 focus:ring-2 focus:ring-blue-500'">
                                    <p class="text-xs text-gray-400 mt-1" x-show="selectedKategori === 'kursus'">Gunakan kode unik untuk kursus ini.</p>
                                    @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Deskripsi Kursus --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    <span x-show="selectedKategori === 'kursus'">Deskripsi Kursus</span>
                                    <span x-show="selectedKategori === 'webinar'" x-cloak>Deskripsi Webinar <span class="text-gray-300 dark:text-gray-600">(opsional)</span></span>
                                </label>
                                <textarea name="deskripsi" id="add_deskripsi" rows="3" :placeholder="selectedKategori === 'webinar' ? 'Jelaskan topik dan manfaat webinar ini...' : 'Jelaskan tentang kursus ini...'" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-transparent resize-none" :class="selectedKategori === 'webinar' ? 'focus:ring-2 focus:ring-purple-500' : 'focus:ring-2 focus:ring-blue-500'">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Persyaratan Kursus --}}
                            <div x-show="selectedKategori !== 'webinar'" x-transition>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Persyaratan Kursus (Opsional)</label>
                                <textarea name="persyaratan" id="add_persyaratan" data-course-only-field rows="3" placeholder="Contoh:&#10;STIN4101 - Pengantar Teknologi Informasi&#10;Memiliki laptop dan koneksi internet stabil" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('persyaratan') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Tulis satu persyaratan per baris.</p>
                                @error('persyaratan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jurusan/Prodi & Tingkat Kesulitan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="selectedKategori !== 'webinar'" x-transition>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jurusan/Prodi</label>
                                    <div class="relative">
                                        <select name="id_jurusan" id="add_id_jurusan" data-course-only-field class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                        <select name="level" id="add_level" data-course-only-field class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Pilih Tingkat</option>
                                            <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                            <option value="Menengah" {{ old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                            <option value="Mahir" {{ old('level') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="selectedKategori === 'webinar'" x-cloak x-transition>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Pembicara / Dosen <span class="text-gray-300 dark:text-gray-600">(otomatis)</span></label>
                                    <input type="text" readonly value="{{ $dosen->name ?? Auth::guard('dosen')->user()->name ?? 'Dosen' }}" class="w-full px-3 py-2.5 bg-gray-100 dark:bg-gray-600 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white cursor-not-allowed focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Program Studi <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                    <div class="relative">
                                        <select name="id_jurusan" data-webinar-only-field class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="">Pilih Prodi</option>
                                            @foreach($jurusans ?? [] as $jurusan)
                                            <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                            @endforeach
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Estimasi Waktu --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="selectedKategori !== 'webinar'" x-transition>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Estimasi Waktu Belajar</label>
                                    <input type="number" name="estimasi_waktu" id="add_estimasi_waktu" data-course-only-field min="0" placeholder="20" value="{{ old('estimasi_waktu', 20) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('estimasi_waktu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Satuan Durasi</label>
                                    <div class="relative">
                                        <select name="durasi_satuan" id="add_durasi_satuan" data-course-only-field class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="Jam" {{ old('durasi_satuan', 'Jam') === 'Jam' ? 'selected' : '' }}>Jam</option>
                                            <option value="Minggu" {{ old('durasi_satuan') === 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    @error('durasi_satuan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Playlist YouTube / Link Meeting --}}
                            <div x-show="selectedKategori !== 'webinar'" x-transition>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    <span x-show="selectedKategori === 'kursus'">Link Playlist YouTube (Opsional)</span>
                                </label>
                                <input type="url" name="youtube_playlist" id="add_youtube_playlist" data-course-only-field placeholder="https://www.youtube.com/playlist?list=..." value="{{ old('youtube_playlist') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('youtube_playlist')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Thumbnail --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                                    <span x-show="selectedKategori === 'kursus'">Thumbnail Kursus</span>
                                    <span x-show="selectedKategori === 'webinar'" x-cloak>Thumbnail Webinar <span class="text-gray-300 dark:text-gray-600">(opsional)</span></span>
                                </label>
                                <div class="flex items-center gap-4">
                                    <div id="thumbnailPreview" class="w-20 h-14 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <svg x-show="selectedKategori === 'kursus'" class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <svg x-show="selectedKategori === 'webinar'" x-cloak class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center gap-2 px-3 py-1.5 border text-sm font-medium rounded-lg cursor-pointer transition" :class="selectedKategori === 'webinar' ? 'border-purple-500 text-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20' : 'border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span x-show="selectedKategori === 'kursus'">Upload Thumbnail</span>
                                            <span x-show="selectedKategori === 'webinar'" x-cloak>Unggah Gambar</span>
                                            <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewThumbnail(this)">
                                        </label>
                                        <p class="text-xs text-gray-400 mt-1" x-show="selectedKategori === 'kursus'">Maksimal 2MB, JPG/PNG</p>
                                        <p class="text-[11px] text-gray-400 mt-1" x-show="selectedKategori === 'webinar'" x-cloak>JPG, PNG, maks 2MB. Rasio 16:9 disarankan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Jadwal Pelaksanaan (Webinar Only) --}}
                    <div x-show="selectedKategori === 'webinar'" x-transition x-cloak class="border border-purple-200 dark:border-purple-700/50 rounded-xl p-5 bg-purple-50/40 dark:bg-purple-900/10">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">2</span>
                            Jadwal Pelaksanaan
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tanggal Webinar <span class="text-red-400">*</span></label>
                                    <input type="date" name="tanggal_webinar" data-webinar-only-field value="{{ old('tanggal_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('tanggal_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Mulai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_mulai_webinar" data-webinar-only-field value="{{ old('jam_mulai_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('jam_mulai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Selesai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_selesai_webinar" data-webinar-only-field value="{{ old('jam_selesai_webinar') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    @error('jam_selesai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kuota Peserta <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                <input type="number" name="kuota_peserta" data-webinar-only-field min="1" placeholder="Kosongkan jika tidak dibatasi" value="{{ old('kuota_peserta') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                @error('kuota_peserta')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Link Meeting <span class="text-gray-300 dark:text-gray-600">(opsional)</span></label>
                                <div class="relative">
                                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        <input type="url" name="youtube_playlist" data-webinar-only-field placeholder="https://zoom.us/j/... atau https://meet.google.com/..." value="{{ old('youtube_playlist') }}" class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Link Zoom / Google Meet akan dibagikan ke peserta yang terdaftar.</p>
                                @error('youtube_playlist')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
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
                                <input type="text" name="modul_judul" data-course-only-field value="{{ old('modul_judul') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Masukkan judul modul">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                <select name="modul_tipe" id="modul_tipe" data-course-only-field class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
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
                                <textarea id="modul_konten" name="modul_konten" data-course-only-field rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Deskripsi modul...">{{ old('modul_konten') }}</textarea>
                            </div>
                            <div id="modul_video_url_group">
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                                <input type="url" id="modul_video_url" name="modul_video_url" data-course-only-field value="{{ old('modul_video_url') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div id="modul_durasi_group">
                                <label id="modul_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                                <input type="number" id="modul_durasi" name="modul_durasi" data-course-only-field min="0" value="{{ old('modul_durasi') }}" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="0">
                            </div>
                            <p id="modul_type_hint" class="text-xs text-gray-500 dark:text-gray-400"></p>
                            @error('modul_video_url')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
                            @error('modul_konten')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
                            @error('modul_durasi')<p class="text-red-500 text-xs -mt-2">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- 3. Pengaturan Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/30' : ''">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'">3</span>
                            <span x-show="selectedKategori === 'kursus'">Pengaturan Kursus</span>
                            <span x-show="selectedKategori === 'webinar'" x-cloak>Pengaturan Webinar</span>
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Status Kursus Toggle --}}
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 class="font-medium text-gray-900 dark:text-white text-xs">
                                                <span x-show="selectedKategori === 'kursus'">Status Kursus</span>
                                            <span x-show="selectedKategori === 'webinar'" x-cloak>Status Pengajuan</span>
                                            </h5>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400" x-text="selectedKategori === 'webinar' ? 'Ajukan ke admin atau simpan draft' : 'Aktif atau simpan draft'"></p>
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
                                    <h5 class="font-medium text-gray-900 dark:text-white text-xs">
                                        <span x-show="selectedKategori === 'kursus'">Sertifikat Penyelesaian</span>
                                        <span x-show="selectedKategori === 'webinar'" x-cloak>Sertifikat Kehadiran</span>
                                    </h5>
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
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/30' : ''">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'">4</span>
                            <span x-show="selectedKategori === 'kursus'">Pricing & Akses Kursus</span>
                            <span x-show="selectedKategori === 'webinar'" x-cloak>Harga & Akses</span>
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kategori Kursus</label>
                                <div class="relative">
                                    <select name="kategori" id="add_kategori" required x-model="selectedKategori" class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="kursus" {{ old('kategori') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                        <option value="webinar" {{ old('kategori') === 'webinar' ? 'selected' : '' }}>Webinar</option>
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

            <!-- Bottom Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                <a href="{{ route('dosen.kursus') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-bold rounded-xl transition focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                
                <button type="submit" name="status_btn" value="draft" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 border text-sm font-bold rounded-xl transition focus:ring-4" :class="selectedKategori === 'webinar' ? 'text-purple-600 dark:text-purple-400 border-purple-300 dark:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 focus:ring-purple-100 dark:focus:ring-purple-900' : 'text-blue-600 dark:text-blue-400 border-blue-300 dark:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:ring-blue-100 dark:focus:ring-blue-900'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span x-text="selectedKategori === 'webinar' ? 'Simpan Draft Webinar' : 'Simpan Draft'"></span>
                </button>

                <button type="submit" name="status_btn" value="aktif" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-opacity-30 focus:ring-4" :class="selectedKategori === 'webinar' ? 'bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 shadow-purple-500 focus:ring-purple-200 dark:focus:ring-purple-900' : 'bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-blue-500 focus:ring-blue-200 dark:focus:ring-blue-900'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span x-text="selectedKategori === 'webinar' ? 'Ajukan Webinar' : 'Buat Kursus'"></span>
                </button>
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
            const kategoriSelect = document.getElementById('add_kategori');
            const kodeCourseInput = document.getElementById('add_kode_course');
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

            const generateWebinarCode = () => {
                const now = new Date();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const random = Math.random().toString(36).slice(2, 6).toUpperCase();

                return `WEB${mm}${dd}${random}`;
            };

            const syncKategoriMode = () => {
                if (!kategoriSelect || !kodeCourseInput) return;

                const isWebinar = kategoriSelect.value === 'webinar';
                const courseDefault = kodeCourseInput.dataset.courseDefault || '';
                const webinarDefault = kodeCourseInput.dataset.webinarDefault || generateWebinarCode();
                const courseOnlyFields = document.querySelectorAll('[data-course-only-field]');
                const webinarOnlyFields = document.querySelectorAll('[data-webinar-only-field]');

                courseOnlyFields.forEach((field) => {
                    field.disabled = isWebinar;
                });

                webinarOnlyFields.forEach((field) => {
                    field.disabled = !isWebinar;
                });

                if (isWebinar) {
                    kodeCourseInput.readOnly = true;
                    kodeCourseInput.classList.add('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
                    kodeCourseInput.classList.remove('bg-gray-50', 'dark:bg-gray-700');

                    if (!kodeCourseInput.value || kodeCourseInput.value === courseDefault || kodeCourseInput.value.startsWith('C-')) {
                        kodeCourseInput.value = webinarDefault.startsWith('WEB') ? webinarDefault : generateWebinarCode();
                    }
                } else {
                    kodeCourseInput.readOnly = false;
                    kodeCourseInput.classList.remove('bg-gray-100', 'dark:bg-gray-600', 'cursor-not-allowed');
                    kodeCourseInput.classList.add('bg-gray-50', 'dark:bg-gray-700');

                    if (!kodeCourseInput.value || kodeCourseInput.value.startsWith('WEB')) {
                        kodeCourseInput.value = courseDefault || '';
                    }
                }
            };

            if (kategoriSelect) {
                kategoriSelect.addEventListener('change', syncKategoriMode);
                syncKategoriMode();
            }

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

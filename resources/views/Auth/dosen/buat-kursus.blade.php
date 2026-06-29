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
    $initialModules = old('initial_modules', [
        [
            'judul' => '',
            'tipe' => 'video',
            'konten' => '',
            'video_url' => '',
            'durasi' => '',
        ],
    ]);
    $learningGoals = old('learning_goals', [
        ['judul_goal' => '', 'deskripsi' => ''],
    ]);
@endphp

<x-layouts.dosen :title="$pageTitles[$defaultKategori] ?? 'Buat Kursus Baru'" active="buat-kursus">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data" id="buatKursusForm" x-data="{ isLoading: false, selectedKategori: '{{ $defaultKategori }}' }" @submit="isLoading = true">
            @csrf
            
            {{-- Page Header + Action Buttons --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6 mb-6" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/40 shadow-purple-500/5' : ''">
                <div class="mb-4">
                    <h1 id="form-page-title" class="text-lg font-bold text-gray-900 dark:text-white">Buat Kursus Baru</h1>
                    <p id="form-page-subtitle" class="text-sm text-blue-500">Lengkapi informasi berikut untuk membuat kursus baru.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sm:p-6">
                <div class="space-y-4">
                    {{-- 1. Informasi Dasar Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'">1</span>
                            <span id="basic-info-title">Informasi Dasar Kursus</span>
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div :class="selectedKategori === 'kursus' ? 'sm:col-span-2' : ''">
                                    <label id="label-nama-course" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Judul Kursus <span class="text-red-400">*</span></label>
                                    <input type="text" name="nama_course" id="add_nama_course" required :placeholder="selectedKategori === 'webinar' ? 'Masukkan judul webinar' : 'Masukkan judul kursus'" value="{{ old('nama_course') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-transparent" :class="selectedKategori === 'webinar' ? 'focus:ring-2 focus:ring-purple-500' : 'focus:ring-2 focus:ring-blue-500'">
                                    @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label id="label-kode-course" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kode Kursus <span class="text-red-400">*</span></label>
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
                                    <p id="kode-course-help" class="text-xs text-gray-400 mt-1">Gunakan kode unik untuk kursus ini.</p>
                                    @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Deskripsi Kursus --}}
                            <div>
                                <label id="label-deskripsi-course" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Kursus</label>
                                <textarea name="deskripsi" id="add_deskripsi" rows="3" :placeholder="selectedKategori === 'webinar' ? 'Jelaskan topik dan manfaat webinar ini...' : 'Jelaskan tentang kursus ini...'" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-transparent resize-none" :class="selectedKategori === 'webinar' ? 'focus:ring-2 focus:ring-purple-500' : 'focus:ring-2 focus:ring-blue-500'">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Persyaratan Kursus --}}
                            <div data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar'])>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Persyaratan Kursus (Opsional)</label>
                                <textarea name="persyaratan" id="add_persyaratan" data-course-only-field rows="3" placeholder="Contoh:&#10;STIN4101 - Pengantar Teknologi Informasi&#10;Memiliki laptop dan koneksi internet stabil" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('persyaratan') }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">Tulis satu persyaratan per baris.</p>
                                @error('persyaratan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Jurusan/Prodi & Tingkat Kesulitan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar'])>
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

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" data-kategori-section="webinar" @class(['hidden' => $defaultKategori !== 'webinar'])>
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar'])>
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
                            <div data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar'])>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Link Playlist YouTube (Opsional)</label>
                                <input type="url" name="youtube_playlist" id="add_youtube_playlist" data-course-only-field placeholder="https://www.youtube.com/playlist?list=..." value="{{ old('youtube_playlist') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('youtube_playlist')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            {{-- Thumbnail --}}
                            <div>
                                <label id="label-thumbnail-course" class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Thumbnail Kursus</label>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <div id="thumbnailPreview" class="w-20 h-14 shrink-0 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <svg data-thumbnail-icon="kursus" @class(['hidden' => $defaultKategori === 'webinar']) class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <svg data-thumbnail-icon="webinar" @class(['hidden' => $defaultKategori !== 'webinar']) class="w-7 h-7 text-gray-300 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <label class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 border text-sm font-medium rounded-xl cursor-pointer transition" :class="selectedKategori === 'webinar' ? 'border-purple-500 text-purple-500 hover:bg-purple-50 dark:hover:bg-purple-900/20' : 'border-blue-500 text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span id="thumbnail-button-text">Upload Thumbnail</span>
                                            <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewThumbnail(this)">
                                        </label>
                                        <p id="thumbnail-help-course" @class(['hidden' => $defaultKategori === 'webinar']) class="text-xs text-gray-400 mt-1.5 leading-relaxed">Maksimal 2MB, format JPG/PNG.</p>
                                        <p id="thumbnail-help-webinar" @class(['hidden' => $defaultKategori !== 'webinar']) class="text-[11px] text-gray-400 mt-1.5 leading-relaxed">JPG/PNG, maksimal 2MB. Rasio 16:9 disarankan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Jadwal Pelaksanaan (Webinar Only) --}}
                    <div data-kategori-section="webinar" @class(['hidden' => $defaultKategori !== 'webinar']) class="border border-purple-200 dark:border-purple-700/50 rounded-xl p-5 bg-purple-50/40 dark:bg-purple-900/10">
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
                    <div data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar']) class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">2</span>
                            Struktur Modul Awal
                        </h4>

                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Tambahkan beberapa modul awal sekaligus</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Semua item di sini akan otomatis dibuat ke modul default kursus saat disimpan.</p>
                                </div>
                                <button type="button" id="addInitialModuleButton" class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-600 transition hover:border-blue-300 hover:bg-blue-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Modul
                                </button>
                            </div>

                            <div id="initialModulesContainer" class="space-y-4">
                                @foreach ($initialModules as $index => $module)
                                    <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-800/60" data-initial-module-card>
                                        <div class="mb-4 flex items-start justify-between gap-3">
                                            <div>
                                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Modul Awal {{ $index + 1 }}</h5>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tentukan tipe materi dan konten dasar yang ingin langsung dibuat.</p>
                                            </div>
                                            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-initial-module {{ count($initialModules) === 1 ? 'disabled' : '' }}>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">Judul Modul</label>
                                                <input type="text" name="initial_modules[{{ $index }}][judul]" data-course-only-field value="{{ $module['judul'] ?? '' }}" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="Masukkan judul modul">
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">Tipe</label>
                                                <select name="initial_modules[{{ $index }}][tipe]" data-course-only-field data-initial-module-type class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                                    <option value="video" {{ ($module['tipe'] ?? 'video') == 'video' ? 'selected' : '' }}>Video</option>
                                                    <option value="bacaan" {{ ($module['tipe'] ?? '') == 'bacaan' ? 'selected' : '' }}>Bacaan</option>
                                                    <option value="kuis" {{ ($module['tipe'] ?? '') == 'kuis' ? 'selected' : '' }}>Kuis</option>
                                                    <option value="tugas" {{ ($module['tipe'] ?? '') == 'tugas' ? 'selected' : '' }}>Tugas Akhir (Opsional)</option>
                                                </select>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Saat kursus dibuat, item ini langsung masuk ke modul default dan bisa diedit lagi setelahnya.</p>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400" data-initial-module-content-label>Konten/Deskripsi</label>
                                                <textarea name="initial_modules[{{ $index }}][konten]" data-course-only-field data-initial-module-content rows="3" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600 resize-none" placeholder="Deskripsi modul...">{{ $module['konten'] ?? '' }}</textarea>
                                            </div>

                                            <div data-initial-module-video-group>
                                                <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">URL Video (opsional)</label>
                                                <input type="url" name="initial_modules[{{ $index }}][video_url]" data-course-only-field data-initial-module-video value="{{ $module['video_url'] ?? '' }}" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="https://www.youtube.com/watch?v=...">
                                            </div>

                                            <div data-initial-module-duration-group>
                                                <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400" data-initial-module-duration-label>Durasi (menit)</label>
                                                <input type="number" name="initial_modules[{{ $index }}][durasi]" data-course-only-field data-initial-module-duration min="0" value="{{ $module['durasi'] ?? '' }}" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="0">
                                            </div>

                                            <p class="text-xs text-gray-500 dark:text-gray-400" data-initial-module-hint></p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('initial_modules')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('initial_modules.*.judul')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('initial_modules.*.tipe')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('initial_modules.*.konten')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('initial_modules.*.video_url')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('initial_modules.*.durasi')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- 2.5. Tujuan Pembelajaran --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" data-kategori-section="kursus" @class(['hidden' => $defaultKategori === 'webinar'])>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-xs font-bold flex items-center justify-center">2.5</span>
                            Tujuan Pembelajaran
                        </h4>

                        <div class="space-y-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Kompetensi yang akan dicapai mahasiswa</p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Cantumkan 2-4 tujuan pembelajaran agar mahasiswa memahami hasil akhir kursus.</p>
                                </div>
                                <button type="button" id="addLearningGoalButton" data-add-learning-goal class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-600 transition hover:border-emerald-300 hover:bg-emerald-100">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Tujuan
                                </button>
                            </div>

                            <div id="learningGoalsContainer" class="space-y-3">
                                @foreach ($learningGoals as $index => $goal)
                                    <div class="learning-goal-card rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-800/60" data-learning-goal-card>
                                        <div class="mb-3 flex items-start justify-between gap-3">
                                            <div>
                                                <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Tujuan {{ $index + 1 }}</h5>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tulis singkat, mudah diingat, dan dapat diukur.</p>
                                            </div>
                                            <button type="button" class="remove-learning-goal inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-learning-goal {{ count($learningGoals) === 1 ? 'disabled' : '' }}>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="mb-1 block text-xs text-gray-500 dark:text-gray-400">Judul Tujuan <span class="text-red-400">*</span></label>
                                                <input type="text" name="learning_goals[{{ $index }}][judul_goal]" data-course-only-field value="{{ is_array($goal) ? ($goal['judul_goal'] ?? '') : ($goal->judul_goal ?? '') }}" placeholder="Contoh: Memahami konsep OOP" class="w-full rounded-lg bg-white px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                            </div>
                                            <div>
                                                <label class="mb-1 block text-xs text-gray-500 dark:text-gray-400">Deskripsi (opsional)</label>
                                                <textarea name="learning_goals[{{ $index }}][deskripsi]" data-course-only-field rows="2" placeholder="Mahasiswa mampu menjelaskan pilar OOP dan contoh implementasinya pada studi kasus sederhana." class="w-full rounded-lg bg-white px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600 resize-none">{{ is_array($goal) ? ($goal['deskripsi'] ?? '') : ($goal->deskripsi ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('learning_goals')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('learning_goals.*.judul_goal')<p class="text-red-500 text-xs">Tujuan pembelajaran harus memiliki judul yang diisi.</p>@enderror
                        </div>
                    </div>

                    {{-- 3. Pengaturan Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/30' : ''">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'">3</span>
                            <span id="settings-section-title">Pengaturan Kursus</span>
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Status Kursus Toggle --}}
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                        <div>
                                            <h5 id="status-card-title" class="font-medium text-gray-900 dark:text-white text-xs">Status Kursus</h5>
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

                            {{-- Sertifikat Select --}}
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                <div>
                                    <h5 id="certificate-card-title" class="font-medium text-gray-900 dark:text-white text-xs">Sertifikat Penyelesaian</h5>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Wajib pilih template sertifikat</p>
                                </div>
                                <input type="hidden" name="sertifikat" value="1">
                                <select name="certificate_template_id" required class="w-1/2 px-2 py-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg text-xs text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">-- Pilih Template --</option>
                                    @foreach($certificateTemplates as $template)
                                        <option value="{{ $template->id }}" {{ old('certificate_template_id') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Pricing & Akses Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" :class="selectedKategori === 'webinar' ? 'border-purple-100 dark:border-purple-700/30' : ''">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center" :class="selectedKategori === 'webinar' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400'">4</span>
                            <span id="pricing-section-title">Pricing & Akses Kursus</span>
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
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-start gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                <a href="{{ route('dosen.kursus') }}" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-bold rounded-xl transition focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700">
                    <svg class="w-5 h-5 border-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                
                <button type="submit" name="status_btn" value="draft" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-600 text-sm font-bold rounded-xl transition hover:bg-blue-50 dark:hover:bg-blue-900/20 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900" :class="selectedKategori === 'webinar' ? 'text-purple-600 dark:text-purple-400 border-purple-300 dark:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 focus:ring-purple-100 dark:focus:ring-purple-900' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Simpan Draft</span>
                </button>

                <button type="submit" name="status_btn" value="aktif" class="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-3 text-white bg-blue-600 hover:bg-blue-700 text-sm font-bold rounded-xl transition shadow-lg shadow-blue-500/30 focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900" :class="selectedKategori === 'webinar' ? 'bg-purple-600 hover:bg-purple-700 shadow-purple-500/30 focus:ring-purple-200 dark:focus:ring-purple-900' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function showUploadError(message) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload tidak valid',
                    text: message,
                    confirmButtonText: 'Oke',
                });
                return;
            }

            alert(message);
        }

        function isValidThumbnailFile(input) {
            const file = input?.files?.[0];
            if (!file) return true;

            const maxSizeMb = Number(input.dataset.maxSizeMb || 2);
            if (!file.type.startsWith('image/')) {
                showUploadError('Harap pilih file gambar JPG, PNG, atau WebP.');
                input.value = '';
                return false;
            }

            if (file.size > maxSizeMb * 1024 * 1024) {
                showUploadError(`Ukuran thumbnail maksimal ${maxSizeMb}MB. Kompres gambar atau pilih file yang lebih kecil.`);
                input.value = '';
                return false;
            }

            return true;
        }

        // Thumbnail Preview (matching admin style)
        function previewThumbnail(input) {
            const file = input.files[0];
            if (file) {
                if (!isValidThumbnailFile(input)) {
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
            const pageTitle = document.getElementById('form-page-title');
            const pageSubtitle = document.getElementById('form-page-subtitle');
            const basicInfoTitle = document.getElementById('basic-info-title');
            const namaCourseLabel = document.getElementById('label-nama-course');
            const kodeCourseLabel = document.getElementById('label-kode-course');
            const kodeCourseHelp = document.getElementById('kode-course-help');
            const deskripsiLabel = document.getElementById('label-deskripsi-course');
            const thumbnailLabel = document.getElementById('label-thumbnail-course');
            const thumbnailButtonText = document.getElementById('thumbnail-button-text');
            const thumbnailHelpCourse = document.getElementById('thumbnail-help-course');
            const thumbnailHelpWebinar = document.getElementById('thumbnail-help-webinar');
            const settingsSectionTitle = document.getElementById('settings-section-title');
            const statusCardTitle = document.getElementById('status-card-title');
            const certificateCardTitle = document.getElementById('certificate-card-title');
            const pricingSectionTitle = document.getElementById('pricing-section-title');
            const statusToggle = document.getElementById('status_toggle');
            const statusInput = document.getElementById('status_input');
            const gratisToggle = document.getElementById('gratis_toggle');
            const tipeInput = document.getElementById('tipe_input');
            const hargaInput = document.getElementById('harga_input');
            const diskonInput = document.getElementById('diskon_input');
            const initialModulesContainer = document.getElementById('initialModulesContainer');
            const addInitialModuleButton = document.getElementById('addInitialModuleButton');
            const learningGoalsContainer = document.getElementById('learningGoalsContainer');
            const addLearningGoalButton = document.getElementById('addLearningGoalButton');

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
                const courseSections = document.querySelectorAll('[data-kategori-section=\"kursus\"]');
                const webinarSections = document.querySelectorAll('[data-kategori-section=\"webinar\"]');
                const courseIcons = document.querySelectorAll('[data-thumbnail-icon=\"kursus\"]');
                const webinarIcons = document.querySelectorAll('[data-thumbnail-icon=\"webinar\"]');

                courseOnlyFields.forEach((field) => {
                    field.disabled = isWebinar;
                });

                webinarOnlyFields.forEach((field) => {
                    field.disabled = !isWebinar;
                });

                courseSections.forEach((section) => {
                    section.classList.toggle('hidden', isWebinar);
                });

                webinarSections.forEach((section) => {
                    section.classList.toggle('hidden', !isWebinar);
                });

                courseIcons.forEach((icon) => {
                    icon.classList.toggle('hidden', isWebinar);
                });

                webinarIcons.forEach((icon) => {
                    icon.classList.toggle('hidden', !isWebinar);
                });

                if (pageTitle) {
                    pageTitle.textContent = isWebinar ? 'Buat Webinar Baru' : 'Buat Kursus Baru';
                }

                if (pageSubtitle) {
                    pageSubtitle.textContent = isWebinar
                        ? 'Lengkapi informasi webinar yang akan dilaksanakan.'
                        : 'Lengkapi informasi berikut untuk membuat kursus baru.';
                    pageSubtitle.classList.toggle('text-purple-500', isWebinar);
                    pageSubtitle.classList.toggle('text-blue-500', !isWebinar);
                }

                if (basicInfoTitle) basicInfoTitle.textContent = isWebinar ? 'Informasi Dasar Webinar' : 'Informasi Dasar Kursus';
                if (namaCourseLabel) namaCourseLabel.innerHTML = isWebinar ? 'Judul Webinar <span class=\"text-red-400\">*</span>' : 'Judul Kursus <span class=\"text-red-400\">*</span>';
                if (kodeCourseLabel) kodeCourseLabel.innerHTML = isWebinar ? 'Kode Webinar <span class=\"text-gray-300 dark:text-gray-600\">(otomatis)</span>' : 'Kode Kursus <span class=\"text-red-400\">*</span>';
                if (kodeCourseHelp) kodeCourseHelp.classList.toggle('hidden', isWebinar);
                if (deskripsiLabel) deskripsiLabel.innerHTML = isWebinar ? 'Deskripsi Webinar <span class=\"text-gray-300 dark:text-gray-600\">(opsional)</span>' : 'Deskripsi Kursus';
                if (thumbnailLabel) thumbnailLabel.innerHTML = isWebinar ? 'Thumbnail Webinar <span class=\"text-gray-300 dark:text-gray-600\">(opsional)</span>' : 'Thumbnail Kursus';
                if (thumbnailButtonText) thumbnailButtonText.textContent = isWebinar ? 'Unggah Gambar' : 'Upload Thumbnail';
                if (thumbnailHelpCourse) thumbnailHelpCourse.classList.toggle('hidden', isWebinar);
                if (thumbnailHelpWebinar) thumbnailHelpWebinar.classList.toggle('hidden', !isWebinar);
                if (settingsSectionTitle) settingsSectionTitle.textContent = isWebinar ? 'Pengaturan Webinar' : 'Pengaturan Kursus';
                if (statusCardTitle) statusCardTitle.textContent = isWebinar ? 'Status Pengajuan' : 'Status Kursus';
                if (certificateCardTitle) certificateCardTitle.textContent = isWebinar ? 'Sertifikat Kehadiran' : 'Sertifikat Penyelesaian';
                if (pricingSectionTitle) pricingSectionTitle.textContent = isWebinar ? 'Harga & Akses' : 'Pricing & Akses Kursus';

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

            const initialModuleTypeConfig = {
                video: {
                    contentLabel: 'Ringkasan Video (opsional)',
                    contentPlaceholder: 'Ringkas isi video yang akan dipelajari...',
                    showVideoUrl: true,
                    showDuration: true,
                    durationLabel: 'Durasi Video (menit)',
                    hint: 'Tipe video menampilkan URL video dan durasi tayang.',
                },
                bacaan: {
                    contentLabel: 'Konten Bacaan',
                    contentPlaceholder: 'Tulis isi materi bacaan, rangkuman, atau poin utama...',
                    showVideoUrl: false,
                    showDuration: true,
                    durationLabel: 'Estimasi Baca (menit)',
                    hint: 'Tipe bacaan fokus ke isi materi. URL video disembunyikan.',
                },
                kuis: {
                    contentLabel: 'Instruksi Kuis',
                    contentPlaceholder: 'Jelaskan aturan, jumlah soal, dan nilai minimum kelulusan...',
                    showVideoUrl: false,
                    showDuration: true,
                    durationLabel: 'Durasi Kuis (menit)',
                    hint: 'Tipe kuis cocok untuk evaluasi belajar dengan batas waktu.',
                },
                tugas: {
                    contentLabel: 'Instruksi Tugas Akhir (Opsional)',
                    contentPlaceholder: 'Tuliskan instruksi pengerjaan, format pengumpulan, dan kriteria penilaian tugas akhir...',
                    showVideoUrl: false,
                    showDuration: false,
                    durationLabel: 'Durasi (menit)',
                    hint: 'Tugas akhir bersifat opsional: dosen bebas menambahkan atau tidak.',
                },
            };

            const syncInitialModuleCard = (card) => {
                if (!card) return;

                const typeSelect = card.querySelector('[data-initial-module-type]');
                const contentLabel = card.querySelector('[data-initial-module-content-label]');
                const contentInput = card.querySelector('[data-initial-module-content]');
                const videoGroup = card.querySelector('[data-initial-module-video-group]');
                const videoInput = card.querySelector('[data-initial-module-video]');
                const durationGroup = card.querySelector('[data-initial-module-duration-group]');
                const durationLabel = card.querySelector('[data-initial-module-duration-label]');
                const durationInput = card.querySelector('[data-initial-module-duration]');
                const hint = card.querySelector('[data-initial-module-hint]');
                const selectedType = typeSelect?.value || 'video';
                const selected = initialModuleTypeConfig[selectedType] || initialModuleTypeConfig.video;

                if (contentLabel) contentLabel.textContent = selected.contentLabel;
                if (contentInput) contentInput.placeholder = selected.contentPlaceholder;
                if (durationLabel) durationLabel.textContent = selected.durationLabel;
                if (hint) hint.textContent = selected.hint;
                if (videoGroup) videoGroup.classList.toggle('hidden', !selected.showVideoUrl);
                if (durationGroup) durationGroup.classList.toggle('hidden', !selected.showDuration);

                if (!selected.showVideoUrl && videoInput) {
                    videoInput.value = '';
                }

                if (!selected.showDuration && durationInput) {
                    durationInput.value = '';
                }
            };

            const renumberInitialModuleCards = () => {
                if (!initialModulesContainer) return;

                const cards = Array.from(initialModulesContainer.querySelectorAll('[data-initial-module-card]'));

                cards.forEach((card, index) => {
                    const title = card.querySelector('h5');
                    const removeButton = card.querySelector('[data-remove-initial-module]');

                    if (title) {
                        title.textContent = `Modul Awal ${index + 1}`;
                    }

                    card.querySelectorAll('input, textarea, select').forEach((field) => {
                        const currentName = field.getAttribute('name');
                        if (!currentName) return;
                        field.setAttribute('name', currentName.replace(/initial_modules\[\d+\]/, `initial_modules[${index}]`));
                    });

                    if (removeButton) {
                        removeButton.disabled = cards.length === 1;
                    }
                });
            };

            const createInitialModuleCard = (index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-800/60';
                wrapper.setAttribute('data-initial-module-card', 'true');
                wrapper.innerHTML = `
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Modul Awal ${index + 1}</h5>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tentukan tipe materi dan konten dasar yang ingin langsung dibuat.</p>
                        </div>
                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-initial-module>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">Judul Modul</label>
                            <input type="text" name="initial_modules[${index}][judul]" data-course-only-field class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="Masukkan judul modul">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">Tipe</label>
                            <select name="initial_modules[${index}][tipe]" data-course-only-field data-initial-module-type class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas Akhir (Opsional)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Saat kursus dibuat, item ini langsung masuk ke modul default dan bisa diedit lagi setelahnya.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400" data-initial-module-content-label>Konten/Deskripsi</label>
                            <textarea name="initial_modules[${index}][konten]" data-course-only-field data-initial-module-content rows="3" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600 resize-none" placeholder="Deskripsi modul..."></textarea>
                        </div>
                        <div data-initial-module-video-group>
                            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400">URL Video (opsional)</label>
                            <input type="url" name="initial_modules[${index}][video_url]" data-course-only-field data-initial-module-video class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div data-initial-module-duration-group>
                            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-400" data-initial-module-duration-label>Durasi (menit)</label>
                            <input type="number" name="initial_modules[${index}][durasi]" data-course-only-field data-initial-module-duration min="0" class="w-full rounded-lg bg-white px-3 py-2 text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="0">
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400" data-initial-module-hint></p>
                    </div>
                `;

                return wrapper;
            };

            const bindInitialModuleCardEvents = (card) => {
                if (!card) return;

                const typeSelect = card.querySelector('[data-initial-module-type]');
                const removeButton = card.querySelector('[data-remove-initial-module]');

                if (typeSelect) {
                    typeSelect.addEventListener('change', () => syncInitialModuleCard(card));
                }

                if (removeButton) {
                    removeButton.addEventListener('click', () => {
                        const cards = initialModulesContainer?.querySelectorAll('[data-initial-module-card]') || [];
                        if (cards.length <= 1) return;

                        card.remove();
                        renumberInitialModuleCards();
                    });
                }

                syncInitialModuleCard(card);
            };

            if (initialModulesContainer) {
                initialModulesContainer.querySelectorAll('[data-initial-module-card]').forEach((card) => {
                    bindInitialModuleCardEvents(card);
                });
                renumberInitialModuleCards();
            }

            if (addInitialModuleButton && initialModulesContainer) {
                addInitialModuleButton.addEventListener('click', () => {
                    const nextIndex = initialModulesContainer.querySelectorAll('[data-initial-module-card]').length;
                    const card = createInitialModuleCard(nextIndex);
                    initialModulesContainer.appendChild(card);
                    bindInitialModuleCardEvents(card);
                    renumberInitialModuleCards();
                });
            }

            // Learning Goals: dynamic add/remove rows
            const renumberLearningGoals = () => {
                if (!learningGoalsContainer) return;
                const cards = Array.from(learningGoalsContainer.querySelectorAll('[data-learning-goal-card]'));
                cards.forEach((card, idx) => {
                    const title = card.querySelector('h5');
                    if (title) title.textContent = `Tujuan ${idx + 1}`;
                    card.querySelectorAll('input, textarea').forEach((field) => {
                        const name = field.getAttribute('name');
                        if (!name) return;
                        field.setAttribute('name', name.replace(/learning_goals\[\d+\]/, `learning_goals[${idx}]`));
                    });
                    const removeBtn = card.querySelector('[data-remove-learning-goal]');
                    if (removeBtn) removeBtn.disabled = cards.length === 1;
                });
            };

            const createLearningGoalCard = (index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'learning-goal-card rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-800/60';
                wrapper.setAttribute('data-learning-goal-card', 'true');
                wrapper.innerHTML = `
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">Tujuan ${index + 1}</h5>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tulis singkat, mudah diingat, dan dapat diukur.</p>
                        </div>
                        <button type="button" class="remove-learning-goal inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-learning-goal>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs text-gray-500 dark:text-gray-400">Judul Tujuan <span class="text-red-400">*</span></label>
                            <input type="text" name="learning_goals[${index}][judul_goal]" data-course-only-field class="w-full rounded-lg bg-white px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600" placeholder="Contoh: Memahami konsep OOP">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-gray-500 dark:text-gray-400">Deskripsi (opsional)</label>
                            <textarea name="learning_goals[${index}][deskripsi]" data-course-only-field rows="2" class="w-full rounded-lg bg-white px-3 py-2 text-sm text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white dark:ring-gray-600 resize-none" placeholder="Mahasiswa mampu menjelaskan pilar OOP dan contoh implementasinya..."></textarea>
                        </div>
                    </div>
                `;
                return wrapper;
            };

            const bindLearningGoalCardEvents = (card) => {
                if (!card) return;
                const removeBtn = card.querySelector('[data-remove-learning-goal]');
                if (removeBtn) {
                    removeBtn.addEventListener('click', () => {
                        const cards = learningGoalsContainer?.querySelectorAll('[data-learning-goal-card]') || [];
                        if (cards.length <= 1) return;
                        card.remove();
                        renumberLearningGoals();
                    });
                }
            };

            if (learningGoalsContainer) {
                learningGoalsContainer.querySelectorAll('[data-learning-goal-card]').forEach((card) => {
                    bindLearningGoalCardEvents(card);
                });
                renumberLearningGoals();
            }

            if (addLearningGoalButton && learningGoalsContainer) {
                addLearningGoalButton.addEventListener('click', () => {
                    const nextIndex = learningGoalsContainer.querySelectorAll('[data-learning-goal-card]').length;
                    const card = createLearningGoalCard(nextIndex);
                    learningGoalsContainer.appendChild(card);
                    bindLearningGoalCardEvents(card);
                    renumberLearningGoals();
                });
            }

            // Handle submit buttons — set status based on which button was clicked
            const form = document.getElementById('buatKursusForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const thumbnailInput = document.getElementById('thumbnail-input');
                    if (thumbnailInput && !isValidThumbnailFile(thumbnailInput)) {
                        e.preventDefault();
                        e.stopPropagation();
                        return;
                    }

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

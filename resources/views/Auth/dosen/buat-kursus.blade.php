<x-layouts.dosen title="Buat Kursus Baru" active="buat-kursus">
    <div class="max-w-3xl mx-auto">
        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data" id="buatKursusForm">
            @csrf
            
            {{-- Page Header + Action Buttons --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
                <div class="mb-4">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white">Buat Kursus Baru</h1>
                    <p class="text-sm text-blue-500">Lengkapi informasi berikut untuk membuat kursus baru.</p>
                </div>
                
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('dosen.kursus') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                        Batal
                    </a>
                    <button type="submit" name="status_btn" value="draft" class="px-4 py-2 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 hover:bg-blue-50 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition">
                        Simpan Draft
                    </button>
                    <button type="submit" name="status_btn" value="aktif" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition shadow-sm shadow-blue-500/25">
                        Buat Kursus
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
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

                            {{-- Hidden Kode Course (Auto-generated) --}}
                            <input type="hidden" name="kode_course" value="{{ 'C-' . strtoupper(substr(md5(time()), 0, 6)) }}">

                            {{-- Deskripsi Kursus --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Deskripsi Kursus</label>
                                <textarea name="deskripsi" id="add_deskripsi" rows="3" placeholder="Jelaskan tentang kursus ini..." class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Kategori & Tingkat Kesulitan --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kategori Kursus</label>
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
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Estimasi Waktu Belajar (Jam)</label>
                                    <input type="number" name="estimasi_waktu" id="add_estimasi_waktu" min="0" placeholder="20" value="{{ old('estimasi_waktu', 20) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div></div>
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
                                            <input type="file" name="thumbnail" id="thumbnail-input" accept="image/jpeg,image/png,image/jpg" class="hidden" onchange="previewThumbnail(this)">
                                        </label>
                                        <p class="text-xs text-gray-400 mt-1">Maksimal 2MB, JPG/PNG</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Struktur Modul Awal --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5" x-data="{ selectedContent: '{{ old('konten_tipe', '') }}' }">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 text-xs font-bold flex items-center justify-center">2</span>
                                Struktur Modul Awal
                            </span>
                        </h4>

                        {{-- Content Type Selection Cards --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                            <button type="button" @click="selectedContent = selectedContent === 'video' ? '' : 'video'" :class="selectedContent === 'video' ? 'border-red-400 bg-red-50 dark:bg-red-900/20 ring-1 ring-red-400' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'" class="flex flex-col items-center gap-2 p-3 rounded-xl border transition cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>
                                <div class="text-center"><p class="font-medium text-xs text-gray-800 dark:text-gray-200">Video</p><p class="text-[10px] text-gray-400">YouTube Playlist</p></div>
                            </button>
                            <a href="{{ route('dosen.kelola-quiz') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                                <div class="text-center"><p class="font-medium text-xs text-gray-800 dark:text-gray-200">Quiz</p><p class="text-[10px] text-gray-400">Kuis interaktif</p></div>
                            </a>
                            <a href="{{ route('dosen.kelola-bacaan') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 transition cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                                <div class="text-center"><p class="font-medium text-xs text-gray-800 dark:text-gray-200">Bacaan</p><p class="text-[10px] text-gray-400">Materi teks/artikel</p></div>
                            </a>
                            <a href="{{ route('dosen.kelola-tugas') }}" class="flex flex-col items-center gap-2 p-3 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition cursor-pointer">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center"><svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg></div>
                                <div class="text-center"><p class="font-medium text-xs text-gray-800 dark:text-gray-200">Tugas</p><p class="text-[10px] text-gray-400">Buat assignment</p></div>
                            </a>
                        </div>

                        {{-- YouTube Playlist URL (shown when Video is selected) --}}
                        <div x-show="selectedContent === 'video'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="p-4 rounded-xl border border-red-200 dark:border-red-800/30 bg-red-50/50 dark:bg-red-900/10 mb-4" style="display: none;">
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Konten Video</p>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">YouTube Playlist URL</label>
                                <input type="url" name="youtube_playlist" id="add_youtube_playlist" placeholder="https://www.youtube.com/playlist?list=..." value="{{ old('youtube_playlist') }}" class="w-full px-3 py-2.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <p class="text-xs text-gray-400 mt-1">Masukkan URL playlist YouTube untuk kursus ini.</p>
                            </div>
                        </div>

                        {{-- Empty state (shown when no content selected) --}}
                        <div x-show="selectedContent === ''" class="flex flex-col items-center justify-center py-8 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-700/30">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-xs text-center">Pilih tipe konten di atas untuk memulai.</p>
                        </div>
                    </div>

                    {{-- 3. Pengaturan Kursus --}}
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-bold flex items-center justify-center">3</span>
                            Pengaturan Kursus
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-3">
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
                                    <select name="kategori" id="add_kategori" required class="w-full px-3 py-2.5 pr-10 appearance-none bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="kursus" {{ old('kategori') === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                        <option value="webinar" {{ old('kategori') === 'webinar' ? 'selected' : '' }}>Webinar</option>
                                        <option value="tiket" {{ old('kategori') === 'tiket' ? 'selected' : '' }}>Tiket</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                                @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-3 gap-3 items-end">
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

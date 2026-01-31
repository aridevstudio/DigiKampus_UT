<x-layouts.dosen title="Buat Kursus Baru" active="buat-kursus">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Kursus Baru</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Lengkapi informasi berikut untuk membuat kursus baru Anda.</p>
        </div>

        <form action="{{ route('dosen.kursus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                {{-- 1. Informasi Dasar Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Informasi Dasar Kursus</h2>
                    
                    <div class="space-y-4">
                        {{-- Judul Kursus --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Kursus</label>
                            <input type="text" name="nama_course" value="{{ old('nama_course') }}" required placeholder="Masukkan judul kursus" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Hidden Kode Course (Auto-generated) --}}
                        <input type="hidden" name="kode_course" value="{{ 'C-' . strtoupper(substr(md5(time()), 0, 6)) }}">

                        {{-- Deskripsi Kursus --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Kursus</label>
                            <textarea name="deskripsi" rows="3" placeholder="Jelaskan tentang kursus Anda..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none">{{ old('deskripsi') }}</textarea>
                        </div>

                        {{-- Kategori & Tingkat Kesulitan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategori Kursus</label>
                                <div class="relative">
                                    <select name="id_jurusan" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($jurusans ?? [] as $jurusan)
                                        <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan') == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tingkat Kesulitan</label>
                                <div class="relative">
                                    <select name="level" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                                        <option value="">Pilih Tingkat</option>
                                        <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                        <option value="Menengah" {{ old('level') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                        <option value="Mahir" {{ old('level') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                    </select>
                                    <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Estimasi Waktu & Thumbnail --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimasi Waktu Belajar (Jam)</label>
                                <input type="number" name="estimasi_waktu" value="{{ old('estimasi_waktu', 20) }}" min="0" placeholder="20" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Thumbnail Kursus</label>
                                <div class="relative">
                                    <input type="file" name="thumbnail" id="thumbnail-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div id="thumbnail-preview" class="flex flex-col items-center justify-center w-full h-[42px] px-4 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition text-center">
                                        <div id="file-placeholder" class="flex items-center gap-2 text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                            <span class="text-sm">Klik untuk upload atau drag & drop</span>
                                        </div>
                                        <p id="file-name" class="text-sm text-gray-800 dark:text-gray-200 font-medium truncate w-full hidden"></p>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG hingga 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Struktur Modul Awal --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Struktur Modul Awal</h2>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg flex items-center gap-2 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah Modul Pertama
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            {{-- Dropdown Menu --}}
                            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 z-50 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-2" style="display: none;">
                                <p class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Pilih Tipe Konten</p>
                                
                                <a href="{{ route('dosen.kelola-video') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Video</p>
                                        <p class="text-xs text-gray-400">Unggah video pembelajaran</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('dosen.kelola-quiz') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Quiz</p>
                                        <p class="text-xs text-gray-400">Buat kuis interaktif</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('dosen.kelola-bacaan') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 transition">
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Bacaan</p>
                                        <p class="text-xs text-gray-400">Tambah materi teks/artikel</p>
                                    </div>
                                </a>
                                
                                <a href="{{ route('dosen.kelola-tugas') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-medium">Tugas</p>
                                        <p class="text-xs text-gray-400">Buat tugas/assignment</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-center justify-center py-10 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-700/30">
                        <svg class="w-14 h-14 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400 text-sm text-center">Belum ada modul. Klik "Tambah Modul Pertama" untuk memulai.</p>
                    </div>
                </div>

                {{-- 3. Pengaturan Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pengaturan Kursus</h2>
                    
                    <div class="space-y-4">
                        {{-- Status Kursus & Akses Publik - Side by Side --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">Status Kursus</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Aktif atau simpan sebagai draft</p>
                                </div>
                                <input type="hidden" name="status" id="status_input" value="draft">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="status_toggle" class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white text-sm">Akses Publik</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Dapat dilihat oleh semua mahasiswa</p>
                                </div>
                                <input type="hidden" name="akses_publik" value="0">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="akses_publik" value="1" class="sr-only peer" checked>
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                            </div>
                        </div>

                        {{-- Sertifikat Penyelesaian --}}
                        <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm">Sertifikat Penyelesaian</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Berikan sertifikat setelah selesai</p>
                            </div>
                            <input type="hidden" name="sertifikat" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sertifikat" value="1" class="sr-only peer">
                                <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- 4. Pricing & Akses Kursus --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pricing & Akses Kursus</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Harga Kursus (Rp)</label>
                                <input type="number" name="harga" id="harga_input" value="{{ old('harga', 0) }}" min="0" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Diskon (%)</label>
                                <input type="number" name="diskon" id="diskon_input" value="{{ old('diskon', 0) }}" min="0" max="100" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div class="flex items-center gap-3 py-2.5">
                                <input type="hidden" name="tipe" id="tipe_input" value="berbayar">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="gratis_toggle" class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                </label>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kursus Gratis</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                    <button type="button" class="flex items-center gap-2 px-4 py-2.5 text-blue-600 hover:text-blue-700 border border-blue-200 dark:border-blue-500/30 bg-white dark:bg-gray-800 font-medium text-sm rounded-xl transition hover:bg-blue-50 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        Pratinjau Sekilas
                    </button>
                    
                    <div class="flex items-center gap-3">
                        <button type="submit" onclick="setStatus('draft')" class="px-5 py-2.5 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30 font-medium rounded-xl hover:bg-blue-50 dark:hover:bg-gray-600 transition text-sm">
                            Simpan Sebagai Draft
                        </button>
                        <button type="submit" onclick="setStatus('aktif')" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition shadow-sm text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            Buat Kursus
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusToggle = document.getElementById('status_toggle');
            const statusInput = document.getElementById('status_input');
            const gratisToggle = document.getElementById('gratis_toggle');
            const tipeInput = document.getElementById('tipe_input');
            const hargaInput = document.getElementById('harga_input');
            const diskonInput = document.getElementById('diskon_input');
            const thumbnailInput = document.getElementById('thumbnail-input');
            const filePlaceholder = document.getElementById('file-placeholder');
            const fileNameDisplay = document.getElementById('file-name');

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

            // Thumbnail Preview
            if (thumbnailInput) {
                thumbnailInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        if (!file.type.startsWith('image/')) {
                            alert('Harap pilih file gambar (PNG, JPG)');
                            this.value = '';
                            return;
                        }
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file maksimal 2MB');
                            this.value = '';
                            return;
                        }
                        filePlaceholder.classList.add('hidden');
                        fileNameDisplay.classList.remove('hidden');
                        fileNameDisplay.innerHTML = `<span class="flex items-center gap-2"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>${file.name}</span>`;
                    } else {
                        filePlaceholder.classList.remove('hidden');
                        fileNameDisplay.classList.add('hidden');
                    }
                });
            }

            // Set Status Function
            window.setStatus = function(status) {
                statusInput.value = status;
                if (statusToggle) {
                    statusToggle.checked = (status === 'aktif');
                }
            };

            // Form Submit Loading
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = e.submitter;
                    if (submitBtn && submitBtn.type === 'submit') {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `<svg class="animate-spin w-4 h-4 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Menyimpan...`;
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.dosen>

<x-layouts.dosen title="Kelola Video Pembelajaran" active="buat-kursus">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
        <a href="{{ route('dosen.dashboard') }}" class="hover:text-blue-500">Dashboard Dosen</a>
        <span>›</span>
        <a href="{{ route('dosen.kursus') }}" class="hover:text-blue-500">Kursus Saya</a>
        <span>›</span>
        <span class="text-blue-500">Kelola Video</span>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Video Pembelajaran</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Unggah dan atur video untuk modul kursus</p>
    </div>

    <form action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - 2/3 width --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Upload Video Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Upload Video</h2>
                    
                    <div id="videoDropZone" class="border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 transition cursor-pointer" onclick="document.getElementById('videoInput').click()">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">Seret dan lepas video di sini</p>
                            <p class="text-sm text-gray-400 mb-4">atau klik untuk memilih file</p>
                            <button type="button" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                                Pilih File Video
                            </button>
                        </div>
                        <input type="file" id="videoInput" name="video" accept="video/*" class="hidden">
                    </div>
                    
                    <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Format: MP4, maksimal 500MB
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Resolusi disarankan: 1080p (1920×1080)
                        </span>
                    </div>
                </div>

                {{-- Informasi Video Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Video</h2>
                    
                    <div class="space-y-4">
                        {{-- Judul Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Video <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" required placeholder="Masukkan judul video" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Pilih Modul --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Modul</label>
                                <div class="relative">
                                    <select name="modul" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition appearance-none pr-10">
                                        <option value="">Pilih Modul</option>
                                        <option value="1">Modul 1: Pengenalan</option>
                                        <option value="2">Modul 2: Dasar-dasar</option>
                                        <option value="3">Modul 3: Lanjutan</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            
                            {{-- Urutan Video --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Urutan Video</label>
                                <input type="number" name="urutan" value="1" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                        
                        {{-- Durasi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Durasi</label>
                            <input type="text" name="durasi" value="15:30" placeholder="00:00" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        {{-- Deskripsi Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Video</label>
                            <textarea name="deskripsi" rows="4" placeholder="Masukkan deskripsi video..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Tambahan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pengaturan Tambahan</h2>
                    
                    <div class="space-y-4">
                        {{-- Video Gratis Toggle --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Video Gratis (Preview)</p>
                                <p class="text-sm text-gray-400">Izinkan akses tanpa berlangganan</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="gratis" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                        
                        {{-- Izinkan Komentar Toggle --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-700 dark:text-gray-300">Izinkan Komentar</p>
                                <p class="text-sm text-gray-400">Mahasiswa dapat menambahkan komentar</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="komentar" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                        
                        {{-- Wajib Sebelum Kuis --}}
                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="wajib_sebelum_kuis" id="wajibKuis" class="w-4 h-4 text-blue-500 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="wajibKuis" class="text-sm text-gray-700 dark:text-gray-300">Tandai sebagai video wajib sebelum kuis</label>
                        </div>
                        
                        {{-- Jadwalkan Tayang --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jadwalkan Tayang</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="relative">
                                    <input type="date" name="tanggal_tayang" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>
                                <div class="relative">
                                    <input type="time" name="waktu_tayang" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - 1/3 width --}}
            <div class="space-y-6">
                {{-- Thumbnail Video --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thumbnail Video</h2>
                    
                    <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center mb-4">
                        <div class="text-center">
                            <svg class="w-10 h-10 text-gray-300 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-sm text-gray-400">Preview Thumbnail</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <button type="button" class="w-full px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Unggah Thumbnail
                        </button>
                        <button type="button" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Ambil dari Frame Video
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-400 mt-3 text-center">Ukuran: 1280×720px, Format: JPG/PNG</p>
                </div>

                {{-- Preview Video --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Preview Video</h2>
                    
                    <div class="aspect-video bg-gray-900 rounded-xl flex items-center justify-center relative overflow-hidden">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-white/50 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <p class="text-sm text-white/50">Preview untuk mahasiswa</p>
                        </div>
                    </div>
                    
                    {{-- Video Controls --}}
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-xs text-gray-400 mb-2">
                            <span>0:00</span>
                            <span>15:30</span>
                        </div>
                        <div class="w-full h-1 bg-gray-200 dark:bg-gray-700 rounded-full mb-4">
                            <div class="h-full w-0 bg-blue-500 rounded-full"></div>
                        </div>
                        <div class="flex items-center justify-center gap-4">
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h2v12H6zm3.5 6l8.5 6V6z"/></svg>
                            </button>
                            <button type="button" class="p-3 bg-blue-500 hover:bg-blue-600 text-white rounded-full transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                            </button>
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="button" onclick="history.back()" class="px-5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-medium transition">
                Batal
            </button>
            <button type="submit" name="action" value="draft" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                Simpan Draft
            </button>
            <button type="submit" name="action" value="publish" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Publikasikan Video
            </button>
        </div>
    </form>
</x-layouts.dosen>

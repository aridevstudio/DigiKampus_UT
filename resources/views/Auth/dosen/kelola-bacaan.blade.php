<x-layouts.dosen title="Kelola Bacaan" active="buat-kursus">
    <div x-data="bacaanManager()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Header --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Bacaan</h1>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola materi bacaan untuk modul pembelajaran</p>
                    </div>
                </div>

                {{-- Informasi Bacaan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Informasi Bacaan</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Materi</label>
                            <input type="text" x-model="formData.judul" placeholder="Masukkan judul materi bacaan" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Kursus</label>
                                <div class="relative">
                                    <select x-model="formData.kursus" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm appearance-none">
                                        <option value="">Pilih kursus</option>
                                        <option value="1">Pemrograman Web</option>
                                        <option value="2">Basis Data</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Modul</label>
                                <div class="relative">
                                    <select x-model="formData.modul" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm appearance-none">
                                        <option value="">Pilih modul</option>
                                        <option value="1">Modul 1: Pengenalan</option>
                                        <option value="2">Modul 2: Dasar-dasar</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Urutan dalam Modul</label>
                                <input type="number" x-model="formData.urutan" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimasi Waktu Baca (menit)</label>
                                <input type="number" x-model="formData.durasi" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ringkasan</label>
                            <textarea x-model="formData.ringkasan" rows="2" placeholder="Ringkasan singkat materi bacaan..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Konten Bacaan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Konten Bacaan</h2>
                    
                    {{-- Rich Text Editor --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex-wrap">
                            <button type="button" @click="formatText('bold')" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"><span class="font-bold text-sm">B</span></button>
                            <button type="button" @click="formatText('italic')" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"><span class="italic text-sm">I</span></button>
                            <button type="button" @click="formatText('underline')" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"><span class="underline text-sm">U</span></button>
                            <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            </button>
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/></svg>
                            </button>
                            <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h8m-8 4h16M4 18h8"/></svg>
                            </button>
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </button>
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </button>
                            <div class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                            <select class="px-2 py-1 text-sm bg-transparent border border-gray-300 dark:border-gray-600 rounded">
                                <option>Heading 1</option>
                                <option>Heading 2</option>
                                <option>Heading 3</option>
                                <option selected>Paragraph</option>
                            </select>
                        </div>
                        <textarea x-model="formData.konten" rows="12" placeholder="Tulis konten materi bacaan di sini...

Anda dapat menggunakan format teks seperti:
• Heading untuk judul bagian
• Bold, Italic untuk penekanan
• Bullet points untuk daftar
• Link untuk referensi
• Gambar untuk ilustrasi" class="w-full px-4 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none resize-none"></textarea>
                    </div>
                    
                    <p class="text-xs text-gray-400 mt-2">Tip: Gunakan toolbar di atas untuk memformat teks Anda</p>
                </div>

                {{-- Lampiran & Sumber --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Lampiran & Sumber Referensi</h2>
                        <button type="button" @click="addAttachment()" class="text-blue-500 hover:text-blue-600 text-sm font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Lampiran
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(item, index) in formData.attachments" :key="index">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <input type="text" x-model="item.nama" placeholder="Nama file/link" class="w-full px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm mb-1">
                                    <input type="text" x-model="item.url" placeholder="URL atau upload file" class="w-full px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                </div>
                                <button type="button" @click="removeAttachment(index)" class="p-2 text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                        
                        <div x-show="formData.attachments.length === 0" class="text-center py-6 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            <p class="text-sm">Belum ada lampiran. Klik "Tambah Lampiran" untuk menambahkan.</p>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Tambahan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pengaturan Tambahan</h2>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Wajib Dibaca</p>
                                <p class="text-xs text-gray-400">Mahasiswa harus menyelesaikan bacaan ini untuk lanjut</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="formData.wajib" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Tampilkan Progress Bar</p>
                                <p class="text-xs text-gray-400">Tunjukkan progress membaca ke mahasiswa</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="formData.showProgress" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Izinkan Komentar</p>
                                <p class="text-xs text-gray-400">Mahasiswa dapat bertanya atau berdiskusi</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="formData.allowComments" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Preview Bacaan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Tampilan yang akan dilihat mahasiswa</p>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        {{-- Preview Header --}}
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Materi Bacaan</span>
                            </div>
                            <h3 class="font-bold text-gray-900 dark:text-white" x-text="formData.judul || 'Judul Materi'"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="formData.ringkasan || 'Ringkasan materi akan muncul di sini...'"></p>
                        </div>
                        
                        {{-- Preview Info --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="formData.durasi + ' menit baca'"></span>
                            </div>
                            <div class="flex items-center gap-1 text-gray-500 dark:text-gray-400" x-show="formData.attachments.length > 0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span x-text="formData.attachments.length + ' lampiran'"></span>
                            </div>
                        </div>
                        
                        {{-- Preview Content --}}
                        <div class="p-4">
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400" x-html="formData.konten ? formData.konten.substring(0, 200) + '...' : 'Konten bacaan akan muncul di sini...'"></div>
                        </div>
                        
                        {{-- Preview Progress --}}
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700" x-show="formData.showProgress">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-500 dark:text-gray-400">Progress Membaca</span>
                                <span class="text-gray-500 dark:text-gray-400">0%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full">
                                <div class="h-full w-0 bg-green-500 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-400 mt-4 text-center">* Preview akan diperbarui otomatis saat Anda mengisi form</p>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="button" onclick="history.back()" class="px-5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-medium transition">
                Batal
            </button>
            <button type="button" @click="saveDraft()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                Simpan Draft
            </button>
            <button type="button" @click="publish()" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Publikasikan Bacaan
            </button>
        </div>

        {{-- Toast Notification --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50" style="display: none;">
            <div class="px-4 py-3 rounded-xl shadow-lg flex items-center gap-3" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="toast.message"></span>
            </div>
        </div>
    </div>

    <script>
        function bacaanManager() {
            return {
                toast: { show: false, message: '', type: 'success' },
                
                formData: {
                    judul: '',
                    kursus: '',
                    modul: '',
                    urutan: 1,
                    durasi: 10,
                    ringkasan: '',
                    konten: '',
                    attachments: [],
                    wajib: true,
                    showProgress: true,
                    allowComments: true
                },
                
                formatText(type) {
                    // Placeholder for rich text formatting
                    this.showToast(`Format ${type} diterapkan (demo)`, 'success');
                },
                
                addAttachment() {
                    this.formData.attachments.push({ nama: '', url: '' });
                },
                
                removeAttachment(index) {
                    this.formData.attachments.splice(index, 1);
                },
                
                saveDraft() {
                    this.showToast('Draft berhasil disimpan (demo)', 'success');
                },
                
                publish() {
                    if (!this.formData.judul) {
                        this.showToast('Judul materi tidak boleh kosong', 'error');
                        return;
                    }
                    if (!this.formData.konten) {
                        this.showToast('Konten bacaan tidak boleh kosong', 'error');
                        return;
                    }
                    this.showToast('Bacaan berhasil dipublikasikan (demo)', 'success');
                },
                
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            }
        }
    </script>
</x-layouts.dosen>

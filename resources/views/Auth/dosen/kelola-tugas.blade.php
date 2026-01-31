<x-layouts.dosen title="Kelola Tugas" active="buat-kursus">
    <div x-data="tugasManager()">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informasi Dasar Tugas --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Informasi Dasar Tugas</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Tugas Akhir</label>
                            <input type="text" x-model="formData.judul" placeholder="Masukkan judul tugas akhir" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pilih Modul (Opsional)</label>
                                <div class="relative">
                                    <select x-model="formData.modul" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm appearance-none">
                                        <option value="">Semua modul</option>
                                        <option value="1">Modul 1: Pengenalan</option>
                                        <option value="2">Modul 2: Dasar-dasar</option>
                                    </select>
                                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Tugas</label>
                            {{-- Simple Rich Text Toolbar --}}
                            <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                                <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                    <button type="button" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"><span class="font-bold text-sm">B</span></button>
                                    <button type="button" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition"><span class="italic text-sm">I</span></button>
                                    <button type="button" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h10M4 18h10"/></svg>
                                    </button>
                                    <button type="button" class="p-1.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    </button>
                                </div>
                                <textarea x-model="formData.deskripsi" rows="4" placeholder="Deskripsikan tugas akhir yang akan dikerjakan mahasiswa..." class="w-full px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Instruksi & Ketentuan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Instruksi & Ketentuan</h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Instruksi Pengerjaan</label>
                            <textarea x-model="formData.instruksi" rows="3" placeholder="Berikan instruksi detail tentang cara mengerjakan tugas..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Ketentuan Pengumpulan</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Format File yang Diizinkan</label>
                                    <div class="flex flex-wrap gap-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" x-model="formData.formats" value="pdf" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">PDF</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" x-model="formData.formats" value="docx" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">DOCX</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" x-model="formData.formats" value="zip" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">ZIP</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" x-model="formData.formats" value="link" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300">Link</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Ukuran Maksimal File</label>
                                    <div class="relative w-32">
                                        <select x-model="formData.maxSize" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm appearance-none">
                                            <option value="5">5 MB</option>
                                            <option value="10">10 MB</option>
                                            <option value="25">25 MB</option>
                                            <option value="50">50 MB</option>
                                        </select>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="formData.multipleFiles" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Izinkan unggah lebih dari 1 file</span>
                                    </label>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="formData.allowLinks" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Izinkan link Google Drive / GitHub</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Deadline --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pengaturan Deadline</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai</label>
                                <div class="relative">
                                    <input type="datetime-local" x-model="formData.startDate" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Deadline</label>
                                <div class="relative">
                                    <input type="datetime-local" x-model="formData.deadline" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" x-model="formData.lateSubmission" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Izinkan pengumpulan setelah deadline</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" x-model="formData.showCountdown" checked class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Tampilkan countdown ke mahasiswa</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Penilaian --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pengaturan Penilaian</h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bobot Nilai (%)</label>
                                <input type="number" x-model="formData.bobot" min="0" max="100" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nilai Maksimal</label>
                                <input type="number" x-model="formData.nilaiMax" min="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Metode Penilaian</label>
                            <div class="relative">
                                <select x-model="formData.metodePenilaian" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm appearance-none">
                                    <option value="manual">Manual oleh dosen</option>
                                    <option value="peer">Peer review</option>
                                    <option value="auto">Otomatis</option>
                                </select>
                                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        
                        {{-- Rubrik Penilaian --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Rubrik Penilaian (Opsional)</label>
                                <button type="button" @click="addKriteria()" class="text-blue-500 hover:text-blue-600 text-sm font-medium flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah Kriteria
                                </button>
                            </div>
                            
                            <div class="space-y-2">
                                <template x-for="(kriteria, index) in formData.rubrik" :key="index">
                                    <div class="flex items-center gap-3">
                                        <input type="text" x-model="kriteria.nama" placeholder="Kriteria penilaian" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        <input type="number" x-model="kriteria.persentase" placeholder="%" class="w-20 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm text-center">
                                        <span class="text-gray-400 text-sm">%</span>
                                        <button type="button" @click="removeKriteria(index)" class="p-1.5 text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                                
                                <div x-show="formData.rubrik.length === 0" class="text-sm text-gray-400 py-2">
                                    Belum ada kriteria. Klik "Tambah Kriteria" untuk menambahkan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Preview --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 sticky top-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Preview Tugas</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Tampilan yang akan dilihat mahasiswa</p>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-4">
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white" x-text="'Tugas Akhir: ' + (formData.judul || 'Project Website')"></h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="formData.deskripsi || 'Buat website portfolio menggunakan HTML, CSS, dan JavaScript sesuai dengan materi yang telah dipelajari.'"></p>
                        </div>
                        
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span x-text="formData.deadline ? formatDate(formData.deadline) : 'Deadline: 15 Des 2026'"></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>23:59 WIB</span>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Format yang diizinkan:</p>
                            <div class="flex gap-2">
                                <template x-for="format in formData.formats" :key="format">
                                    <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded uppercase" x-text="format"></span>
                                </template>
                                <span x-show="formData.formats.length === 0" class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 rounded">-</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Bobot: <span class="font-medium text-gray-900 dark:text-white" x-text="formData.bobot + '%'"></span></span>
                            <span class="text-gray-500 dark:text-gray-400">Max: <span class="font-medium text-gray-900 dark:text-white" x-text="formData.nilaiMax + ' poin'"></span></span>
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
                Simpan sebagai Draft
            </button>
            <button type="button" @click="publish()" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Publikasikan Tugas
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
        function tugasManager() {
            return {
                toast: { show: false, message: '', type: 'success' },
                
                formData: {
                    judul: '',
                    kursus: '',
                    modul: '',
                    deskripsi: '',
                    instruksi: '',
                    formats: ['pdf'],
                    maxSize: '5',
                    multipleFiles: false,
                    allowLinks: false,
                    startDate: '',
                    deadline: '',
                    lateSubmission: false,
                    showCountdown: true,
                    bobot: 30,
                    nilaiMax: 100,
                    metodePenilaian: 'manual',
                    rubrik: []
                },
                
                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return 'Deadline: ' + date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                },
                
                addKriteria() {
                    this.formData.rubrik.push({ nama: '', persentase: 25 });
                },
                
                removeKriteria(index) {
                    this.formData.rubrik.splice(index, 1);
                },
                
                saveDraft() {
                    this.showToast('Draft berhasil disimpan (demo)', 'success');
                },
                
                publish() {
                    if (!this.formData.judul) {
                        this.showToast('Judul tugas tidak boleh kosong', 'error');
                        return;
                    }
                    this.showToast('Tugas berhasil dipublikasikan (demo)', 'success');
                },
                
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            }
        }
    </script>
</x-layouts.dosen>

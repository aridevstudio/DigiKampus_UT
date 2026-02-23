<x-layouts.dosen title="Input Kuis" active="buat-kursus">
    <div x-data="quizManager()" x-init="init()" class="pb-20">
        {{-- Header with Filters --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Input Kuis</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola soal kuis untuk modul pembelajaran</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[200px]">
                    <select x-model="selectedCourseId" class="w-full px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none">
                        <option value="">-- Pilih Kursus --</option>
                        <template x-for="course in courses" :key="course.id">
                            <option :value="course.id" x-text="course.nama"></option>
                        </template>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                
                <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Soal
                </button>
            </div>
        </div>

        {{-- Quiz Info Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Kuis</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Kuis <span class="text-red-500">*</span></label>
                    <input type="text" x-model="form.judul_modul" placeholder="Contoh: Kuis Evaluasi Bab 1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Durasi Pengerjaan (Menit)</label>
                    <input type="number" x-model="form.durasi" min="5" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>
        </div>

        <form @submit.prevent="saveQuiz">
            <div class="space-y-6">
                {{-- Daftar Soal Kuis --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Daftar Soal Kuis</h2>
                    
                    <div class="space-y-3" id="questionsList">
                        <template x-for="(question, index) in questions" :key="question.id">
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400" x-text="'≡ Soal ' + (index + 1)"></span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full" 
                                          :class="question.type === 'pilihan_ganda' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
                                          x-text="question.type === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Benar-Salah'"></span>
                                    <span class="text-xs text-gray-400" x-text="'Bobot: ' + question.bobot + ' poin'"></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    {{-- Edit --}}
                                    <button type="button" @click="editQuestion(index)" class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    {{-- Duplicate --}}
                                    <button type="button" @click="duplicateQuestion(index)" class="p-1.5 text-gray-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 rounded transition" title="Duplikat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                    {{-- Delete --}}
                                    <button type="button" @click="deleteQuestion(index)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    {{-- Expand --}}
                                    <button type="button" @click="previewIndex = index" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded transition" title="Preview">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        {{-- Empty State --}}
                        <div x-show="questions.length === 0" class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p>Belum ada soal. Klik "Tambah Soal" untuk memulai.</p>
                        </div>
                        
                        {{-- Add Question Button --}}
                        <button type="button" @click="showAddModal = true" class="w-full py-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-gray-400 hover:text-blue-500 hover:border-blue-300 dark:hover:border-blue-700 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Soal Baru</span>
                        </button>
                    </div>
                </div>

                {{-- Preview Kuis --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Preview Kuis</h2>
                    
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6">
                        {{-- Quiz Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="form.judul_modul || 'Judul Kuis Preview'"></h3>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="formatTime(form.durasi * 60)"></span>
                            </div>
                        </div>
                        
                        {{-- Progress --}}
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 dark:text-gray-400" x-text="'Soal ' + (previewIndex + 1) + ' dari ' + questions.length"></span>
                            {{-- total bobot calculation optional --}}
                        </div>
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full mb-6">
                            <div class="h-full bg-blue-500 rounded-full transition-all" :style="'width: ' + ((previewIndex + 1) / Math.max(questions.length, 1) * 100) + '%'"></div>
                        </div>
                        
                        {{-- Question --}}
                        <div class="mb-6" x-show="questions.length > 0">
                            <p class="text-gray-900 dark:text-white font-medium mb-4" x-text="currentQuestion?.pertanyaan || 'Pertanyaan akan muncul di sini'"></p>
                            
                            <div class="space-y-3">
                                <template x-for="(option, optIndex) in currentQuestion?.options || []" :key="optIndex">
                                    <label class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-700 cursor-pointer transition" :class="selectedAnswer === optIndex ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''">
                                        <input type="radio" :name="'preview_answer_' + previewIndex" :value="optIndex" x-model="selectedAnswer" class="w-4 h-4 text-blue-500 border-gray-300 focus:ring-blue-500">
                                        <span class="text-gray-700 dark:text-gray-300" x-text="String.fromCharCode(65 + optIndex) + '. ' + option"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                        
                        {{-- Empty Preview --}}
                        <div x-show="questions.length === 0" class="py-8 text-center text-gray-400">
                            <p>Tambahkan soal untuk melihat preview</p>
                        </div>
                        
                        {{-- Navigation --}}
                        <div class="flex items-center justify-between" x-show="questions.length > 0">
                            <button type="button" @click="prevQuestion()" :disabled="previewIndex === 0" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-medium flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Sebelumnya
                            </button>
                            <button type="button" @click="nextQuestion()" :disabled="previewIndex >= questions.length - 1" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl flex items-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                Selanjutnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                <button type="button" onclick="history.back()" class="px-5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-medium transition">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting || !selectedCourseId" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Publikasikan Kuis'"></span>
                </button>
            </div>
        </form>

        {{-- Add/Edit Question Modal --}}
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="closeModal()"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5" x-text="editingIndex !== null ? 'Edit Soal' : 'Tambah Soal Baru'"></h3>
                    
                    <div class="space-y-4">
                        {{-- Tipe Soal --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipe Soal</label>
                            <select x-model="modalForm.type" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="benar_salah">Benar-Salah</option>
                            </select>
                        </div>
                        
                        {{-- Pertanyaan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan</label>
                            <textarea x-model="modalForm.pertanyaan" rows="3" placeholder="Masukkan pertanyaan..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                        </div>
                        
                        {{-- Options --}}
                        <div x-show="modalForm.type === 'pilihan_ganda'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opsi Jawaban</label>
                            <div class="space-y-2">
                                <template x-for="(opt, i) in modalForm.options" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 w-6" x-text="String.fromCharCode(65 + i) + '.'"></span>
                                        <input type="text" x-model="modalForm.options[i]" :placeholder="'Opsi ' + String.fromCharCode(65 + i)" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        <input type="radio" :name="'correct_answer'" :value="i" x-model="modalForm.correctAnswer" class="w-4 h-4 text-green-500" title="Jawaban benar">
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        {{-- Benar/Salah --}}
                        <div x-show="modalForm.type === 'benar_salah'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jawaban Benar</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="benar_salah" value="true" x-model="modalForm.correctAnswer" class="w-4 h-4 text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Benar</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="benar_salah" value="false" x-model="modalForm.correctAnswer" class="w-4 h-4 text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Salah</span>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Bobot --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bobot (poin)</label>
                            <input type="number" x-model="modalForm.bobot" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm font-medium">Batal</button>
                        <button type="button" @click="saveQuestion()" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast Notification --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-50" style="display: none;">
            <div class="px-4 py-3 rounded-xl shadow-lg flex items-center gap-3" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                <span x-text="toast.message"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quizManager', () => ({
                courses: [],
                isLoading: false,
                isSubmitting: false,
                selectedCourseId: @json(request('course_id', '')),
                form: {
                    judul_modul: @json(request('modul_judul', '')),
                    durasi: @json(request('modul_durasi', 15)),
                    tipe: 'kuis',
                    konten: ''
                },
                
                // State for Quiz Content
                questions: [],
                
                // Preview state
                previewIndex: 0,
                selectedAnswer: null,

                // Modal state
                showAddModal: false,
                editingIndex: null,
                modalForm: {
                    type: 'pilihan_ganda',
                    pertanyaan: '',
                    options: ['', '', '', ''],
                    correctAnswer: 0,
                    bobot: 10
                },

                toast: { show: false, message: '', type: 'success' },

                init() {
                    this.fetchCourses();
                },

                async fetchCourses() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/dosen/api/courses?sort=terbaru&per_page=100', {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.courses = data.data.courses;
                        }
                    } catch (error) {
                        console.error('Error fetching courses:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                },
                
                get currentQuestion() {
                    return this.questions[this.previewIndex] || null;
                },

                nextQuestion() {
                    if (this.previewIndex < this.questions.length - 1) {
                        this.previewIndex++;
                        this.selectedAnswer = null;
                    }
                },
                
                prevQuestion() {
                    if (this.previewIndex > 0) {
                        this.previewIndex--;
                        this.selectedAnswer = null;
                    }
                },

                // Modal Actions
                closeModal() {
                    this.showAddModal = false;
                    this.editingIndex = null;
                    this.modalForm = {
                        type: 'pilihan_ganda',
                        pertanyaan: '',
                        options: ['', '', '', ''],
                        correctAnswer: 0,
                        bobot: 10
                    };
                },

                editQuestion(index) {
                    const q = this.questions[index];
                    this.modalForm = JSON.parse(JSON.stringify(q)); // deep copy
                    this.editingIndex = index;
                    this.showAddModal = true;
                },

                duplicateQuestion(index) {
                    const q = JSON.parse(JSON.stringify(this.questions[index]));
                    q.id = Date.now();
                    this.questions.splice(index + 1, 0, q);
                    this.showToast('Soal berhasil diduplikat', 'success');
                },

                deleteQuestion(index) {
                    if (confirm('Yakin ingin menghapus soal ini?')) {
                        this.questions.splice(index, 1);
                        if (this.previewIndex >= this.questions.length) {
                            this.previewIndex = Math.max(0, this.questions.length - 1);
                        }
                        this.showToast('Soal dihapus');
                    }
                },

                saveQuestion() {
                    if (!this.modalForm.pertanyaan.trim()) {
                        this.showToast('Pertanyaan tidak boleh kosong', 'error');
                        return;
                    }
                    
                    const question = {
                        id: this.editingIndex !== null ? this.questions[this.editingIndex].id : Date.now(),
                        ...this.modalForm
                    };
                    
                    if (this.modalForm.type === 'benar_salah') {
                        question.options = ['Benar', 'Salah'];
                    }

                    if (this.editingIndex !== null) {
                        this.questions[this.editingIndex] = question;
                    } else {
                        this.questions.push(question);
                    }
                    
                    this.closeModal();
                    this.showToast('Soal tersimpan');
                },

                async saveQuiz() {
                    if (!this.selectedCourseId) {
                        alert('Mohon pilih kursus.');
                        return;
                    }
                    if (!this.form.judul_modul) {
                        alert('Mohon isi Judul Kuis.');
                        return;
                    }
                    if (this.questions.length === 0) {
                        alert('Minimal 1 soal.');
                        return;
                    }

                    // Serialize questions to JSON string for storage in 'konten'
                    this.form.konten = JSON.stringify(this.questions);

                    this.isSubmitting = true;
                    try {
                        const response = await fetch(`/dosen/api/courses/${this.selectedCourseId}/modules`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            alert('Kuis berhasil dibuat!');
                            // Reset
                            this.questions = [];
                            this.form.judul_modul = '';
                            this.form.durasi = 15;
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            }));
        });
    </script>
    @endpush
</x-layouts.dosen>

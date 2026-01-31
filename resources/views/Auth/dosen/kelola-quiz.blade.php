<x-layouts.dosen title="Input Kuis" active="buat-kursus">
    <div x-data="quizManager()">
        {{-- Header with Filters --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Input Kuis</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola soal kuis untuk modul pembelajaran</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select x-model="selectedCourse" class="px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none min-w-[160px]">
                        <option value="">Pilih Kursus</option>
                        <option value="1">Pemrograman Web</option>
                        <option value="2">Basis Data</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                
                <div class="relative">
                    <select x-model="selectedModule" class="px-4 py-2.5 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none min-w-[160px]">
                        <option value="">Pilih Modul</option>
                        <option value="1">Modul 1: Pengenalan</option>
                        <option value="2">Modul 2: Dasar-dasar</option>
                    </select>
                    <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                
                <button type="button" @click="showAddModal = true" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Soal
                </button>
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
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="quizTitle || 'Kuis Baru'"></h3>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="formatTime(timerSeconds)"></span>
                            </div>
                        </div>
                        
                        {{-- Progress --}}
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500 dark:text-gray-400" x-text="'Soal ' + (previewIndex + 1) + ' dari ' + questions.length"></span>
                            <span class="text-gray-500 dark:text-gray-400" x-text="currentQuestion?.bobot + ' poin'"></span>
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
                <button type="button" @click="saveDraft()" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                    Simpan Draft
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Publikasikan Kuis
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
                            <select x-model="formData.type" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="benar_salah">Benar-Salah</option>
                            </select>
                        </div>
                        
                        {{-- Pertanyaan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan</label>
                            <textarea x-model="formData.pertanyaan" rows="3" placeholder="Masukkan pertanyaan..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                        </div>
                        
                        {{-- Options --}}
                        <div x-show="formData.type === 'pilihan_ganda'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opsi Jawaban</label>
                            <div class="space-y-2">
                                <template x-for="(opt, i) in formData.options" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 w-6" x-text="String.fromCharCode(65 + i) + '.'"></span>
                                        <input type="text" x-model="formData.options[i]" :placeholder="'Opsi ' + String.fromCharCode(65 + i)" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white text-sm">
                                        <input type="radio" :name="'correct_answer'" :value="i" x-model="formData.correctAnswer" class="w-4 h-4 text-green-500" title="Jawaban benar">
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        {{-- Benar/Salah --}}
                        <div x-show="formData.type === 'benar_salah'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jawaban Benar</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="benar_salah" value="true" x-model="formData.correctAnswer" class="w-4 h-4 text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Benar</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="benar_salah" value="false" x-model="formData.correctAnswer" class="w-4 h-4 text-blue-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Salah</span>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Bobot --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bobot (poin)</label>
                            <input type="number" x-model="formData.bobot" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
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
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" class="fixed bottom-4 right-4 z-50" style="display: none;">
            <div class="px-4 py-3 rounded-xl shadow-lg flex items-center gap-3" :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'">
                <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="toast.type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="toast.message"></span>
            </div>
        </div>
    </div>

    <script>
        function quizManager() {
            return {
                quizTitle: 'Kuis HTML Dasar',
                selectedCourse: '',
                selectedModule: '',
                timerSeconds: 1725, // 28:45
                previewIndex: 0,
                selectedAnswer: null,
                showAddModal: false,
                editingIndex: null,
                toast: { show: false, message: '', type: 'success' },
                
                questions: [
                    {
                        id: 1,
                        type: 'pilihan_ganda',
                        pertanyaan: 'Apa yang dimaksud dengan HTML?',
                        options: ['HyperText Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlink and Text Markup Language'],
                        correctAnswer: 0,
                        bobot: 10
                    },
                    {
                        id: 2,
                        type: 'benar_salah',
                        pertanyaan: 'CSS digunakan untuk styling halaman web',
                        options: ['Benar', 'Salah'],
                        correctAnswer: 0,
                        bobot: 5
                    }
                ],
                
                formData: {
                    type: 'pilihan_ganda',
                    pertanyaan: '',
                    options: ['', '', '', ''],
                    correctAnswer: 0,
                    bobot: 10
                },
                
                get currentQuestion() {
                    return this.questions[this.previewIndex] || null;
                },
                
                formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
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
                
                editQuestion(index) {
                    const q = this.questions[index];
                    this.formData = {
                        type: q.type,
                        pertanyaan: q.pertanyaan,
                        options: [...q.options],
                        correctAnswer: q.correctAnswer,
                        bobot: q.bobot
                    };
                    this.editingIndex = index;
                    this.showAddModal = true;
                },
                
                duplicateQuestion(index) {
                    const q = { ...this.questions[index], id: Date.now() };
                    this.questions.splice(index + 1, 0, q);
                    this.showToast('Soal berhasil diduplikat', 'success');
                },
                
                deleteQuestion(index) {
                    if (confirm('Apakah Anda yakin ingin menghapus soal ini?')) {
                        this.questions.splice(index, 1);
                        if (this.previewIndex >= this.questions.length) {
                            this.previewIndex = Math.max(0, this.questions.length - 1);
                        }
                        this.showToast('Soal berhasil dihapus', 'success');
                    }
                },
                
                saveQuestion() {
                    if (!this.formData.pertanyaan.trim()) {
                        this.showToast('Pertanyaan tidak boleh kosong', 'error');
                        return;
                    }
                    
                    const question = {
                        id: this.editingIndex !== null ? this.questions[this.editingIndex].id : Date.now(),
                        ...this.formData
                    };
                    
                    if (this.formData.type === 'benar_salah') {
                        question.options = ['Benar', 'Salah'];
                    }
                    
                    if (this.editingIndex !== null) {
                        this.questions[this.editingIndex] = question;
                        this.showToast('Soal berhasil diperbarui', 'success');
                    } else {
                        this.questions.push(question);
                        this.showToast('Soal berhasil ditambahkan', 'success');
                    }
                    
                    this.closeModal();
                },
                
                closeModal() {
                    this.showAddModal = false;
                    this.editingIndex = null;
                    this.formData = {
                        type: 'pilihan_ganda',
                        pertanyaan: '',
                        options: ['', '', '', ''],
                        correctAnswer: 0,
                        bobot: 10
                    };
                },
                
                saveDraft() {
                    this.showToast('Draft berhasil disimpan (demo)', 'success');
                },
                
                saveQuiz() {
                    if (this.questions.length === 0) {
                        this.showToast('Tambahkan minimal 1 soal terlebih dahulu', 'error');
                        return;
                    }
                    this.showToast('Kuis berhasil dipublikasikan (demo)', 'success');
                },
                
                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                }
            }
        }
    </script>
</x-layouts.dosen>

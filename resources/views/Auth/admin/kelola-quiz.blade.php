<x-layouts.admin title="Kelola Kuis">
    <div x-data="quizManager()" x-init="init()" class="pb-20">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Kuis</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $course['nama'] }}</span> &bull; {{ $module['judul'] }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 admin-responsive-toolbar">
                <a href="{{ route('admin.kursus.modul', $course['id']) }}" class="px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl flex items-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Modul
                </a>
                <button type="button" @click="openCreateQuizModal()" class="px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-xl flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Kuis Baru
                </button>
            </div>
        </div>

        {{-- Quiz List --}}
        <template x-for="(quiz, qIdx) in quizzes" :key="quiz.id">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 mb-6 overflow-hidden">
                {{-- Quiz Header --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 cursor-pointer" @click="toggleQuiz(quiz.id)">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-start gap-3 sm:items-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="quiz.is_pretest ? 'bg-green-100 dark:bg-green-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30'">
                                <svg class="w-5 h-5" :class="quiz.is_pretest ? 'text-green-600' : 'text-yellow-600'" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white" x-text="quiz.judul"></h2>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-500">
                                    <span x-show="quiz.is_pretest" class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full font-medium">Pretest</span>
                                    <span x-text="quiz.jumlah_soal + ' soal'"></span>
                                    <span>&bull;</span>
                                    <span x-text="quiz.durasi_menit + ' menit'"></span>
                                    <span>&bull;</span>
                                    <span x-text="'Total ' + quiz.total_bobot + ' poin'"></span>
                                    <span>&bull;</span>
                                    <span x-text="'Passing: ' + quiz.passing_score + '%'"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 self-end md:self-auto">
                            <button type="button" @click.stop="openEditQuizModal(quiz)" class="p-2 text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition" title="Edit Kuis">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" @click.stop="deleteQuiz(quiz.id, qIdx)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus Kuis">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="expandedQuiz === quiz.id ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Quiz Questions (Expanded) --}}
                <div x-show="expandedQuiz === quiz.id" x-transition class="p-6">
                    {{-- Questions List --}}
                    <div class="space-y-3 mb-4">
                        <template x-for="(question, qIndex) in quiz.questions" :key="question.id">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400" x-text="'Soal ' + (qIndex + 1)"></span>
                                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full"
                                                  :class="question.tipe === 'pilihan_ganda' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
                                                  x-text="question.tipe === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Benar-Salah'"></span>
                                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400" x-text="question.bobot + ' poin'"></span>
                                        </div>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium mb-3" x-text="question.pertanyaan"></p>

                                        {{-- Options Display --}}
                                        <div class="space-y-2">
                                            <template x-for="(opt, optIdx) in question.opsi" :key="optIdx">
                                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm"
                                                     :class="String(optIdx) === String(question.jawaban_benar) || opt === question.jawaban_benar
                                                        ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300'
                                                        : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300'">
                                                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                                                          :class="String(optIdx) === String(question.jawaban_benar) || opt === question.jawaban_benar
                                                            ? 'bg-green-500 text-white'
                                                            : 'bg-gray-200 dark:bg-gray-600 text-gray-500 dark:text-gray-400'"
                                                          x-text="String.fromCharCode(65 + optIdx)"></span>
                                                    <span x-text="opt"></span>
                                                    <svg x-show="String(optIdx) === String(question.jawaban_benar) || opt === question.jawaban_benar" class="w-4 h-4 text-green-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- Explanation --}}
                                        <div x-show="question.penjelasan" class="mt-3 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                            <p class="text-xs text-blue-700 dark:text-blue-400"><strong>Penjelasan:</strong> <span x-text="question.penjelasan"></span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 flex-shrink-0 self-end sm:self-auto">
                                        <button type="button" @click="openEditQuestionModal(quiz, qIndex)" class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" @click="deleteQuestion(quiz, qIndex)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty --}}
                        <div x-show="quiz.questions.length === 0" class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p>Belum ada soal. Klik "Tambah Soal" untuk memulai.</p>
                        </div>
                    </div>

                    {{-- Add Question Button --}}
                    <button type="button" @click="openAddQuestionModal(quiz)" class="w-full py-4 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-gray-400 hover:text-blue-500 hover:border-blue-300 dark:hover:border-blue-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Soal Baru</span>
                    </button>
                </div>
            </div>
        </template>

        {{-- Empty State --}}
        <div x-show="quizzes.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada kuis</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Buat kuis pertama untuk modul ini. Setiap soal memiliki poin seperti Google Form.</p>
            <button type="button" @click="openCreateQuizModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Kuis Baru
            </button>
        </div>

        {{-- Create/Edit Quiz Modal --}}
        <div x-show="showQuizModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showQuizModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5" x-text="editingQuizId ? 'Edit Kuis' : 'Buat Kuis Baru'"></h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Kuis <span class="text-red-500">*</span></label>
                            <input type="text" x-model="quizForm.judul" placeholder="Contoh: Kuis Evaluasi Bab 1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi</label>
                            <textarea x-model="quizForm.deskripsi" rows="2" placeholder="Deskripsi kuis..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Durasi (Menit)</label>
                                <input type="number" x-model="quizForm.durasi_menit" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Passing Score (%)</label>
                                <input type="number" x-model="quizForm.passing_score" min="0" max="100" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" x-model="quizForm.acak_soal" class="rounded border-gray-300 text-blue-500 focus:ring-blue-500"> Acak Urutan Soal
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" x-model="quizForm.tampilkan_nilai" class="rounded border-gray-300 text-blue-500 focus:ring-blue-500"> Tampilkan Nilai
                            </label>
                        </div>
                    </div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-3 mt-6">
                        <button type="button" @click="showQuizModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm">Batal</button>
                        <button type="button" @click="saveQuiz()" :disabled="isSaving" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition">
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Kuis'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add/Edit Question Modal --}}
        <div x-show="showQuestionModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-black/50" @click="showQuestionModal = false"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5" x-text="editingQuestionId ? 'Edit Soal' : 'Tambah Soal Baru'"></h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tipe Soal</label>
                            <select x-model="questionForm.tipe" @change="onQuestionTypeChange()" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                                <option value="pilihan_ganda">Pilihan Ganda</option>
                                <option value="benar_salah">Benar-Salah</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                            <textarea x-model="questionForm.pertanyaan" rows="3" placeholder="Masukkan pertanyaan..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm resize-none"></textarea>
                        </div>

                        {{-- Pilihan Ganda Options --}}
                        <div x-show="questionForm.tipe === 'pilihan_ganda'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opsi Jawaban</label>
                            <div class="space-y-2">
                                <template x-for="(opt, i) in questionForm.opsi" :key="i">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 w-6 flex-shrink-0" x-text="String.fromCharCode(65 + i) + '.'"></span>
                                        <input type="text" x-model="questionForm.opsi[i]" :placeholder="'Opsi ' + String.fromCharCode(65 + i)" class="flex-1 px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm">
                                        <label class="flex items-center gap-1 text-xs text-gray-500 flex-shrink-0 cursor-pointer" :class="String(questionForm.jawaban_benar) === String(i) ? 'text-green-600 font-bold' : ''">
                                            <input type="radio" name="jawaban_benar_modal" :value="String(i)" x-model="questionForm.jawaban_benar" class="w-4 h-4 text-green-500">
                                            Benar
                                        </label>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="questionForm.opsi.push('')" x-show="questionForm.opsi.length < 6" class="mt-2 text-sm text-blue-500 hover:text-blue-700 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Tambah Opsi
                            </button>
                        </div>

                        {{-- Benar/Salah --}}
                        <div x-show="questionForm.tipe === 'benar_salah'">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jawaban Benar</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 text-sm"><input type="radio" name="bs_answer" value="0" x-model="questionForm.jawaban_benar" class="w-4 h-4 text-blue-500"> Benar</label>
                                <label class="flex items-center gap-2 text-sm"><input type="radio" name="bs_answer" value="1" x-model="questionForm.jawaban_benar" class="w-4 h-4 text-blue-500"> Salah</label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bobot (poin) <span class="text-red-500">*</span></label>
                            <input type="number" x-model="questionForm.bobot" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm">
                            <p class="text-xs text-gray-400 mt-1">Poin yang diberikan jika menjawab benar (seperti Google Form)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Penjelasan Jawaban <span class="text-gray-400">(opsional)</span></label>
                            <textarea x-model="questionForm.penjelasan" rows="2" placeholder="Penjelasan mengapa jawaban ini benar..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm resize-none"></textarea>
                        </div>
                    </div>
                    <div class="admin-responsive-modal-actions flex justify-end gap-3 mt-6">
                        <button type="button" @click="showQuestionModal = false" class="px-4 py-2 text-gray-600 dark:text-gray-400 text-sm">Batal</button>
                        <button type="button" @click="saveQuestion()" :disabled="isSaving" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition">
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Soal'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="fixed bottom-4 right-4 z-[60]" style="display: none;">
            <div class="px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-white text-sm font-medium" :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'">
                <span x-text="toast.message"></span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('quizManager', () => ({
                courseId: @json($course['id']),
                moduleId: @json($module['id']),
                quizzes: @json($quizzes),
                expandedQuiz: @json(request('focus_quiz') ? (int) request('focus_quiz') : null),

                // Quiz Modal
                showQuizModal: false,
                editingQuizId: null,
                quizForm: { judul: '', deskripsi: '', durasi_menit: 15, passing_score: 60, acak_soal: false, tampilkan_nilai: true },

                // Question Modal
                showQuestionModal: false,
                editingQuestionId: null,
                currentQuizForQuestion: null,
                questionForm: { pertanyaan: '', tipe: 'pilihan_ganda', opsi: ['', '', '', ''], jawaban_benar: '0', bobot: 10, penjelasan: '' },

                isSaving: false,
                toast: { show: false, message: '', type: 'success' },

                init() {
                    // Auto-expand focus quiz
                    if (this.expandedQuiz) {
                        const q = this.quizzes.find(x => x.id === this.expandedQuiz);
                        if (!q) this.expandedQuiz = null;
                    }
                },

                toggleQuiz(id) {
                    this.expandedQuiz = this.expandedQuiz === id ? null : id;
                },

                // ========================================
                // Quiz CRUD
                // ========================================
                openCreateQuizModal() {
                    this.editingQuizId = null;
                    this.quizForm = { judul: '', deskripsi: '', durasi_menit: 15, passing_score: 60, acak_soal: false, tampilkan_nilai: true };
                    this.showQuizModal = true;
                },

                openEditQuizModal(quiz) {
                    this.editingQuizId = quiz.id;
                    this.quizForm = {
                        judul: quiz.judul,
                        deskripsi: quiz.deskripsi || '',
                        durasi_menit: quiz.durasi_menit,
                        passing_score: quiz.passing_score,
                        acak_soal: quiz.acak_soal,
                        tampilkan_nilai: quiz.tampilkan_nilai,
                    };
                    this.showQuizModal = true;
                },

                async saveQuiz() {
                    if (!this.quizForm.judul.trim()) { this.showToast('Judul kuis wajib diisi.', 'error'); return; }
                    this.isSaving = true;
                    try {
                        const isEdit = this.editingQuizId !== null;
                        const url = isEdit
                            ? `/admin/kursus/${this.courseId}/quiz/${this.editingQuizId}`
                            : `/admin/kursus/${this.courseId}/module/${this.moduleId}/quiz`;
                        const method = isEdit ? 'PUT' : 'POST';

                        const res = await fetch(url, {
                            method, credentials: 'same-origin',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(this.quizForm)
                        });
                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.showToast(data.message, 'success');
                            if (isEdit) {
                                const idx = this.quizzes.findIndex(q => q.id === this.editingQuizId);
                                if (idx !== -1) Object.assign(this.quizzes[idx], this.quizForm);
                            } else {
                                this.quizzes.push({ id: data.data.id, ...this.quizForm, questions: [], jumlah_soal: 0, total_bobot: 0 });
                                this.expandedQuiz = data.data.id;
                            }
                            this.showQuizModal = false;
                        } else {
                            this.showToast(data.error || data.message || 'Gagal menyimpan.', 'error');
                        }
                    } catch (e) { this.showToast('Terjadi kesalahan.', 'error'); }
                    finally { this.isSaving = false; }
                },

                async deleteQuiz(quizId, qIdx) {
                    if (!confirm('Yakin ingin menghapus kuis ini beserta semua soalnya?')) return;
                    try {
                        const res = await fetch(`/admin/kursus/${this.courseId}/quiz/${quizId}`, {
                            method: 'DELETE', credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.quizzes.splice(qIdx, 1);
                            this.showToast('Kuis dihapus.', 'success');
                        }
                    } catch (e) { this.showToast('Gagal menghapus.', 'error'); }
                },

                // ========================================
                // Question CRUD
                // ========================================
                openAddQuestionModal(quiz) {
                    this.currentQuizForQuestion = quiz;
                    this.editingQuestionId = null;
                    this.questionForm = { pertanyaan: '', tipe: 'pilihan_ganda', opsi: ['', '', '', ''], jawaban_benar: '0', bobot: 10, penjelasan: '' };
                    this.showQuestionModal = true;
                },

                openEditQuestionModal(quiz, qIndex) {
                    const q = quiz.questions[qIndex];
                    this.currentQuizForQuestion = quiz;
                    this.editingQuestionId = q.id;
                    this.questionForm = {
                        pertanyaan: q.pertanyaan,
                        tipe: q.tipe,
                        opsi: q.tipe === 'benar_salah' ? ['Benar', 'Salah'] : [...(q.opsi || ['', '', '', ''])],
                        jawaban_benar: String(q.jawaban_benar),
                        bobot: q.bobot,
                        penjelasan: q.penjelasan || '',
                    };
                    this.showQuestionModal = true;
                },

                onQuestionTypeChange() {
                    if (this.questionForm.tipe === 'benar_salah') {
                        this.questionForm.opsi = ['Benar', 'Salah'];
                        this.questionForm.jawaban_benar = '0';
                    } else {
                        this.questionForm.opsi = ['', '', '', ''];
                        this.questionForm.jawaban_benar = '0';
                    }
                },

                async saveQuestion() {
                    if (!this.questionForm.pertanyaan.trim()) { this.showToast('Pertanyaan wajib diisi.', 'error'); return; }
                    if (!this.currentQuizForQuestion) return;

                    const quizId = this.currentQuizForQuestion.id;
                    this.isSaving = true;

                    const payload = {
                        pertanyaan: this.questionForm.pertanyaan,
                        tipe: this.questionForm.tipe,
                        opsi: this.questionForm.tipe === 'benar_salah' ? ['Benar', 'Salah'] : this.questionForm.opsi.filter(o => o.trim()),
                        jawaban_benar: String(this.questionForm.jawaban_benar),
                        bobot: parseInt(this.questionForm.bobot) || 10,
                        penjelasan: this.questionForm.penjelasan || null,
                    };

                    try {
                        const isEdit = this.editingQuestionId !== null;
                        const url = isEdit
                            ? `/admin/kursus/${this.courseId}/quiz/${quizId}/question/${this.editingQuestionId}`
                            : `/admin/kursus/${this.courseId}/quiz/${quizId}/question`;
                        const method = isEdit ? 'PUT' : 'POST';

                        const res = await fetch(url, {
                            method, credentials: 'same-origin',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();

                        if (res.ok && data.success) {
                            const quiz = this.quizzes.find(q => q.id === quizId);
                            if (isEdit) {
                                const idx = quiz.questions.findIndex(q => q.id === this.editingQuestionId);
                                if (idx !== -1) quiz.questions[idx] = data.data;
                            } else {
                                quiz.questions.push(data.data);
                            }
                            quiz.jumlah_soal = quiz.questions.length;
                            quiz.total_bobot = quiz.questions.reduce((sum, q) => sum + q.bobot, 0);
                            this.showQuestionModal = false;
                            this.showToast(data.message, 'success');
                        } else {
                            this.showToast(data.error || data.message || 'Gagal menyimpan soal.', 'error');
                        }
                    } catch (e) { this.showToast('Terjadi kesalahan.', 'error'); }
                    finally { this.isSaving = false; }
                },

                async deleteQuestion(quiz, qIndex) {
                    if (!confirm('Yakin ingin menghapus soal ini?')) return;
                    const question = quiz.questions[qIndex];
                    try {
                        const res = await fetch(`/admin/kursus/${this.courseId}/quiz/${quiz.id}/question/${question.id}`, {
                            method: 'DELETE', credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        if (data.success) {
                            quiz.questions.splice(qIndex, 1);
                            quiz.jumlah_soal = quiz.questions.length;
                            quiz.total_bobot = quiz.questions.reduce((sum, q) => sum + q.bobot, 0);
                            this.showToast('Soal dihapus.', 'success');
                        }
                    } catch (e) { this.showToast('Gagal menghapus.', 'error'); }
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            }));
        });
    </script>
    @endpush
</x-layouts.admin>

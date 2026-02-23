<x-layouts.dosen title="Kelola Tugas" active="buat-kursus">
    <div x-data="tugasManager()" x-init="init()" class="pb-20">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('dosen.dashboard') }}" class="hover:text-blue-500">Dashboard Dosen</a>
            <span>›</span>
            <a href="{{ route('dosen.kursus') }}" class="hover:text-blue-500">Kursus Saya</a>
            <span>›</span>
            <span class="text-blue-500">Kelola Tugas</span>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Tugas</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Buat tugas dan atur pengumpulan untuk mahasiswa</p>
        </div>

        <form @submit.prevent="saveTugas">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column - Form --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Course Selection --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pilih Kursus</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kursus <span class="text-red-500">*</span></label>
                                <select x-model="selectedCourseId" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                    <option value="">-- Pilih Kursus --</option>
                                    <template x-for="course in courses" :key="course.id">
                                        <option :value="course.id" x-text="course.nama"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Dasar Tugas --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Informasi Dasar Tugas</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Tugas <span class="text-red-500">*</span></label>
                                <input type="text" x-model="form.judul_modul" required placeholder="Contoh: Tugas Akhir Website" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Tugas</label>
                                <textarea x-model="assignmentData.deskripsi" rows="4" placeholder="Deskripsikan tugas yang akan dikerjakan mahasiswa..." class="w-full px-4 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Instruksi & Ketentuan --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Instruksi & Ketentuan</h2>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Instruksi Pengerjaan</label>
                                <textarea x-model="assignmentData.instruksi" rows="3" placeholder="Berikan instruksi detail tentang cara mengerjakan tugas..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm resize-none"></textarea>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Deadline & Pengumpulan</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Deadline</label>
                                        <input type="datetime-local" x-model="assignmentData.deadline" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5">Format File</label>
                                        <select x-model="assignmentData.format" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                                            <option value="pdf">PDF Document (.pdf)</option>
                                            <option value="zip">ZIP Archive (.zip)</option>
                                            <option value="docx">Word Document (.docx)</option>
                                            <option value="any">Semua Format</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" x-model="assignmentData.allowLinks" class="w-4 h-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Izinkan pengumpulan Link (G-Drive/Github)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column - Preview --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 sticky top-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Preview Tugas</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Tampilan ringkas</p>
                        
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-4">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white" x-text="form.judul_modul || 'Judul Tugas'"></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-3" x-text="assignmentData.deskripsi || 'Deskripsi tugas...'"></p>
                            </div>
                            
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-text="formatDate(assignmentData.deadline) || 'Set Deadline'"></span>
                                </div>
                            </div>
                            
                            <div>
                                <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded uppercase" x-text="assignmentData.format"></span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit" :disabled="isSubmitting || !selectedCourseId" class="w-full px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="isSubmitting ? 'Menyimpan...' : 'Publikasikan Tugas'"></span>
                            </button>
                            <button type="button" onclick="history.back()" class="w-full mt-3 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tugasManager', () => ({
                courses: [],
                isLoading: false,
                isSubmitting: false,
                selectedCourseId: @json(request('course_id', '')),
                form: {
                    judul_modul: '',
                    tipe: 'text', // Using 'text' as container for assignment
                    konten: '',
                    durasi: 60 // Default estimate
                },
                
                // Structured data to be serialized into 'konten'
                assignmentData: {
                    deskripsi: '',
                    instruksi: '',
                    deadline: '',
                    format: 'pdf',
                    allowLinks: false,
                    is_tugas: true // Marker flag
                },

                init() {
                    this.fetchCourses();
                },

                async fetchCourses() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/dosen/courses?sort=terbaru&per_page=100', {
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
                
                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                },

                async saveTugas() {
                    if (!this.selectedCourseId) {
                        alert('Mohon pilih kursus.');
                        return;
                    }
                    if (!this.form.judul_modul) {
                        alert('Mohon isi Judul Tugas.');
                        return;
                    }

                    // Serialize assignment data into 'konten'
                    this.form.konten = JSON.stringify(this.assignmentData);

                    this.isSubmitting = true;
                    try {
                        const response = await fetch(`/api/dosen/courses/${this.selectedCourseId}/modules`, {
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
                            alert('Tugas berhasil dibuat!');
                            // Reset
                            this.form.judul_modul = '';
                            this.assignmentData.deskripsi = '';
                            this.assignmentData.instruksi = '';
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-layouts.dosen>

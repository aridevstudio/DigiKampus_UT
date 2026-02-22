<x-layouts.dosen title="Kelola Video Pembelajaran" active="buat-kursus">
    <div x-data="videoManager()" x-init="init()" class="pb-20">
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
            <p class="text-gray-500 dark:text-gray-400 mt-1">Tambahkan video materi ke dalam kursus Anda.</p>
        </div>

        <form @submit.prevent="saveVideo" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - 2/3 width --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Course Selection (Critical for linking video to course) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Pilih Kursus</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kursus <span class="text-red-500">*</span></label>
                            <select x-model="selectedCourseId" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="">-- Pilih Kursus --</option>
                                <template x-for="course in courses" :key="course.id">
                                    <option :value="course.id" x-text="course.nama"></option>
                                </template>
                            </select>
                            <p x-show="courses.length === 0 && !isLoading" class="text-xs text-red-500 mt-1">Tidak ada kursus ditemukan. Silakan buat kursus terlebih dahulu.</p>
                        </div>
                    </div>
                </div>

                {{-- Video Input Section (URL Mode) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Sumber Video</h2>
                    
                    <div class="space-y-4">
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 rounded-xl text-sm mb-4">
                            <span class="font-bold">Catatan:</span> Saat ini sistem hanya mendukung URL video eksternal (YouTube, Vimeo, Google Drive, dll). Fitur upload file langsung akan segera hadir.
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Video URL <span class="text-red-500">*</span></label>
                            <input type="url" x-model="form.video_url" required placeholder="https://youtube.com/watch?v=..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                {{-- Informasi Video Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Detail Materi</h2>
                    
                    <div class="space-y-4">
                        {{-- Judul Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.judul_modul" required placeholder="Contoh: Pengenalan HTML" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Durasi --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Durasi (Menit)</label>
                                <input type="number" x-model="form.durasi" min="1" placeholder="15" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                        
                        {{-- Deskripsi Video --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konten / Deskripsi Singkat</label>
                            <textarea x-model="form.konten" rows="4" placeholder="Jelaskan isi video ini..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - 1/3 width --}}
            <div class="space-y-6">
                {{-- Preview Video --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Preview</h2>
                    
                    <div class="aspect-video bg-gray-900 rounded-xl flex items-center justify-center relative overflow-hidden">
                        <template x-if="getEmbedUrl(form.video_url)">
                            <iframe :src="getEmbedUrl(form.video_url)" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </template>
                        <template x-if="!getEmbedUrl(form.video_url)">
                            <div class="text-center p-4">
                                <svg class="w-12 h-12 text-white/50 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <p class="text-sm text-white/50">Masukkan URL video untuk preview</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <button type="submit" :disabled="isSubmitting || !selectedCourseId" class="w-full px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition flex items-center justify-center gap-2">
                        <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Materi Video'"></span>
                    </button>
                    <button type="button" onclick="history.back()" class="w-full mt-3 px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('videoManager', () => ({
                courses: [],
                isLoading: false,
                isSubmitting: false,
                selectedCourseId: '',
                form: {
                    judul_modul: '',
                    video_url: '',
                    durasi: '',
                    konten: '',
                    tipe: 'video'
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

                getEmbedUrl(url) {
                    if (!url) return null;
                    // Simple YouTube regex for demo
                    const ytRegExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                    const match = url.match(ytRegExp);
                    if (match && match[2].length === 11) {
                        return 'https://www.youtube.com/embed/' + match[2];
                    }
                    return null;
                },

                async saveVideo() {
                    if (!this.selectedCourseId) {
                        alert('Mohon pilih kursus terlebih dahulu.');
                        return;
                    }

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
                            alert('Video berhasil ditambahkan!');
                            // Reset form
                            this.form.judul_modul = '';
                            this.form.video_url = '';
                            this.form.durasi = '';
                            this.form.konten = '';
                        } else {
                            alert('Gagal menyimpan: ' + data.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan saat menyimpan.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-layouts.dosen>
</x-layouts.dosen>

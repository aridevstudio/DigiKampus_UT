<x-layouts.admin title="Kelola Bacaan">
    <div x-data="bacaanManager()" x-init="init()" class="pb-20">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-500">Dashboard Admin</a>
            <span>›</span>
            <a href="{{ route('admin.kursus') }}" class="hover:text-blue-500">Manajemen Kursus</a>
            <span>›</span>
            <span class="text-blue-500">Kelola Bacaan</span>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Bacaan</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Buat dan kelola materi bacaan untuk modul pembelajaran</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Form --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Course Selection --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Pilih Kursus</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kursus <span class="text-red-500">*</span></label>
                            <select x-model="selectedCourseId" :disabled="courseLocked" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition disabled:opacity-70 disabled:cursor-not-allowed">
                                <option value="">-- Pilih Kursus --</option>
                                <template x-for="course in courses" :key="course.id">
                                    <option :value="course.id" x-text="course.nama"></option>
                                </template>
                            </select>
                            <p x-show="courseLocked" class="text-xs text-blue-500 mt-1">Kursus disesuaikan otomatis dari halaman sebelumnya.</p>
                            <p x-show="courses.length === 0 && !isLoading" class="text-xs text-red-500 mt-1">Tidak ada kursus ditemukan.</p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Bacaan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Informasi Bacaan</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" x-model="form.judul_modul" required placeholder="Masukkan judul materi bacaan" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estimasi Waktu Baca (menit)</label>
                                <input type="number" x-model="form.durasi" min="1" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Konten Bacaan --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Konten Bacaan</h2>
                    
                    {{-- Simple Text Editor --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex-wrap">
                            <span class="text-xs text-gray-500">Editor Teks (HTML Supported)</span>
                        </div>
                        <textarea x-model="form.konten" rows="12" placeholder="Tulis konten materi bacaan di sini..." class="w-full px-4 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none resize-none"></textarea>
                    </div>
                </div>

                {{-- Lampiran & Referensi --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Lampiran & Sumber Referensi</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Upload Lampiran (opsional)</label>
                            <input
                                x-ref="lampiranInput"
                                type="file"
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.txt,.jpg,.jpeg,.png,.webp"
                                @change="onLampiranChange($event)"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm"
                            >
                            <p class="text-xs text-gray-500 mt-1">Maksimum 10MB. Format umum dokumen/gambar didukung.</p>
                            <div x-show="lampiranFileName" class="mt-2 flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                                <span x-text="`Terpilih: ${lampiranFileName}`"></span>
                                <button type="button" @click="clearLampiran()" class="text-red-500 hover:text-red-600">Hapus</button>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sumber Referensi (URL)</label>
                                <button type="button" @click="addReferenceField()" class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">
                                    + Tambah Link
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(reference, index) in form.sumber_referensi" :key="`reference-${index}`">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="url"
                                            x-model="form.sumber_referensi[index]"
                                            placeholder="https://contoh.com/referensi"
                                            class="flex-1 px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white text-sm"
                                        >
                                        <button
                                            type="button"
                                            @click="removeReferenceField(index)"
                                            x-show="form.sumber_referensi.length > 1"
                                            class="px-3 py-2.5 text-xs rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </template>
                            </div>
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
                            <h3 class="font-bold text-gray-900 dark:text-white" x-text="form.judul_modul || 'Judul Materi'"></h3>
                        </div>
                        
                        {{-- Preview Info --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 flex items-center gap-4 text-xs">
                            <div class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="(form.durasi || 0) + ' menit baca'"></span>
                            </div>
                        </div>
                        
                        {{-- Preview Content --}}
                        <div class="p-4">
                            <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400" x-html="form.konten ? form.konten.substring(0, 200) + '...' : 'Konten bacaan akan muncul di sini...'"></div>
                        </div>

                        <div class="px-4 pb-4 space-y-2">
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-show="lampiranFileName">
                                Lampiran: <span class="font-medium text-gray-700 dark:text-gray-300" x-text="lampiranFileName"></span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400" x-show="normalizedReferences().length > 0">
                                Referensi: <span class="font-medium text-gray-700 dark:text-gray-300" x-text="`${normalizedReferences().length} link`"></span>
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
            <button type="button" @click="saveBacaan()" :disabled="isSubmitting || !selectedCourseId" class="px-5 py-2.5 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 text-white text-sm font-medium rounded-xl transition flex items-center gap-2">
                <svg x-show="!isSubmitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="isSubmitting" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Publikasikan Bacaan'"></span>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bacaanManager', () => ({
                courses: [],
                isLoading: false,
                isSubmitting: false,
                selectedCourseId: @json((string) request('course_id', '')),
                lockedCourseId: @json((string) request('course_id', '')),
                courseLocked: false,
                form: {
                    judul_modul: @json(request('modul_judul', '')),
                    konten: @json(request('modul_konten', '')),
                    durasi: @json(request('modul_durasi', 10)),
                    tipe: 'bacaan',
                    sumber_referensi: @json((array) request('sumber_referensi', ['']))
                },
                lampiranFile: null,
                lampiranFileName: '',

                init() {
                    this.ensureReferenceField();
                    this.fetchCourses();
                },

                ensureReferenceField() {
                    if (!Array.isArray(this.form.sumber_referensi)) {
                        this.form.sumber_referensi = [''];
                        return;
                    }

                    if (this.form.sumber_referensi.length === 0) {
                        this.form.sumber_referensi.push('');
                    }
                },

                addReferenceField() {
                    this.form.sumber_referensi.push('');
                },

                removeReferenceField(index) {
                    this.form.sumber_referensi.splice(index, 1);
                    this.ensureReferenceField();
                },

                normalizedReferences() {
                    return (this.form.sumber_referensi || [])
                        .map((item) => String(item || '').trim())
                        .filter((item) => item.length > 0);
                },

                onLampiranChange(event) {
                    const [file] = event.target.files || [];
                    this.lampiranFile = file || null;
                    this.lampiranFileName = file ? file.name : '';
                },

                clearLampiran() {
                    this.lampiranFile = null;
                    this.lampiranFileName = '';
                    if (this.$refs.lampiranInput) {
                        this.$refs.lampiranInput.value = '';
                    }
                },

                extractErrorMessage(data) {
                    if (!data) {
                        return 'Terjadi kesalahan saat menyimpan.';
                    }

                    if (data.message) {
                        return data.message;
                    }

                    if (data.errors && typeof data.errors === 'object') {
                        const firstKey = Object.keys(data.errors)[0];
                        if (firstKey && Array.isArray(data.errors[firstKey]) && data.errors[firstKey][0]) {
                            return data.errors[firstKey][0];
                        }
                    }

                    return 'Terjadi kesalahan saat menyimpan.';
                },

                async fetchCourses() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/admin/api/courses?sort=terbaru&per_page=100', {
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.courses = data.data.courses;
                            this.applyLockedCourseSelection();
                        }
                    } catch (error) {
                        console.error('Error fetching courses:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                applyLockedCourseSelection() {
                    const lockedId = String(this.lockedCourseId || '');
                    if (!lockedId) {
                        this.courseLocked = false;
                        return;
                    }

                    const matchedCourse = this.courses.find((course) => String(course.id) === lockedId);
                    if (!matchedCourse) {
                        this.selectedCourseId = '';
                        this.courseLocked = false;
                        return;
                    }

                    this.selectedCourseId = lockedId;
                    this.courses = [matchedCourse];
                    this.courseLocked = true;
                },

                async saveBacaan() {
                    if (!this.selectedCourseId) {
                        alert('Mohon pilih kursus terlebih dahulu.');
                        return;
                    }
                    if (!this.form.judul_modul || !String(this.form.judul_modul).trim()) {
                        alert('Mohon isi judul materi bacaan.');
                        return;
                    }

                    this.isSubmitting = true;
                    try {
                        const formData = new FormData();
                        formData.append('judul_modul', this.form.judul_modul);
                        formData.append('tipe', this.form.tipe);
                        formData.append('konten', this.form.konten ?? '');
                        formData.append('durasi', this.form.durasi ?? 10);

                        this.normalizedReferences().forEach((url) => {
                            formData.append('sumber_referensi[]', url);
                        });

                        if (this.lampiranFile) {
                            formData.append('lampiran_file', this.lampiranFile);
                        }

                        const response = await fetch(`/admin/api/courses/${this.selectedCourseId}/modules`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok && data.success) {
                            alert('Materi Bacaan berhasil ditambahkan!');
                            // Reset form
                            this.form.judul_modul = '';
                            this.form.konten = '';
                            this.form.durasi = 10;
                            this.form.sumber_referensi = [''];
                            this.clearLampiran();
                        } else {
                            alert('Gagal menyimpan: ' + this.extractErrorMessage(data));
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
</x-layouts.admin>

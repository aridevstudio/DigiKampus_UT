<x-layouts.dosen title="Edit Kursus" active="kursus-saya">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white" x-data="{ kat: '{{ old('kategori', $course->kategori ?? 'kursus') }}' }">
            <span x-show="kat !== 'webinar'">Edit Kursus</span>
            <span x-show="kat === 'webinar'" x-cloak>Edit Webinar</span>
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi dan struktur materi kursus Anda</p>
    </div>

    @php
        $isDraftCourse = strtolower((string) ($course->status ?? '')) === 'draft';
    @endphp

    @if(($course->kategori ?? '') === 'webinar' && ($course->approval_status ?? '') === 'pending')
    <div class="mb-6 rounded-xl border border-blue-200 dark:border-blue-800/40 bg-blue-50 dark:bg-blue-900/10 px-4 py-3 text-sm text-blue-700 dark:text-blue-300">
        Webinar ini sedang menunggu persetujuan admin. Jika Anda menyimpan perubahan saat status tetap aktif, webinar akan tetap diajukan untuk review.
    </div>
    @elseif(($course->kategori ?? '') === 'webinar' && ($course->approval_status ?? '') === 'ditolak')
    <div class="mb-6 rounded-xl border border-red-200 dark:border-red-800/40 bg-red-50 dark:bg-red-900/10 px-4 py-3 text-sm text-red-700 dark:text-red-300">
        Webinar ini ditolak admin.
        @if(!empty($course->approval_notes))
            Catatan: {{ $course->approval_notes }}
        @endif
    </div>
    @endif

    @if(!$isDraftCourse)
    <div class="mb-6 rounded-xl border border-amber-200 dark:border-amber-800/40 bg-amber-50 dark:bg-amber-900/10 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
        Kursus sudah dipublish. Perubahan modul/materi hanya bisa dilakukan saat status kursus masih Draft.
    </div>
    @endif

    <style>
        #editKursusGrid {
            grid-template-columns: 1fr;
        }

        @media (min-width: 1024px) {
            #editKursusGrid { grid-template-columns: 3fr 2fr; }
        }
    </style>
    <div class="grid grid-cols-1 gap-6 items-start w-full" id="editKursusGrid">
        {{-- Informasi Kursus --}}
        <div class="w-full">
            <div class="bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-xl shadow-sm p-4 sm:p-6 lg:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Kursus</h2>
                </div>

                <form id="editCourseForm" action="{{ route('dosen.kursus.update', $course->id_course) }}" method="POST" enctype="multipart/form-data" x-data="{ isLoading: false, selectedKategori: '{{ old('kategori', $course->kategori ?? 'kursus') }}' }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Kursus</label>
                            <input type="text" name="nama_course" value="{{ old('nama_course', $course->nama_course) }}" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('nama_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Kursus <span class="text-red-500">*</span></label>
                            <input type="text" id="edit_kode_course" name="kode_course" value="{{ old('kode_course', $course->kode_course) }}" required placeholder="Contoh: EKMA4116" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Gunakan kode unik, karena dipakai sebagai identitas kursus.</p>
                            @error('kode_course')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Kursus</label>
                            <textarea name="deskripsi" rows="4" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('deskripsi', $course->deskripsi) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Persyaratan Kursus</label>
                            <textarea name="persyaratan" rows="4" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none" placeholder="Satu persyaratan per baris">{{ old('persyaratan', $course->persyaratan) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Contoh: STIN4101 - Pengantar TI, satu persyaratan per baris.</p>
                            @error('persyaratan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Tujuan Pembelajaran --}}
                        <div x-show="selectedKategori !== 'webinar'" x-cloak class="rounded-xl border border-emerald-200 dark:border-emerald-700/50 bg-emerald-50/40 dark:bg-emerald-900/10 p-4 space-y-3">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h5 class="text-sm font-semibold text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Tujuan Pembelajaran
                                    </h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kompetensi yang akan dicapai mahasiswa setelah menyelesaikan kursus.</p>
                                </div>
                                <button type="button" id="editAddLearningGoalButton" data-add-learning-goal class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Tambah Tujuan
                                </button>
                            </div>

                            <div id="editLearningGoalsContainer" class="space-y-3">
                                @php
                                    $editGoalsPrefill = old('learning_goals');
                                    if ($editGoalsPrefill === null) {
                                        $editGoalsPrefill = ($course->relationLoaded('learningGoals') ? $course->learningGoals : $course->learningGoals()->orderBy('urutan')->get())
                                            ->map(fn ($g) => ['judul_goal' => $g->judul_goal, 'deskripsi' => $g->deskripsi])
                                            ->all();
                                    }
                                    if (empty($editGoalsPrefill)) {
                                        $editGoalsPrefill = [['judul_goal' => '', 'deskripsi' => '']];
                                    }
                                @endphp
                                @foreach ($editGoalsPrefill as $index => $goal)
                                    <div class="learning-goal-card rounded-xl border border-emerald-100 dark:border-emerald-800/30 bg-white dark:bg-gray-800 p-3" data-learning-goal-card>
                                        <div class="mb-2 flex items-center justify-between gap-2">
                                            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300" data-learning-goal-title>Tujuan {{ $index + 1 }}</span>
                                            <button type="button" class="remove-learning-goal inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-learning-goal {{ count($editGoalsPrefill) === 1 ? 'disabled' : '' }}>
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                        <div class="space-y-2">
                                            <input type="text" name="learning_goals[{{ $index }}][judul_goal]" value="{{ is_array($goal) ? ($goal['judul_goal'] ?? '') : ($goal->judul_goal ?? '') }}" placeholder="Contoh: Memahami konsep OOP" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                            <textarea name="learning_goals[{{ $index }}][deskripsi]" rows="2" placeholder="Deskripsi singkat..." class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none">{{ is_array($goal) ? ($goal['deskripsi'] ?? '') : ($goal->deskripsi ?? '') }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('learning_goals')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
                            @error('learning_goals.*.judul_goal')<p class="text-red-500 text-xs">Tujuan pembelajaran harus memiliki judul yang diisi.</p>@enderror
                        </div>

                        {{-- Baris 1: Jurusan/Prodi & Tingkat Kesulitan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jurusan/Prodi</label>
                                <select name="id_jurusan" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusans ?? [] as $jurusan)
                                    <option value="{{ $jurusan->id_jurusan }}" {{ old('id_jurusan', $course->id_jurusan) == $jurusan->id_jurusan ? 'selected' : '' }}>{{ $jurusan->nama_jurusan }}</option>
                                    @endforeach
                                </select>
                                @error('id_jurusan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tingkat Kesulitan</label>
                                <select name="level" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Pilih Level</option>
                                    <option value="Pemula" {{ old('level', $course->level) == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                                    <option value="Menengah" {{ old('level', $course->level) == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                                    <option value="Mahir" {{ old('level', $course->level) == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                                </select>
                                @error('level')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Baris 2: Estimasi Waktu Belajar & Tipe Harga (sejajar 2 kolom sesuai wireframe) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimasi Waktu Belajar</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="number" name="estimasi_waktu" value="{{ old('estimasi_waktu', $course->estimasi_waktu) }}" min="0" placeholder="20" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <select name="durasi_satuan" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="Jam" {{ old('durasi_satuan', $course->durasi_satuan ?: 'Jam') === 'Jam' ? 'selected' : '' }}>Jam</option>
                                        <option value="Minggu" {{ old('durasi_satuan', $course->durasi_satuan) === 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                </div>
                                @error('estimasi_waktu')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                @error('durasi_satuan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Harga</label>
                                <select name="tipe" id="tipe" required onchange="toggleHarga()" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="gratis" {{ old('tipe', $course->tipe) === 'gratis' ? 'selected' : '' }}>Gratis</option>
                                    <option value="berbayar" {{ old('tipe', $course->tipe) === 'berbayar' ? 'selected' : '' }}>Berbayar</option>
                                </select>
                                @error('tipe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Kategori Kursus (Kursus/Webinar/Tiket) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kursus</label>
                            <select name="kategori" x-model="selectedKategori" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="kursus" {{ old('kategori', $course->kategori) === 'kursus' ? 'selected' : '' }}>Kursus</option>
                                <option value="webinar" {{ old('kategori', $course->kategori) === 'webinar' ? 'selected' : '' }}>Webinar</option>
                                <option value="tiket" {{ old('kategori', $course->kategori) === 'tiket' ? 'selected' : '' }}>Tiket</option>
                            </select>
                            @error('kategori')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Template Sertifikat --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Sertifikat</label>
                            <input type="hidden" name="sertifikat" value="1">
                            <select name="certificate_template_id" required class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Template Sertifikat</option>
                                @foreach($certificateTemplates as $template)
                                    <option value="{{ $template->id }}" {{ old('certificate_template_id', $course->certificate_template_id) == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                                @endforeach
                            </select>
                            @error('certificate_template_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                <span x-show="selectedKategori !== 'webinar'">URL Playlist YouTube (Opsional)</span>
                                <span x-show="selectedKategori === 'webinar'" x-cloak>Link Meeting (Zoom/Google Meet)</span>
                            </label>
                            <input type="url" name="youtube_playlist" value="{{ old('youtube_playlist', $course->youtube_playlist) }}" :placeholder="selectedKategori === 'webinar' ? 'https://zoom.us/j/... atau https://meet.google.com/...' : 'https://www.youtube.com/playlist?list=...'" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('youtube_playlist')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Jadwal Pelaksanaan (Webinar Only) --}}
                        <div x-show="selectedKategori === 'webinar'" x-transition x-cloak class="rounded-xl border border-indigo-200 dark:border-indigo-700/50 bg-indigo-50/30 dark:bg-indigo-900/10 p-4 space-y-4">
                            <h5 class="text-sm font-semibold text-indigo-700 dark:text-indigo-300 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                Jadwal Pelaksanaan
                            </h5>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Tanggal Webinar <span class="text-red-400">*</span></label>
                                <input type="date" name="tanggal_webinar" value="{{ old('tanggal_webinar', optional($course->tanggal_webinar)->format('Y-m-d')) }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('tanggal_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Mulai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_mulai_webinar" value="{{ old('jam_mulai_webinar', $course->jam_mulai_webinar ?? '') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('jam_mulai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Jam Selesai <span class="text-red-400">*</span></label>
                                    <input type="time" name="jam_selesai_webinar" value="{{ old('jam_selesai_webinar', $course->jam_selesai_webinar ?? '') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('jam_selesai_webinar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1.5">Kuota Peserta (Opsional)</label>
                                <input type="number" name="kuota_peserta" min="1" placeholder="Kosongkan jika tidak dibatasi" value="{{ old('kuota_peserta', $course->kuota_peserta ?? '') }}" class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('kuota_peserta')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Harga (condisional, muncul kalau Berbayar) --}}
                        <div id="hargaField" class="{{ old('tipe', $course->tipe) === 'berbayar' ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga (Rp)</label>
                            <input type="number" name="harga" value="{{ old('harga', $course->harga) }}" min="0" class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('harga')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Thumbnail Kursus</label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 flex items-center gap-4">
                                <div class="shrink-0 relative group">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail" class="w-32 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-32 h-20 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm mb-1">Ganti Thumbnail</h4>
                                    <p class="text-xs text-gray-500 mb-3">PNG, JPG, WebP hingga 2MB. Rasio 16:9 direkomendasikan</p>
                                    <label class="inline-flex items-center px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 cursor-pointer transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                        Upload Gambar
                                        <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp" data-max-size-mb="2" class="hidden" onchange="previewImage(this)">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ ($course->kategori ?? '') === 'webinar' ? 'Status Webinar' : 'Status Kursus' }}</label>
                             <div class="flex gap-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="aktif" {{ $course->status === 'aktif' ? 'checked' : '' }} class="form-radio text-green-500 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ($course->kategori ?? '') === 'webinar' ? 'Ajukan / Aktif' : 'Aktif' }}</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="draft" {{ $course->status === 'draft' ? 'checked' : '' }} class="form-radio text-yellow-500 focus:ring-yellow-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Draft</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="nonaktif" {{ $course->status === 'nonaktif' ? 'checked' : '' }} class="form-radio text-red-500 focus:ring-red-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Nonaktif</span>
                                </label>
                             </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-8 flex justify-center items-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-base transition shadow-lg shadow-blue-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{ ($course->kategori ?? '') === 'webinar' ? 'Simpan Perubahan Webinar' : 'Simpan Perubahan Informasi' }}
                    </button>
                    
                    <div class="mt-4 text-center">
                         <a href="{{ route('dosen.kursus.preview', $course->id_course) }}" target="_blank" class="text-sm text-blue-500 hover:underline">Lihat Pratinjau Kursus &rarr;</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modul Utama --}}
        <div class="w-full">
            <div class="bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 rounded-xl shadow-sm p-4 sm:p-6 lg:p-8 flex flex-col min-h-[400px]">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Modul Utama</h2>
                <div id="modulesList" class="space-y-6 flex-1">
                @forelse($course->modules as $module)
                <div data-module-id="{{ $module->id_module }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    {{-- Module Header --}}
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 rounded-t-2xl group">
                        <div class="flex items-center gap-3">
                            <div class="cursor-move module-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-white dark:hover:bg-gray-600 transition">
                                <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $module->judul_module }}</h3>
                                @if($module->deskripsi)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $module->deskripsi }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            @php
                                $modulePrimaryMaterial = $module->materials->first();
                            @endphp
                            <button
                                @if($isDraftCourse && $modulePrimaryMaterial)
                                    onclick="redirectToTypedContentEditor('{{ $modulePrimaryMaterial->tipe }}', {{ $module->id_module }}, {{ $modulePrimaryMaterial->id_material }})"
                                @else
                                    type="button" disabled
                                @endif
                                class="p-2 text-gray-400 {{ $isDraftCourse && $modulePrimaryMaterial ? 'hover:text-blue-500 transition' : 'opacity-40 cursor-not-allowed' }}"
                                title="{{ !$isDraftCourse ? 'Kursus sudah publish' : (!$modulePrimaryMaterial ? 'Belum ada materi untuk diedit' : 'Edit konten') }}"
                            >
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button
                                @if($isDraftCourse)
                                    onclick="confirmDeleteModule({{ $module->id_module }})"
                                @else
                                    type="button" disabled
                                @endif
                                class="p-2 text-gray-400 {{ $isDraftCourse ? 'hover:text-red-500 transition' : 'opacity-40 cursor-not-allowed' }}"
                                title="{{ $isDraftCourse ? 'Hapus modul' : 'Kursus sudah publish' }}"
                            >
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Module Body (Materials) --}}
                    <div class="p-4">
                        <div id="materials-{{ $module->id_module }}" class="space-y-3 materials-list" data-module-id="{{ $module->id_module }}">
                            @if($module->materials->count() === 0)
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 mb-4">
                                    <h4 class="font-semibold text-blue-700 dark:text-blue-300 mb-3 text-base">Tambah Konten Awal</h4>
                                    @if(!$isDraftCourse)
                                        <p class="text-xs text-amber-600 dark:text-amber-300 mb-3">Kursus sudah dipublish, tambah konten dinonaktifkan.</p>
                                    @endif
                                    <form action="{{ route('dosen.material.store', $course->id_course) }}" method="POST" class="initial-content-form" x-data="{ isLoading: false }" @submit="isLoading = true">
                                        @csrf
                                        <input type="hidden" name="id_module" value="{{ $module->id_module }}">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                                                <input type="text" name="judul_material" required class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                                <select name="tipe" required class="initial-type-select w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                                    <option value="video">Video</option>
                                                    <option value="bacaan">Bacaan</option>
                                                    <option value="kuis">Kuis</option>
                                                    <option value="tugas">Tugas Akhir (Opsional)</option>
                                                </select>
                                                <p class="initial-type-hint mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                                            <textarea name="konten" rows="2" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-3">
                                            <div class="initial-video-group">
                                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                                                <input type="url" name="video_url" class="initial-video-input w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div class="initial-durasi-group">
                                                <label class="initial-durasi-label block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                                                <input type="number" name="durasi" min="0" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                        <button type="submit" {{ !$isDraftCourse ? 'disabled' : '' }} class="initial-submit-btn w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center justify-center gap-2 transition shadow shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed">Setup Video</button>
                                    </form>
                                </div>
                            @endif
                            @foreach($module->materials as $material)
                            <div data-material-id="{{ $material->id_material }}" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 transition group/material">
                                {{-- Material Handle --}}
                                <div class="cursor-move material-handle text-gray-400 hover:text-gray-600 p-1">
                                    <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                                </div>

                                {{-- Icon --}}
                                <div class="shrink-0">
                                    @php
                                        $colors = [
                                            'video' => 'bg-red-100 text-red-500', 
                                            'bacaan' => 'bg-blue-100 text-blue-500', 
                                            'kuis' => 'bg-green-100 text-green-500', 
                                            'tugas' => 'bg-purple-100 text-purple-500',
                                            'quiz' => 'bg-green-100 text-green-500',
                                            'text' => 'bg-blue-100 text-blue-500',
                                        ];
                                        $icons = [
                                            'video' => '<path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />',
                                            'bacaan' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />',
                                            'kuis' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />',
                                            'tugas' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />',
                                            'quiz' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />',
                                            'text' => '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />',
                                        ];
                                        $typeLabels = [
                                            'video' => 'video',
                                            'bacaan' => 'bacaan',
                                            'kuis' => 'kuis',
                                            'tugas' => 'tugas',
                                            'quiz' => 'kuis',
                                            'text' => 'bacaan',
                                        ];
                                        $typeKey = $material->tipe;
                                    @endphp
                                    <div class="w-8 h-8 rounded-lg {{ $colors[$typeKey] ?? $colors['bacaan'] }} flex items-center justify-center">
                                        <svg width="16" height="16" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">{!! $icons[$typeKey] ?? $icons['bacaan'] !!}</svg>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ $material->judul_material }}</h4>
                                    <div class="text-xs text-gray-500 flex gap-2">
                                        <span class="capitalize">{{ $typeLabels[$typeKey] ?? $typeKey }}</span>
                                        @if($material->durasi) &bull; {{ $material->durasi }} menit @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center opacity-0 group-hover/material:opacity-100 transition-opacity">
                                    <button
                                        @if($isDraftCourse)
                                            onclick="redirectToTypedContentEditor('{{ $material->tipe }}', {{ $module->id_module }}, {{ $material->id_material }})"
                                        @else
                                            type="button" disabled
                                        @endif
                                        class="p-1.5 text-gray-400 {{ $isDraftCourse ? 'hover:text-blue-500' : 'opacity-40 cursor-not-allowed' }}"
                                        title="{{ $isDraftCourse ? 'Edit materi' : 'Kursus sudah publish' }}"
                                    >
                                        <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <button
                                        @if($isDraftCourse)
                                            onclick="confirmDeleteMaterial({{ $material->id_material }})"
                                        @else
                                            type="button" disabled
                                        @endif
                                        class="p-1.5 text-gray-400 {{ $isDraftCourse ? 'hover:text-red-500' : 'opacity-40 cursor-not-allowed' }}"
                                        title="{{ $isDraftCourse ? 'Hapus materi' : 'Kursus sudah publish' }}"
                                    >
                                        <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <button onclick="openAddMaterialModal({{ $module->id_module }})" {{ !$isDraftCourse ? 'disabled' : '' }} class="w-full flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400 font-medium px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-lg hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 dark:hover:text-blue-400 bg-white dark:bg-gray-800 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah Konten
                            </button>
                            <button onclick="openAddFinalTaskModal({{ $module->id_module }})" {{ !$isDraftCourse ? 'disabled' : '' }} class="w-full flex items-center justify-center gap-2 text-sm text-purple-700 dark:text-purple-300 font-medium px-4 py-2.5 border border-purple-200 dark:border-purple-600/40 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg width="16" height="16" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M8 4h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z" /></svg>
                                Tambah Tugas Akhir (Opsional)
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-4 sm:p-6 lg:p-8 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                         <svg width="24" height="24" class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-gray-900 dark:text-white font-medium">Belum ada modul</h3>
                    <p class="text-gray-500 text-sm mt-1">Buat modul pertama untuk mulai menyusun materi.</p>
                </div>
                @endforelse
            </div>

            <button onclick="openAddModuleModal()" {{ !$isDraftCourse ? 'disabled' : '' }} class="w-full mt-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2 text-base transition shadow-lg shadow-blue-500/30 disabled:opacity-40 disabled:cursor-not-allowed">
                <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul Baru
            </button>
        </div>
    </div>

    {{-- Modals --}}

    {{-- Add Module Modal --}}
    <div id="addModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModuleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form action="{{ route('dosen.module.store', $course->id_course) }}" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Modul Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_module" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Modul</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Module Modal --}}
    <div id="editModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditModuleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="editModuleForm" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Modul</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_module" id="edit_module_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_module_deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Module Modal --}}
    <div id="deleteModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                 <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg width="32" height="32" class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Modul?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Semua materi dalam modul ini juga akan dihapus. Data tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteModuleForm" method="POST" class="inline" x-data="{ isLoading: false }" @submit="isLoading = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus Modul</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Material Modal --}}
    <div id="addMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddMaterialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="addMaterialForm" action="{{ route('dosen.material.store', $course->id_course) }}" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true" onsubmit="return handleAddMaterialSubmit(event)">
                    @csrf
                    <input type="hidden" name="id_module" id="add_material_module_id">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Materi Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                            <input type="text" name="judul_material" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="add_material_tipe" onchange="onAddMaterialTypeChange()" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas Akhir (Opsional)</option>
                            </select>
                            <p id="add_material_type_hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div>
                            <label id="add_material_konten_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" id="add_material_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div id="add_material_video_group">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" name="video_url" id="add_material_video_url" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="add_material_durasi_group">
                            <label id="add_material_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" id="add_material_durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" id="add_material_submit_btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Setup Video</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Material Modal --}}
    <div id="editMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
             <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditMaterialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="editMaterialForm" method="POST" class="p-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Materi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi</label>
                            <input type="text" name="judul_material" id="edit_material_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="edit_material_tipe" onchange="onEditMaterialTypeChange()" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas Akhir (Opsional)</option>
                            </select>
                            <p id="edit_material_type_hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div>
                            <label id="edit_material_konten_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" id="edit_material_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                            <p id="edit_material_konten_hint" class="mt-1 hidden text-xs text-indigo-600 dark:text-indigo-300"></p>
                        </div>
                        <div id="edit_material_video_group">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" name="video_url" id="edit_material_video_url" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div id="edit_material_durasi_group">
                            <label id="edit_material_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" id="edit_material_durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Material Modal --}}
    <div id="deleteMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                 <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg width="32" height="32" class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Materi?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Materi akan dihapus permanen.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteMaterialForm" method="POST" class="inline" x-data="{ isLoading: false }" @submit="isLoading = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const courseId = {{ $course->id_course }};
        const courseIsDraft = @json($isDraftCourse);
        const typedContentRoutes = {
            video: @json(route('dosen.kelola-video')),
            bacaan: @json(route('dosen.kelola-bacaan')),
            kuis: @json(route('dosen.kelola-quiz')),
            tugas: @json(route('dosen.kelola-tugas')),
        };

        function showDraftOnlyLockMessage() {
            alert('Kursus sudah dipublish. Perubahan modul hanya bisa saat status draft.');
        }

        function normalizeMaterialType(type) {
            const value = (type || '').toLowerCase();
            if (value === 'quiz') return 'kuis';
            if (value === 'text') return 'bacaan';
            if (['video', 'bacaan', 'kuis', 'tugas'].includes(value)) return value;
            return 'video';
        }

        function applyMaterialTypeState(prefix, rawType) {
            const type = normalizeMaterialType(rawType);
            const kontenLabel = document.getElementById(`${prefix}_konten_label`);
            const kontenInput = document.getElementById(`${prefix}_konten`);
            const typeHint = document.getElementById(`${prefix}_type_hint`);
            const submitBtn = document.getElementById(`${prefix}_submit_btn`);
            const videoGroup = document.getElementById(`${prefix}_video_group`);
            const videoInput = document.getElementById(`${prefix}_video_url`);
            const durasiGroup = document.getElementById(`${prefix}_durasi_group`);
            const durasiLabel = document.getElementById(`${prefix}_durasi_label`);
            const durasiInput = document.getElementById(`${prefix}_durasi`);
            const usesSetupFlow = Boolean(submitBtn);

            if (!kontenLabel || !videoGroup || !durasiGroup) {
                return;
            }

            if (type === 'video') {
                kontenLabel.textContent = 'Deskripsi Video';
                if (kontenInput) kontenInput.placeholder = 'Ringkasan materi video...';
                if (typeHint) {
                    typeHint.textContent = usesSetupFlow
                        ? 'Klik Setup Video untuk lanjut ke halaman kelola video.'
                        : 'Masukkan URL video (opsional) dan isi durasi secara manual.';
                }
                if (submitBtn) submitBtn.textContent = 'Setup Video';
                videoGroup.classList.remove('hidden');
                if (videoInput) videoInput.required = false;
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Video (menit)';
                return;
            }

            if (type === 'bacaan') {
                kontenLabel.textContent = 'Konten Bacaan';
                if (kontenInput) kontenInput.placeholder = 'Tulis konten bacaan...';
                if (typeHint) {
                    typeHint.textContent = usesSetupFlow
                        ? 'Klik Setup Bacaan untuk lanjut ke halaman kelola bacaan.'
                        : 'Isi konten bacaan dan estimasi waktu baca.';
                }
                if (submitBtn) submitBtn.textContent = 'Setup Bacaan';
                videoGroup.classList.add('hidden');
                if (videoInput) {
                    videoInput.required = false;
                    videoInput.value = '';
                }
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Estimasi Baca (menit)';
                return;
            }

            if (type === 'kuis') {
                kontenLabel.textContent = 'Instruksi Kuis';
                if (kontenInput) kontenInput.placeholder = 'Petunjuk pengerjaan kuis...';
                if (typeHint) {
                    typeHint.textContent = usesSetupFlow
                        ? 'Klik Setup Kuis untuk lanjut ke halaman input kuis.'
                        : 'Isi instruksi kuis dan durasi pengerjaan.';
                }
                if (submitBtn) submitBtn.textContent = 'Setup Kuis';
                videoGroup.classList.add('hidden');
                if (videoInput) {
                    videoInput.required = false;
                    videoInput.value = '';
                }
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Kuis (menit)';
                return;
            }

            kontenLabel.textContent = 'Deskripsi Tugas';
            if (kontenInput) kontenInput.placeholder = 'Jelaskan instruksi dan ketentuan tugas...';
            if (typeHint) {
                typeHint.textContent = usesSetupFlow
                    ? 'Klik Setup Tugas untuk lanjut ke halaman kelola tugas.'
                    : 'Isi deskripsi tugas. Durasi tidak wajib untuk tipe ini.';
            }
            if (submitBtn) submitBtn.textContent = 'Setup Tugas';
            videoGroup.classList.add('hidden');
            if (videoInput) {
                videoInput.required = false;
                videoInput.value = '';
            }
            durasiGroup.classList.add('hidden');
            if (durasiInput) durasiInput.value = '';
        }

        function applyInitialContentTypeState(form) {
            if (!form) return;

            const select = form.querySelector('.initial-type-select');
            const videoGroup = form.querySelector('.initial-video-group');
            const videoInput = form.querySelector('.initial-video-input');
            const durasiGroup = form.querySelector('.initial-durasi-group');
            const durasiLabel = form.querySelector('.initial-durasi-label');
            const durasiInput = form.querySelector('input[name="durasi"]');
            const typeHint = form.querySelector('.initial-type-hint');
            const submitBtn = form.querySelector('.initial-submit-btn');

            if (!select || !videoGroup || !durasiGroup) {
                return;
            }

            const type = normalizeMaterialType(select.value || 'video');

            if (type === 'video') {
                videoGroup.classList.remove('hidden');
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Video (menit)';
                if (typeHint) typeHint.textContent = 'Klik Setup Video untuk lanjut ke halaman kelola video.';
                if (submitBtn) submitBtn.textContent = 'Setup Video';
                return;
            }

            videoGroup.classList.add('hidden');
            if (videoInput) videoInput.value = '';

            if (type === 'bacaan') {
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Estimasi Baca (menit)';
                if (typeHint) typeHint.textContent = 'Klik Setup Bacaan untuk lanjut ke halaman kelola bacaan.';
                if (submitBtn) submitBtn.textContent = 'Setup Bacaan';
                return;
            }

            if (type === 'kuis') {
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Kuis (menit)';
                if (typeHint) typeHint.textContent = 'Klik Setup Kuis untuk lanjut ke halaman input kuis.';
                if (submitBtn) submitBtn.textContent = 'Setup Kuis';
                return;
            }

            durasiGroup.classList.add('hidden');
            if (durasiInput) durasiInput.value = '';
            if (typeHint) typeHint.textContent = 'Klik Setup Tugas untuk lanjut ke halaman kelola tugas.';
            if (submitBtn) submitBtn.textContent = 'Setup Tugas';
        }

        function initializeInitialContentForms() {
            document.querySelectorAll('.initial-content-form').forEach((form) => {
                const select = form.querySelector('.initial-type-select');
                if (!select) return;

                const updateState = () => applyInitialContentTypeState(form);
                const handleSubmit = (event) => {
                    event.preventDefault();
                    redirectToTypedContentSetup(form, select.value || 'video');
                };

                select.addEventListener('change', updateState);
                form.addEventListener('submit', handleSubmit);
                updateState();
            });
        }

        function onAddMaterialTypeChange() {
            const select = document.getElementById('add_material_tipe');
            applyMaterialTypeState('add_material', select?.value || 'video');
        }

        function onEditMaterialTypeChange() {
            const select = document.getElementById('edit_material_tipe');
            applyMaterialTypeState('edit_material', select?.value || 'video');
            syncStructuredEditMaterialContent();
        }

        function syncStructuredEditMaterialContent() {
            const input = document.getElementById('edit_material_konten');
            const hint = document.getElementById('edit_material_konten_hint');
            const typeSelect = document.getElementById('edit_material_tipe');

            if (!input || !hint || !typeSelect) {
                return;
            }

            const isStructured = input.dataset.structured === 'true';
            const originalType = input.dataset.structuredType || '';
            const currentType = normalizeMaterialType(typeSelect.value || 'video');
            const shouldLockStructuredPreview = isStructured && originalType === currentType;

            input.readOnly = shouldLockStructuredPreview;
            input.classList.toggle('cursor-not-allowed', shouldLockStructuredPreview);
            input.classList.toggle('opacity-90', shouldLockStructuredPreview);

            if (shouldLockStructuredPreview) {
                input.value = input.dataset.displayContent || input.value;
                hint.textContent = input.dataset.structuredHint || '';
                hint.classList.toggle('hidden', !hint.textContent.trim());
                return;
            }

            hint.textContent = '';
            hint.classList.add('hidden');
        }

        function redirectToTypedContentSetup(form, rawType) {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return false;
            }

            const selectedType = normalizeMaterialType(rawType);
            const targetRoute = typedContentRoutes[selectedType];

            if (!targetRoute) {
                return true;
            }

            const judulInput = form.querySelector('input[name="judul_material"]');
            const judul = judulInput?.value?.trim();

            if (!judul) {
                alert('Judul materi wajib diisi sebelum setup konten.');
                judulInput?.focus();
                return false;
            }

            const params = new URLSearchParams();
            params.set('course_id', String(courseId));
            params.set('modul_judul', judul);

            const konten = form.querySelector('textarea[name="konten"]')?.value?.trim();
            const durasi = form.querySelector('input[name="durasi"]')?.value;
            const videoUrl = form.querySelector('input[name="video_url"]')?.value?.trim();
            const moduleId = form.querySelector('input[name="id_module"]')?.value;

            if (konten) params.set('modul_konten', konten);
            if (durasi !== undefined && durasi !== null && durasi !== '') {
                params.set('modul_durasi', durasi);
            }
            if (videoUrl) params.set('modul_video_url', videoUrl);
            if (moduleId) params.set('module_id', moduleId);

            const query = params.toString();
            window.location.href = query ? `${targetRoute}?${query}` : targetRoute;
            return false;
        }

        function redirectToTypedContentEditor(rawType, moduleId, materialId) {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }

            const selectedType = normalizeMaterialType(rawType);
            const targetRoute = typedContentRoutes[selectedType];

            if (!targetRoute || !materialId) {
                alert('Materi belum tersedia untuk diedit.');
                return;
            }

            const params = new URLSearchParams();
            params.set('course_id', String(courseId));
            params.set('material_id', String(materialId));
            if (moduleId) params.set('module_id', String(moduleId));
            params.set('edit_mode', '1');

            window.location.href = `${targetRoute}?${params.toString()}`;
        }

        function handleAddMaterialSubmit(event) {
            event.preventDefault();
            const form = event.target;
            const selectedType = form.querySelector('select[name="tipe"]')?.value || 'video';
            return redirectToTypedContentSetup(form, selectedType);
        }

        // Toggle Harga Field
        function toggleHarga() {
            const tipe = document.getElementById('tipe').value;
            document.getElementById('hargaField').classList.toggle('hidden', tipe !== 'berbayar');
        }

        // Learning Goals add/remove rows (Dosen edit form)
        (function initLearningGoalsEditor() {
            const container = document.getElementById('editLearningGoalsContainer');
            const addBtn = document.getElementById('editAddLearningGoalButton');
            if (!container || !addBtn) return;

            const renumber = () => {
                const cards = Array.from(container.querySelectorAll('[data-learning-goal-card]'));
                cards.forEach((card, idx) => {
                    const title = card.querySelector('[data-learning-goal-title]');
                    if (title) title.textContent = `Tujuan ${idx + 1}`;
                    card.querySelectorAll('input, textarea').forEach((field) => {
                        const name = field.getAttribute('name');
                        if (!name) return;
                        field.setAttribute('name', name.replace(/learning_goals\[\d+\]/, `learning_goals[${idx}]`));
                    });
                    const removeBtn = card.querySelector('[data-remove-learning-goal]');
                    if (removeBtn) removeBtn.disabled = cards.length === 1;
                });
            };

            const createCard = (index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'learning-goal-card rounded-xl border border-emerald-100 dark:border-emerald-800/30 bg-white dark:bg-gray-800 p-3';
                wrapper.setAttribute('data-learning-goal-card', 'true');
                wrapper.innerHTML = `
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300" data-learning-goal-title>Tujuan ${index + 1}</span>
                        <button type="button" class="remove-learning-goal inline-flex h-7 w-7 items-center justify-center rounded-lg border border-red-200 text-red-500 transition hover:bg-red-50" data-remove-learning-goal>
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <input type="text" name="learning_goals[${index}][judul_goal]" placeholder="Contoh: Memahami konsep OOP" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <textarea name="learning_goals[${index}][deskripsi]" rows="2" placeholder="Deskripsi singkat..." class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"></textarea>
                    </div>
                `;
                return wrapper;
            };

            addBtn.addEventListener('click', () => {
                const nextIndex = container.querySelectorAll('[data-learning-goal-card]').length;
                const card = createCard(nextIndex);
                const removeBtn = card.querySelector('[data-remove-learning-goal]');
                removeBtn.addEventListener('click', () => {
                    const cards = container.querySelectorAll('[data-learning-goal-card]');
                    if (cards.length <= 1) return;
                    card.remove();
                    renumber();
                });
                container.appendChild(card);
                renumber();
            });

            container.querySelectorAll('[data-learning-goal-card]').forEach((card) => {
                const removeBtn = card.querySelector('[data-remove-learning-goal]');
                if (removeBtn) {
                    removeBtn.addEventListener('click', () => {
                        const cards = container.querySelectorAll('[data-learning-goal-card]');
                        if (cards.length <= 1) return;
                        card.remove();
                        renumber();
                    });
                }
            });

            renumber();
        })();

        // Initialize Sortable for Modules
        var modulesList = document.getElementById('modulesList');
        if(modulesList && courseIsDraft) {
            Sortable.create(modulesList, {
                handle: '.module-handle',
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: function (evt) {
                    var order = [];
                    document.querySelectorAll('#modulesList > div[data-module-id]').forEach(function(item) {
                        order.push(item.getAttribute('data-module-id'));
                    });
                    
                    fetch(`{{ route('dosen.module.reorder', $course->id_course) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    });
                },
            });
        }

        // Initialize Sortable for Materials (within each module)
        if (courseIsDraft) {
            document.querySelectorAll('.materials-list').forEach(function(list) {
                Sortable.create(list, {
                    group: 'materials', // Allow dragging between modules if needed (optional)
                    handle: '.material-handle',
                    animation: 150,
                    ghostClass: 'bg-blue-50',
                    onEnd: function (evt) {
                        var moduleId = evt.to.getAttribute('data-module-id');
                        var order = [];
                        evt.to.querySelectorAll('[data-material-id]').forEach(function(item) {
                            order.push(item.getAttribute('data-material-id'));
                        });

                        // TODO: Handle move between modules if 'group' is enabled.
                        // For now assuming reorder within module.
                        // If we support moving between modules, we need an endpoint that accepts module_id too.
                        // Using existing reorder endpoint which just updates urutan.

                        fetch(`{{ route('dosen.material.reorder', $course->id_course) }}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: order }) // Should probably send module_id too?
                        });
                    },
                });
            });
        }

        // Module Modal Functions
        function openAddModuleModal() {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }
            document.getElementById('addModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeAddModuleModal() {
            document.getElementById('addModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModuleModal(id, judul, deskripsi) {
            document.getElementById('editModuleForm').action = `/dosen/kursus/${courseId}/module/${id}`;
            document.getElementById('edit_module_judul').value = judul;
            document.getElementById('edit_module_deskripsi').value = deskripsi || '';
            document.getElementById('editModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModuleModal() {
            document.getElementById('editModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function confirmDeleteModule(id) {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }
            document.getElementById('deleteModuleForm').action = `/dosen/kursus/${courseId}/module/${id}`;
            document.getElementById('deleteModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteModuleModal() {
            document.getElementById('deleteModuleModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Material Modal Functions
        function openAddMaterialModal(moduleId, preferredType = 'video') {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }
            const form = document.getElementById('addMaterialForm');
            if (form) {
                form.reset();
            }
            document.getElementById('add_material_module_id').value = moduleId;
            const typeSelect = document.getElementById('add_material_tipe');
            if (typeSelect && ['video', 'bacaan', 'kuis', 'tugas'].includes(preferredType)) {
                typeSelect.value = preferredType;
            }
            onAddMaterialTypeChange();
            document.getElementById('addMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openAddFinalTaskModal(moduleId) {
            openAddMaterialModal(moduleId, 'tugas');
        }
        function closeAddMaterialModal() {
            document.getElementById('addMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditMaterialModal(id) {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }
            fetch(`/dosen/kursus/${courseId}/material/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editMaterialForm').action = `/dosen/kursus/${courseId}/material/${id}`;
                    const normalizedType = normalizeMaterialType(data.tipe || 'video');
                    document.getElementById('edit_material_judul').value = data.judul_material || '';
                    document.getElementById('edit_material_tipe').value = normalizedType;
                    const editKonten = document.getElementById('edit_material_konten');
                    editKonten.value = data.konten_display || data.konten || '';
                    editKonten.dataset.rawContent = data.konten || '';
                    editKonten.dataset.displayContent = data.konten_display || data.konten || '';
                    editKonten.dataset.structured = data.is_structured_content ? 'true' : 'false';
                    editKonten.dataset.structuredType = normalizedType;
                    editKonten.dataset.structuredHint = data.structured_hint || '';
                    document.getElementById('edit_material_video_url').value = data.video_url || '';
                    document.getElementById('edit_material_durasi').value = data.durasi || '';
                    onEditMaterialTypeChange();
                    document.getElementById('editMaterialModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
        }
        function closeEditMaterialModal() {
            document.getElementById('editMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('editMaterialForm')?.addEventListener('submit', function () {
            const editKonten = document.getElementById('edit_material_konten');
            const typeSelect = document.getElementById('edit_material_tipe');

            if (!editKonten || !typeSelect) {
                return;
            }

            const shouldRestoreRawContent = editKonten.dataset.structured === 'true'
                && normalizeMaterialType(typeSelect.value || 'video') === (editKonten.dataset.structuredType || '');

            if (shouldRestoreRawContent) {
                editKonten.value = editKonten.dataset.rawContent || '';
            }
        });

        function confirmDeleteMaterial(id) {
            if (!courseIsDraft) {
                showDraftOnlyLockMessage();
                return;
            }
            document.getElementById('deleteMaterialForm').action = `/dosen/kursus/${courseId}/material/${id}`;
            document.getElementById('deleteMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteMaterialModal() {
            document.getElementById('deleteMaterialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        onAddMaterialTypeChange();
        onEditMaterialTypeChange();
        initializeInitialContentForms();

        function showUploadError(message) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload tidak valid',
                    text: message,
                    confirmButtonText: 'Oke',
                });
                return;
            }

            alert(message);
        }

        function isValidThumbnailFile(input) {
            const file = input?.files?.[0];
            if (!file) return true;

            const maxSizeMb = Number(input.dataset.maxSizeMb || 2);
            if (!file.type.startsWith('image/')) {
                showUploadError('Harap pilih file gambar JPG, PNG, atau WebP.');
                input.value = '';
                return false;
            }

            if (file.size > maxSizeMb * 1024 * 1024) {
                showUploadError(`Ukuran thumbnail maksimal ${maxSizeMb}MB. Kompres gambar atau pilih file yang lebih kecil.`);
                input.value = '';
                return false;
            }

            return true;
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                if (!isValidThumbnailFile(input)) {
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = document.querySelector('img[alt="Thumbnail"]');
                    var placeholder = document.querySelector('.border-dashed .bg-gray-100'); // Selector for placeholder div

                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                        if (placeholder) placeholder.classList.add('hidden'); // Hide placeholder if it exists separate from img
                    } else {
                        // If no image tag exists yet (only placeholder), we might need to swap them or just assume the layout
                        var previewContainer = document.querySelector('.shrink-0.relative.group');
                        if(previewContainer) {
                             previewContainer.innerHTML = `<img src="${e.target.result}" alt="Thumbnail" class="w-32 h-20 object-cover rounded-lg">`;
                        }
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('editCourseForm')?.addEventListener('submit', function (event) {
            const thumbnailInput = this.querySelector('input[name="thumbnail"]');
            if (thumbnailInput && !isValidThumbnailFile(thumbnailInput)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    </script>
    @endpush
</x-layouts.dosen>

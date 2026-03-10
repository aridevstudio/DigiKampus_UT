<x-layouts.admin title="Kelola Modul Kursus">
    {{-- Page Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Modul Kursus</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Atur modul, video, pretest, dan kuis untuk kursus ini</p>
        </div>
        
        {{-- Course Info Card --}}
        <div class="w-full lg:w-auto lg:min-w-[280px] bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 mb-3">{{ $course['nama'] }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Kode:</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $course['kode'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Mahasiswa:</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $course['mahasiswa_count'] }} mahasiswa</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Status:</span>
                    @php
                        $statusColors = [
                            'aktif' => 'text-green-600 dark:text-green-400',
                            'draft' => 'text-yellow-600 dark:text-yellow-400',
                            'nonaktif' => 'text-red-600 dark:text-red-400',
                        ];
                    @endphp
                    <span class="{{ $statusColors[$course['status']] ?? 'text-gray-600' }} font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full {{ $course['status'] === 'aktif' ? 'bg-green-500' : ($course['status'] === 'draft' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                        {{ ucfirst($course['status']) }}
                    </span>
                </div>
            </div>
            <button onclick="openAddModuleModal()" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul Baru
            </button>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <a href="{{ route('admin.kursus') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Kursus
        </a>
    </div>

    {{-- Modules List --}}
    <div id="modulesList" class="space-y-6">
        @forelse($modules ?? [] as $index => $module)
        <div data-id="{{ $module['id'] }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Module Header --}}
            <div class="flex items-center gap-4 p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="text-gray-400 cursor-move handle p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg">Modul {{ $index + 1 }}</span>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $module['judul'] }}</h3>
                    </div>
                    @if($module['deskripsi'])
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ \Str::limit($module['deskripsi'], 100) }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 mt-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-full">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" /></svg>
                            {{ $module['video_count'] }} Video
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 text-xs font-medium rounded-full">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                            {{ $module['bacaan_count'] }} Bacaan
                        </span>
                        @if(count($module['quizzes']) > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 text-xs font-medium rounded-full">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            {{ count($module['quizzes']) }} Kuis
                        </span>
                        @endif
                        @if($module['has_pretest'])
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs font-medium rounded-full">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            Pretest ({{ $module['pretest']['jumlah_soal'] }} Soal)
                        </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="toggleModule({{ $module['id'] }})" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon-module-{{ $module['id'] }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <button onclick="openEditModuleModal({{ $module['id'] }}, {{ json_encode($module['judul']) }}, {{ json_encode($module['deskripsi'] ?? '') }})" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit Modul">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </button>
                    <button onclick="confirmDeleteModule({{ $module['id'] }})" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus Modul">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>

            {{-- Module Details (Expandable) --}}
            <div id="details-module-{{ $module['id'] }}" class="hidden">
                {{-- Pretest Section --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Pretest <span class="text-gray-400 font-normal">(Opsional)</span></h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($module['has_pretest'])
                                        {{ $module['pretest']['jumlah_soal'] }} soal &bull; {{ $module['pretest']['durasi'] }} menit &bull; Total {{ $module['pretest']['total_bobot'] }} poin
                                    @else
                                        Belum ada pretest untuk modul ini
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($module['has_pretest'])
                            <a href="{{ route('admin.quiz.kelola', ['courseId' => $course['id'], 'moduleId' => $module['id']]) }}?focus_quiz={{ $module['pretest']['id'] }}" class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition">Kelola Soal Pretest</a>
                            @endif
                            <button onclick="togglePretest({{ $module['id'] }})" class="px-3 py-1.5 {{ $module['has_pretest'] ? 'bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:text-green-400' }} text-xs font-medium rounded-lg transition">
                                {{ $module['has_pretest'] ? 'Hapus Pretest' : 'Aktifkan Pretest' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Materials --}}
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" /></svg>
                            Materi (Video & Bacaan)
                        </h4>
                        <button onclick="openAddMaterialModal({{ $module['id'] }})" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Tambah Materi
                        </button>
                    </div>
                    @if(count($module['materials']) > 0)
                    <div class="space-y-2">
                        @foreach($module['materials'] as $matIndex => $material)
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400 w-6">{{ $matIndex + 1 }}.</span>
                            @if($material['tipe'] === 'video')
                            <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" /></svg>
                            @else
                            <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>
                            @endif
                            <div class="flex-1">
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $material['judul'] }}</span>
                                @if($material['durasi'])
                                <span class="text-xs text-gray-400 ml-2">{{ $material['durasi'] }} menit</span>
                                @endif
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-medium rounded {{ $material['tipe'] === 'video' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">{{ ucfirst($material['tipe']) }}</span>
                            <button onclick="confirmDeleteMaterial({{ $material['id'] }})" class="p-1 text-gray-400 hover:text-red-500 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center py-6 text-sm text-gray-400">Belum ada materi. Tambahkan video atau bacaan.</p>
                    @endif
                </div>

                {{-- Quizzes --}}
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            Kuis (setiap soal memiliki poin)
                        </h4>
                        <a href="{{ route('admin.quiz.kelola', ['courseId' => $course['id'], 'moduleId' => $module['id']]) }}" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded-lg transition flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Kelola Kuis
                        </a>
                    </div>
                    @if(count($module['quizzes']) > 0)
                    <div class="space-y-2">
                        @foreach($module['quizzes'] as $quiz)
                        <div class="flex items-center gap-3 px-4 py-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-xl border border-yellow-100 dark:border-yellow-900/30">
                            <svg class="w-4 h-4 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            <div class="flex-1">
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $quiz['judul'] }}</span>
                                <span class="text-xs text-gray-400 ml-2">{{ $quiz['jumlah_soal'] }} soal &bull; {{ $quiz['durasi'] }} menit &bull; Total {{ $quiz['total_bobot'] }} poin</span>
                            </div>
                            <a href="{{ route('admin.quiz.kelola', ['courseId' => $course['id'], 'moduleId' => $module['id']]) }}?focus_quiz={{ $quiz['id'] }}" class="px-2.5 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-[10px] font-medium rounded-lg transition">Kelola Soal</a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center py-6 text-sm text-gray-400">Belum ada kuis. Klik "Kelola Kuis" untuk membuat.</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada modul</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tambahkan modul pertama. Setiap modul bisa berisi video, bacaan, pretest (opsional), dan kuis.</p>
            <button onclick="openAddModuleModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Modul Baru
            </button>
        </div>
        @endforelse
    </div>

    {{-- Add Module Modal --}}
    <div id="addModuleModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModuleModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModuleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form action="{{ route('admin.module.store', $course['id']) }}" method="POST" class="p-6">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Modul Baru</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_module" required placeholder="Contoh: Modul 1 - Pengenalan" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" placeholder="Deskripsi modul..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Simpan Modul</button>
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="editModuleForm" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Modul</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_module" id="edit_module_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_module_deskripsi" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Material Modal --}}
    <div id="addMaterialModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddMaterialModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddMaterialModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <form id="addMaterialForm" action="{{ route('admin.material.store', $course['id']) }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="id_module" id="add_material_module_id">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Materi</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Materi <span class="text-red-500">*</span></label>
                            <input type="text" name="judul_material" required placeholder="Contoh: Video Pengenalan" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="add_mat_tipe" onchange="onMatTypeChange()" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                            </select>
                        </div>
                        <div id="mat_video_group">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video</label>
                            <input type="url" name="video_url" placeholder="https://youtube.com/..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" rows="3" placeholder="Deskripsi materi..." class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" min="0" placeholder="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Simpan Materi</button>
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
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Modul?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Semua materi, kuis, dan soal di dalam modul ini akan dihapus.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModuleModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteModuleForm" method="POST" class="inline">@csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
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
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Materi?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data materi akan dihapus permanen.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteMaterialModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteMaterialForm" method="POST" class="inline">@csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="fixed bottom-4 right-4 z-[60] hidden">
        <div id="toastContent" class="px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-white text-sm font-medium"><span id="toastMessage"></span></div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const courseId = {{ $course['id'] }};
        const csrfToken = '{{ csrf_token() }}';

        // Sortable modules
        const modulesList = document.getElementById('modulesList');
        if (modulesList) {
            Sortable.create(modulesList, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: function() {
                    const order = [];
                    modulesList.querySelectorAll(':scope > div[data-id]').forEach(el => order.push(el.getAttribute('data-id')));
                    fetch(`/admin/kursus/${courseId}/module/reorder`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ order })
                    }).catch(err => console.error('Reorder error:', err));
                }
            });
        }

        function toggleModule(id) {
            const d = document.getElementById(`details-module-${id}`);
            const i = document.getElementById(`icon-module-${id}`);
            d.classList.toggle('hidden');
            i?.classList.toggle('rotate-180');
        }

        function openAddModuleModal() { document.getElementById('addModuleModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        function closeAddModuleModal() { document.getElementById('addModuleModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }

        function openEditModuleModal(moduleId, judul, deskripsi) {
            document.getElementById('editModuleForm').action = `/admin/kursus/${courseId}/module/${moduleId}`;
            document.getElementById('edit_module_judul').value = judul;
            document.getElementById('edit_module_deskripsi').value = deskripsi;
            document.getElementById('editModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModuleModal() { document.getElementById('editModuleModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }

        function confirmDeleteModule(moduleId) {
            document.getElementById('deleteModuleForm').action = `/admin/kursus/${courseId}/module/${moduleId}`;
            document.getElementById('deleteModuleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteModuleModal() { document.getElementById('deleteModuleModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }

        function openAddMaterialModal(moduleId) {
            document.getElementById('add_material_module_id').value = moduleId;
            document.getElementById('addMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            onMatTypeChange();
        }
        function closeAddMaterialModal() { document.getElementById('addMaterialModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }
        function onMatTypeChange() {
            const t = document.getElementById('add_mat_tipe').value;
            document.getElementById('mat_video_group').classList.toggle('hidden', t !== 'video');
        }

        function confirmDeleteMaterial(materialId) {
            document.getElementById('deleteMaterialForm').action = `/admin/kursus/${courseId}/material/${materialId}`;
            document.getElementById('deleteMaterialModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteMaterialModal() { document.getElementById('deleteMaterialModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }

        function togglePretest(moduleId) {
            fetch(`/admin/kursus/${courseId}/module/${moduleId}/pretest`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
                else showToast(data.error || 'Gagal', 'error');
            })
            .catch(() => showToast('Terjadi kesalahan', 'error'));
        }

        function showToast(message, type = 'success') {
            const t = document.getElementById('toast');
            const c = document.getElementById('toastContent');
            document.getElementById('toastMessage').textContent = message;
            c.className = `px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 text-white text-sm font-medium ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 3000);
        }
    </script>
    @endpush
</x-layouts.admin>

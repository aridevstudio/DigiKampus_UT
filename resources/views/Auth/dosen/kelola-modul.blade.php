<x-layouts.dosen title="Kelola Modul Kursus" active="kursus-saya">
    {{-- Page Header --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kelola Modul Kursus</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Atur materi, video, dan bacaan untuk kursus ini</p>
        </div>
        
        {{-- Course Info Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm min-w-[280px]">
            <h3 class="font-semibold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2 mb-3">{{ $course['nama'] }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Nama Kursus:</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $course['nama'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Mahasiswa Terdaftar:</span>
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
            <button onclick="openAddModal()" class="w-full mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul Baru
            </button>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex gap-2">
            <a href="{{ route('dosen.kursus.preview', $course['id']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Pratinjau Kursus
            </a>
            <a href="{{ route('dosen.kursus.edit', $course['id']) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Ubah Informasi Kursus
            </a>
        </div>
        <form action="{{ route('dosen.kursus.publish', $course['id']) }}" method="POST" class="inline" onsubmit="return confirm('Publikasikan kursus ini?')">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Publikasikan Perubahan
            </button>
        </form>
    </div>

    {{-- Materials List --}}
    <div id="materialsList" class="space-y-4">
        @forelse($materials ?? [] as $index => $material)
        <div data-id="{{ $material['id'] }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden group">
            <div class="flex items-center gap-4 p-4">
                {{-- Drag Handle --}}
                <div class="text-gray-400 cursor-move handle p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    </svg>
                </div>
                
                {{-- Material Info --}}
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Modul {{ $index + 1 }}: {{ $material['judul'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ \Str::limit($material['konten'], 60) ?: 'Tidak ada deskripsi' }}</p>
                    
                    {{-- Material Stats --}}
                    <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                        @if($material['durasi'])
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ $material['durasi'] }} menit
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            @php
                                $materialType = $material['tipe'] === 'quiz'
                                    ? 'kuis'
                                    : ($material['tipe'] === 'text' ? 'bacaan' : $material['tipe']);
                            @endphp
                            @if($materialType === 'video')
                            <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                            </svg>
                            Video
                            @elseif($materialType === 'bacaan')
                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                            </svg>
                            Bacaan
                            @elseif($materialType === 'kuis')
                            <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            Kuis
                            @else
                            <svg class="w-3 h-3 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                            Tugas
                            @endif
                        </span>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <button onclick="toggleMaterial({{ $material['id'] }})" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition" id="btn-{{ $material['id'] }}">
                        <svg class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="icon-{{ $material['id'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button onclick="openEditModal({{ $material['id'] }})" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="confirmDelete({{ $material['id'] }})" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            {{-- Expanded Details --}}
            <div id="details-{{ $material['id'] }}" class="hidden px-4 pb-4 pt-0 border-t border-gray-100 dark:border-gray-700 mt-2">
                <div class="pt-4 text-sm text-gray-600 dark:text-gray-300">
                    <h4 class="font-medium mb-1">Deskripsi Lengkap:</h4>
                    <p class="mb-3">{{ $material['konten'] ?: 'Tidak ada deskripsi' }}</p>
                    
                    @if($material['video_url'])
                    <div class="mb-3">
                        <h4 class="font-medium mb-1">Link Video:</h4>
                        <a href="{{ $material['video_url'] }}" target="_blank" class="text-blue-500 hover:underline flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            {{ $material['video_url'] }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada modul</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tambahkan modul pertama untuk kursus ini</p>
            <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Modul Baru
            </button>
        </div>
        @endforelse
    </div>

    {{-- Add Modal --}}
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeAddModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form id="addMaterialForm" action="{{ route('dosen.material.store', $course['id']) }}" method="POST" class="p-6" onsubmit="return handleAddMaterialSubmit(event)">
                    @csrf
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah Modul Baru</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_material" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="add_tipe" onchange="onAddTypeChange()" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas</option>
                            </select>
                            <p id="add_type_hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div>
                            <label id="add_konten_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" id="add_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div id="add_video_group">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" id="add_video_url" name="video_url" onchange="syncVideoDuration('add')" onblur="syncVideoDuration('add')" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p id="add_video_duration_hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div id="add_durasi_group">
                            <label id="add_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" id="add_durasi" name="durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" id="add_submit_btn" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEditModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">
                <button onclick="closeEditModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <form id="editForm" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Modul</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Judul Modul</label>
                            <input type="text" name="judul_material" id="edit_judul" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                            <select name="tipe" id="edit_tipe" onchange="onEditTypeChange()" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <option value="video">Video</option>
                                <option value="bacaan">Bacaan</option>
                                <option value="kuis">Kuis</option>
                                <option value="tugas">Tugas</option>
                            </select>
                        </div>
                        <div>
                            <label id="edit_konten_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Konten/Deskripsi</label>
                            <textarea name="konten" id="edit_konten" rows="3" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        </div>
                        <div id="edit_video_group">
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">URL Video (opsional)</label>
                            <input type="url" name="video_url" id="edit_video_url" onchange="syncVideoDuration('edit')" onblur="syncVideoDuration('edit')" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <p id="edit_video_duration_hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <div id="edit_durasi_group">
                            <label id="edit_durasi_label" class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Durasi (menit)</label>
                            <input type="number" name="durasi" id="edit_durasi" min="0" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Modul?</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Data modul akan dihapus permanen dan tidak dapat dikembalikan.</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition">Batal</button>
                        <form id="deleteForm" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div id="validationAlert" class="fixed top-4 right-4 z-[60] max-w-md bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <p class="font-semibold text-sm mb-1">Data gagal disimpan:</p>
                <ul class="text-xs space-y-0.5 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button onclick="this.closest('#validationAlert').remove()" class="ml-2 shrink-0">&times;</button>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('validationAlert')?.remove(), 8000);</script>
    @endif

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div id="successAlert" class="fixed top-4 right-4 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('successAlert')?.remove(), 5000);</script>
    @endif

    @if(session('error'))
    <div id="errorAlert" class="fixed top-4 right-4 z-[60] bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-2">&times;</button>
    </div>
    <script>setTimeout(() => document.getElementById('errorAlert')?.remove(), 5000);</script>
    @endif

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const courseId = {{ $course['id'] }};
        const typedContentRoutes = {
            video: @json(route('dosen.kelola-video')),
            bacaan: @json(route('dosen.kelola-bacaan')),
            kuis: @json(route('dosen.kelola-quiz')),
            tugas: @json(route('dosen.kelola-tugas')),
        };

        function normalizeMaterialType(type) {
            const value = (type || '').toLowerCase();
            if (value === 'quiz') return 'kuis';
            if (value === 'text') return 'bacaan';
            if (['video', 'bacaan', 'kuis', 'tugas'].includes(value)) return value;
            return 'video';
        }

        function applyTypeState(prefix, rawType) {
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

            if (!kontenLabel || !videoGroup || !durasiGroup) {
                return;
            }

            if (type === 'video') {
                kontenLabel.textContent = 'Deskripsi Video';
                if (kontenInput) kontenInput.placeholder = 'Ringkasan materi video...';
                if (typeHint) typeHint.textContent = 'Setelah klik tombol, Anda akan diarahkan ke halaman Kelola Video.';
                if (submitBtn) submitBtn.textContent = 'Lanjut Kelola Video';
                videoGroup.classList.remove('hidden');
                if (videoInput) videoInput.required = false;
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Video (menit)';
                if (videoInput?.value?.trim()) {
                    syncVideoDuration(prefix);
                } else {
                    setVideoDurationHint(prefix, '');
                }
                return;
            }

            if (type === 'bacaan') {
                kontenLabel.textContent = 'Konten Bacaan';
                if (kontenInput) kontenInput.placeholder = 'Tulis konten bacaan...';
                if (typeHint) typeHint.textContent = 'Setelah klik tombol, Anda akan diarahkan ke halaman Kelola Bacaan.';
                if (submitBtn) submitBtn.textContent = 'Lanjut Kelola Bacaan';
                videoGroup.classList.add('hidden');
                if (videoInput) {
                    videoInput.required = false;
                    videoInput.value = '';
                }
                setVideoDurationHint(prefix, '');
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Estimasi Baca (menit)';
                return;
            }

            if (type === 'kuis') {
                kontenLabel.textContent = 'Instruksi Kuis';
                if (kontenInput) kontenInput.placeholder = 'Petunjuk pengerjaan kuis...';
                if (typeHint) typeHint.textContent = 'Setelah klik tombol, Anda akan diarahkan ke halaman Input Kuis untuk mengisi soal.';
                if (submitBtn) submitBtn.textContent = 'Lanjut Isi Soal';
                videoGroup.classList.add('hidden');
                if (videoInput) {
                    videoInput.required = false;
                    videoInput.value = '';
                }
                setVideoDurationHint(prefix, '');
                durasiGroup.classList.remove('hidden');
                if (durasiLabel) durasiLabel.textContent = 'Durasi Kuis (menit)';
                return;
            }

            // tugas
            kontenLabel.textContent = 'Deskripsi Tugas';
            if (kontenInput) kontenInput.placeholder = 'Jelaskan instruksi dan ketentuan tugas...';
            if (typeHint) typeHint.textContent = 'Setelah klik tombol, Anda akan diarahkan ke halaman Kelola Tugas.';
            if (submitBtn) submitBtn.textContent = 'Lanjut Kelola Tugas';
            videoGroup.classList.add('hidden');
            if (videoInput) {
                videoInput.required = false;
                videoInput.value = '';
            }
            setVideoDurationHint(prefix, '');
            durasiGroup.classList.add('hidden');
            if (durasiInput) durasiInput.value = '';
        }

        function onAddTypeChange() {
            const select = document.getElementById('add_tipe');
            applyTypeState('add', select?.value || 'video');
        }

        function onEditTypeChange() {
            const select = document.getElementById('edit_tipe');
            applyTypeState('edit', select?.value || 'video');
        }

        function setVideoDurationHint(prefix, message, tone = 'neutral') {
            const hint = document.getElementById(`${prefix}_video_duration_hint`);
            if (!hint) return;

            hint.textContent = message || '';
            hint.classList.remove('text-red-500', 'text-green-600', 'dark:text-green-400', 'text-gray-500', 'dark:text-gray-400');

            if (tone === 'error') {
                hint.classList.add('text-red-500');
                return;
            }

            if (tone === 'success') {
                hint.classList.add('text-green-600', 'dark:text-green-400');
                return;
            }

            hint.classList.add('text-gray-500', 'dark:text-gray-400');
        }

        async function syncVideoDuration(prefix) {
            const type = normalizeMaterialType(document.getElementById(`${prefix}_tipe`)?.value || 'video');
            if (type !== 'video') {
                setVideoDurationHint(prefix, '');
                return;
            }

            const videoInput = document.getElementById(`${prefix}_video_url`);
            const durasiInput = document.getElementById(`${prefix}_durasi`);

            if (!videoInput || !durasiInput) {
                return;
            }

            const url = videoInput.value?.trim();
            if (!url) {
                setVideoDurationHint(prefix, '');
                return;
            }

            setVideoDurationHint(prefix, 'Mendeteksi durasi video...');

            try {
                const response = await fetch(`/dosen/api/video-duration?url=${encodeURIComponent(url)}`, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                    },
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    durasiInput.value = data.data.minutes;
                    const provider = (data?.data?.provider || 'video').toUpperCase();
                    setVideoDurationHint(prefix, `Durasi otomatis: ${data.data.minutes} menit (${provider}).`, 'success');
                    return;
                }

                setVideoDurationHint(prefix, data?.message || 'Durasi belum bisa dideteksi otomatis.', 'error');
            } catch (error) {
                setVideoDurationHint(prefix, 'Gagal koneksi saat mendeteksi durasi video.', 'error');
            }
        }

        function handleAddMaterialSubmit(event) {
            const form = event.target;
            const selectedType = normalizeMaterialType(document.getElementById('add_tipe')?.value || 'video');
            const targetRoute = typedContentRoutes[selectedType];

            // Always continue on the dedicated typed content page so form fields stay aligned with module type.
            if (!targetRoute) return true;

            event.preventDefault();

            const params = new URLSearchParams();
            params.set('course_id', String(courseId));

            const judul = form.querySelector('input[name="judul_material"]')?.value?.trim();
            const konten = form.querySelector('textarea[name="konten"]')?.value?.trim();
            const durasi = form.querySelector('input[name="durasi"]')?.value;
            const videoUrl = form.querySelector('input[name="video_url"]')?.value?.trim();

            if (judul) params.set('modul_judul', judul);
            if (konten) params.set('modul_konten', konten);
            if (durasi !== undefined && durasi !== null && durasi !== '') {
                params.set('modul_durasi', durasi);
            }
            if (videoUrl) params.set('modul_video_url', videoUrl);

            const query = params.toString();
            window.location.href = query ? `${targetRoute}?${query}` : targetRoute;
            return false;
        }
        
        // Initialize Sortable
        var el = document.getElementById('materialsList');
        if(el) {
            var sortable = Sortable.create(el, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: function (evt) {
                    var itemEl = evt.item;  // dragged HTMLElement
                    
                    // Get new order
                    var order = [];
                    document.querySelectorAll('#materialsList > div').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });

                    // Send to server
                    fetch(`{{ route('dosen.material.reorder', $course['id']) }}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Optional: Show toast
                            console.log('Order updated');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                },
            });
        }
        
        
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            onAddTypeChange();
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function openEditModal(id) {
            fetch(`/dosen/kursus/${courseId}/material/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editForm').action = `/dosen/kursus/${courseId}/material/${id}`;
                    document.getElementById('edit_judul').value = data.judul_material || '';
                    document.getElementById('edit_tipe').value = normalizeMaterialType(data.tipe || 'video');
                    document.getElementById('edit_konten').value = data.konten || '';
                    document.getElementById('edit_video_url').value = data.video_url || '';
                    document.getElementById('edit_durasi').value = data.durasi || '';
                    onEditTypeChange();
                    document.getElementById('editModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                })
                .catch(err => alert('Gagal memuat data modul'));
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = `/dosen/kursus/${courseId}/material/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function toggleMaterial(id) {
            const details = document.getElementById(`details-${id}`);
            const icon = document.getElementById(`icon-${id}`);
            
            if (details.classList.contains('hidden')) {
                details.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                details.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        onAddTypeChange();
        onEditTypeChange();
    </script>
    @endpush
</x-layouts.dosen>

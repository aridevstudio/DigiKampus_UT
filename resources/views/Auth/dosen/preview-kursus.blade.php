<x-layouts.dosen title="Preview Kursus" active="kursus-saya">
    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('dosen.kursus.modul', $course->id_course) }}" class="inline-flex items-center gap-2 text-gray-500 dark:text-gray-400 hover:text-blue-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Kelola Modul
        </a>
    </div>

    {{-- Course Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 relative">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->nama_course }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-4 left-6 right-6">
                @php
                    $statusColors = [
                        'aktif' => 'bg-green-500',
                        'draft' => 'bg-yellow-500',
                        'nonaktif' => 'bg-red-500',
                    ];
                @endphp
                <span class="px-2.5 py-1 {{ $statusColors[$course->status] ?? 'bg-gray-500' }} text-white text-xs font-medium rounded-full">
                    {{ ucfirst($course->status) }}
                </span>
                <h1 class="text-2xl font-bold text-white mt-2">{{ $course->nama_course }}</h1>
                <p class="text-white/80 text-sm">{{ $course->kode_course }}</p>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-6 text-sm text-gray-500 dark:text-gray-400 mb-4">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $course->enrollments->count() }} Mahasiswa
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ $course->materials->count() }} Modul
                </span>
                @if($course->jurusan)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ $course->jurusan->nama_jurusan }}
                </span>
                @endif
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $course->tipe === 'gratis' ? 'Gratis' : 'Rp ' . number_format($course->harga, 0, ',', '.') }}
                </span>
            </div>
            @if($course->deskripsi)
            <p class="text-gray-600 dark:text-gray-300">{{ $course->deskripsi }}</p>
            @endif
        </div>
    </div>

    {{-- Modules --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Daftar Modul</h2>
        
        <div class="space-y-3">
            @forelse($course->materials as $index => $material)
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold text-sm">
                    {{ $index + 1 }}
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $material->judul_material }}</h4>
                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                        @if($material->tipe === 'video')
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                            </svg>
                            Video
                        </span>
                        @elseif($material->tipe === 'bacaan')
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                            Bacaan
                        </span>
                        @elseif($material->tipe === 'kuis')
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            Kuis
                        </span>
                        @else
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                            </svg>
                            Tugas
                        </span>
                        @endif
                        @if($material->durasi)
                        <span>{{ $material->durasi }} menit</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>Belum ada modul dalam kursus ini</p>
            </div>
            @endforelse
        </div>
    </div>
</x-layouts.dosen>

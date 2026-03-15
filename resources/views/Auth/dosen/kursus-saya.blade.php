<x-layouts.dosen title="Kursus Saya" active="kursus-saya">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kursus Saya</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola semua kursus yang Anda buat</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dosen.kursus.buat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition shadow-sm shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kursus
            </a>
            <a href="{{ route('dosen.webinar.buat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white font-medium rounded-lg transition shadow-sm shadow-violet-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Tambah Webinar
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('dosen.kursus') }}" class="flex flex-wrap items-center gap-4 mb-6" x-data="{ isLoading: false }" @submit="isLoading = true">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Filter:</span>
            <div class="relative">
                <select name="status" onchange="this.form.submit()" class="appearance-none px-4 py-2 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="aktif" {{ ($statusFilter ?? '') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="draft" {{ ($statusFilter ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="nonaktif" {{ ($statusFilter ?? '') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        
        {{-- Sort --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Urutkan:</span>
            <div class="relative">
                <select name="sort" onchange="this.form.submit()" class="appearance-none px-4 py-2 pr-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="terbaru" {{ ($sortBy ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sortBy ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                    <option value="nama" {{ ($sortBy ?? '') === 'nama' ? 'selected' : '' }}>Nama A-Z</option>
                    <option value="mahasiswa" {{ ($sortBy ?? '') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa Terbanyak</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        
        {{-- Search --}}
        <div class="relative flex-1 max-w-xs ml-auto">
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kursus..." class="w-full px-4 py-2 pl-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </form>

    {{-- Course Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @forelse($coursesData ?? [] as $course)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden hover-lift">
            {{-- Thumbnail with Module Video Previews --}}
            <div class="relative h-40 bg-gradient-to-br from-blue-500 to-blue-600 overflow-hidden">
                @php
                    $previews = $course['module_previews'] ?? collect();
                    $videoPreview = $previews->firstWhere('has_video', true);
                @endphp
                @if($videoPreview)
                    <img src="{{ $videoPreview['video_thumbnail'] }}" alt="{{ $course['nama'] }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                    <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                        <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                            <svg class="w-4 h-4 text-gray-800 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </div>
                @elseif($course['thumbnail'])
                    <img src="{{ asset('storage/' . $course['thumbnail']) }}" alt="{{ $course['nama'] }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                @endif

                {{-- Module count badge --}}
                @if($previews->count() > 0)
                <span class="absolute bottom-3 left-3 bg-black/70 text-white text-xs font-medium px-2 py-0.5 rounded flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    {{ $previews->count() }} Modul
                </span>
                @endif
                
                {{-- Status Badge --}}
                @php
                    $statusColors = [
                        'aktif' => 'bg-green-500',
                        'draft' => 'bg-yellow-500',
                        'nonaktif' => 'bg-red-500',
                    ];
                    $statusLabels = [
                        'aktif' => 'Aktif',
                        'draft' => 'Draft',
                        'nonaktif' => 'Segera Dibuka',
                    ];
                @endphp
                <span class="absolute top-3 left-3 px-2.5 py-1 {{ $statusColors[$course['status']] ?? 'bg-gray-500' }} text-white text-xs font-medium rounded-full">
                    {{ $statusLabels[$course['status']] ?? ucfirst($course['status']) }}
                </span>
            </div>
            
            {{-- Content --}}
            <div class="p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $course['nama'] }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">{{ $course['deskripsi'] ?: 'Tidak ada deskripsi' }}</p>
                
                {{-- Mahasiswa Count --}}
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ $course['mahasiswa_count'] }} mahasiswa
                </div>
                
                {{-- Progress --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-500 dark:text-gray-400">Rata-rata proges</span>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $course['progress_avg'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ $course['progress_avg'] }}%"></div>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('dosen.kursus.edit', $course['id']) }}" class="flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-sm font-medium rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition text-center">
                        Kelola Kursus & Modul
                    </a>
                    <a href="{{ route('dosen.kursus.preview', $course['id']) }}" class="px-3 py-2 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition" title="Lihat Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada kursus</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Mulai buat kursus pertama Anda untuk berbagi ilmu dengan mahasiswa</p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('dosen.kursus.buat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kursus
                </a>
                <a href="{{ route('dosen.webinar.buat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-500 hover:bg-violet-600 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Tambah Webinar
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(isset($coursesPaginated) && $coursesPaginated->hasPages())
    <div class="flex items-center justify-center gap-1">
        {{-- Previous --}}
        @if($coursesPaginated->onFirstPage())
        <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        @else
        <a href="{{ $coursesPaginated->previousPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        @endif
        
        {{-- Page Numbers --}}
        @for($i = 1; $i <= $coursesPaginated->lastPage(); $i++)
            @if($i <= 3 || $i === $coursesPaginated->lastPage() || abs($i - $coursesPaginated->currentPage()) <= 1)
            <a href="{{ $coursesPaginated->url($i) }}" class="w-9 h-9 flex items-center justify-center text-sm font-medium rounded-lg transition {{ $i === $coursesPaginated->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                {{ $i }}
            </a>
            @elseif($i === 4 && $coursesPaginated->currentPage() > 5)
            <span class="text-gray-400">...</span>
            @endif
        @endfor
        
        {{-- Next --}}
        @if($coursesPaginated->hasMorePages())
        <a href="{{ $coursesPaginated->nextPageUrl() }}" class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
        @else
        <button class="p-2 text-gray-300 dark:text-gray-600 rounded-lg cursor-not-allowed" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        @endif
    </div>
    @endif
</x-layouts.dosen>

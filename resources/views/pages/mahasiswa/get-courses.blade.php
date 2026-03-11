<x-layouts.dashboard :active="'get-courses'">
@php
    // Type colors mapping
    $typeColors = [
        'webinar' => 'bg-blue-500',
        'kursus' => 'bg-green-500',
        'tiket' => 'bg-rose-500',
    ];
    
    // Default image if thumbnail is empty
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop';

    // Helper to extract YouTube video ID
    function extractYoutubeId($url) {
        if (!$url) return null;
        if (str_contains($url, 'youtube.com/watch?v=')) {
            parse_str(parse_url($url, PHP_URL_QUERY), $params);
            return $params['v'] ?? null;
        } elseif (str_contains($url, 'youtu.be/')) {
            return basename(parse_url($url, PHP_URL_PATH));
        }
        return null;
    }

    // Build flat list of module cards from courses
    $moduleCards = collect();
    foreach ($courses as $course) {
        if ($course->modules && $course->modules->count() > 0) {
            foreach ($course->modules as $module) {
                $firstVideo = $module->materials->where('tipe', 'video')->whereNotNull('video_url')->first();
                $videoId = $firstVideo ? extractYoutubeId($firstVideo->video_url) : null;
                $thumbnail = $videoId
                    ? "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg"
                    : ($course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage);

                $moduleCards->push((object)[
                    'id_course' => $course->id_course,
                    'nama_course' => $course->nama_course,
                    'kategori' => $course->kategori ?? 'kursus',
                    'judul_module' => $module->judul_module,
                    'deskripsi' => $module->deskripsi ?? $course->deskripsi,
                    'thumbnail' => $thumbnail,
                    'has_video' => $videoId !== null,
                    'real_rating' => floatval($course->real_rating ?? 0),
                    'real_jumlah_ulasan' => intval($course->real_jumlah_ulasan ?? 0),
                    'harga' => floatval($course->harga ?? 0),
                    'diskon' => floatval($course->diskon ?? 0),
                    'video_count' => $module->materials->where('tipe', 'video')->count(),
                ]);
            }
        } else {
            // Course without modules — show as single card
            $moduleCards->push((object)[
                'id_course' => $course->id_course,
                'nama_course' => $course->nama_course,
                'kategori' => $course->kategori ?? 'kursus',
                'judul_module' => null,
                'deskripsi' => $course->deskripsi,
                'thumbnail' => $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage,
                'has_video' => false,
                'real_rating' => floatval($course->real_rating ?? 0),
                'real_jumlah_ulasan' => intval($course->real_jumlah_ulasan ?? 0),
                'harga' => floatval($course->harga ?? 0),
                'diskon' => floatval($course->diskon ?? 0),
                'video_count' => 0,
            ]);
        }
    }
@endphp

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Get Courses</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Jelajahi berbagai kursus, webinar, dan tiket acara untuk meningkatkan keahlian Anda.</p>
</div>

{{-- Filter & Search Section --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up delay-100">
    {{-- Filter Tabs --}}
    @php
        $currentTipe = $selectedTipe ?? 'semua';
    @endphp
    <div class="flex items-center gap-2 bg-white dark:bg-[#1f2937] rounded-lg p-1 border border-gray-200 dark:border-gray-700/50 overflow-x-auto">
        <a href="{{ route('mahasiswa.get-courses') }}" 
           class="filter-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'semua' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Semua
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'webinar']) }}" 
           class="filter-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'webinar' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Webinar
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'tiket']) }}" 
           class="filter-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'tiket' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Tiket
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'kursus']) }}" 
           class="filter-tab whitespace-nowrap px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'kursus' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Kursus
        </a>
    </div>
    
    {{-- Search Bar (Live Search) --}}
    <div class="relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input 
            type="text" 
            id="search-courses"
            placeholder="Cari semua di sini..." 
            class="pl-12 pr-4 py-2.5 w-full sm:w-64 border border-gray-200 dark:border-gray-700/50 rounded-lg bg-white dark:bg-[#1f2937] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            oninput="searchCourses(this.value)"
        >
    </div>
</div>

{{-- Courses Grid (Per-Module Preview) --}}
<div id="courses-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up delay-200">
    @forelse($moduleCards as $index => $card)
    @php
        $courseType = strtolower($card->kategori);
    @endphp
    <a href="{{ route('mahasiswa.course-detail', $card->id_course) }}" class="course-card bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover-lift transition block" data-type="{{ $courseType }}" style="animation-delay: {{ $index * 50 }}ms">
        {{-- Module Video Thumbnail --}}
        <div class="relative h-40 overflow-hidden bg-gray-900">
            <img src="{{ $card->thumbnail }}" alt="{{ $card->judul_module ?? $card->nama_course }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110" onerror="this.src='{{ $defaultImage }}'">
            {{-- Play button overlay for video modules --}}
            @if($card->has_video)
            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition">
                <div class="w-12 h-12 rounded-full bg-white/90 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-gray-800 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </div>
            @endif
            {{-- Type Badge --}}
            <span class="absolute top-3 right-3 {{ $typeColors[$courseType] ?? 'bg-gray-500' }} text-white text-xs font-medium px-3 py-1 rounded-full capitalize">
                {{ $card->kategori }}
            </span>
            @if($card->has_video)
            <span class="absolute bottom-3 left-3 bg-black/70 text-white text-xs font-medium px-2 py-0.5 rounded flex items-center gap-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                Video
            </span>
            @endif
        </div>
        
        {{-- Card Content --}}
        <div class="p-4">
            {{-- Module Title --}}
            @if($card->judul_module)
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-1 line-clamp-2">{{ $card->judul_module }}</h3>
            <p class="text-blue-500 dark:text-blue-400 text-xs font-medium mb-2 line-clamp-1">{{ $card->nama_course }}</p>
            @else
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-2 line-clamp-2">{{ $card->nama_course }}</h3>
            @endif
            
            {{-- Description --}}
            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 line-clamp-2">{{ $card->deskripsi ?? 'Tidak ada deskripsi' }}</p>
            
            {{-- Rating --}}
            <div class="flex items-center gap-1 mb-3">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($card->real_rating))
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @elseif($i - 0.5 <= $card->real_rating)
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <defs>
                            <linearGradient id="half-{{ $card->id_course }}-{{ $index }}">
                                <stop offset="50%" stop-color="currentColor"/>
                                <stop offset="50%" stop-color="#D1D5DB"/>
                            </linearGradient>
                        </defs>
                        <path fill="url(#half-{{ $card->id_course }}-{{ $index }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @endif
                @endfor
                <span class="text-gray-400 text-xs ml-1">
                    @if($card->real_jumlah_ulasan > 0)
                        ({{ number_format($card->real_jumlah_ulasan) }} ulasan)
                    @else
                        0 ulasan
                    @endif
                </span>
            </div>
            
            {{-- Price --}}
            @php
                $basePrice = max(0, (float) ($card->harga ?? 0));
                $discountPercent = max(0, min(100, (float) ($card->diskon ?? 0)));
                $hasDiscount = $basePrice > 0 && $discountPercent > 0;
                $finalPrice = $hasDiscount ? ($basePrice * (100 - $discountPercent) / 100) : $basePrice;
            @endphp
            @if($basePrice > 0)
                <div class="space-y-1">
                    <p class="text-blue-600 dark:text-blue-400 font-bold text-2xl leading-tight">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </p>
                    @if($hasDiscount)
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-0.5 text-[11px] font-semibold text-red-600 dark:bg-red-900/20 dark:text-red-300">
                                Diskon {{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }}%
                            </span>
                            <span class="text-sm text-gray-400 line-through dark:text-gray-500">
                                Rp {{ number_format($basePrice, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-emerald-600 dark:text-emerald-400 font-bold text-lg">Gratis</p>
            @endif
        </div>
    </a>
    @empty
    {{-- Empty State when no courses from database --}}
    <div class="col-span-full text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <h3 class="text-gray-500 dark:text-gray-400 font-medium">Belum ada kursus tersedia</h3>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Kursus akan segera ditambahkan</p>
    </div>
    @endforelse
</div>

{{-- Empty State --}}
<div id="empty-state" class="hidden text-center py-12">
    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <h3 class="text-gray-500 dark:text-gray-400 font-medium">Tidak ada kursus ditemukan</h3>
    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Coba ubah filter atau kata kunci pencarian</p>
</div>

@push('scripts')
<script>
    let currentFilter = 'all';
    let currentSearch = '';

    function filterCourses(type) {
        currentFilter = type;
        
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(tab => {
            if (tab.dataset.filter === type) {
                tab.classList.add('bg-blue-500', 'text-white');
                tab.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
            } else {
                tab.classList.remove('bg-blue-500', 'text-white');
                tab.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700/50');
            }
        });
        
        applyFilters();
    }

    function searchCourses(query) {
        currentSearch = query.toLowerCase();
        applyFilters();
    }

    function applyFilters() {
        const cards = document.querySelectorAll('.course-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const type = card.dataset.type;
            const title = card.querySelector('h3').textContent.toLowerCase();
            const description = card.querySelector('p').textContent.toLowerCase();
            
            const matchesFilter = currentFilter === 'all' || type === currentFilter;
            const matchesSearch = currentSearch === '' || 
                title.includes(currentSearch) || 
                description.includes(currentSearch);
            
            if (matchesFilter && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide empty state
        document.getElementById('empty-state').classList.toggle('hidden', visibleCount > 0);
        document.getElementById('courses-grid').classList.toggle('hidden', visibleCount === 0);
    }
</script>
@endpush

</x-layouts.dashboard>

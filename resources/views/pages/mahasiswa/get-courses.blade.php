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
    <div class="flex items-center gap-2 bg-white dark:bg-[#1f2937] rounded-lg p-1 border border-gray-200 dark:border-gray-700/50">
        <a href="{{ route('mahasiswa.get-courses') }}" 
           class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'semua' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Semua
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'webinar']) }}" 
           class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'webinar' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Webinar
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'tiket']) }}" 
           class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'tiket' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
            Tiket
        </a>
        <a href="{{ route('mahasiswa.get-courses', ['tipe' => 'kursus']) }}" 
           class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition {{ $currentTipe === 'kursus' ? 'bg-blue-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
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

{{-- Courses Grid --}}
<div id="courses-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in-up delay-200">
    @forelse($courses as $index => $course)
    @php
        $courseType = strtolower($course->tipe ?? 'kursus');
        $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
        $courseRating = floatval($course->rating ?? 0);
        $courseReviews = intval($course->jumlah_ulasan ?? 0);
        $coursePrice = floatval($course->harga ?? 0);
    @endphp
    <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" class="course-card bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover-lift transition block" data-type="{{ $courseType }}" style="animation-delay: {{ $index * 50 }}ms">
        {{-- Course Image --}}
        <div class="relative h-40 overflow-hidden">
            <img src="{{ $courseImage }}" alt="{{ $course->nama_course }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
            {{-- Type Badge --}}
            <span class="absolute top-3 right-3 {{ $typeColors[$courseType] ?? 'bg-gray-500' }} text-white text-xs font-medium px-3 py-1 rounded-full capitalize">
                {{ $course->tipe ?? 'Kursus' }}
            </span>
        </div>
        
        {{-- Course Content --}}
        <div class="p-4">
            {{-- Title --}}
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-2 line-clamp-2">{{ $course->nama_course }}</h3>
            
            {{-- Description --}}
            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 line-clamp-2">{{ $course->deskripsi ?? 'Tidak ada deskripsi' }}</p>
            
            {{-- Rating --}}
            <div class="flex items-center gap-1 mb-3">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($courseRating))
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @elseif($i - 0.5 <= $courseRating)
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <defs>
                            <linearGradient id="half-{{ $course->id_course }}">
                                <stop offset="50%" stop-color="currentColor"/>
                                <stop offset="50%" stop-color="#D1D5DB"/>
                            </linearGradient>
                        </defs>
                        <path fill="url(#half-{{ $course->id_course }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @endif
                @endfor
                <span class="text-gray-400 text-xs ml-1">
                    @if($courseReviews > 0)
                        ({{ number_format($courseReviews) }} ulasan)
                    @else
                        0 ulasan
                    @endif
                </span>
            </div>
            
            {{-- Price --}}
            <p class="text-blue-600 dark:text-blue-400 font-bold text-lg">
                @if($coursePrice > 0)
                    Rp {{ number_format($coursePrice, 0, ',', '.') }}
                @else
                    Gratis
                @endif
            </p>
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

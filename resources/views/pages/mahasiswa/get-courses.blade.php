<x-layouts.dashboard :active="'get-courses'">
@php
    // Dummy courses data
    $courses = [
        [
            'id' => 1,
            'title' => 'Dasar-dasar Desain UI/UX',
            'description' => 'Pelajari prinsip fundamental desain antarmuka pengguna dan pengalaman pengguna.',
            'type' => 'Webinar',
            'price' => 150000,
            'rating' => 4.5,
            'reviews' => 1250,
            'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=250&fit=crop',
        ],
        [
            'id' => 2,
            'title' => 'Pengembangan Web dengan React',
            'description' => 'Kuasai framework JavaScript populer untuk membangun aplikasi web modern.',
            'type' => 'Kursus',
            'price' => 250000,
            'rating' => 5,
            'reviews' => 2800,
            'image' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=400&h=250&fit=crop',
        ],
        [
            'id' => 3,
            'title' => 'Tech Conference 2025',
            'description' => 'Tiket masuk untuk acara teknologi terbesar tahun ini dengan berbagai pembicara.',
            'type' => 'Tiket',
            'price' => 500000,
            'rating' => 0,
            'reviews' => 0,
            'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=250&fit=crop',
        ],
        [
            'id' => 4,
            'title' => 'Analisis Data dengan Python',
            'description' => 'Dari pemula hingga mahir, pelajari cara mengolah dan menganalisis data.',
            'type' => 'Kursus',
            'price' => 199000,
            'rating' => 4,
            'reviews' => 980,
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=250&fit=crop',
        ],
        [
            'id' => 5,
            'title' => 'Digital Marketing Masterclass',
            'description' => 'Strategi pemasaran digital yang efektif untuk bisnis online Anda.',
            'type' => 'Webinar',
            'price' => 175000,
            'rating' => 4.8,
            'reviews' => 1560,
            'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=250&fit=crop',
        ],
        [
            'id' => 6,
            'title' => 'Mobile App Development',
            'description' => 'Buat aplikasi mobile cross-platform dengan Flutter dan Dart.',
            'type' => 'Kursus',
            'price' => 299000,
            'rating' => 4.7,
            'reviews' => 2100,
            'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=400&h=250&fit=crop',
        ],
        [
            'id' => 7,
            'title' => 'Startup Summit 2025',
            'description' => 'Networking event untuk para founder dan investor startup Indonesia.',
            'type' => 'Tiket',
            'price' => 350000,
            'rating' => 4.2,
            'reviews' => 450,
            'image' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=400&h=250&fit=crop',
        ],
        [
            'id' => 8,
            'title' => 'Machine Learning Fundamentals',
            'description' => 'Dasar-dasar machine learning dan implementasi dengan TensorFlow.',
            'type' => 'Kursus',
            'price' => 275000,
            'rating' => 4.6,
            'reviews' => 1890,
            'image' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=400&h=250&fit=crop',
        ],
    ];
    
    $typeColors = [
        'Webinar' => 'bg-blue-500',
        'Kursus' => 'bg-green-500',
        'Tiket' => 'bg-rose-500',
    ];
@endphp

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Get Courses</h1>
    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Jelajahi berbagai kursus, webinar, dan tiket acara untuk meningkatkan keahlian Anda.</p>
</div>

{{-- Filter & Search Section --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-fade-in-up delay-100">
    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 bg-white dark:bg-[#1f2937] rounded-lg p-1 border border-gray-200 dark:border-gray-700/50">
        <button onclick="filterCourses('all')" data-filter="all" class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition bg-blue-500 text-white">
            Semua
        </button>
        <button onclick="filterCourses('Webinar')" data-filter="Webinar" class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50">
            Webinar
        </button>
        <button onclick="filterCourses('Tiket')" data-filter="Tiket" class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50">
            Tiket
        </button>
        <button onclick="filterCourses('Kursus')" data-filter="Kursus" class="filter-tab px-4 py-2 rounded-lg text-sm font-medium transition text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50">
            Kursus
        </button>
    </div>
    
    {{-- Search Bar --}}
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
    @foreach($courses as $index => $course)
    <a href="{{ route('mahasiswa.course-detail', $course['id']) }}" class="course-card bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover-lift transition block" data-type="{{ $course['type'] }}" style="animation-delay: {{ $index * 50 }}ms">
        {{-- Course Image --}}
        <div class="relative h-40 overflow-hidden">
            <img src="{{ $course['image'] }}" alt="{{ $course['title'] }}" class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
            {{-- Type Badge --}}
            <span class="absolute top-3 right-3 {{ $typeColors[$course['type']] }} text-white text-xs font-medium px-3 py-1 rounded-full">
                {{ $course['type'] }}
            </span>
        </div>
        
        {{-- Course Content --}}
        <div class="p-4">
            {{-- Title --}}
            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-sm mb-2 line-clamp-2">{{ $course['title'] }}</h3>
            
            {{-- Description --}}
            <p class="text-gray-500 dark:text-gray-400 text-xs mb-3 line-clamp-2">{{ $course['description'] }}</p>
            
            {{-- Rating --}}
            <div class="flex items-center gap-1 mb-3">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($course['rating']))
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @elseif($i - 0.5 <= $course['rating'])
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <defs>
                            <linearGradient id="half-{{ $course['id'] }}">
                                <stop offset="50%" stop-color="currentColor"/>
                                <stop offset="50%" stop-color="#D1D5DB"/>
                            </linearGradient>
                        </defs>
                        <path fill="url(#half-{{ $course['id'] }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @else
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    @endif
                @endfor
                <span class="text-gray-400 text-xs ml-1">
                    @if($course['reviews'] > 0)
                        ({{ number_format($course['reviews']) }} ulasan)
                    @else
                        0 ulasan
                    @endif
                </span>
            </div>
            
            {{-- Price --}}
            <p class="text-blue-600 dark:text-blue-400 font-bold text-lg">
                Rp {{ number_format($course['price'], 0, ',', '.') }}
            </p>
        </div>
    </a>
    @endforeach
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

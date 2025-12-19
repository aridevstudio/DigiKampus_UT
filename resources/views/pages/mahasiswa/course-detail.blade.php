<x-layouts.dashboard :active="'get-courses'">
@php
    // Use data from controller - $course is passed from CourseController@show
    // Default image if thumbnail is empty
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop';
    $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;
    
    // Map course data for easy access in view
    $courseData = [
        'id' => $course->id_course,
        'code' => $course->kode_course,
        'title' => $course->nama_course,
        'description' => $course->deskripsi ?? 'Tidak ada deskripsi tersedia',
        'type' => ucfirst($course->tipe ?? 'kursus'),
        'price' => floatval($course->harga ?? 0),
        'rating' => floatval($course->rating ?? 0),
        'reviews' => intval($course->jumlah_ulasan ?? 0),
        'image' => $courseImage,
        'duration' => '16 minggu (1 semester)', // Could be added to course model later
        'method' => 'Online learning dengan tutorial tatap muka',
        'objectives' => $course->deskripsi ?? 'Memahami konsep dasar dan penerapannya.',
        'creator' => $course->dosen ? $course->dosen->name : 'Universitas Terbuka',
    ];
    
    // Build modules from course materials
    $modules = [];
    if ($course->materials && $course->materials->count() > 0) {
        // Group materials by module (urutan or create single module)
        $currentModule = [
            'title' => 'Modul 1: Materi Pembelajaran',
            'items' => $course->materials->count(),
            'contents' => []
        ];
        
        foreach ($course->materials as $material) {
            $currentModule['contents'][] = [
                'type' => $material->tipe ?? 'document', // video, file, text
                'title' => $material->judul_material,
                'duration' => '',
            ];
        }
        
        $modules[] = $currentModule;
    }
    
    // Add assignments as separate module
    if ($course->assignments && $course->assignments->count() > 0) {
        $assignmentModule = [
            'title' => 'Tugas & Penilaian',
            'items' => $course->assignments->count(),
            'contents' => []
        ];
        
        foreach ($course->assignments as $assignment) {
            $assignmentModule['contents'][] = [
                'type' => 'assignment',
                'title' => $assignment->judul,
                'duration' => $assignment->deadline ? $assignment->deadline->format('d M Y') : '',
            ];
        }
        
        $modules[] = $assignmentModule;
    }
    
    // If no modules, add placeholder
    if (empty($modules)) {
        $modules[] = [
            'title' => 'Modul Belum Tersedia',
            'items' => 0,
            'contents' => []
        ];
    }
    
    // Prerequisites (could be added to course model later)
    $prerequisites = [
        ['code' => 'STIN4101', 'name' => 'Pengantar Teknologi Informasi'],
    ];
    
    // Get reviews from course ratings - calculate real breakdown
    $allRatings = $course->ratings ?? collect();
    $totalReviews = $allRatings->count();
    $avgRating = $totalReviews > 0 ? $allRatings->avg('rating') : 0;
    
    $reviewStats = [
        'average' => round($avgRating, 1),
        'total' => $totalReviews,
        'breakdown' => [
            5 => $allRatings->where('rating', 5)->count(),
            4 => $allRatings->where('rating', 4)->count(),
            3 => $allRatings->where('rating', 3)->count(),
            2 => $allRatings->where('rating', 2)->count(),
            1 => $allRatings->where('rating', 1)->count(),
        ]
    ];
    
    // Get sample reviews from database
    $reviews = [];
    if ($allRatings->count() > 0) {
        foreach ($allRatings->sortByDesc('created_at')->take(10) as $rating) {
            // Get profile photo or fallback to ui-avatars
            $profilePhoto = null;
            if ($rating->mahasiswa && $rating->mahasiswa->profile && $rating->mahasiswa->profile->foto_profile) {
                $profilePhoto = asset('storage/' . $rating->mahasiswa->profile->foto_profile);
            } else {
                $profilePhoto = 'https://ui-avatars.com/api/?name=' . urlencode($rating->mahasiswa ? $rating->mahasiswa->name : 'A') . '&background=3b82f6&color=fff';
            }
            
            $reviews[] = [
                'name' => $rating->mahasiswa ? $rating->mahasiswa->name : 'Anonymous',
                'avatar' => $profilePhoto,
                'rating' => $rating->rating,
                'date' => $rating->created_at ? $rating->created_at->diffForHumans() : 'Baru saja',
                'comment' => $rating->ulasan ?? 'Kursus yang bagus!',
            ];
        }
    }
    
    $typeColors = [
        'Webinar' => 'bg-blue-500',
        'Kursus' => 'bg-green-500',
        'Tiket' => 'bg-rose-500',
        'webinar' => 'bg-blue-500',
        'kursus' => 'bg-green-500',
        'tiket' => 'bg-rose-500',
    ];
@endphp

{{-- Page Header --}}
<div class="mb-6 animate-fade-in-up">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('mahasiswa.get-courses') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $courseData['title'] }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $courseData['code'] }}</p>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="flex items-center gap-6 border-b border-gray-200 dark:border-gray-700/50 mb-6 animate-fade-in-up delay-100 overflow-x-auto">
    <button onclick="showTab('ringkasan')" data-tab="ringkasan" class="tab-btn pb-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap">
        Ringkasan
    </button>
    <button onclick="showTab('konten')" data-tab="konten" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Konten Kursus
    </button>
    <button onclick="showTab('prasyarat')" data-tab="prasyarat" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Prasyarat
    </button>
    <button onclick="showTab('deskripsi')" data-tab="deskripsi" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Deskripsi
    </button>
    <button onclick="showTab('ulasan')" data-tab="ulasan" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Ulasan
    </button>
</div>

{{-- Main Content --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Content Area --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Tab: Ringkasan (default) --}}
        <div id="tab-ringkasan" class="tab-content animate-fade-in-up">
            {{-- Konten Kursus --}}
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Konten Kursus</h2>
                
                <div class="space-y-3">
                    @foreach($modules as $moduleIndex => $module)
                    <div class="border border-gray-200 dark:border-gray-700/50 rounded-xl overflow-hidden">
                        <button onclick="toggleModule(this)" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-gray-400 module-arrow transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span class="font-medium text-gray-800 dark:text-gray-100 text-sm text-left">{{ $module['title'] }}</span>
                            </div>
                            <span class="text-gray-400 text-sm whitespace-nowrap">{{ $module['items'] }} item</span>
                        </button>
                        {{-- Module Content (hidden by default) --}}
                        <div class="module-content hidden border-t border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/30">
                            @foreach($module['contents'] as $content)
                            <div class="flex items-center gap-3 p-3 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border-b border-gray-200 dark:border-gray-700/50 last:border-b-0">
                                @if($content['type'] === 'video')
                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @elseif($content['type'] === 'document')
                                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                @elseif($content['type'] === 'quiz')
                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                @else
                                <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $content['title'] }}</p>
                                </div>
                                @if($content['duration'])
                                <span class="text-xs text-gray-400">{{ $content['duration'] }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Prasyarat --}}
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Prasyarat
                </h2>
                
                <div class="space-y-2">
                    @foreach($prerequisites as $prereq)
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300">{{ $prereq['name'] }} ({{ $prereq['code'] }})</span>
                    </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Deskripsi Kursus --}}
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Deskripsi Kursus</h2>
                
                <div class="space-y-4">
                    {{-- Durasi --}}
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Durasi</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $courseData['duration'] }}</p>
                        </div>
                    </div>
                    
                    {{-- Metode --}}
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Metode</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $courseData['method'] }}</p>
                        </div>
                    </div>
                    
                    {{-- Tujuan Pembelajaran --}}
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Tujuan Pembelajaran</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $courseData['objectives'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Tab: Konten Kursus --}}
        <div id="tab-konten" class="tab-content hidden">
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Konten Kursus Lengkap</h2>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($modules) }} Modul • {{ collect($modules)->sum('items') }} Item</span>
                </div>
                
                <div class="space-y-4">
                    @foreach($modules as $moduleIndex => $module)
                    <div class="border border-gray-200 dark:border-gray-700/50 rounded-xl overflow-hidden">
                        {{-- Module Header --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center text-sm font-bold">
                                    {{ $moduleIndex + 1 }}
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ $module['title'] }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $module['items'] }} item pembelajaran</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Module Content Items --}}
                        <div class="divide-y divide-gray-200 dark:divide-gray-700/50">
                            @foreach($module['contents'] as $contentIndex => $content)
                            <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                {{-- Content Type Icon --}}
                                @if($content['type'] === 'video')
                                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @elseif($content['type'] === 'document')
                                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                @elseif($content['type'] === 'quiz')
                                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                @else
                                <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                @endif
                                
                                {{-- Content Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $content['title'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $content['type'] }}</p>
                                </div>
                                
                                {{-- Duration / Info --}}
                                @if($content['duration'])
                                <span class="text-sm text-gray-400 flex-shrink-0">{{ $content['duration'] }}</span>
                                @endif
                                
                                {{-- Lock Icon (not enrolled) --}}
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                
                {{-- Enrollment CTA --}}
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-center">
                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-3">Daftar kursus untuk membuka semua konten pembelajaran</p>
                    <button class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition">
                        Daftar Sekarang
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Tab: Prasyarat --}}
        <div id="tab-prasyarat" class="tab-content hidden">
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Prasyarat Detail</h2>
                @foreach($prerequisites as $prereq)
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl mb-3">
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $prereq['name'] }}</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Kode: {{ $prereq['code'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        {{-- Tab: Deskripsi --}}
        <div id="tab-deskripsi" class="tab-content hidden">
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4">Deskripsi Lengkap</h2>
                <p class="text-gray-600 dark:text-gray-300">{{ $courseData['description'] }}</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-4">{{ $courseData['objectives'] }}</p>
            </div>
        </div>
        
        {{-- Tab: Ulasan --}}
        <div id="tab-ulasan" class="tab-content hidden">
            {{-- Rating Summary --}}
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-6">
                    {{-- Average Rating --}}
                    <div class="text-center sm:text-left sm:pr-6 sm:border-r border-gray-200 dark:border-gray-700/50">
                        <div class="text-5xl font-bold text-gray-800 dark:text-gray-100">{{ $reviewStats['average'] }}</div>
                        <div class="flex items-center justify-center sm:justify-start gap-1 my-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($reviewStats['average']))
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @else
                                <svg class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                @endif
                            @endfor
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">{{ number_format($reviewStats['total']) }} ulasan</p>
                    </div>
                    
                    {{-- Rating Breakdown --}}
                    <div class="flex-1 space-y-2">
                        @foreach($reviewStats['breakdown'] as $star => $count)
                        @php
                            $percentage = $reviewStats['total'] > 0 ? ($count / $reviewStats['total']) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400 w-6">{{ $star }}★</span>
                            <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400 w-8">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            {{-- Sort Dropdown --}}
            <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Urutkan berdasarkan:</span>
                    <select class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-[#111827] text-gray-700 dark:text-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Terbaru</option>
                        <option>Rating Tertinggi</option>
                        <option>Rating Terendah</option>
                        <option>Paling Membantu</option>
                    </select>
                </div>
            </div>
            
            {{-- Review Cards --}}
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    {{-- Review Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $review['avatar'] }}" alt="{{ $review['name'] }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <h4 class="font-medium text-gray-800 dark:text-gray-100">{{ $review['name'] }}</h4>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review['rating'])
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        @else
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-400">{{ $review['date'] }}</span>
                    </div>
                    
                    {{-- Review Content --}}
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3">{{ $review['comment'] }}</p>
                    
                    {{-- Read More Link --}}
                    <button class="text-blue-500 hover:text-blue-600 text-sm font-medium">Lihat Selengkapnya</button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    {{-- Right Sidebar - Course Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden sticky top-24 animate-fade-in-up delay-200 hover-lift">
            {{-- Course Image --}}
            <div class="relative h-44 overflow-hidden">
                <img src="{{ $courseData['image'] }}" alt="{{ $courseData['title'] }}" class="w-full h-full object-cover">
                <span class="absolute top-3 right-3 {{ $typeColors[$courseData['type']] ?? 'bg-green-500' }} text-white text-xs font-medium px-3 py-1 rounded-full">
                    {{ $courseData['type'] }}
                </span>
            </div>
            
            {{-- Course Info --}}
            <div class="p-5">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $courseData['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ $courseData['description'] }}</p>
                
                {{-- Rating --}}
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($courseData['rating']))
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        @endif
                    @endfor
                    <span class="text-gray-400 text-xs ml-1">({{ number_format($courseData['reviews']) }} ulasan)</span>
                </div>
                
                {{-- Price --}}
                <p class="text-blue-600 dark:text-blue-400 font-bold text-xl mb-4">
                    @if($courseData['price'] > 0)
                        Rp {{ number_format($courseData['price'], 0, ',', '.') }}
                    @else
                        Gratis
                    @endif
                </p>
                
                {{-- Buttons --}}
                <a href="{{ route('mahasiswa.checkout') }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition mb-3 btn-pulse block text-center">
                    Checkout Sekarang
                </a>
                
                <button class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                    </svg>
                    Simpan ke Favorit
                </button>
                
                {{-- Creator --}}
                <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Created by {{ $courseData['creator'] }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected tab content
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            } else {
                btn.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            }
        });
    }

    function toggleModule(button) {
        const arrow = button.querySelector('.module-arrow');
        const content = button.parentElement.querySelector('.module-content');
        
        arrow.classList.toggle('rotate-90');
        content.classList.toggle('hidden');
    }
</script>
@endpush

</x-layouts.dashboard>

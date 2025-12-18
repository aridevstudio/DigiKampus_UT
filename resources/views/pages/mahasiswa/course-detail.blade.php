<x-layouts.dashboard :active="'get-courses'">
@php
    // Get course ID from URL or default to 1
    $courseId = request()->route('id') ?? 1;
    
    // Dummy course data
    $courses = [
        1 => [
            'id' => 1,
            'code' => 'STIN4101',
            'title' => 'Dasar-dasar Desain UI/UX',
            'description' => 'Pelajari prinsip fundamental desain antarmuka pengguna dan pengalaman pengguna untuk membuat produk digital yang menarik.',
            'type' => 'Webinar',
            'price' => 150000,
            'rating' => 4.5,
            'reviews' => 1250,
            'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=250&fit=crop',
            'duration' => '8 minggu (1 semester)',
            'method' => 'Online learning dengan tutorial tatap muka',
            'objectives' => 'Memahami prinsip-prinsip desain UI/UX, membuat wireframe dan prototype, serta menguji usability produk digital.',
            'creator' => 'Universitas Terbuka',
            'prerequisites' => [
                ['code' => 'STIN4100', 'name' => 'Pengantar Teknologi Informasi'],
                ['code' => 'MATA4110', 'name' => 'Matematika Dasar'],
            ],
            'modules' => [
                [
                    'title' => 'Modul 1: Pengantar UI/UX Design', 
                    'items' => 5,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Apa itu UI/UX Design?', 'duration' => '12:30'],
                        ['type' => 'video', 'title' => 'Perbedaan UI dan UX', 'duration' => '08:45'],
                        ['type' => 'document', 'title' => 'Modul PDF - Pengantar', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Pengantar UI/UX', 'duration' => '10 soal'],
                        ['type' => 'assignment', 'title' => 'Tugas: Analisis Desain', 'duration' => ''],
                    ]
                ],
                [
                    'title' => 'Modul 2: User Research', 
                    'items' => 4,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Metode User Research', 'duration' => '15:20'],
                        ['type' => 'video', 'title' => 'Interview dan Survey', 'duration' => '11:10'],
                        ['type' => 'document', 'title' => 'Template Survey', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: User Research', 'duration' => '8 soal'],
                    ]
                ],
                [
                    'title' => 'Modul 3: Wireframing & Prototyping', 
                    'items' => 6,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Pengantar Wireframing', 'duration' => '10:00'],
                        ['type' => 'video', 'title' => 'Tools untuk Prototyping', 'duration' => '18:30'],
                        ['type' => 'video', 'title' => 'Membuat Prototype dengan Figma', 'duration' => '25:00'],
                        ['type' => 'document', 'title' => 'Template Wireframe', 'duration' => ''],
                        ['type' => 'assignment', 'title' => 'Tugas: Buat Wireframe', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Prototyping', 'duration' => '12 soal'],
                    ]
                ],
                [
                    'title' => 'Modul 4: Usability Testing', 
                    'items' => 3,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Metode Usability Testing', 'duration' => '14:00'],
                        ['type' => 'document', 'title' => 'Checklist Testing', 'duration' => ''],
                        ['type' => 'assignment', 'title' => 'Tugas Akhir: Usability Report', 'duration' => ''],
                    ]
                ],
            ],
        ],
        4 => [
            'id' => 4,
            'code' => 'STIN4113',
            'title' => 'Analisis Data dengan Python',
            'description' => 'Dari pemula hingga mahir, pelajari cara mengolah dan menganalisis data menggunakan Python dan library populer.',
            'type' => 'Kursus',
            'price' => 199000,
            'rating' => 4,
            'reviews' => 980,
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=250&fit=crop',
            'duration' => '16 minggu (1 semester)',
            'method' => 'Online learning dengan tutorial tatap muka',
            'objectives' => 'Memahami konsep dasar sistem informasi, komponen-komponennya, dan penerapannya dalam organisasi.',
            'creator' => 'Universitas Terbuka',
            'prerequisites' => [
                ['code' => 'STIN4101', 'name' => 'Pengantar Teknologi Informasi'],
                ['code' => 'MATA4110', 'name' => 'Matematika Dasar'],
            ],
            'modules' => [
                [
                    'title' => 'Modul 1: Konsep Dasar Analisis Data', 
                    'items' => 5,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Pengantar Data Science', 'duration' => '15:00'],
                        ['type' => 'video', 'title' => 'Tipe Data dan Struktur', 'duration' => '12:30'],
                        ['type' => 'video', 'title' => 'Data Cleaning Dasar', 'duration' => '18:00'],
                        ['type' => 'document', 'title' => 'Modul PDF - Konsep Dasar', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Konsep Dasar', 'duration' => '10 soal'],
                    ]
                ],
                [
                    'title' => 'Modul 2: Database dan Manajemen Data', 
                    'items' => 4,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Pengantar SQL', 'duration' => '20:00'],
                        ['type' => 'video', 'title' => 'Query Data dengan Python', 'duration' => '16:45'],
                        ['type' => 'assignment', 'title' => 'Praktik SQL', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Database', 'duration' => '8 soal'],
                    ]
                ],
                [
                    'title' => 'Modul 3: Python untuk Data Science', 
                    'items' => 6,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Pandas Dasar', 'duration' => '22:00'],
                        ['type' => 'video', 'title' => 'NumPy Fundamentals', 'duration' => '18:30'],
                        ['type' => 'video', 'title' => 'Data Manipulation', 'duration' => '25:00'],
                        ['type' => 'document', 'title' => 'Cheatsheet Pandas', 'duration' => ''],
                        ['type' => 'assignment', 'title' => 'Tugas: Analisis Dataset', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Python', 'duration' => '15 soal'],
                    ]
                ],
                [
                    'title' => 'Modul 4: Visualisasi Data', 
                    'items' => 4,
                    'contents' => [
                        ['type' => 'video', 'title' => 'Matplotlib & Seaborn', 'duration' => '20:00'],
                        ['type' => 'video', 'title' => 'Interactive Charts', 'duration' => '15:00'],
                        ['type' => 'assignment', 'title' => 'Tugas Akhir: Dashboard', 'duration' => ''],
                        ['type' => 'quiz', 'title' => 'Quiz: Visualisasi', 'duration' => '10 soal'],
                    ]
                ],
            ],
        ],
    ];
    
    // Get course or default
    $course = $courses[$courseId] ?? $courses[4];
    
    // Dummy reviews data
    $reviewStats = [
        'average' => 4.7,
        'total' => 245,
        'breakdown' => [
            5 => 191,
            4 => 37,
            3 => 12,
            2 => 3,
            1 => 2,
        ]
    ];
    
    $reviews = [
        [
            'name' => 'Ahmad Rizki',
            'avatar' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=3b82f6&color=fff',
            'rating' => 5,
            'date' => '2 hari yang lalu',
            'comment' => 'Kursus ini sangat membantu dalam memahami konsep dasar statistika. Materi dijelaskan dengan sangat detail dan mudah dipahami. Dosen pengajar juga responsif dalam menjawab pertanyaan di forum diskusi.',
        ],
        [
            'name' => 'Maya Sari',
            'avatar' => 'https://ui-avatars.com/api/?name=Maya+Sari&background=ec4899&color=fff',
            'rating' => 4,
            'date' => '5 hari yang lalu',
            'comment' => 'Materi kursus lengkap dan terstruktur dengan baik. Video pembelajaran berkualitas tinggi. Hanya saja beberapa quiz agak sulit, tapi overall sangat recommended untuk yang ingin belajar statistika dari dasar.',
        ],
        [
            'name' => 'Budi Santoso',
            'avatar' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=10b981&color=fff',
            'rating' => 5,
            'date' => '1 minggu yang lalu',
            'comment' => 'Excellent course! Saya sudah mencoba beberapa kursus online lainnya, tapi ini yang paling komprehensif. Tugas-tugasnya relevan dengan dunia kerja.',
        ],
        [
            'name' => 'Siti Nurhaliza',
            'avatar' => 'https://ui-avatars.com/api/?name=Siti+Nurhaliza&background=f59e0b&color=fff',
            'rating' => 4,
            'date' => '2 minggu yang lalu',
            'comment' => 'Bagus untuk pemula. Penjelasannya step by step dan tidak terlalu cepat. Recommended!',
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
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('mahasiswa.get-courses') }}" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 rounded-lg transition">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $course['title'] }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $course['code'] }}</p>
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
                    @foreach($course['modules'] as $moduleIndex => $module)
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
                    @foreach($course['prerequisites'] as $prereq)
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
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $course['duration'] }}</p>
                        </div>
                    </div>
                    
                    {{-- Metode --}}
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Metode</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $course['method'] }}</p>
                        </div>
                    </div>
                    
                    {{-- Tujuan Pembelajaran --}}
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100 text-sm">Tujuan Pembelajaran</p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $course['objectives'] }}</p>
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
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ count($course['modules']) }} Modul • {{ collect($course['modules'])->sum('items') }} Item</span>
                </div>
                
                <div class="space-y-4">
                    @foreach($course['modules'] as $moduleIndex => $module)
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
                @foreach($course['prerequisites'] as $prereq)
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
                <p class="text-gray-600 dark:text-gray-300">{{ $course['description'] }}</p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-4">{{ $course['objectives'] }}</p>
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
                            $percentage = ($count / $reviewStats['total']) * 100;
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
                <img src="{{ $course['image'] }}" alt="{{ $course['title'] }}" class="w-full h-full object-cover">
                <span class="absolute top-3 right-3 {{ $typeColors[$course['type']] }} text-white text-xs font-medium px-3 py-1 rounded-full">
                    {{ $course['type'] }}
                </span>
            </div>
            
            {{-- Course Info --}}
            <div class="p-5">
                <h3 class="font-bold text-gray-800 dark:text-gray-100 mb-2">{{ $course['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-3 line-clamp-2">{{ $course['description'] }}</p>
                
                {{-- Rating --}}
                <div class="flex items-center gap-1 mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($course['rating']))
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        @endif
                    @endfor
                    <span class="text-gray-400 text-xs ml-1">({{ number_format($course['reviews']) }} ulasan)</span>
                </div>
                
                {{-- Price --}}
                <p class="text-blue-600 dark:text-blue-400 font-bold text-xl mb-4">
                    Rp {{ number_format($course['price'], 0, ',', '.') }}
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
                    <span class="text-gray-500 dark:text-gray-400 text-sm">Created by {{ $course['creator'] }}</span>
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

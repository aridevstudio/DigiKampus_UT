<x-layouts.dashboard :active="'get-courses'">
@php
    $defaultImage = 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop';
    $courseImage = $course->thumbnail ? asset('storage/' . $course->thumbnail) : $defaultImage;

    $allRatings = $course->ratings ?? collect();
    $totalReviews = $allRatings->count();
    $avgRating = $totalReviews > 0 ? (float) $allRatings->avg('rating') : 0.0;

    $estimatedDuration = null;
    if (!empty($course->estimasi_waktu) && !empty($course->durasi_satuan)) {
        $estimatedDuration = trim($course->estimasi_waktu . ' ' . $course->durasi_satuan);
    }

    $totalMaterialMinutes = (int) ($course->materials?->sum('durasi') ?? 0);
    if (!$estimatedDuration && $totalMaterialMinutes > 0) {
        $hours = intdiv($totalMaterialMinutes, 60);
        $minutes = $totalMaterialMinutes % 60;

        if ($hours > 0 && $minutes > 0) {
            $estimatedDuration = $hours . ' jam ' . $minutes . ' menit';
        } elseif ($hours > 0) {
            $estimatedDuration = $hours . ' jam';
        } else {
            $estimatedDuration = $minutes . ' menit';
        }
    }

    if (!$estimatedDuration) {
        $estimatedDuration = 'Belum ditentukan';
    }

    $normalizedCategory = strtolower((string) ($course->kategori ?? 'kursus'));
    $methodByCategory = [
        'kursus' => 'Belajar mandiri berbasis materi',
        'webinar' => 'Sesi live interaktif bersama pengajar',
        'tiket' => 'Kehadiran pada event terjadwal',
    ];

    $courseData = [
        'id' => $course->id_course,
        'code' => $course->kode_course ?? '-',
        'title' => $course->nama_course,
        'description' => $course->deskripsi ?? 'Tidak ada deskripsi tersedia',
        'type' => ucfirst($normalizedCategory ?: 'kursus'),
        'price' => floatval($course->harga ?? 0),
        'rating' => round($avgRating, 1),
        'reviews' => $totalReviews,
        'image' => $courseImage,
        'duration' => $estimatedDuration,
        'method' => $methodByCategory[$normalizedCategory] ?? 'Belajar mandiri berbasis materi',
        'objectives' => $course->deskripsi ?? 'Memahami konsep dasar dan penerapannya.',
        'creator' => $course->dosen ? $course->dosen->name : 'Universitas Terbuka',
    ];

    $mapContentType = static function (?string $itemType): string {
        return match ($itemType) {
            'video' => 'video',
            'kuis', 'quiz' => 'quiz',
            'tugas' => 'assignment',
            'bacaan', 'text' => 'document',
            default => 'document',
        };
    };

    $modules = [];

    if ($course->modules && $course->modules->count() > 0) {
        $moduleNumber = 1;
        foreach ($course->modules as $module) {
            $contents = [];

            foreach ($module->materials as $material) {
                $contents[] = [
                    'type' => $mapContentType($material->tipe),
                    'title' => $material->judul_material,
                    'duration' => $material->durasi ? ($material->durasi . ' menit') : '',
                ];
            }

            $modules[] = [
                'title' => 'Modul ' . $moduleNumber . ': ' . ($module->judul_module ?? 'Materi Pembelajaran'),
                'items' => count($contents),
                'contents' => $contents,
            ];
            $moduleNumber++;
        }
    } elseif ($course->materials && $course->materials->count() > 0) {
        $groupedMaterials = $course->materials
            ->sortBy('urutan')
            ->groupBy(fn ($material) => $material->id_module ?: 'default');

        $moduleNumber = 1;
        foreach ($groupedMaterials as $materials) {
            $contents = [];
            foreach ($materials as $material) {
                $contents[] = [
                    'type' => $mapContentType($material->tipe),
                    'title' => $material->judul_material,
                    'duration' => $material->durasi ? ($material->durasi . ' menit') : '',
                ];
            }

            $modules[] = [
                'title' => 'Modul ' . $moduleNumber . ': Materi Pembelajaran',
                'items' => count($contents),
                'contents' => $contents,
            ];
            $moduleNumber++;
        }
    }

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

    if (empty($modules)) {
        $modules[] = [
            'title' => 'Modul Belum Tersedia',
            'items' => 0,
            'contents' => []
        ];
    }

    $prerequisites = [];
    $rawPrerequisites = trim((string) ($course->persyaratan ?? ''));
    if ($rawPrerequisites !== '') {
        $lines = preg_split('/\r\n|\r|\n/', $rawPrerequisites) ?: [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $code = null;
            $name = $line;

            if (str_contains($line, '|')) {
                [$first, $second] = array_map('trim', explode('|', $line, 2));
                $code = $first !== '' ? $first : null;
                $name = $second !== '' ? $second : $line;
            } elseif (preg_match('/^([A-Za-z0-9\/\-.]+)\s*[-:]\s*(.+)$/', $line, $matches)) {
                $code = trim($matches[1]) ?: null;
                $name = trim($matches[2]) ?: $line;
            }

            $prerequisites[] = [
                'code' => $code,
                'name' => $name,
            ];
        }
    }

    $reviewStats = [
        'average' => round($avgRating, 1),
        'total' => $totalReviews,
        'breakdown' => [
            5 => $allRatings->where('rating', 5)->count(),
            4 => $allRatings->where('rating', 4)->count(),
            3 => $allRatings->where('rating', 3)->count(),
            2 => $allRatings->where('rating', 2)->count(),
            1 => $allRatings->where('rating', 1)->count(),
        ],
    ];

    $reviews = $allRatings
        ->sortByDesc('created_at')
        ->take(10)
        ->map(function ($rating) {
            if ($rating->mahasiswa && $rating->mahasiswa->profile && $rating->mahasiswa->profile->foto_profile) {
                $profilePhoto = asset('storage/' . $rating->mahasiswa->profile->foto_profile);
            } else {
                $profilePhoto = 'https://ui-avatars.com/api/?name=' . urlencode($rating->mahasiswa ? $rating->mahasiswa->name : 'A') . '&background=3b82f6&color=fff';
            }

            return [
                'name' => $rating->mahasiswa ? $rating->mahasiswa->name : 'Anonymous',
                'avatar' => $profilePhoto,
                'rating' => $rating->rating,
                'date' => $rating->created_at ? $rating->created_at->diffForHumans() : 'Baru saja',
                'comment' => $rating->ulasan ?: 'Tidak ada komentar.',
            ];
        })
        ->values()
        ->all();

    $typeColors = [
        'Webinar' => 'bg-blue-500',
        'Kursus' => 'bg-green-500',
        'Tiket' => 'bg-rose-500',
        'webinar' => 'bg-blue-500',
        'kursus' => 'bg-green-500',
        'tiket' => 'bg-rose-500',
    ];

    $certificateEligible = (bool) ($course->sertifikat ?? false)
        && $isEnrolled
        && (($enrollment->status ?? null) === 'selesai' || (int) ($enrollment->progress ?? 0) >= 100);
    $issuedCertificateNumber = $issuedCertificate['number'] ?? null;
    $issuedCertificateDate = $issuedCertificate['issued_date'] ?? now()->format('d F Y');
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
<div class="flex items-center gap-3 sm:gap-4 md:gap-6 px-1 border-b border-gray-200 dark:border-gray-700/50 mb-6 animate-fade-in-up delay-100 overflow-x-auto">
    <button onclick="showTab('ringkasan')" data-tab="ringkasan" class="tab-btn pb-3 text-xs sm:text-sm font-medium border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 whitespace-nowrap">
        Ringkasan
    </button>
    <button onclick="showTab('konten')" data-tab="konten" class="tab-btn pb-3 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Konten Kursus
    </button>
    <button onclick="showTab('prasyarat')" data-tab="prasyarat" class="tab-btn pb-3 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Prasyarat
    </button>
    <button onclick="showTab('deskripsi')" data-tab="deskripsi" class="tab-btn pb-3 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
        Deskripsi
    </button>
    <button onclick="showTab('ulasan')" data-tab="ulasan" class="tab-btn pb-3 text-xs sm:text-sm font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 whitespace-nowrap">
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
                            @if(count($module['contents']) > 20)
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                                @foreach($module['contents'] as $index => $content)
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center gap-2 transition hover:border-blue-300">
                                    @if($content['type'] === 'video')
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg></div>
                                    @elseif($content['type'] === 'document')
                                    <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-500/20 flex items-center justify-center text-green-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                    @elseif($content['type'] === 'quiz')
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center text-purple-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                    @else
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center text-orange-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg></div>
                                    @endif
                                    <p class="text-xs text-gray-700 dark:text-gray-300 line-clamp-2" title="{{ $content['title'] }}"><span class="font-bold mr-1">{{ $index + 1 }}.</span> {{ $content['title'] }}</p>
                                </div>
                                @endforeach
                            </div>
                            @else
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
                            @endif
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
                    @forelse($prerequisites as $prereq)
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-700 dark:text-gray-300">
                            {{ $prereq['name'] }}@if(!empty($prereq['code'])) ({{ $prereq['code'] }})@endif
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada prasyarat.</p>
                    @endforelse
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
                            @if(count($module['contents']) > 20)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4">
                                @foreach($module['contents'] as $index => $content)
                                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center gap-2 hover:shadow-sm transition">
                                    @if($content['type'] === 'video')
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-500"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg></div>
                                    @elseif($content['type'] === 'document')
                                    <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-500/20 flex items-center justify-center text-green-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                                    @elseif($content['type'] === 'quiz')
                                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center text-purple-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                    @else
                                    <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-500/20 flex items-center justify-center text-orange-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg></div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0 w-full mt-1">
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-100 line-clamp-2" title="{{ $content['title'] }}">{{ $index + 1 }}. {{ $content['title'] }}</p>
                                    </div>
                                    
                                    @if($content['duration'])
                                    <span class="text-[10px] text-gray-400 flex-shrink-0 mt-auto">{{ $content['duration'] }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
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
                            @endif
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
                @forelse($prerequisites as $prereq)
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl mb-3">
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $prereq['name'] }}</p>
                    @if(!empty($prereq['code']))
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Kode: {{ $prereq['code'] }}</p>
                    @endif
                </div>
                @empty
                <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada prasyarat untuk kursus ini.</p>
                </div>
                @endforelse
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
                            <span class="text-sm text-gray-600 dark:text-gray-400 w-6">{{ $star }}&starf;</span>
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
                @forelse($reviews as $review)
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
                @empty
                <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada ulasan untuk kursus ini.</p>
                </div>
                @endforelse
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
                @if($isEnrolled)
                    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2 mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $enrollment && $enrollment->progress >= 100 ? 'Lihat Kembali Materi' : 'Lanjutkan Belajar' }}
                    </a>
                    @if($enrollment && $enrollment->progress >= 100)
                        <button onclick="openReviewModal()" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2 mb-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            Beri Ulasan Kursus
                        </button>
                    @endif

                    @if($certificateEligible)
                        <div class="mb-3 space-y-2">
                            <button type="button" onclick="downloadDetailCertificate(@js($courseData['title']), @js(optional(Auth::guard('mahasiswa')->user())->name ?? 'Mahasiswa'), @js($issuedCertificateDate), @js($issuedCertificateNumber))" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-3 rounded-xl font-medium transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v10m0 0l-4-4m4 4l4-4M4 19h16" />
                                </svg>
                                Download Sertifikat
                            </button>
                            <button type="button" onclick="printDetailCertificate(@js($courseData['title']), @js(optional(Auth::guard('mahasiswa')->user())->name ?? 'Mahasiswa'), @js($issuedCertificateDate), @js($issuedCertificateNumber))" class="w-full border border-emerald-300 dark:border-emerald-700/50 text-emerald-700 dark:text-emerald-300 py-3 rounded-xl font-medium hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7m-9 12h6m-7 0h8a2 2 0 002-2v-5H6v5a2 2 0 002 2zM6 14H4a2 2 0 01-2-2v-3a2 2 0 012-2h16a2 2 0 012 2v3a2 2 0 01-2 2h-2" />
                                </svg>
                                Cetak Sertifikat
                            </button>
                            @if($issuedCertificateNumber)
                            <p class="text-[11px] text-emerald-700/90 dark:text-emerald-300/90">No. Sertifikat: {{ $issuedCertificateNumber }}</p>
                            @endif
                        </div>
                    @endif
                @else
                    <form action="{{ route('mahasiswa.cart.add') }}" method="POST" class="mb-3">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id_course }}">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-xl font-medium transition btn-pulse flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Tambah ke Keranjang
                        </button>
                    </form>
                @endif
                
                @if($isFavorited)
                <form action="{{ route('mahasiswa.favorite.remove', $course->id_course) }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full border border-red-500 text-red-500 py-3 rounded-xl font-medium hover:bg-red-50 dark:hover:bg-red-900/20 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        Hapus dari Favorit
                    </button>
                </form>
                @else
                <form action="{{ route('mahasiswa.favorite.add') }}" method="POST" class="w-full">
                    @csrf
                    <input type="hidden" name="id_course" value="{{ $course->id_course }}">
                    <button type="submit" class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700/50 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Simpan ke Favorit
                    </button>
                </form>
                @endif
                
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
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Auto show specific tab if present in URL
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            showTab(tabParam);
        } else {
            // Check hash as fallback to support #tab=ulasan etc
            const hash = window.location.hash;
            if (hash === '#ulasan' || hash === '#tab-ulasan') {
                showTab('ulasan');
            }
        }
        
        // Auto open review modal if requested
        if (urlParams.get('review') === 'true') {
            showTab('ulasan');
            openReviewModal();
        }
    });

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

    function escapeDetailCertificateHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildDetailCertificateNumber(certificateNumber) {
        if (certificateNumber && String(certificateNumber).trim() !== '') {
            return String(certificateNumber).trim();
        }

        const year = new Date().getFullYear();
        const serial = String(Date.now()).slice(-6);
        return `SRT-${year}-${serial}`;
    }

    function buildDetailCertificateHtml(courseTitle, studentName, completedDate, certificateNumber) {
        const safeCourse = escapeDetailCertificateHtml(courseTitle || 'Kursus');
        const safeStudent = escapeDetailCertificateHtml(studentName || 'Mahasiswa');
        const safeDate = escapeDetailCertificateHtml(completedDate || '');
        const safeNumber = escapeDetailCertificateHtml(certificateNumber || '-');

        return `
            <html>
            <head>
                <title>Sertifikat ${safeCourse}</title>
                <style>
                    body { margin:0; font-family: Georgia, 'Times New Roman', serif; background:#eef2ff; }
                    .page { width:1123px; height:794px; margin:24px auto; background:linear-gradient(145deg,#ffffff 0%,#f8fafc 55%,#eef2ff 100%); border:12px solid #1d4ed8; box-sizing:border-box; position:relative; }
                    .inner { position:absolute; inset:18px; border:2px solid #93c5fd; padding:56px 72px; text-align:center; }
                    .title { font-size:44px; font-weight:700; color:#1e3a8a; letter-spacing:1px; margin-top:10px; text-transform:uppercase; }
                    .subtitle { font-size:18px; color:#475569; margin-top:18px; }
                    .name { font-size:44px; color:#0f172a; font-weight:700; margin:18px 0 10px; }
                    .course { font-size:24px; color:#1d4ed8; font-weight:600; margin:8px 0 20px; }
                    .badge { display:inline-block; font-size:12px; color:#0f172a; background:#e2e8f0; padding:6px 14px; border-radius:999px; margin-top:12px; }
                    .meta { display:flex; justify-content:space-between; margin-top:48px; color:#334155; font-size:14px; gap:24px; }
                    .line { border-top:1px solid #94a3b8; width:260px; margin:10px auto 6px; }
                    @media print { body { background:#fff; } .page { margin:0 auto; } }
                </style>
            </head>
            <body>
                <div class="page">
                    <div class="inner">
                        <div class="title">Sertifikat Kelulusan</div>
                        <div class="subtitle">Diberikan kepada</div>
                        <div class="name">${safeStudent}</div>
                        <div class="subtitle">atas keberhasilan menyelesaikan</div>
                        <div class="course">${safeCourse}</div>
                        <div class="badge">Nomor Sertifikat: ${safeNumber}</div>
                        <div class="meta">
                            <div>
                                <div>Tanggal Terbit</div>
                                <div><strong>${safeDate}</strong></div>
                            </div>
                            <div>
                                <div class="line"></div>
                                <div>Pengajar / Platform</div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `;
    }

    function printDetailCertificate(courseTitle, studentName, completedDate, certificateNumber = null) {
        const certNo = buildDetailCertificateNumber(certificateNumber);
        const popup = window.open('', '_blank', 'width=1200,height=800');
        if (!popup) return;

        popup.document.write(buildDetailCertificateHtml(courseTitle, studentName, completedDate, certNo) + `
            <script>
                window.onload = function() { window.print(); };
            <\/script>
        `);
        popup.document.close();
    }

    function downloadDetailCertificate(courseTitle, studentName, completedDate, certificateNumber = null) {
        const certNo = buildDetailCertificateNumber(certificateNumber);

        if (!window.jspdf || !window.jspdf.jsPDF) {
            printDetailCertificate(courseTitle, studentName, completedDate, certNo);
            return;
        }

        const doc = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();
        const centerX = pageWidth / 2;

        doc.setFillColor(248, 250, 252);
        doc.rect(0, 0, pageWidth, pageHeight, 'F');

        doc.setDrawColor(29, 78, 216);
        doc.setLineWidth(8);
        doc.rect(24, 24, pageWidth - 48, pageHeight - 48);

        doc.setDrawColor(147, 197, 253);
        doc.setLineWidth(1.5);
        doc.rect(38, 38, pageWidth - 76, pageHeight - 76);

        doc.setTextColor(30, 58, 138);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(34);
        doc.text('SERTIFIKAT KELULUSAN', centerX, 120, { align: 'center' });

        doc.setTextColor(71, 85, 105);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(15);
        doc.text('Diberikan kepada', centerX, 165, { align: 'center' });

        doc.setTextColor(15, 23, 42);
        doc.setFont('times', 'bold');
        doc.setFontSize(36);
        doc.text(String(studentName || 'Mahasiswa'), centerX, 220, { align: 'center' });

        doc.setTextColor(71, 85, 105);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(14);
        doc.text('atas keberhasilan menyelesaikan', centerX, 258, { align: 'center' });

        doc.setTextColor(29, 78, 216);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(22);
        const wrappedCourse = doc.splitTextToSize(String(courseTitle || 'Kursus'), pageWidth - 180);
        doc.text(wrappedCourse, centerX, 295, { align: 'center' });

        doc.setTextColor(30, 41, 59);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.text(`Nomor Sertifikat: ${certNo}`, centerX, 350, { align: 'center' });
        doc.text(`Tanggal Terbit: ${String(completedDate || '-')}`, centerX, 375, { align: 'center' });

        doc.setDrawColor(148, 163, 184);
        doc.setLineWidth(1);
        doc.line(pageWidth - 300, pageHeight - 140, pageWidth - 80, pageHeight - 140);

        doc.setTextColor(51, 65, 85);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.text('Pengajar / Platform', pageWidth - 190, pageHeight - 120, { align: 'center' });

        const slug = String(courseTitle || 'sertifikat')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        doc.save(`sertifikat-${slug || 'kursus'}-${certNo}.pdf`);
    }

    // Modal Review
    function openReviewModal() {
        document.getElementById('reviewModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    
    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Star Rating Logic
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-rating');
        const ratingInput = document.getElementById('rating-input');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;
                
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.remove('text-gray-300', 'dark:text-gray-600');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.add('text-gray-300', 'dark:text-gray-600');
                        s.classList.remove('text-yellow-400');
                    }
                });
            });
            
            // Optional: Hover effect
            star.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-value');
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('text-yellow-300');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                stars.forEach(s => {
                    s.classList.remove('text-yellow-300');
                });
            });
        });
    });
</script>
@endpush

{{-- Modal Beri Ulasan --}}
<div id="reviewModal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeReviewModal()"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative mx-3 sm:mx-0 bg-white dark:bg-[#1f2937] rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full max-h-[calc(100dvh-2rem)] overflow-y-auto">
            <form action="{{ route('mahasiswa.course.review', $course->id_course) }}" method="POST">
                @csrf
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Berikan Ulasan Anda</h3>
                        <button type="button" onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        {{-- Rating Stars --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Penilaian Anda</label>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="rating" id="rating-input" value="5" required>
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="star-rating focus:outline-none text-yellow-400 transition" data-value="{{ $i }}">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                                @endfor
                            </div>
                        </div>

                        {{-- Review Text --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tuliskan pengalaman Anda</label>
                            <textarea name="ulasan" rows="4" class="w-full bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-800 dark:text-gray-100" placeholder="Apa yang Anda pelajari dari kursus ini? Bagaimana penyampaian materinya?" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:justify-end gap-3 rounded-b-2xl">
                    <button type="button" onclick="closeReviewModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-500 border border-transparent rounded-xl hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition w-full sm:w-auto">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-layouts.dashboard>

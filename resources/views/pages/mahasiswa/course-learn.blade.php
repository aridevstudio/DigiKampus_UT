<x-layouts.dashboard :active="'courses'">
@php
    $extractYoutubeId = static function (?string $url): ?string {
        if (!$url) {
            return null;
        }

        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if (str_contains($host, 'youtu.be')) {
            $id = trim($path, '/');
            return $id !== '' ? $id : null;
        }

        if (str_contains($host, 'youtube.com')) {
            parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
            if (!empty($query['v'])) {
                return $query['v'];
            }

            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            $embedIndex = array_search('embed', $segments, true);
            if ($embedIndex !== false && !empty($segments[$embedIndex + 1])) {
                return $segments[$embedIndex + 1];
            }

            $shortsIndex = array_search('shorts', $segments, true);
            if ($shortsIndex !== false && !empty($segments[$shortsIndex + 1])) {
                return $segments[$shortsIndex + 1];
            }
        }

        return null;
    };

    $courseVideoIds = collect($modules ?? [])
        ->flatMap(fn ($module) => collect($module['materials'] ?? []))
        ->filter(fn ($material) => ($material['type'] ?? null) === 'video' && !empty($material['video_url']))
        ->map(fn ($material) => $extractYoutubeId($material['video_url']))
        ->filter()
        ->values()
        ->all();

    $isPlaylistMode = request('play') === 'pack';
    $certificateEligible = (bool) ($course->sertifikat ?? false)
        && (($enrollment->status ?? null) === 'selesai' || (int) ($progressPercent ?? 0) >= 100);
@endphp

{{-- Back Link & Title Row --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6 animate-fade-in-up">
    <div class="flex items-center gap-4">
        <a href="{{ route('mahasiswa.courses') }}" class="inline-flex items-center gap-2 text-blue-500 hover:text-blue-600 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Kursus
        </a>
        <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 font-medium transition border-l pl-4 border-gray-300 dark:border-gray-700 text-sm">
            Lihat Detail Kursus
        </a>
    </div>
    
    {{-- Progress Badge --}}
    <div class="flex items-center gap-2 bg-white dark:bg-[#1f2937] px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-700/50">
        <span class="text-sm text-gray-600 dark:text-gray-400">Progress Kursus</span>
        <div class="w-16 sm:w-20 lg:w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
        </div>
        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $progressPercent }}% selesai</span>
    </div>
</div>

{{-- Main 3-Column Layout --}}
<div class="course-learn-layout flex flex-col lg:flex-row gap-6">
    
    {{-- LEFT SIDEBAR: Module List --}}
    <div class="course-sidebar-left w-full lg:w-[250px] lg:min-w-[250px] lg:flex-shrink-0">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sticky top-24">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-2">Modul Pembelajaran</h2>
            
            {{-- Progress Bar --}}
            <div class="flex items-center gap-2 mb-4">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Progress Keseluruhan: {{ $progressPercent }}%</p>
            
            @if($progressPercent >= 100)
            <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}?review=true" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white text-center py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2 mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                Beri Ulasan
            </a>
            @endif
            
            {{-- Modules --}}
            <div class="space-y-3" style="max-height: 60vh; overflow-y: auto;">
                @forelse($modules as $moduleIndex => $module)
                <div class="border border-gray-200 dark:border-gray-700/50 rounded-xl overflow-hidden">
                    {{-- Module Header --}}
                    <button onclick="toggleModule({{ $moduleIndex }})" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition text-left">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800 dark:text-gray-100 text-sm">{{ $module['title'] }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    @php $moduleProgress = $module['total'] > 0 ? ($module['completed'] / $module['total']) * 100 : 0; @endphp
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $moduleProgress }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $module['completed'] }}/{{ $module['total'] }} selesai</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 module-arrow-{{ $moduleIndex }} transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    {{-- Materials List --}}
                    <div id="module-{{ $moduleIndex }}" class="{{ $loop->first ? '' : 'hidden' }} border-t border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-800/30">
                        @if(count($module['materials']) > 20)
                            {{-- Compact 4-Column Grid for >20 materials --}}
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-4 xl:grid-cols-5 gap-2 p-3">
                                @foreach($module['materials'] as $index => $material)
                                <a href="{{ route('mahasiswa.course-learn', ['id' => $course->id_course, 'material' => $material['id']]) }}" 
                                   title="{{ $material['title'] }}"
                                   class="relative aspect-square flex flex-col items-center justify-center rounded-xl border transition-all hover:scale-105 {{ $currentMaterial && $currentMaterial['id'] == $material['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-blue-300' }}">
                                    
                                    <span class="text-xs font-bold {{ $currentMaterial && $currentMaterial['id'] == $material['id'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $index + 1 }}
                                    </span>
                                    
                                    @if($material['is_completed'])
                                    <div class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-green-500 flex items-center justify-center border-2 border-white dark:border-gray-800">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    @endif
                                </a>
                                @endforeach
                            </div>
                        @else
                            {{-- Standard List View --}}
                            @foreach($module['materials'] as $material)
                            <a href="{{ route('mahasiswa.course-learn', ['id' => $course->id_course, 'material' => $material['id']]) }}" 
                               class="flex items-center gap-3 p-3 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition border-b border-gray-200 dark:border-gray-700/50 last:border-b-0 {{ $currentMaterial && $currentMaterial['id'] == $material['id'] ? 'bg-blue-50 dark:bg-blue-500/10' : '' }}" style="{{ $currentMaterial && $currentMaterial['id'] == $material['id'] ? 'border-left: 4px solid #3b82f6;' : '' }}">
                                
                                {{-- Status Icon --}}
                                @if($material['is_completed'])
                                <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @elseif($currentMaterial && $currentMaterial['id'] == $material['id'])
                                <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                @else
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                @endif
                                
                                {{-- Material Info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm {{ $material['is_completed'] ? 'text-gray-500' : 'text-gray-800 dark:text-gray-100' }} truncate">{{ $material['title'] }}</p>
                                    <p class="text-xs {{ $material['is_completed'] ? 'text-green-500' : ($currentMaterial && $currentMaterial['id'] == $material['id'] ? 'text-blue-500' : 'text-gray-400') }}">
                                        {{ $material['is_completed'] ? 'Selesai' : ($currentMaterial && $currentMaterial['id'] == $material['id'] ? 'Sedang berlangsung' : '') }}
                                    </p>
                                </div>
                            </a>
                            @endforeach
                        @endif
                        
                        {{-- Quiz Link --}}
                        @if(!empty($module['quiz']))
                        <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $module['quiz']['id']]) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 transition bg-yellow-50/50 dark:bg-yellow-500/5 border-t border-yellow-200 dark:border-yellow-700/30">
                            <div class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center flex-shrink-0 relative">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                @if($module['quiz_completed'] ?? false)
                                <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-green-500 flex items-center justify-center border border-white dark:border-gray-800">
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-yellow-700 dark:text-yellow-400">{{ $module['quiz']['title'] ?? 'Kuis Akhir Modul' }}</p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-500">
                                    @if($module['quiz_completed'] ?? false)
                                        Selesai
                                    @else
                                        Durasi: {{ $module['quiz']['duration'] ?? 30 }} menit
                                    @endif
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                        
                        {{-- Assignment Link --}}
                        @if(!empty($module['assignment']))
                        <a href="{{ route('mahasiswa.assignment-detail', ['courseId' => $course->id_course, 'assignmentId' => $module['assignment']['id']]) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-orange-50 dark:hover:bg-orange-500/10 transition bg-orange-50/50 dark:bg-orange-500/5 border-t border-orange-200 dark:border-orange-700/30">
                            <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0 relative">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                @if($module['assignment_completed'] ?? false)
                                <div class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-green-500 flex items-center justify-center border border-white dark:border-gray-800">
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-orange-700 dark:text-orange-400">{{ $module['assignment']['title'] ?? 'Tugas Akhir Modul' }}</p>
                                <p class="text-xs text-orange-600 dark:text-orange-500">
                                    @if($module['assignment_completed'] ?? false)
                                        Selesai
                                    @else
                                        Opsional (boleh dikumpulkan)
                                    @endif
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                        
                        {{-- Feedback & Nilai Link - Module complete without requiring assignment (assignment is optional) --}}
                        @php
                            $completedMaterials = collect($module['materials'])->where('is_completed', true)->count();
                            $totalMaterials = count($module['materials']);
                            $materialsComplete = $totalMaterials > 0 && $completedMaterials == $totalMaterials;
                            $hasQuiz = !empty($module['quiz']);
                            $hasAssignment = !empty($module['assignment']);
                            $quizComplete = !$hasQuiz || ($module['quiz_completed'] ?? false);
                            $assignmentComplete = true; // assignment optional
                            $isModuleComplete = $materialsComplete && $quizComplete && $assignmentComplete;
                        @endphp
                        @if($isModuleComplete)
                        <a href="{{ route('mahasiswa.module-feedback', ['courseId' => $course->id_course, 'moduleId' => $moduleIndex]) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition bg-indigo-50/50 dark:bg-indigo-500/5 border-t border-indigo-200 dark:border-indigo-700/30">
                            <div class="w-6 h-6 rounded-full bg-indigo-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-700 dark:text-indigo-400">Feedback & Nilai</p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-500">Lihat hasil evaluasi</p>
                            </div>
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p class="text-sm">Belum ada materi</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    {{-- CENTER: Content Area --}}
    <div class="course-content-center flex-1 min-w-0">
        <div class="space-y-4">
            {{-- Course Title --}}
            <div>
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $currentMaterial ? $currentMaterial['title'] : $course->nama_course }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    @if($currentMaterial)
                    @php
                        $materialTypeLabel = [
                            'video' => 'Video',
                            'bacaan' => 'Bacaan',
                            'kuis' => 'Kuis',
                            'tugas' => 'Tugas',
                        ];
                    @endphp
                    {{ $modules[$currentModuleIndex]['title'] ?? 'Materi' }} - {{ $materialTypeLabel[$currentMaterial['type']] ?? 'Materi' }}: {{ $currentMaterial['title'] }}
                    @else
                    Pilih materi untuk memulai
                    @endif
                </p>
            </div>
            
            {{-- Video/Content Area --}}
            <div id="video-protected-player" tabindex="0" class="bg-gray-900 rounded-2xl overflow-hidden relative focus:outline-none" style="aspect-ratio: 16/9;">
                @if($currentMaterial)
                    @if($currentMaterial['type'] == 'video' && !empty($currentMaterial['video_url']))
                        @php
                            $videoUrl = $currentMaterial['video_url'];
                            $embedUrl = '';
                            $videoId = $extractYoutubeId($videoUrl);
                            $isPackPlayable = $videoId && in_array($videoId, $courseVideoIds, true) && count($courseVideoIds) > 1;

                            if ($videoId) {
                                $playlistIds = array_values(array_filter($courseVideoIds, fn ($id) => $id !== $videoId));
                                $params = [
                                    'rel' => '0',
                                    'modestbranding' => '1',
                                    'playsinline' => '1',
                                    'fs' => '0',
                                    'disablekb' => '1',
                                    'enablejsapi' => '1',
                                    'iv_load_policy' => '3',
                                    'cc_load_policy' => '0',
                                    'origin' => request()->getSchemeAndHttpHost(),
                                ];

                                if ($isPlaylistMode && $isPackPlayable) {
                                    $params['autoplay'] = '1';
                                    if (!empty($playlistIds)) {
                                        $params['playlist'] = implode(',', $playlistIds);
                                    }
                                }

                                $embedUrl = "https://www.youtube-nocookie.com/embed/{$videoId}?" . http_build_query($params);
                            }
                        @endphp

                        @if($embedUrl)
                            <iframe id="course-youtube-player" src="{{ $embedUrl }}" title="Video Player" frameborder="0" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" class="w-full h-full"></iframe>
                        @else
                            {{-- Fallback for non-YouTube or direct files --}}
                            <video id="course-html5-player" controls controlsList="nodownload noplaybackrate noremoteplayback" disablePictureInPicture disableRemotePlayback oncontextmenu="return false" class="w-full h-full">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                Browser Anda tidak mendukung tag video.
                            </video>
                        @endif
                    @elseif($currentMaterial['type'] == 'video')
                         <div class="absolute inset-0 flex items-center justify-center text-gray-400 bg-gray-800">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <p>Video tidak tersedia</p>
                            </div>
                        </div>
                    @elseif($currentMaterial['type'] == 'bacaan')
                        <div class="absolute inset-0 p-8 overflow-y-auto bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                             <div class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($currentMaterial['content'])) !!}
                             </div>
                        </div>
                    @elseif($currentMaterial['type'] == 'kuis')
                         <div class="absolute inset-0 flex items-center justify-center text-gray-400 bg-gray-800">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                <p>Ini adalah materi Kuis</p>
                                <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $currentMaterial['id']]) }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Mulai Kuis</a>
                            </div>
                        </div>
                    @else
                         <div class="absolute inset-0 flex items-center justify-center text-gray-400 bg-gray-800">
                            <p>Tipe konten tidak didukung</p>
                        </div>
                    @endif
                @else
                <div class="absolute inset-0 flex items-center justify-center text-gray-400 bg-gray-800">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <p>Pilih materi untuk memulai</p>
                    </div>
                </div>
                @endif

                <div class="pointer-events-none absolute bottom-3 right-3 rounded-md bg-black/45 px-2 py-1 text-[10px] text-white/90 backdrop-blur-sm">
                    Private Course • {{ Auth::guard('mahasiswa')->user()->name ?? 'Mahasiswa' }}
                </div>

                <div id="video-private-overlay" class="absolute inset-0 hidden items-center justify-center bg-black/80 px-6 text-center">
                    <div>
                        <p class="text-lg font-semibold text-white">Sesi video selesai</p>
                        <p class="mt-2 text-sm text-white/80">Player diprivasi setelah video selesai. Buka lagi dari modul jika ingin menonton ulang.</p>
                    </div>
                </div>
            </div>

            @if($currentMaterial && $currentMaterial['type'] == 'video' && count($courseVideoIds) > 1)
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('mahasiswa.course-learn', ['id' => $course->id_course, 'material' => $currentMaterial['id'], 'play' => 'pack']) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition {{ $isPlaylistMode ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h4m-4 6h16" />
                    </svg>
                    Putar 1 Paket Video
                </a>
                <a href="{{ route('mahasiswa.course-learn', ['id' => $course->id_course, 'material' => $currentMaterial['id']]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition {{ !$isPlaylistMode ? 'bg-gray-900 text-white dark:bg-gray-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    Mode Single
                </a>
                <span class="text-xs text-gray-500 dark:text-gray-400">Mode paket memutar video YouTube berurutan otomatis.</span>
            </div>
            @endif
            
            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <form action="{{ route('mahasiswa.favorite.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_course" value="{{ $course->id_course }}">
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-xl font-medium transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        Tambahkan ke Favorit
                    </button>
                </form>
                
                @if($currentMaterial)
                <form action="{{ route('mahasiswa.material.complete', $currentMaterial['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 {{ $currentMaterial['is_completed'] ? 'bg-green-500' : 'bg-blue-500 hover:bg-blue-600' }} text-white rounded-xl font-medium transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Tandai Selesai
                    </button>
                </form>
                @endif
            </div>

            {{-- Certificate Ready --}}
            @if($certificateEligible)
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-700/40 bg-emerald-50 dark:bg-emerald-900/20 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Sertifikat Siap Dicetak</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-0.5">Kursus/webinar sudah selesai dan fitur sertifikat diaktifkan dosen.</p>
                </div>
                <button type="button"
                        onclick="printCourseCertificate(@js($course->nama_course), @js(Auth::guard('mahasiswa')->user()->name ?? 'Mahasiswa'), @js(now()->format('d F Y')))"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7m-9 12h6m-7 0h8a2 2 0 002-2v-5H6v5a2 2 0 002 2zM6 14H4a2 2 0 01-2-2v-3a2 2 0 012-2h16a2 2 0 012 2v3a2 2 0 01-2 2h-2" />
                    </svg>
                    Cetak Sertifikat
                </button>
            </div>
            @endif

            {{-- Dropdown Pre-test / Kuis Modul Ini --}}
            @if($currentMaterial && isset($modules[$currentModuleIndex]) && !empty($modules[$currentModuleIndex]['quiz']))
            <div class="mt-6 border border-gray-200 dark:border-gray-700/50 rounded-2xl overflow-hidden bg-white dark:bg-[#1f2937] shadow-sm">
                <button onclick="togglePretest()" class="w-full flex items-center justify-between p-4 focus:outline-none hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-500/20 flex items-center justify-center flex-shrink-0 text-yellow-600 dark:text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-bold text-gray-800 dark:text-gray-100 text-base">Pre-test / Kuis Modul Ini</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tutup video dan kerjakan pre-test untuk mengevaluasi pemahaman Anda.</p>
                        </div>
                    </div>
                    <svg id="pretest-arrow" class="w-5 h-5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div id="pretest-content" class="hidden border-t border-gray-200 dark:border-gray-700/50 bg-gray-50 dark:bg-gray-900/50 p-6">
                    <div class="flex flex-col md:flex-row gap-6 items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $modules[$currentModuleIndex]['quiz']['title'] ?? 'Pre-test / Kuis Akhir' }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Selesaikan kuis ini untuk memvalidasi pengetahuan Anda tentang materi di modul ini. Durasi pengerjaan: {{ $modules[$currentModuleIndex]['quiz']['duration'] ?? 30 }} menit.</p>
                            
                            @if($modules[$currentModuleIndex]['quiz_completed'] ?? false)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold rounded-full">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Sudah Dikerjakan
                            </span>
                            @endif
                        </div>
                        <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $modules[$currentModuleIndex]['quiz']['id']]) }}" class="flex-shrink-0 px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-xl transition shadow-sm">
                            Mulai Pre-test Sekarang
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    {{-- RIGHT SIDEBAR: Discussion/Notes/Favorites --}}
    <div class="course-sidebar-right w-full lg:w-[280px] lg:min-w-[280px] lg:flex-shrink-0">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden sticky top-24">
            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700/50">
                <button onclick="showTab('diskusi')" class="tab-btn flex-1 py-3 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-500" data-tab="diskusi">Diskusi</button>
                <button onclick="showTab('catatan')" class="tab-btn flex-1 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700" data-tab="catatan">Catatan</button>
                <button onclick="showTab('favorit')" class="tab-btn flex-1 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700" data-tab="favorit">Favorit</button>
            </div>
            
            {{-- Diskusi Tab --}}
            <div id="tab-diskusi" class="tab-content">
                <div id="diskusi-container" class="space-y-5 p-4" style="height: 400px; overflow-y: auto;"></div>

                <div class="border-t border-gray-200 bg-white p-4 dark:border-gray-700/50 dark:bg-[#1f2937]">
                    <textarea
                        id="discussion-input"
                        placeholder="Tulis pertanyaan atau komentar Anda... (Enter untuk kirim)"
                        rows="2"
                        class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 placeholder-gray-400 transition-all focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-[#111827] dark:text-gray-200"></textarea>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-[11px] text-gray-400"><span class="font-semibold">Realtime:</span> Diskusi diperbarui otomatis setiap beberapa detik.</span>
                        <button id="discussion-send-btn" onclick="sendCourseDiscussion()" class="flex items-center gap-2 rounded-lg bg-blue-500 px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-600">
                            Kirim
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Catatan Tab --}}
            <div id="tab-catatan" class="tab-content hidden">
                <div class="flex h-[400px] flex-col overflow-y-auto">
                    @forelse($dosenNotes as $note)
                    <div class="border-b border-yellow-200 bg-yellow-50/50 p-4 dark:border-yellow-900/50 dark:bg-yellow-500/5">
                        <div class="mb-2 flex items-center gap-2">
                            <svg class="h-4 w-4 text-yellow-600 dark:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs font-bold uppercase tracking-widest text-yellow-800 dark:text-yellow-400">{{ $note['title'] }}</span>
                            @if($note['created_at'])
                            <span class="ml-auto text-[11px] text-yellow-700/80 dark:text-yellow-300/70">{{ $note['created_at'] }}</span>
                            @endif
                        </div>
                        <p class="text-sm leading-relaxed text-yellow-800 dark:text-yellow-200/80">{{ $note['content'] }}</p>
                    </div>
                    @empty
                    <div class="p-6 text-center">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Belum ada catatan dari dosen.</p>
                    </div>
                    @endforelse

                    <div class="relative flex flex-1 flex-col p-4">
                        <div class="mb-3 flex items-center justify-between text-sm">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-200">Catatan Pribadi</h4>
                            <span id="personal-note-state" class="text-xs font-medium text-gray-400">Belum Disimpan</span>
                        </div>

                        <textarea
                            id="personal-note-textarea"
                            class="min-h-[220px] flex-1 resize-none rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm leading-relaxed text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-600 dark:bg-[#111827] dark:text-gray-300"
                            placeholder="Ketik catatan pribadi Anda di sini... Catatan ini hanya tersimpan di browser Anda."
                        ></textarea>
                    </div>
                </div>

                <div class="border-t border-gray-200 bg-white p-4 dark:border-gray-700/50 dark:bg-[#1f2937]">
                    <button id="save-personal-note-btn" onclick="savePersonalCourseNote()" class="w-full rounded-lg bg-gray-900 py-2.5 text-sm font-medium text-white transition-all hover:bg-black dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Simpan Catatan
                    </button>
                    <p class="mt-3 text-center text-[11px] text-gray-400">Catatan pribadi disimpan lokal di browser Anda.</p>
                </div>
            </div>
            
            {{-- Favorit Tab --}}
            <div id="tab-favorit" class="tab-content hidden p-4">
                <div class="h-[400px] flex flex-col items-center justify-center text-center px-4">
                    <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-gray-800 dark:text-gray-100 mb-1">Materi Favoritmu</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm">
                        Anda dapat menandai materi-materi penting dalam kursus ini agar lebih mudah dicari nanti.
                    </p>
                    
                    <form action="{{ route('mahasiswa.favorite.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_course" value="{{ $course->id_course }}">
                        <button type="submit" class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800 rounded-lg text-sm font-medium transition-colors">
                            Favoritkan Kursus Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Navigation --}}
<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-[#1f2937] border-t border-gray-200 dark:border-gray-700/50 px-4 sm:px-6 py-3 sm:py-4 z-40">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition text-sm sm:text-base">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="hidden sm:inline">Modul Sebelumnya</span>
            <span class="sm:hidden">Prev</span>
        </button>
        
        <div class="flex-1 max-w-[400px] mx-4 sm:mx-8 hidden sm:block">
            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>
        
        <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition text-sm sm:text-base">
            <span class="hidden sm:inline">Modul Berikutnya</span>
            <span class="sm:hidden">Next</span>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>
    </div>
</div>

<div class="h-20"></div>

@push('scripts')
<script>
    function toggleModule(index) {
        const content = document.getElementById('module-' + index);
        const arrow = document.querySelector('.module-arrow-' + index);
        
        content.classList.toggle('hidden');
        if (arrow) arrow.classList.toggle('rotate-180');
    }
    
    function togglePretest() {
        const content = document.getElementById('pretest-content');
        const arrow = document.getElementById('pretest-arrow');
        
        content.classList.toggle('hidden');
        if (arrow) arrow.classList.toggle('rotate-180');
    }
    
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.dataset.tab === tabName) {
                btn.classList.add('text-blue-600', 'dark:text-blue-400', 'border-blue-500');
                btn.classList.remove('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            } else {
                btn.classList.remove('text-blue-600', 'dark:text-blue-400', 'border-blue-500');
                btn.classList.add('text-gray-500', 'dark:text-gray-400', 'border-transparent');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const playerBox = document.getElementById('video-protected-player');
        if (playerBox) {
            const block = (event) => event.preventDefault();
            playerBox.addEventListener('contextmenu', block);
            playerBox.addEventListener('copy', block);
            playerBox.addEventListener('cut', block);
            playerBox.addEventListener('dragstart', block);
            playerBox.addEventListener('selectstart', block);

            playerBox.addEventListener('keydown', function (event) {
                const key = (event.key || '').toLowerCase();
                if ((event.ctrlKey || event.metaKey) && ['c', 'x', 'u', 's'].includes(key)) {
                    event.preventDefault();
                }
            });
        }

        initCourseDiscussion();
        initPersonalCourseNote();
        initProtectedVideoPlayer();
    });

    const discussionEndpoint = '{{ route('mahasiswa.course-discussions.index', ['courseId' => $course->id_course], false) }}';
    const discussionStoreEndpoint = '{{ route('mahasiswa.course-discussions.store', ['courseId' => $course->id_course], false) }}';
    let discussionPoller = null;
    let discussionComments = [];
    let discussionPendingComments = [];
    let discussionTempSeed = 0;
    const discussionCurrentUser = @json([
        'name' => auth('mahasiswa')->user()?->name ?? 'Mahasiswa',
        'role' => 'Mahasiswa',
        'avatar' => auth('mahasiswa')->user()?->profile?->foto_profile
            ? asset('storage/' . auth('mahasiswa')->user()->profile->foto_profile)
            : 'https://ui-avatars.com/api/?name=' . urlencode(auth('mahasiswa')->user()?->name ?? 'Mahasiswa') . '&background=0D9488&color=fff',
    ]);

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getDiscussionRenderableItems() {
        return [...discussionComments, ...discussionPendingComments];
    }

    function renderDiscussionComments() {
        const container = document.getElementById('diskusi-container');
        if (!container) return;

        const comments = getDiscussionRenderableItems();

        if (!Array.isArray(comments) || comments.length === 0) {
            container.innerHTML = `
                <div class="flex h-full flex-col items-center justify-center space-y-3 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 dark:bg-gray-800">
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Belum ada diskusi</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Mulai percakapan atau tanyakan sesuatu</p>
                    </div>
                </div>`;
            return;
        }

        container.innerHTML = comments.map((comment) => `
            <div class="flex gap-3" data-discussion-id="${escapeHtml(comment.id ?? comment.temp_id ?? '')}">
                <img src="${escapeHtml(comment.avatar)}" alt="${escapeHtml(comment.name)}" class="h-8 w-8 flex-shrink-0 rounded-full border border-gray-200 object-cover dark:border-gray-700">
                <div class="flex-1">
                    <div class="mb-1 flex items-baseline justify-between">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">${escapeHtml(comment.name)}</h4>
                            ${comment.role === 'Pengajar' ? '<span class="rounded bg-blue-100 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Pengajar</span>' : ''}
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500">${escapeHtml(comment.time ?? '')}</span>
                    </div>
                    <div class="rounded-r-xl rounded-bl-xl border ${comment.local_status === 'failed' ? 'border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10' : 'border-gray-100 bg-gray-50 dark:border-gray-700/50 dark:bg-gray-800/60'} p-3">
                        <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">${escapeHtml(comment.text)}</p>
                        ${comment.local_status ? `
                            <div class="mt-2 flex items-center justify-between gap-3 text-[11px]">
                                <span class="${comment.local_status === 'failed' ? 'text-red-500 dark:text-red-300' : 'text-amber-500 dark:text-amber-300'}">
                                    ${comment.local_status === 'failed' ? escapeHtml(comment.error_message || 'Gagal dikirim') : 'Mengirim...'}
                                </span>
                                ${comment.local_status === 'failed' ? `<button type="button" onclick="retryCourseDiscussion('${escapeHtml(comment.temp_id)}')" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200">Coba lagi</button>` : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        container.scrollTop = container.scrollHeight;
    }

    async function fetchCourseDiscussion() {
        const response = await fetch(discussionEndpoint, {
            headers: {
                'Accept': 'application/json',
            },
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal memuat diskusi.');
        }
        discussionComments = Array.isArray(data.data) ? data.data : [];
        renderDiscussionComments();
    }

    function createPendingDiscussionComment(message) {
        discussionTempSeed += 1;

        return {
            temp_id: `discussion-temp-${Date.now()}-${discussionTempSeed}`,
            name: discussionCurrentUser.name,
            role: discussionCurrentUser.role,
            text: message,
            time: 'Baru saja',
            avatar: discussionCurrentUser.avatar,
            local_status: 'sending',
        };
    }

    function upsertPendingDiscussionComment(comment) {
        const existingIndex = discussionPendingComments.findIndex((item) => item.temp_id === comment.temp_id);

        if (existingIndex >= 0) {
            discussionPendingComments[existingIndex] = comment;
        } else {
            discussionPendingComments.push(comment);
        }

        renderDiscussionComments();
    }

    function removePendingDiscussionComment(tempId) {
        discussionPendingComments = discussionPendingComments.filter((item) => item.temp_id !== tempId);
        renderDiscussionComments();
    }

    async function submitCourseDiscussion(message, pendingComment) {
        const response = await fetch(discussionStoreEndpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: (() => {
                const formData = new FormData();
                formData.append('message', message);
                return formData;
            })(),
        });

        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Gagal mengirim diskusi.');
        }

        removePendingDiscussionComment(pendingComment.temp_id);
        discussionComments = [...discussionComments, data.data];
        renderDiscussionComments();
    }

    async function retryCourseDiscussion(tempId) {
        const pendingComment = discussionPendingComments.find((item) => item.temp_id === tempId);
        if (!pendingComment) return;

        pendingComment.local_status = 'sending';
        pendingComment.error_message = null;
        upsertPendingDiscussionComment(pendingComment);

        try {
            await submitCourseDiscussion(pendingComment.text, pendingComment);
        } catch (error) {
            pendingComment.local_status = 'failed';
            pendingComment.error_message = error.message || 'Gagal dikirim';
            upsertPendingDiscussionComment(pendingComment);
        }
    }

    async function sendCourseDiscussion() {
        const input = document.getElementById('discussion-input');
        const button = document.getElementById('discussion-send-btn');
        if (!input || !button) return;

        const message = input.value.trim();
        if (!message) return;

        button.disabled = true;
        const pendingComment = createPendingDiscussionComment(message);
        discussionPendingComments.push(pendingComment);
        renderDiscussionComments();
        input.value = '';

        try {
            await submitCourseDiscussion(message, pendingComment);
        } catch (error) {
            pendingComment.local_status = 'failed';
            pendingComment.error_message = error.message || 'Terjadi kesalahan saat mengirim pesan diskusi.';
            upsertPendingDiscussionComment(pendingComment);
        } finally {
            button.disabled = false;
        }
    }

    function initCourseDiscussion() {
        fetchCourseDiscussion().catch(() => {});
        if (discussionPoller) {
            window.clearInterval(discussionPoller);
        }
        discussionPoller = window.setInterval(() => {
            fetchCourseDiscussion().catch(() => {});
        }, 5000);

        const input = document.getElementById('discussion-input');
        input?.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendCourseDiscussion();
            }
        });
    }

    const personalNoteStorageKey = 'course-note-{{ $course->id_course }}';

    function initPersonalCourseNote() {
        const textarea = document.getElementById('personal-note-textarea');
        const state = document.getElementById('personal-note-state');
        if (!textarea || !state) return;

        textarea.value = localStorage.getItem(personalNoteStorageKey) || '';
        state.textContent = textarea.value.trim() ? 'Tersimpan Lokal' : 'Belum Disimpan';

        textarea.addEventListener('input', function () {
            state.textContent = 'Belum Disimpan';
        });
    }

    function savePersonalCourseNote() {
        const textarea = document.getElementById('personal-note-textarea');
        const state = document.getElementById('personal-note-state');
        if (!textarea || !state) return;

        localStorage.setItem(personalNoteStorageKey, textarea.value);
        state.textContent = 'Tersimpan Lokal';

        Swal.fire({
            icon: 'success',
            title: 'Catatan tersimpan',
            text: 'Catatan pribadi disimpan di browser ini.',
            timer: 1800,
            showConfirmButton: false,
        });
    }

    function completeCurrentMaterialSilently() {
        @if($currentMaterial)
        fetch('{{ route('mahasiswa.material.complete', $currentMaterial['id']) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        }).catch(() => {});
        @endif
    }

    function privatizeVideoPlayer() {
        const overlay = document.getElementById('video-private-overlay');
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
        }
        completeCurrentMaterialSilently();
    }

    function initProtectedVideoPlayer() {
        const html5Player = document.getElementById('course-html5-player');
        if (html5Player) {
            html5Player.addEventListener('ended', privatizeVideoPlayer);
        }

        const ytIframe = document.getElementById('course-youtube-player');
        if (!ytIframe) return;

        const script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(script);

        window.onYouTubeIframeAPIReady = function () {
            const player = new YT.Player('course-youtube-player', {
                events: {
                    onStateChange(event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            try {
                                event.target.stopVideo();
                            } catch (error) {
                                // Ignore stopping failures and still privatize the player.
                            }
                            privatizeVideoPlayer();
                        }
                    }
                }
            });

            window.__currentCourseYoutubePlayer = player;
        };
    }

    function printCourseCertificate(courseTitle, studentName, completedDate) {
        const safeCourse = String(courseTitle || 'Kursus');
        const safeStudent = String(studentName || 'Mahasiswa');
        const safeDate = String(completedDate || '');
        const certNo = 'CERT-' + Date.now();

        const popup = window.open('', '_blank', 'width=1200,height=800');
        if (!popup) return;

        popup.document.write(`
            <html>
            <head>
                <title>Sertifikat ${safeCourse}</title>
                <style>
                    body { margin:0; font-family: Arial, sans-serif; background:#f3f4f6; }
                    .page { width:1123px; height:794px; margin:24px auto; background:#fff; border:14px solid #1d4ed8; box-sizing:border-box; position:relative; }
                    .inner { position:absolute; inset:18px; border:2px solid #93c5fd; padding:56px 72px; text-align:center; }
                    .title { font-size:44px; font-weight:700; color:#1e3a8a; letter-spacing:1px; margin-top:16px; }
                    .subtitle { font-size:18px; color:#475569; margin-top:20px; }
                    .name { font-size:40px; color:#0f172a; font-weight:700; margin:18px 0; }
                    .course { font-size:24px; color:#1d4ed8; font-weight:600; margin:8px 0 22px; }
                    .meta { display:flex; justify-content:space-between; margin-top:46px; color:#334155; font-size:14px; }
                    .line { border-top:1px solid #94a3b8; width:260px; margin:10px auto 6px; }
                    .badge { display:inline-block; font-size:12px; color:#0f172a; background:#e2e8f0; padding:6px 12px; border-radius:999px; margin-top:14px; }
                    @media print { body { background:#fff; } .page { margin:0 auto; } }
                </style>
            </head>
            <body>
                <div class="page">
                    <div class="inner">
                        <div class="title">SERTIFIKAT KELULUSAN</div>
                        <div class="subtitle">Diberikan kepada</div>
                        <div class="name">${safeStudent}</div>
                        <div class="subtitle">atas keberhasilan menyelesaikan</div>
                        <div class="course">${safeCourse}</div>
                        <div class="badge">Nomor Sertifikat: ${certNo}</div>
                        <div class="meta">
                            <div>
                                <div>Tanggal Selesai</div>
                                <div><strong>${safeDate}</strong></div>
                            </div>
                            <div>
                                <div class="line"></div>
                                <div>Pengajar / Platform</div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    window.onload = function() { window.print(); };
                <\/script>
            </body>
            </html>
        `);
        popup.document.close();
    }
</script>
@endpush

{{-- Mobile responsive style override --}}
<style>
    @media (max-width: 1023px) {
        .course-learn-layout {
            flex-direction: column !important;
        }
        .course-sidebar-left,
        .course-sidebar-right,
        .course-content-center {
            width: 100% !important;
            min-width: 100% !important;
        }
    }
</style>

</x-layouts.dashboard>

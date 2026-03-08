<x-layouts.dashboard :active="'courses'">

{{-- Back Link & Title Row --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6 animate-fade-in-up">
    <a href="{{ route('mahasiswa.courses') }}" class="inline-flex items-center gap-2 text-blue-500 hover:text-blue-600 font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Daftar Kursus
    </a>
    
    {{-- Progress Badge --}}
    <div class="flex items-center gap-2 bg-white dark:bg-[#1f2937] px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-700/50">
        <span class="text-sm text-gray-600 dark:text-gray-400">Progress Kursus</span>
        <div class="w-20 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
        </div>
        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $progressPercent }}% selesai</span>
    </div>
</div>

{{-- Main 3-Column Layout --}}
<div class="course-learn-layout" style="display: flex; flex-direction: row; gap: 1.5rem; flex-wrap: nowrap;">
    
    {{-- LEFT SIDEBAR: Module List --}}
    <div class="course-sidebar-left" style="width: 250px; min-width: 250px; flex-shrink: 0;">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-4 sticky top-24">
            <h2 class="font-bold text-gray-800 dark:text-gray-100 mb-2">Modul Pembelajaran</h2>
            
            {{-- Progress Bar --}}
            <div class="flex items-center gap-2 mb-4">
                <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $progressPercent }}%"></div>
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Progress Keseluruhan: {{ $progressPercent }}%</p>
            
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
                        
                        {{-- Quiz Link --}}
                        @if(!empty($module['quiz']))
                        <a href="{{ route('mahasiswa.course-quiz', ['courseId' => $course->id_course, 'quizId' => $module['quiz']['id']]) }}" 
                           class="flex items-center gap-3 p-3 hover:bg-yellow-50 dark:hover:bg-yellow-500/10 transition bg-yellow-50/50 dark:bg-yellow-500/5 border-t border-yellow-200 dark:border-yellow-700/30">
                            <div class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-yellow-700 dark:text-yellow-400">{{ $module['quiz']['title'] ?? 'Kuis Akhir Modul' }}</p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-500">Durasi: {{ $module['quiz']['duration'] ?? 30 }} menit</p>
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
                            <div class="w-6 h-6 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-orange-700 dark:text-orange-400">{{ $module['assignment']['title'] ?? 'Tugas Akhir Modul' }}</p>
                                <p class="text-xs text-orange-600 dark:text-orange-500">Kumpulkan tugas modul ini</p>
                            </div>
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endif
                        
                        {{-- Feedback & Nilai Link - Only show if module is complete (materials, quiz, assignment) --}}
                        @php
                            $completedMaterials = collect($module['materials'])->where('is_completed', true)->count();
                            $totalMaterials = count($module['materials']);
                            $materialsComplete = $totalMaterials > 0 && $completedMaterials == $totalMaterials;
                            $hasQuiz = !empty($module['quiz']);
                            $hasAssignment = !empty($module['assignment']);
                            $quizComplete = !$hasQuiz || ($module['quiz_completed'] ?? false);
                            $assignmentComplete = !$hasAssignment || ($module['assignment_completed'] ?? false);
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
    <div class="course-content-center" style="flex: 1; min-width: 0;">
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
            <div class="bg-gray-900 rounded-2xl overflow-hidden relative" style="aspect-ratio: 16/9;">
                @if($currentMaterial)
                    @if($currentMaterial['type'] == 'video' && !empty($currentMaterial['video_url']))
                        @php
                            $videoUrl = $currentMaterial['video_url'];
                            $embedUrl = '';
                            if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
                                parse_str(parse_url($videoUrl, PHP_URL_QUERY), $params);
                                $videoId = $params['v'] ?? '';
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            } elseif (str_contains($videoUrl, 'youtu.be/')) {
                                $videoId = basename(parse_url($videoUrl, PHP_URL_PATH));
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            }
                        @endphp

                        @if($embedUrl)
                            <iframe src="{{ $embedUrl }}" title="Video Player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                        @else
                            {{-- Fallback for non-YouTube or direct files --}}
                            <video controls class="w-full h-full">
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
            </div>
            
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
    <div class="course-sidebar-right" style="width: 280px; min-width: 280px; flex-shrink: 0;">
        <div class="bg-white dark:bg-[#1f2937] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden sticky top-24">
            {{-- Tabs --}}
            <div class="flex border-b border-gray-200 dark:border-gray-700/50">
                <button onclick="showTab('diskusi')" class="tab-btn flex-1 py-3 text-sm font-medium text-blue-600 dark:text-blue-400 border-b-2 border-blue-500" data-tab="diskusi">Diskusi</button>
                <button onclick="showTab('catatan')" class="tab-btn flex-1 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700" data-tab="catatan">Catatan</button>
                <button onclick="showTab('favorit')" class="tab-btn flex-1 py-3 text-sm font-medium text-gray-500 dark:text-gray-400 border-b-2 border-transparent hover:text-gray-700" data-tab="favorit">Favorit</button>
            </div>
            
            {{-- Diskusi Tab --}}
            <div id="tab-diskusi" class="tab-content" x-data="{
                newComment: '',
                comments: [
                    { id: 1, name: 'Budi Santoso', role: 'Mahasiswa', text: 'Permisi Pak/Bu, saya kurang paham di menit ke 4:20 mengenai variabel scope. Apakah variabel di dalam function tidak bisa diakses dari luar sama sekali?', time: '2 jam yang lalu', avatar: 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random' },
                    { id: 2, name: 'Dosen DigiKampus', role: 'Pengajar', text: 'Benar sekali Budi. Variabel yang dideklarasikan di dalam fungsi (local scope) hanya hidup selama fungsi tersebut dieksekusi. Ia tidak bisa diakses dari luar fungsi tersebut secara langsung.', time: '1 jam yang lalu', avatar: 'https://ui-avatars.com/api/?name=Dosen+DigiKampus&background=4F46E5&color=fff' }
                ],
                postComment() {
                    if(this.newComment.trim() === '') return;
                    this.comments.push({
                        id: Date.now(),
                        name: 'Anda (Mahasiswa)',
                        role: 'Mahasiswa',
                        text: this.newComment,
                        time: 'Baru saja',
                        avatar: 'https://ui-avatars.com/api/?name=Mahasiswa&background=0D9488&color=fff'
                    });
                    this.newComment = '';
                    // Scroll to bottom
                    setTimeout(() => {
                        const container = document.getElementById('diskusi-container');
                        container.scrollTop = container.scrollHeight;
                    }, 50);
                }
            }">
                {{-- Messages Container --}}
                <div id="diskusi-container" class="p-4 space-y-5" style="height: 400px; overflow-y: auto;">
                    <template x-for="comment in comments" :key="comment.id">
                        <div class="flex gap-3">
                            <img :src="comment.avatar" :alt="comment.name" class="w-8 h-8 rounded-full flex-shrink-0 object-cover border border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <div class="flex items-baseline justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100" x-text="comment.name"></h4>
                                        <span x-show="comment.role === 'Pengajar'" class="px-1.5 py-0.5 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-[10px] font-bold rounded">Pengajar</span>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500" x-text="comment.time"></span>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/60 rounded-r-xl rounded-bl-xl p-3 border border-gray-100 dark:border-gray-700/50">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed" x-text="comment.text"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="comments.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-center space-y-3">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Belum ada diskusi</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Mulai percakapan atau tanyakan sesuatu</p>
                            </div>
                        </div>
                    </template>
                </div>
                
                {{-- Comment Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700/50 bg-white dark:bg-[#1f2937]">
                    <textarea 
                        x-model="newComment"
                        @keydown.enter.prevent="postComment()"
                        placeholder="Tulis pertanyaan atau komentar Anda... (Enter untuk kirim)" 
                        rows="2" 
                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-[#111827] text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm resize-none transition-all"></textarea>
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-[11px] text-gray-400"><span class="font-semibold">Bantuan:</span> Tekan Enter untuk mengirim</span>
                        <button 
                            @click="postComment()" 
                            :disabled="newComment.trim() === ''"
                            class="px-5 py-2 bg-blue-500 hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg font-medium text-sm transition-colors flex items-center gap-2">
                            Kirim
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Catatan Tab --}}
            <div id="tab-catatan" class="tab-content hidden" x-data="{
                saving: false,
                isSaved: true,
                noteText: 'Mempelajari cara kerja React hooks.\n- useState: menyimpan data.\n- useEffect: menjalankan *side effect* saat nilai berubah.',
                instructorNote: 'Perhatikan baik-baik di bagian useEffect dependencies agar tidak terjadi infinite loop rendering.',
                
                saveNote() {
                    if (this.noteText.trim() === '') return;
                    this.saving = true;
                    this.isSaved = false;
                    
                    // Simulate API Call delay
                    setTimeout(() => {
                        this.saving = false;
                        this.isSaved = true;
                    }, 800);
                }
            }">
                <div class="h-[400px] overflow-y-auto w-full flex flex-col">
                    
                    {{-- Dosen Pinned Note (If Exists) --}}
                    <template x-if="instructorNote">
                        <div class="p-4 border-b border-yellow-200 dark:border-yellow-900/50 bg-yellow-50/50 dark:bg-yellow-500/5">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs font-bold text-yellow-800 dark:text-yellow-400 uppercase tracking-widest">Catatan Dosen</span>
                            </div>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200/80 leading-relaxed italic" x-text="instructorNote"></p>
                        </div>
                    </template>
                    
                    {{-- Personal Notes Editor --}}
                    <div class="p-4 flex-1 flex flex-col relative">
                        <div class="flex items-center justify-between mb-3 text-sm">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-200">Catatan Pribadi</h4>
                            
                            {{-- Save State Indicators --}}
                            <div class="flex items-center gap-1.5 text-xs font-medium">
                                <span x-show="saving" x-transition class="text-blue-500 flex items-center gap-1">
                                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                                <span x-show="isSaved && !saving" x-transition class="text-green-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Tersimpan
                                </span>
                                <span x-show="!isSaved && !saving" x-transition class="text-gray-400">Belum Disimpan</span>
                            </div>
                        </div>

                        <textarea 
                            x-model="noteText"
                            @input="isSaved = false"
                            class="w-full h-full flex-1 min-h-[220px] bg-transparent border-0 resize-none text-sm text-gray-700 dark:text-gray-300 leading-relaxed focus:ring-0 p-0 placeholder-gray-400"
                            placeholder="Ketik catatan pribadi Anda di sini... Catatan ini hanya bisa dilihat oleh Anda."
                        ></textarea>
                    </div>
                </div>
                
                {{-- Floating Action Bar --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700/50 bg-white dark:bg-[#1f2937]">
                    <button 
                        @click="saveNote()" 
                        :disabled="saving || isSaved || noteText.trim() === ''"
                        class="w-full py-2.5 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-100 dark:text-gray-900 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 text-white disabled:opacity-50 disabled:cursor-not-allowed rounded-lg font-medium text-sm transition-all"
                        >
                        <span x-text="saving ? 'Menyimpan...' : 'Simpan Catatan'"></span>
                    </button>
                    <p class="text-[11px] text-gray-400 text-center mt-3">Teks mendukung Markdown sederhana.</p>
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
<div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-[#1f2937] border-t border-gray-200 dark:border-gray-700/50 px-6 py-4 z-40">
    <div style="max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between;">
        <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Modul Sebelumnya
        </button>
        
        <div style="flex: 1; max-width: 400px; margin: 0 2rem;">
            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>
        
        <button class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-blue-500 transition">
            Modul Berikutnya
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>
    </div>
</div>

<div style="height: 80px;"></div>

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
</script>
@endpush

{{-- Mobile responsive style override --}}
<style>
    @media (max-width: 1024px) {
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

<x-layouts.dashboard :active="$active ?? 'learning-goals'">
    @php
        $summary = $summary ?? [
            'total_enrollments' => 0,
            'courses_running' => 0,
            'courses_completed' => 0,
            'overall_progress_avg' => 0,
            'overall_quiz_score_avg' => null,
            'total_competencies_achieved' => 0,
            'earned_certificates_count' => 0,
            'total_quizzes_taken' => 0,
            'total_assignments_submitted' => 0,
        ];
        $courseGoals = $courseGoals ?? collect();
        $quizAttempts = $quizAttempts ?? collect();
        $assignmentSubmissions = $assignmentSubmissions ?? collect();
        $totalEnrollments = (int) ($totalEnrollments ?? 0);
        $isEmptyState = $courseGoals->isEmpty();

        $statusBadgeMeta = [
            'selesai' => [
                'label' => 'Selesai',
                'bgClass' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                'dotClass' => 'bg-emerald-500',
            ],
            'sedang_berjalan' => [
                'label' => 'Sedang Berjalan',
                'bgClass' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
                'dotClass' => 'bg-blue-500',
            ],
            'belum_mulai' => [
                'label' => 'Belum Mulai',
                'bgClass' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/60 dark:text-gray-300',
                'dotClass' => 'bg-gray-400',
            ],
        ];
    @endphp

    <div class="relative space-y-6 sm:space-y-8 animate-fade-in" x-data="{ activeTab: 'goals' }">

        {{-- Header Section --}}
        <header class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-indigo-600 shadow-xs ring-1 ring-indigo-100 backdrop-blur dark:bg-gray-800/60 dark:text-indigo-300 dark:ring-indigo-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    </span>
                    Dashboard Capaian Akademik
                </span>
                <h1 class="text-2xl font-bold leading-tight text-gray-900 dark:text-white sm:text-3xl">
                    Capaian Pembelajaran & Learning Goals
                </h1>
                <p class="max-w-3xl text-sm leading-relaxed text-gray-600 dark:text-gray-300 sm:text-base">
                    Pantau progress belajar, rata-rata nilai kuis, pengumpulan tugas, kompetensi, dan sertifikat yang Anda raih dari seluruh aktivitas pembelajaran nyata di DigiKampus.
                </p>
            </div>

            <div class="flex items-center gap-3 self-start sm:self-auto">
                <a href="{{ route('mahasiswa.get-courses') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Jelajah Kursus Baru
                </a>
            </div>
        </header>

        {{-- 5 Summary Metric Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
            
            {{-- Card 1: Course Berjalan --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 shadow-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">Course Berjalan</span>
                    <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['courses_running'] }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Sedang Anda ikuti</p>
            </div>

            {{-- Card 2: Course Selesai --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 shadow-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Course Selesai</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['courses_completed'] }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Tuntas 100%</p>
            </div>

            {{-- Card 3: Rata-Rata Progress --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 shadow-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">Rata-Rata Progress</span>
                    <span class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h6m0 0v6m0-6L10 17"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['overall_progress_avg'] }}%</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Penyelesaian materi</p>
            </div>

            {{-- Card 4: Rata-Rata Nilai Kuis --}}
            <div class="p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 shadow-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400">Rata-Rata Nilai</span>
                    <span class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.443a1 1 0 00-.364 1.118l1.286 3.957c.3.922-.755 1.688-1.54 1.118l-3.362-2.443a1 1 0 00-1.176 0l-3.362 2.443c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.07 8.384c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.287-3.957z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $summary['overall_quiz_score_avg'] !== null ? $summary['overall_quiz_score_avg'] . '%' : '-' }}
                </p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">Dari {{ $summary['total_quizzes_taken'] }} kuis</p>
            </div>

            {{-- Card 5: Sertifikat & Kompetensi --}}
            <div class="col-span-2 sm:col-span-1 p-4 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/50 shadow-xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-rose-600 dark:text-rose-400">Sertifikat & Skill</span>
                    <span class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['earned_certificates_count'] }} <span class="text-xs font-normal text-gray-500">Sertifikat</span></p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">{{ $summary['total_competencies_achieved'] }} target kompetensi</p>
            </div>

        </div>

        {{-- Interactive Tab Navigation --}}
        <div class="flex border-b border-gray-200 dark:border-gray-700/60 overflow-x-auto space-x-1 sm:space-x-4">
            <button @click="activeTab = 'goals'" :class="activeTab === 'goals' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                🎯 Target Kompetensi & Course Goals
            </button>
            <button @click="activeTab = 'quizzes'" :class="activeTab === 'quizzes' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                📊 Evaluasi & Nilai Kuis ({{ $quizAttempts->count() }})
            </button>
            <button @click="activeTab = 'assignments'" :class="activeTab === 'assignments' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                📝 Pengumpulan Tugas ({{ $assignmentSubmissions->count() }})
            </button>
            <button @click="activeTab = 'certificates'" :class="activeTab === 'certificates' ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400 font-bold' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 font-medium'" class="py-3 px-4 text-sm border-b-2 transition whitespace-nowrap flex items-center gap-2">
                🎓 Sertifikat Kelulusan ({{ $summary['earned_certificates_count'] }})
            </button>
        </div>

        {{-- TAB 1: Target Kompetensi & Course Goals --}}
        <div x-show="activeTab === 'goals'" class="space-y-6">
            @if ($isEmptyState)
                <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center bg-white dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada course aktif</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Daftar ke kursus baru untuk mulai mengumpulkan poin capaian pembelajaran.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($courseGoals as $row)
                        @php
                            $course = $row['course'];
                            $progressPercent = $row['progress_percent'];
                            $achieveCount = $row['achieved_count'];
                            $totalG = $row['total_goals'];
                            $allAch = $row['all_achieved'];
                            $badgeMeta = $statusBadgeMeta[$row['status_badge']] ?? $statusBadgeMeta['sedang_berjalan'];
                            $detailRoute = $row['is_bootcamp'] ? 'mahasiswa.bootcamp-detail' : 'mahasiswa.course-detail';
                            $learnRoute = $row['is_bootcamp'] ? 'mahasiswa.bootcamp-learn' : 'mahasiswa.course-learn';
                        @endphp
                        <article class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 sm:p-6 shadow-xs space-y-4">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-xs">
                                        {{ \Str::upper(\Str::substr($course->nama_course ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                {{ $row['is_bootcamp'] ? 'Bootcamp' : 'Kursus' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $badgeMeta['bgClass'] }} text-[11px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $badgeMeta['dotClass'] }}"></span>
                                                {{ $badgeMeta['label'] }}
                                            </span>
                                            @if($row['avg_quiz_score'] !== null)
                                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">
                                                    ⭐ Rata-Rata Kuis: {{ $row['avg_quiz_score'] }}%
                                                </span>
                                            @endif
                                        </div>
                                        <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-snug">
                                            {{ $course->nama_course }}
                                        </h2>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-auto">
                                    <a href="{{ route($detailRoute, $course->id_course) }}" class="px-3.5 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-700 dark:text-gray-200 text-xs font-semibold transition">
                                        Detail
                                    </a>
                                    @if ($progressPercent < 100)
                                        <a href="{{ route($learnRoute, $course->id_course) }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition">
                                            Lanjut Belajar
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-gray-600 dark:text-gray-300">Progres Pembelajaran</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $progressPercent }}% Selesai</span>
                                </div>
                                <div class="w-full h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            {{-- Goals & Outcomes List --}}
                            @if($totalG > 0)
                                <div class="pt-2 space-y-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Target Kompetensi & Skill:</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach ($row['goals'] as $goalIndex => $goal)
                                            <div class="flex items-start gap-2.5 p-3 rounded-xl border {{ $goal['is_achieved'] ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-800/40 dark:bg-emerald-950/20' : 'border-gray-100 bg-gray-50/50 dark:border-gray-700/50 dark:bg-gray-800/50' }}">
                                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5 {{ $goal['is_achieved'] ? 'bg-emerald-500 text-white' : 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                                                    {{ $goal['is_achieved'] ? '✓' : ($goalIndex + 1) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold {{ $goal['is_achieved'] ? 'text-emerald-800 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300' }} truncate">{{ $goal['judul'] }}</p>
                                                    @if(!empty($goal['deskripsi']))
                                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $goal['deskripsi'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB 2: Evaluasi & Nilai Kuis --}}
        <div x-show="activeTab === 'quizzes'" class="space-y-4">
            @forelse($quizAttempts as $attempt)
                @php
                    $isPassed = (float) $attempt->persentase >= 70;
                @endphp
                <div class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-5 shadow-xs flex items-center justify-between gap-4">
                    <div class="space-y-1 min-w-0">
                        <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 truncate block">{{ $attempt->quiz?->course?->nama_course ?? 'Kuis Perkuliahan' }}</span>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ $attempt->quiz?->judul ?? 'Kuis Evaluasi' }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            📅 {{ $attempt->created_at->translatedFormat('d F Y • H:i') }} WIB
                        </p>
                    </div>

                    <div class="text-right flex-shrink-0">
                        <div class="text-xl sm:text-2xl font-black {{ $isPassed ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $attempt->persentase }}%
                        </div>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $isPassed ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' }}">
                            {{ $isPassed ? 'Lulus Kuis' : 'Belum Lulus' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center bg-white dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada riwayat kuis</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nilai kuis yang Anda ikuti akan muncul secara otomatis di sini.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB 3: Pengumpulan Tugas --}}
        <div x-show="activeTab === 'assignments'" class="space-y-4">
            @forelse($assignmentSubmissions as $submission)
                <div class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-4 sm:p-5 shadow-xs space-y-2">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 truncate block">{{ $submission->course?->nama_course ?? 'Tugas Perkuliahan' }}</span>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $submission->material?->judul ?? 'Pengumpulan Tugas' }}</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $submission->status === 'reviewed' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' }}">
                            {{ $submission->status === 'reviewed' ? 'Dinilai / Direview' : 'Terkirim' }}
                        </span>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        📁 File: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $submission->original_file_name ?? 'Dokumen Tugas' }}</span> • 
                        ⏱️ Dikirim {{ $submission->submitted_at ? $submission->submitted_at->translatedFormat('d F Y • H:i') : $submission->created_at->diffForHumans() }} WIB
                    </p>

                    @if(!empty($submission->catatan_dosen))
                        <div class="mt-2 p-3 rounded-xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-800/40 text-xs text-gray-700 dark:text-gray-200">
                            <span class="font-bold text-blue-600 dark:text-blue-400">Catatan Pengajar:</span> {{ $submission->catatan_dosen }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center bg-white dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada tugas disubmit</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tugas yang Anda kumpulkan akan tercatat di sini.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB 4: Sertifikat Kelulusan --}}
        <div x-show="activeTab === 'certificates'" class="space-y-4">
            @php
                $completedCourses = $courseGoals->where('status_badge', 'selesai');
            @endphp
            @forelse($completedCourses as $item)
                @php $course = $item['course']; @endphp
                <div class="w-full bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700/50 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white font-bold text-xl shadow-xs">
                            🏆
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $course->nama_course }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Sertifikat Kelulusan Resmi DigiKampus</p>
                        </div>
                    </div>

                    <a href="{{ route('mahasiswa.course-detail', $course->id_course) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold shadow-md shadow-amber-500/20 transition">
                        🎓 Lihat / Download Sertifikat
                    </a>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center bg-white dark:bg-gray-800">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada sertifikat terbit</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selesaikan 100% materi & kuis pada kursus Anda untuk meraih sertifikat kelulusan.</p>
                </div>
            @endforelse
        </div>

    </div>
</x-layouts.dashboard>

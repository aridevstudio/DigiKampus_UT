<x-layouts.dashboard :active="$active ?? 'learning-goals'">
    @php
        $summary = $summary ?? [
            'courses_count' => 0,
            'total_goals' => 0,
            'total_achieved' => 0,
            'fully_achieved_courses' => 0,
            'in_progress_courses' => 0,
            'achievement_rate' => 0,
        ];
        $courseGoals = $courseGoals ?? collect();
        $totalEnrollments = (int) ($totalEnrollments ?? 0);
        $isEmptyState = $courseGoals->isEmpty();

        $statusBadgeMeta = [
            'selesai' => [
                'label' => 'Selesai',
                'bgClass' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300',
                'dotClass' => 'bg-emerald-500',
                'pulseClass' => 'bg-emerald-400 opacity-60',
            ],
            'sedang_berjalan' => [
                'label' => 'Sedang Berjalan',
                'bgClass' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300',
                'dotClass' => 'bg-blue-500',
                'pulseClass' => 'bg-blue-400 opacity-60',
            ],
            'belum_mulai' => [
                'label' => 'Belum Mulai',
                'bgClass' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/60 dark:text-gray-300',
                'dotClass' => 'bg-gray-400',
                'pulseClass' => 'bg-gray-300 opacity-50',
            ],
        ];

        $goalProgressLabel = function (int $achieved, int $total) {
            if ($total <= 0) {
                return 'Belum ada tujuan';
            }
            return "{$achieved}/{$total} tujuan tercapai";
        };
    @endphp

    <div class="relative space-y-6 sm:space-y-8 animate-fade-in-up">

        {{-- Decorative background glow --}}
        <div aria-hidden="true" class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-br from-blue-300/30 via-indigo-300/20 to-purple-300/30 blur-3xl dark:from-blue-500/20 dark:via-indigo-500/15 dark:to-purple-500/20"></div>
        <div aria-hidden="true" class="pointer-events-none absolute top-40 -left-20 h-64 w-64 rounded-full bg-gradient-to-br from-purple-300/25 via-pink-200/20 to-blue-300/25 blur-3xl dark:from-purple-500/15 dark:via-pink-500/10 dark:to-blue-500/15"></div>

        {{-- Header --}}
        <header class="relative flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-indigo-600 shadow-sm ring-1 ring-indigo-100 backdrop-blur dark:bg-gray-800/60 dark:text-indigo-300 dark:ring-indigo-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-indigo-400 opacity-60"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
                    </span>
                    Dashboard Mahasiswa
                </span>
                <h1 class="text-2xl font-bold leading-tight text-gray-900 dark:text-white sm:text-3xl">
                    Learning Goals
                </h1>
                <p class="max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-300 sm:text-base">
                    Pantau pencapaian tujuan pembelajaran Anda di setiap kursus yang sedang Anda ikuti. Selesaikan kursus untuk membuka pencapaian.
                </p>
            </div>

            @unless ($isEmptyState)
            <div class="relative hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                <div class="inline-flex items-center gap-3 rounded-2xl border border-white/70 bg-white/85 px-4 py-2.5 shadow-sm backdrop-blur dark:border-gray-700/60 dark:bg-gray-800/80">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/30">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">Tingkat Pencapaian</p>
                        <p class="text-base font-bold leading-tight text-gray-900 dark:text-white sm:text-lg">{{ $summary['achievement_rate'] }}%</p>
                    </div>
                </div>
            </div>
            @endunless
        </header>

        @if ($isEmptyState && $totalEnrollments === 0)
            {{-- Big empty state: no enrollments at all --}}
            <div class="relative overflow-hidden rounded-[28px] border border-dashed border-indigo-200/80 bg-gradient-to-br from-blue-50 via-white to-purple-50 p-8 dark:border-indigo-700/40 dark:from-indigo-950/30 dark:via-gray-900 dark:to-purple-950/30 sm:p-12">
                <div aria-hidden="true" class="pointer-events-none absolute -right-16 -top-16 h-48 w-48 rounded-full bg-gradient-to-br from-blue-300/40 to-purple-300/40 blur-3xl dark:from-blue-500/20 dark:to-purple-500/20"></div>
                <div class="relative mx-auto flex max-w-xl flex-col items-center text-center">
                    <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-xl shadow-indigo-500/30 sm:h-24 sm:w-24">
                        <svg class="h-10 w-10 text-white sm:h-12 sm:w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl">Belum Ada Kursus Terdaftar</h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 sm:text-base">
                        Anda belum terdaftar di kursus apapun. Mulai dengan memilih kursus yang menyediakan tujuan pembelajaran terstruktur.
                    </p>
                    <a href="{{ route('mahasiswa.get-courses') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 sm:text-base">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                        </svg>
                        Jelajahi Katalog Kursus
                    </a>
                </div>
            </div>
        @elseif ($isEmptyState)
            {{-- Soft empty state: enrollments exist but no course has published goals --}}
            <div class="relative overflow-hidden rounded-[28px] border border-blue-200/80 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 dark:border-blue-700/40 dark:from-blue-950/30 dark:via-gray-900 dark:to-indigo-950/30 sm:p-10">
                <div aria-hidden="true" class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-200/40 blur-3xl dark:bg-blue-500/15"></div>
                <div class="relative flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                    <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30 sm:h-16 sm:w-16">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white sm:text-xl">Tujuan Pembelajaran Belum Tersedia</h2>
                        <p class="mt-1 text-sm leading-relaxed text-gray-600 dark:text-gray-300 sm:text-base">
                            Anda terdaftar di {{ $totalEnrollments }} kursus, namun belum ada yang mempublikasikan tujuan pembelajaran terstruktur. Silakan cek kembali nanti atau hubungi dosen pengampu.
                        </p>
                    </div>
                    <a href="{{ route('mahasiswa.courses') }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 transition hover:bg-blue-50 dark:bg-gray-800 dark:text-blue-300 dark:ring-blue-700/40 dark:hover:bg-gray-700">
                        Lihat Kursus Saya
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        @else
            {{-- Stat cards grid --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
                {{-- Card 1: Total Tujuan Pembelajaran --}}
                <div class="group relative overflow-hidden rounded-2xl border border-white/80 bg-white/85 p-5 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-700/60 dark:bg-gray-800/80 sm:p-6">
                    <div aria-hidden="true" class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-gradient-to-br from-blue-200/40 to-indigo-200/40 blur-2xl transition group-hover:from-blue-300/50 group-hover:to-indigo-300/50 dark:from-blue-500/15 dark:to-indigo-500/15"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-blue-600 dark:text-blue-400 sm:text-[11px]">Total Tujuan</p>
                            <p class="mt-1 text-2xl font-bold leading-tight text-gray-900 dark:text-white sm:text-3xl">{{ $summary['total_goals'] }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tujuan pembelajaran terdaftar</p>
                        </div>
                    </div>
                    <div class="relative mt-4 flex items-center gap-2 text-xs text-blue-600 dark:text-blue-300">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Across {{ $summary['courses_count'] }} kursus
                    </div>
                </div>

                {{-- Card 2: Tujuan Tercapai with progress ring --}}
                <div class="group relative overflow-hidden rounded-2xl border border-white/80 bg-white/85 p-5 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-700/60 dark:bg-gray-800/80 sm:p-6">
                    <div aria-hidden="true" class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-gradient-to-br from-emerald-200/40 to-teal-200/40 blur-2xl transition group-hover:from-emerald-300/50 group-hover:to-teal-300/50 dark:from-emerald-500/15 dark:to-teal-500/15"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/30">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-emerald-600 dark:text-emerald-400 sm:text-[11px]">Tujuan Tercapai</p>
                            <div class="mt-1 flex items-baseline gap-1.5">
                                <p class="text-2xl font-bold leading-tight text-gray-900 dark:text-white sm:text-3xl">{{ $summary['total_achieved'] }}</p>
                                <p class="text-base font-semibold text-gray-400 dark:text-gray-500 sm:text-lg">/ {{ $summary['total_goals'] }}</p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sudah tercapai sejauh ini</p>
                        </div>
                    </div>
                    <div class="relative mt-4 h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700/60">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all" style="width: {{ $summary['achievement_rate'] }}%"></div>
                    </div>
                    <p class="relative mt-2 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ $summary['achievement_rate'] }}% dari total tujuan</p>
                </div>

                {{-- Card 3: Kursus Selesai --}}
                <div class="group relative overflow-hidden rounded-2xl border border-white/80 bg-white/85 p-5 shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:shadow-lg dark:border-gray-700/60 dark:bg-gray-800/80 sm:p-6">
                    <div aria-hidden="true" class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-gradient-to-br from-purple-200/40 to-pink-200/40 blur-2xl transition group-hover:from-purple-300/50 group-hover:to-pink-300/50 dark:from-purple-500/15 dark:to-pink-500/15"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 text-white shadow-lg shadow-purple-500/30">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.156c.969 0 1.371 1.24.588 1.81l-3.363 2.443a1 1 0 00-.364 1.118l1.286 3.957c.3.922-.755 1.688-1.54 1.118l-3.362-2.443a1 1 0 00-1.176 0l-3.362 2.443c-.784.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.364-1.118L2.07 8.384c-.783-.57-.38-1.81.588-1.81h4.156a1 1 0 00.95-.69l1.287-3.957z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-purple-600 dark:text-purple-400 sm:text-[11px]">Kursus Selesai</p>
                            <div class="mt-1 flex items-baseline gap-1.5">
                                <p class="text-2xl font-bold leading-tight text-gray-900 dark:text-white sm:text-3xl">{{ $summary['fully_achieved_courses'] }}</p>
                                <p class="text-base font-semibold text-gray-400 dark:text-gray-500 sm:text-lg">/ {{ $summary['courses_count'] }}</p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Semua tujuan tercapai</p>
                        </div>
                    </div>
                    <div class="relative mt-4 flex items-center gap-2 text-xs">
                        @if ($summary['in_progress_courses'] > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h6m0 0v6m0-6L10 17"/></svg>
                            {{ $summary['in_progress_courses'] }} sedang berjalan
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-300">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Semua kursus tuntas!
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Per-course sections --}}
            <div class="space-y-5 sm:space-y-6">
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
                        $badgeTone = $allAch
                            ? 'emerald'
                            : ($progressPercent === 0 ? 'gray' : 'blue');
                    @endphp
                    <article class="relative overflow-hidden rounded-[24px] border {{ $allAch ? 'border-emerald-200/80 dark:border-emerald-700/40' : 'border-blue-200/80 dark:border-blue-700/40' }} bg-white/90 shadow-sm backdrop-blur dark:bg-gray-900/70">
                        {{-- Decorative gradient blobs --}}
                        <div aria-hidden="true" class="pointer-events-none absolute -right-12 -top-12 h-40 w-40 rounded-full {{ $allAch ? 'bg-gradient-to-br from-emerald-200/40 to-teal-200/40 dark:from-emerald-500/15 dark:to-teal-500/10' : 'bg-gradient-to-br from-blue-200/40 to-indigo-200/40 dark:from-blue-500/15 dark:to-indigo-500/10' }} blur-3xl"></div>
                        <div aria-hidden="true" class="pointer-events-none absolute -left-10 bottom-0 h-32 w-32 rounded-full {{ $allAch ? 'bg-gradient-to-br from-teal-200/40 to-emerald-200/40 dark:from-teal-500/10 dark:to-emerald-500/10' : 'bg-gradient-to-br from-indigo-200/40 to-purple-200/40 dark:from-indigo-500/10 dark:to-purple-500/10' }} blur-3xl"></div>

                        <div class="relative space-y-4 p-4 sm:p-6">
                            {{-- Course header --}}
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl {{ $allAch ? 'bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30' : 'bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/30' }} text-white sm:h-14 sm:w-14">
                                        @if ($row['is_bootcamp'])
                                            <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        @else
                                            <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="mb-1 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-full bg-white/70 px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800/60 dark:text-gray-300 dark:ring-gray-700">
                                                {{ $row['is_bootcamp'] ? 'Bootcamp' : 'Kursus' }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 rounded-full {{ $badgeMeta['bgClass'] }} px-2.5 py-0.5 text-[11px] font-semibold">
                                                <span class="relative flex h-1.5 w-1.5">
                                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $badgeMeta['pulseClass'] }}"></span>
                                                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $badgeMeta['dotClass'] }}"></span>
                                                </span>
                                                {{ $badgeMeta['label'] }}
                                            </span>
                                        </div>
                                        <h2 class="text-base font-bold leading-snug text-gray-900 dark:text-white sm:text-lg">
                                            {{ $course->nama_course }}
                                        </h2>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-start sm:self-auto">
                                    <a href="{{ route($detailRoute, $course->id_course) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm ring-1 ring-blue-200 transition hover:bg-blue-50 dark:bg-gray-800 dark:text-blue-300 dark:ring-blue-700/40 dark:hover:bg-gray-700">
                                        Detail
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                    @if ($progressPercent < 100)
                                    <a href="{{ route($learnRoute, $course->id_course) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm shadow-indigo-500/30 transition hover:from-blue-700 hover:to-indigo-700">
                                        Lanjut Belajar
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress bar --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium {{ $allAch ? 'text-emerald-700 dark:text-emerald-300' : 'text-blue-700 dark:text-blue-300' }}">
                                        {{ $goalProgressLabel($achieveCount, $totalG) }}
                                    </span>
                                    <span class="font-semibold text-gray-600 dark:text-gray-400">{{ $progressPercent }}% selesai</span>
                                </div>
                                <div class="relative h-2 overflow-hidden rounded-full bg-white/70 shadow-inner dark:bg-gray-800/60">
                                    <div class="absolute inset-y-0 left-0 rounded-full {{ $allAch ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500' }} transition-all" style="width: {{ $progressPercent }}%"></div>
                                    <div class="absolute inset-y-0 left-0 rounded-full {{ $allAch ? 'bg-gradient-to-r from-emerald-500/40 to-teal-500/40' : 'bg-gradient-to-r from-blue-500/30 via-indigo-500/30 to-purple-500/30' }} blur-sm transition-all" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            {{-- Goal list --}}
                            <ol class="space-y-2 sm:space-y-2.5">
                                @foreach ($row['goals'] as $goalIndex => $goal)
                                    <li class="relative flex items-start gap-3 rounded-2xl border {{ $goal['is_achieved'] ? 'border-emerald-200/80 bg-white/85 dark:border-emerald-700/40 dark:bg-gray-900/60' : 'border-gray-200/80 bg-white/70 dark:border-gray-700/50 dark:bg-gray-900/40' }} p-3 shadow-sm transition sm:p-4">
                                        <div class="relative mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center sm:h-10 sm:w-10">
                                            <span class="absolute inset-0 rounded-full {{ $goal['is_achieved'] ? 'bg-emerald-100 dark:bg-emerald-500/15' : 'bg-gray-100 dark:bg-gray-700/50' }}"></span>
                                            @if ($goal['is_achieved'])
                                                <svg class="relative h-5 w-5 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                <span class="relative text-xs font-semibold text-gray-500 sm:text-sm dark:text-gray-400">{{ $goalIndex + 1 }}</span>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="text-sm font-semibold leading-snug {{ $goal['is_achieved'] ? 'text-emerald-700 dark:text-emerald-200' : 'text-gray-800 dark:text-gray-100' }} sm:text-base">
                                                    {{ $goal['judul'] }}
                                                </h3>
                                                @if ($goal['is_achieved'])
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                                                        <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                        Tercapai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-700/60 dark:text-gray-300">
                                                        <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Dalam proses
                                                    </span>
                                                @endif
                                            </div>
                                            @if (!empty($goal['deskripsi']))
                                                <p class="mt-1 text-xs leading-relaxed {{ $goal['is_achieved'] ? 'text-emerald-700/80 dark:text-emerald-200/70' : 'text-gray-600 dark:text-gray-300' }} sm:text-sm">
                                                    {{ $goal['deskripsi'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ol>

                            {{-- Closing message --}}
                            @if ($allAch)
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 px-3 py-2.5 dark:border-emerald-700/40 dark:bg-emerald-950/30 sm:px-4 sm:py-3">
                                    <p class="flex items-start gap-2 text-xs font-medium text-emerald-700 dark:text-emerald-300 sm:text-sm">
                                        <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>Selamat! Semua tujuan pembelajaran {{ $row['is_bootcamp'] ? 'bootcamp' : 'kursus' }} ini sudah tercapai.</span>
                                    </p>
                                </div>
                            @else
                                <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400 sm:text-sm">
                                    Selesaikan {{ $row['is_bootcamp'] ? 'bootcamp' : 'kursus' }} untuk membuka pencapaian tiap tujuan pembelajaran berikut.
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.dashboard>

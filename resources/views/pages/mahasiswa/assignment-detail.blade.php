<x-layouts.dashboard :active="'courses'">

{{-- Breadcrumb --}}
<nav aria-label="Breadcrumb" class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 sm:mb-4 sm:gap-2 sm:text-sm">
    <a href="{{ route('mahasiswa.courses') }}" class="text-blue-500 hover:underline">Kursus</a>
    <span aria-hidden="true">›</span>
    <a href="{{ route('mahasiswa.course-learn', $course->id_course) }}" class="max-w-[8rem] truncate text-blue-500 hover:underline sm:max-w-[12rem]">{{ $course->nama_course }}</a>
    <span aria-hidden="true">›</span>
    <span class="font-medium text-gray-800 dark:text-gray-100">Tugas Akhir</span>
</nav>

{{-- Page Header --}}
<div class="mb-4 sm:mb-6">
    <h1 class="mb-1.5 text-xl font-bold leading-tight text-gray-800 dark:text-gray-100 sm:mb-2 sm:text-2xl">Tugas Akhir</h1>
    <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400 sm:text-sm">Tugas akhir bersifat opsional untuk modul ini.</p>
</div>

{{-- Informasi Tugas Section --}}
<section class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:mb-6 sm:p-6">
    <div class="mb-4 flex items-center gap-3 sm:mb-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-500/20 sm:h-10 sm:w-10">
            <svg class="h-4 w-4 text-blue-500 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Informasi Tugas</h2>
    </div>

    <div class="flex flex-col gap-5 lg:flex-row lg:gap-8">
        {{-- Left: Description --}}
        <div class="min-w-0 flex-1">
            <h3 class="mb-2 line-clamp-2 text-sm font-semibold leading-snug text-gray-800 dark:text-gray-100 sm:mb-3 sm:text-base">{{ $assignment['title'] }}</h3>
            <p class="mb-4 text-sm leading-relaxed text-gray-600 dark:text-gray-300 sm:mb-5">{{ $assignment['description'] }}</p>

            <h4 class="mb-2 text-xs font-semibold text-gray-800 dark:text-gray-100 sm:mb-3 sm:text-sm">Tujuan Pembelajaran</h4>
            <ul class="flex flex-col gap-2">
                @foreach($assignment['learning_objectives'] as $objective)
                <li class="flex items-start gap-2">
                    <span class="mt-1.5 h-2 w-2 flex-shrink-0 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-600 dark:text-gray-300 sm:text-sm">{{ $objective }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Right: Details --}}
        <div class="flex w-full flex-col gap-3 sm:gap-4 lg:w-72 lg:flex-shrink-0">
            {{-- Deadline & Weight (stacked on tiny, side-by-side on bigger) --}}
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-1 xl:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/50 sm:p-4">
                    <div class="mb-1.5 flex items-center gap-2 sm:mb-2">
                        <svg class="h-3.5 w-3.5 text-blue-500 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 sm:text-xs">Tenggat</span>
                    </div>
                    <p class="line-clamp-1 text-xs font-semibold text-gray-800 dark:text-gray-100 sm:text-sm">{{ $assignment['deadline']->format('d F Y') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/50 sm:p-4">
                    <div class="mb-1.5 flex items-center gap-2 sm:mb-2">
                        <svg class="h-3.5 w-3.5 text-blue-500 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        </svg>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 sm:text-xs">Bobot</span>
                    </div>
                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 sm:text-sm">{{ $assignment['weight'] }}%</p>
                </div>
            </div>

            {{-- Format & Size --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/50 sm:p-4">
                <div class="mb-1.5 flex items-center gap-2 sm:mb-2">
                    <svg class="h-3.5 w-3.5 text-blue-500 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="text-[10px] text-gray-500 dark:text-gray-400 sm:text-xs">Format & Ukuran</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 sm:text-sm">Format: {{ $assignment['format'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-300 sm:text-sm">Maks: {{ $assignment['max_size'] }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Instruksi Pengerjaan Section --}}
<section class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800 sm:mb-6 sm:p-6">
    <div class="mb-4 flex items-center gap-3 sm:mb-5">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-500/20 sm:h-10 sm:w-10">
            <svg class="h-4 w-4 text-blue-500 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
        </div>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Instruksi Pengerjaan</h2>
    </div>

    <div class="flex flex-col gap-5 lg:flex-row lg:gap-12">
        {{-- Steps --}}
        <div class="min-w-0 flex-1">
            <h4 class="mb-3 text-xs font-semibold text-gray-800 dark:text-gray-100 sm:mb-4 sm:text-sm">Langkah Pengerjaan</h4>
            <ol class="flex flex-col gap-3">
                @foreach($assignment['steps'] as $index => $step)
                <li class="flex items-start gap-2.5 sm:gap-3">
                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-blue-500 text-xs font-semibold text-white">{{ $index + 1 }}</span>
                    <span class="pt-0.5 text-xs text-gray-600 dark:text-gray-300 sm:text-sm">{{ $step }}</span>
                </li>
                @endforeach
            </ol>
        </div>

        {{-- Grading & Notes --}}
        <div class="w-full lg:w-80 lg:flex-shrink-0">
            <h4 class="mb-3 text-xs font-semibold text-gray-800 dark:text-gray-100 sm:mb-4 sm:text-sm">Kriteria Penilaian</h4>
            <div class="mb-4 flex flex-col gap-2 sm:mb-5">
                @foreach($assignment['grading_criteria'] as $criteria)
                <div class="flex items-center justify-between gap-3">
                    <span class="min-w-0 truncate text-xs text-gray-600 dark:text-gray-300 sm:text-sm">{{ $criteria['name'] }}</span>
                    <span class="flex-shrink-0 text-xs font-semibold text-blue-500 sm:text-sm">{{ $criteria['percentage'] }}%</span>
                </div>
                @endforeach
            </div>

            {{-- Instructor Note --}}
            <div class="rounded-xl border-l-4 border-blue-500 bg-blue-50 p-3 dark:bg-blue-500/20 dark:border-blue-400 sm:p-4">
                <div class="mb-1.5 flex items-center gap-2 sm:mb-2">
                    <svg class="h-3.5 w-3.5 text-blue-500 sm:h-4 sm:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-[10px] font-semibold text-blue-700 dark:text-blue-300 sm:text-xs">Catatan Dosen</span>
                </div>
                <p class="text-xs leading-relaxed text-blue-700 dark:text-blue-100 sm:text-sm">{{ $assignment['instructor_note'] }}</p>
            </div>
        </div>
    </div>
</section>

{{-- Submit Button --}}
<a href="{{ route('mahasiswa.assignment-submission', ['courseId' => $course->id_course, 'assignmentId' => $assignment['id']]) }}" class="mb-24 flex w-full items-center justify-center gap-2 rounded-xl bg-blue-500 px-4 py-3 text-sm font-semibold text-white transition-colors hover:bg-blue-600 sm:mb-6 sm:py-3.5 sm:text-base">
    Halaman Submission
    <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
    </svg>
</a>

</x-layouts.dashboard>

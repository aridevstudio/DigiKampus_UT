<x-layouts.dosen title="Detail Progres Mahasiswa" active="kursus-saya">
    @php
        $student = $enrollment->mahasiswa;
        $profile = $student?->profile;
        $progressPercent = (int) ($progressSummary['progress'] ?? 0);
        $studentAvatar = $profile?->foto_profile ? asset('storage/' . $profile->foto_profile) : null;
        $studentInitial = strtoupper(substr($student?->name ?? 'M', 0, 1));
        $lastActivity = $progressSummary['last_activity'] ?? null;
        $statusValue = strtolower((string) ($enrollment->status ?? 'aktif'));
        $statusMap = [
            'selesai' => ['label' => 'Selesai', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-500/20'],
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-sky-100 text-sky-700 border-sky-200 dark:bg-sky-500/15 dark:text-sky-300 dark:border-sky-500/20'],
            'in_progress' => ['label' => 'Aktif', 'class' => 'bg-sky-100 text-sky-700 border-sky-200 dark:bg-sky-500/15 dark:text-sky-300 dark:border-sky-500/20'],
            'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/20'],
            'nonaktif' => ['label' => 'Nonaktif', 'class' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'],
        ];
        $statusBadge = $statusMap[$statusValue] ?? $statusMap['aktif'];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('dosen.kursus') }}" class="transition hover:text-teal-600">Kursus Saya</a>
                    <span>/</span>
                    <a href="{{ route('dosen.kursus.progres', $course->id_course) }}" class="transition hover:text-teal-600">Progres</a>
                    <span>/</span>
                    <span>Detail Mahasiswa</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Progress Mahasiswa</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $course->nama_course }} · audit aktivitas materi, tugas, dan hasil kuiz mahasiswa.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusBadge['class'] }}">{{ $statusBadge['label'] }}</span>
                <a href="{{ route('dosen.kursus.progres', $course->id_course) }}" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke daftar progres
                </a>
            </div>
        </div>

        <section class="overflow-hidden rounded-3xl border border-teal-100 bg-gradient-to-br from-teal-50 via-white to-orange-50 shadow-sm dark:border-teal-500/10 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
            <div class="grid gap-6 p-6 lg:grid-cols-[1.3fr,0.9fr] lg:p-8">
                <div class="flex gap-4">
                    @if($studentAvatar)
                        <img src="{{ $studentAvatar }}" alt="{{ $student?->name }}" class="h-16 w-16 rounded-2xl object-cover ring-4 ring-white/80 dark:ring-gray-800/80">
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-500 text-xl font-bold text-white ring-4 ring-white/80 dark:ring-gray-800/80">{{ $studentInitial }}</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $student?->name ?? 'Mahasiswa' }}</h2>
                            <span class="inline-flex items-center rounded-full bg-white/80 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-teal-700 shadow-sm dark:bg-gray-800 dark:text-teal-300">Progress Review</span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <span>{{ $profile?->nomor_induk ?: '-' }}</span>
                            <span>{{ $student?->email ?: '-' }}</span>
                            <span>{{ $profile?->jurusan?->nama_jurusan ?: 'Jurusan belum diatur' }}</span>
                        </div>
                        <div class="mt-5">
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-600 dark:text-gray-300">Progress Kursus</span>
                                <span class="font-semibold text-teal-700 dark:text-teal-300">{{ $progressPercent }}%</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-teal-100/80 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-teal-500 via-cyan-500 to-orange-400" style="width: {{ $progressPercent }}%"></div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Terakhir aktif {{ $lastActivity ? $lastActivity->diffForHumans() : 'belum ada aktivitas tercatat' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">Materi</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $progressSummary['completed_materials'] }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ $progressSummary['total_materials'] }} materi selesai</div>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">Kuiz</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $progressSummary['attempted_quizzes'] }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ $progressSummary['total_quizzes'] }} kuiz dikerjakan</div>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">Benar</div>
                        <div class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ $progressSummary['correct_answers'] }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ $progressSummary['total_questions'] }} jawaban kuiz</div>
                    </div>
                    <div class="rounded-2xl border border-white/70 bg-white/80 p-4 shadow-sm backdrop-blur dark:border-gray-800 dark:bg-gray-900/80">
                        <div class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-400">Tugas</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $progressSummary['submitted_assignments'] }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $progressSummary['reviewed_assignments'] }} sudah direview</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.55fr,0.95fr]">
            <div class="space-y-6">
                <section class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Peta Progress per Modul</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lihat materi yang sudah selesai, status tugas, dan kuiz per modul.</p>
                    </div>
                    <div class="space-y-4 p-6">
                        @forelse($moduleSummaries as $module)
                            @php
                                $moduleProgress = $module['total_materials'] > 0 ? round(($module['completed_materials'] / $module['total_materials']) * 100) : 0;
                            @endphp
                            <div class="rounded-2xl border border-gray-200 bg-gray-50/70 p-5 dark:border-gray-800 dark:bg-gray-950/50">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $module['title'] }}</h4>
                                            <span class="inline-flex items-center rounded-full bg-teal-100 px-2.5 py-1 text-[11px] font-semibold text-teal-700 dark:bg-teal-500/15 dark:text-teal-300">{{ $module['completed_materials'] }}/{{ $module['total_materials'] }} materi</span>
                                        </div>
                                        @if(!empty($module['description']))
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $module['description'] }}</p>
                                        @endif
                                    </div>
                                    <div class="min-w-[180px]">
                                        <div class="mb-2 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.18em] text-gray-400">
                                            <span>Progres Modul</span>
                                            <span>{{ $moduleProgress }}%</span>
                                        </div>
                                        <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                                            <div class="h-full rounded-full bg-gradient-to-r from-teal-500 to-cyan-500" style="width: {{ $moduleProgress }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3">
                                    @forelse($module['materials'] as $material)
                                        @php
                                            $submission = $material['submission'];
                                            $materialDone = $material['is_completed'];
                                        @endphp
                                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                                            <div class="flex flex-col gap-3 xl:flex-row xl:items-start xl:justify-between">
                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $material['type_label'] }}</span>
                                                        @if($materialDone)
                                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">Selesai</span>
                                                        @else
                                                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">Belum selesai</span>
                                                        @endif
                                                        @if($material['duration'])
                                                            <span class="text-xs text-gray-400">{{ $material['duration'] }} menit</span>
                                                        @endif
                                                    </div>
                                                    <h5 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $material['title'] }}</h5>
                                                    @if($materialDone && $material['completed_at'])
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Diselesaikan {{ optional($material['completed_at'])->format('d M Y H:i') }}</p>
                                                    @endif
                                                </div>
                                                @if($submission)
                                                    <div class="min-w-full rounded-2xl border border-sky-100 bg-sky-50/80 p-4 text-sm dark:border-sky-500/20 dark:bg-sky-500/10 xl:min-w-[320px]">
                                                        <div class="mb-2 flex items-center justify-between gap-3">
                                                            <span class="font-semibold text-sky-800 dark:text-sky-200">Submission Tugas</span>
                                                            <span class="inline-flex items-center rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-sky-700 dark:bg-gray-900 dark:text-sky-300">{{ ucfirst(str_replace('_', ' ', $submission['status'] ?? 'terkirim')) }}</span>
                                                        </div>
                                                        <div class="space-y-1 text-xs text-sky-900/80 dark:text-sky-100/80">
                                                            <div>File: {{ $submission['file_name'] ?: '-' }}</div>
                                                            <div>Dikirim: {{ $submission['submitted_at'] ? optional($submission['submitted_at'])->format('d M Y H:i') : '-' }}</div>
                                                            <div>Direview: {{ $submission['reviewed_at'] ? optional($submission['reviewed_at'])->format('d M Y H:i') : 'Belum direview' }}</div>
                                                        </div>
                                                        @if(!empty($submission['student_note']))
                                                            <div class="mt-3 rounded-xl bg-white/80 p-3 text-xs text-gray-700 dark:bg-gray-900/70 dark:text-gray-200">
                                                                <div class="mb-1 font-semibold text-gray-500 dark:text-gray-400">Catatan mahasiswa</div>
                                                                <div>{{ $submission['student_note'] }}</div>
                                                            </div>
                                                        @endif
                                                        @if(!empty($submission['instructor_note']))
                                                            <div class="mt-3 rounded-xl bg-white/80 p-3 text-xs text-gray-700 dark:bg-gray-900/70 dark:text-gray-200">
                                                                <div class="mb-1 font-semibold text-gray-500 dark:text-gray-400">Catatan dosen</div>
                                                                <div>{{ $submission['instructor_note'] }}</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 bg-white px-4 py-5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                            Belum ada materi pada modul ini.
                                        </div>
                                    @endforelse
                                </div>

                                @if(collect($module['quizzes'])->isNotEmpty())
                                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                                        @foreach($module['quizzes'] as $quiz)
                                            <div class="rounded-2xl border border-orange-100 bg-orange-50/70 p-4 dark:border-orange-500/20 dark:bg-orange-500/10">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <h5 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $quiz['title'] }}</h5>
                                                            @if($quiz['is_pretest'])
                                                                <span class="inline-flex items-center rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.18em] text-orange-700 dark:bg-gray-900 dark:text-orange-300">Pretest</span>
                                                            @endif
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $quiz['question_count'] }} soal · passing {{ $quiz['passing_score'] }}</p>
                                                    </div>
                                                    @if($quiz['attempted'])
                                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ $quiz['latest_score'] }}%</span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">Belum dikerjakan</span>
                                                    @endif
                                                </div>
                                                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $quiz['attempted'] ? 'Attempt terakhir ' . optional($quiz['submitted_at'])->diffForHumans() : 'Belum ada attempt tercatat' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                Modul kursus belum tersedia untuk direview.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Review Jawaban Kuiz</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Menampilkan attempt terakhir mahasiswa per kuiz, termasuk jawaban benar dan salah per soal.</p>
                    </div>
                    <div class="space-y-4 p-6">
                        @forelse($quizReviews as $quizReview)
                            <details class="group rounded-2xl border border-gray-200 bg-gray-50/70 p-5 open:bg-white dark:border-gray-800 dark:bg-gray-950/50 dark:open:bg-gray-900" {{ $loop->first ? 'open' : '' }}>
                                <summary class="flex cursor-pointer list-none flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white">{{ $quizReview['title'] }}</h4>
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $quizReview['module_name'] }}</span>
                                            @if($quizReview['is_pretest'])
                                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-1 text-[11px] font-semibold text-orange-700 dark:bg-orange-500/15 dark:text-orange-300">Pretest</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            @if($quizReview['latest_attempt'])
                                                Attempt terakhir {{ optional($quizReview['latest_attempt']['submitted_at'])->format('d M Y H:i') }} · {{ $quizReview['attempt_count'] }} attempt tercatat
                                            @else
                                                Mahasiswa belum mengerjakan kuiz ini.
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        @if($quizReview['latest_attempt'])
                                            <div class="rounded-2xl bg-white px-4 py-3 text-right shadow-sm dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Nilai Terakhir</div>
                                                <div class="mt-1 text-2xl font-bold {{ $quizReview['latest_attempt']['is_passed'] ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">{{ $quizReview['latest_attempt']['score'] }}%</div>
                                            </div>
                                            <div class="rounded-2xl bg-white px-4 py-3 text-right shadow-sm dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Nilai Terbaik</div>
                                                <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $quizReview['best_score'] }}%</div>
                                            </div>
                                        @else
                                            <div class="rounded-2xl bg-white px-4 py-3 text-sm text-gray-500 shadow-sm dark:bg-gray-950 dark:text-gray-400">Belum ada data attempt</div>
                                        @endif
                                    </div>
                                </summary>

                                @if($quizReview['latest_attempt'])
                                    <div class="mt-5 border-t border-gray-100 pt-5 dark:border-gray-800">
                                        <div class="mb-4 grid gap-3 md:grid-cols-4">
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Benar</div>
                                                <div class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ $quizReview['latest_attempt']['correct_answers'] }}</div>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ $quizReview['latest_attempt']['total_questions'] }} soal</div>
                                            </div>
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Poin</div>
                                                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $quizReview['latest_attempt']['earned_points'] }}</div>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">dari {{ $quizReview['latest_attempt']['max_points'] }} poin</div>
                                            </div>
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Lulus</div>
                                                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $quizReview['latest_attempt']['passing_score'] }}</div>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">passing score</div>
                                            </div>
                                            <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                                                <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Status</div>
                                                <div class="mt-2 text-lg font-bold {{ $quizReview['latest_attempt']['is_passed'] ? 'text-emerald-600 dark:text-emerald-300' : 'text-rose-600 dark:text-rose-300' }}">{{ $quizReview['latest_attempt']['is_passed'] ? 'Lulus' : 'Belum Lulus' }}</div>
                                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">berdasarkan attempt terakhir</div>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            @foreach($quizReview['question_reviews'] as $review)
                                                <div class="rounded-2xl border {{ $review['is_correct'] ? 'border-emerald-200 bg-emerald-50/60 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-rose-200 bg-rose-50/60 dark:border-rose-500/20 dark:bg-rose-500/10' }} p-4">
                                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                                        <div class="min-w-0 flex-1">
                                                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                                                <span class="inline-flex items-center rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-600 shadow-sm dark:bg-gray-900 dark:text-gray-300">Soal {{ $review['number'] }}</span>
                                                                <span class="text-xs font-medium uppercase tracking-[0.16em] text-gray-400">{{ $review['type'] }}</span>
                                                            </div>
                                                            <p class="text-sm font-medium leading-6 text-gray-900 dark:text-white">{{ $review['question'] }}</p>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $review['is_correct'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' }}">{{ $review['is_correct'] ? 'Benar' : 'Salah' }}</span>
                                                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Poin {{ $review['points'] }}/{{ $review['max_points'] }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 grid gap-3 lg:grid-cols-2">
                                                        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
                                                            <div class="mb-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Jawaban Mahasiswa</div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $review['selected_label'] !== '-' ? $review['selected_label'] . '.' : '' }} {{ $review['selected_text'] }}</div>
                                                        </div>
                                                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                                                            <div class="mb-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">Jawaban Benar</div>
                                                            <div class="text-sm font-medium text-emerald-900 dark:text-emerald-100">{{ $review['correct_label'] !== '-' ? $review['correct_label'] . '.' : '' }} {{ $review['correct_text'] }}</div>
                                                        </div>
                                                    </div>
                                                    @if(!empty($review['explanation']))
                                                        <div class="mt-3 rounded-2xl border border-sky-100 bg-sky-50/80 p-4 text-sm text-sky-900 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-100">
                                                            <div class="mb-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-600 dark:text-sky-300">Penjelasan Soal</div>
                                                            <div class="leading-6">{{ $review['explanation'] }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </details>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                Belum ada kuiz aktif pada kursus ini.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ringkasan Mahasiswa</h3>
                    </div>
                    <div class="space-y-4 p-6 text-sm">
                        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950/60">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Nomor Induk</div>
                            <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $profile?->nomor_induk ?: '-' }}</div>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950/60">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Email</div>
                            <div class="mt-2 break-all font-semibold text-gray-900 dark:text-white">{{ $student?->email ?: '-' }}</div>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950/60">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Jurusan</div>
                            <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $profile?->jurusan?->nama_jurusan ?: 'Belum diatur' }}</div>
                        </div>
                        <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-950/60">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400">Tanggal Daftar Kursus</div>
                            <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $enrollment->tanggal_daftar ? optional($enrollment->tanggal_daftar)->format('d M Y') : '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-100 px-6 py-5 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Komposisi Aktivitas</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Snapshot cepat untuk identifikasi area yang masih tertinggal.</p>
                    </div>
                    <div class="space-y-4 p-6">
                        <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                            <div class="mb-2 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-200">
                                <span>Materi selesai</span>
                                <span>{{ $progressSummary['completed_materials'] }}/{{ $progressSummary['total_materials'] }}</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-teal-500 to-cyan-500" style="width: {{ $progressSummary['total_materials'] > 0 ? round(($progressSummary['completed_materials'] / $progressSummary['total_materials']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                            <div class="mb-2 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-200">
                                <span>Kuiz dikerjakan</span>
                                <span>{{ $progressSummary['attempted_quizzes'] }}/{{ $progressSummary['total_quizzes'] }}</span>
                            </div>
                            <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
                                <div class="h-full rounded-full bg-gradient-to-r from-orange-500 to-amber-400" style="width: {{ $progressSummary['total_quizzes'] > 0 ? round(($progressSummary['attempted_quizzes'] / $progressSummary['total_quizzes']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
                            <div class="mb-2 flex items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-200">
                                <span>Tugas dikirim</span>
                                <span>{{ $progressSummary['submitted_assignments'] }}</span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $progressSummary['reviewed_assignments'] }} submission sudah diberi review dosen.</div>
                        </div>
                        <div class="rounded-2xl border border-dashed border-teal-200 bg-teal-50/60 p-4 text-sm text-teal-900 dark:border-teal-500/20 dark:bg-teal-500/10 dark:text-teal-100">
                            <div class="font-semibold">Interpretasi cepat</div>
                            <div class="mt-2 leading-6">
                                @if($progressPercent >= 100)
                                    Mahasiswa sudah menyelesaikan seluruh materi kursus. Fokus review berpindah ke kualitas jawaban kuiz dan tugas yang dikumpulkan.
                                @elseif($progressPercent >= 60)
                                    Mahasiswa sudah cukup jauh berjalan. Periksa modul yang belum selesai dan review attempt kuiz terakhir untuk melihat konsep yang masih lemah.
                                @else
                                    Progress masih rendah. Prioritaskan cek modul awal, konsistensi belajar, dan apakah kuiz atau tugas sudah mulai dikerjakan.
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</x-layouts.dosen>

@php
    $mentorBootcamps = [
        [
            'title' => 'Bootcamp UI/UX Product Sprint',
            'batch' => 'Batch April 2026',
            'role' => 'Lead Mentor',
            'progress' => 68,
            'next_session' => 'Sabtu, 16 Mar 2026 • 09.00',
            'students' => 72,
            'tasks' => '11 submission baru',
            'status' => 'Sedang Jalan',
            'accent' => 'from-sky-500 to-blue-600',
        ],
        [
            'title' => 'Bootcamp Data Analyst Career Track',
            'batch' => 'Batch Mei 2026',
            'role' => 'Mentor Tugas Akhir',
            'progress' => 34,
            'next_session' => 'Senin, 18 Mar 2026 • 19.00',
            'students' => 48,
            'tasks' => '7 review rubrik',
            'status' => 'Persiapan',
            'accent' => 'from-violet-500 to-fuchsia-600',
        ],
    ];

    $sessionQueue = [
        ['time' => '09.00', 'session' => 'Critique wireframe cohort A', 'detail' => 'Perlu buka revisi batch 3', 'tag' => 'Live Review'],
        ['time' => '11.30', 'session' => 'Office hour final assignment', 'detail' => '6 mahasiswa booked', 'tag' => 'Mentoring'],
        ['time' => '19.00', 'session' => 'Data cleaning sprint', 'detail' => 'Module 4 + template notebook', 'tag' => 'Hands-on'],
    ];

    $cohortRows = [
        ['name' => 'Cohort A', 'students' => 24, 'attendance' => '92%', 'completion' => '81%', 'risk' => '2 peserta tertinggal tugas'],
        ['name' => 'Cohort B', 'students' => 28, 'attendance' => '88%', 'completion' => '74%', 'risk' => 'Mentoring tambahan dibutuhkan'],
        ['name' => 'Cohort C', 'students' => 20, 'attendance' => '95%', 'completion' => '86%', 'risk' => 'Aman'],
    ];

    $deliveryChecklist = [
        ['label' => 'Materi sesi minggu ini', 'state' => 'Siap', 'note' => 'Slide, brief, dan template sudah final'],
        ['label' => 'Feedback tugas akhir', 'state' => 'Butuh Review', 'note' => '11 submission baru belum selesai diberi komentar'],
        ['label' => 'Attendance recap', 'state' => 'Sinkron', 'note' => 'Menunggu backend auto attendance'],
        ['label' => 'Sertifikat cohort lulus', 'state' => 'Blocked', 'note' => 'Tergantung logic publish nilai final'],
    ];
@endphp

<x-layouts.dosen title="Bootcamp Saya" active="bootcamp">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute inset-y-0 right-0 hidden w-2/5 bg-[radial-gradient(circle_at_top_right,_rgba(99,102,241,0.16),_transparent_55%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.14),_transparent_45%)] lg:block"></div>
                <div class="relative flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white dark:bg-white dark:text-slate-900">
                            Bootcamp Delivery Desk
                        </span>
                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">Panel dosen fokus ke delivery, bukan operasional tiket</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-gray-400 sm:text-base">
                            Dosen cukup melihat batch yang diampu, sesi terdekat, cohort, tugas, dan kebutuhan mentoring. Pricing, publish, kuota, dan refund tetap ditahan di admin.
                        </p>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3 xl:w-[420px]">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Jadwalkan Sesi
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Review Tugas
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10m-4 4h4" />
                            </svg>
                            Broadcast Cohort
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-2">
            @foreach ($mentorBootcamps as $bootcamp)
                <article class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div class="h-1.5 bg-gradient-to-r {{ $bootcamp['accent'] }}"></div>
                    <div class="p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">{{ $bootcamp['batch'] }}</span>
                                <h2 class="mt-3 text-xl font-semibold text-slate-900 dark:text-white">{{ $bootcamp['title'] }}</h2>
                                <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $bootcamp['role'] }} • {{ $bootcamp['students'] }} peserta</p>
                            </div>
                            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ $bootcamp['status'] }}</span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Next Session</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $bootcamp['next_session'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Queue</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $bootcamp['tasks'] }}</p>
                            </div>
                            <div class="rounded-2xl bg-slate-900 px-4 py-3 text-white dark:bg-blue-500/20">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-300 dark:text-blue-200">Progress Delivery</p>
                                <p class="mt-2 text-2xl font-bold">{{ $bootcamp['progress'] }}%</p>
                            </div>
                        </div>

                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-700">
                            <div class="h-full rounded-full bg-gradient-to-r {{ $bootcamp['accent'] }}" style="width: {{ $bootcamp['progress'] }}%"></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Queue Hari Ini</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Sesi dan mentoring yang perlu ditangani</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">Dosen Focused</span>
                </div>

                <div class="mt-5 grid gap-3">
                    @foreach ($sessionQueue as $session)
                        <article class="rounded-[24px] border border-slate-200 px-4 py-4 dark:border-gray-700">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white dark:bg-white dark:text-slate-900">
                                        {{ $session['time'] }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $session['session'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">{{ $session['detail'] }}</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ $session['tag'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 overflow-hidden rounded-[24px] border border-slate-200 dark:border-gray-700">
                    <div class="border-b border-slate-200 bg-slate-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Ringkasan Cohort</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-gray-700">
                            <thead class="bg-white dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Cohort</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Peserta</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Attendance</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Completion</th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-gray-700">
                                @foreach ($cohortRows as $row)
                                    <tr class="hover:bg-slate-50/70 dark:hover:bg-gray-900/30">
                                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white">{{ $row['name'] }}</td>
                                        <td class="px-4 py-4 text-slate-600 dark:text-gray-300">{{ $row['students'] }}</td>
                                        <td class="px-4 py-4 text-slate-600 dark:text-gray-300">{{ $row['attendance'] }}</td>
                                        <td class="px-4 py-4 text-slate-600 dark:text-gray-300">{{ $row['completion'] }}</td>
                                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-gray-400">{{ $row['risk'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Delivery Checklist</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Yang perlu dijaga dosen</h2>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($deliveryChecklist as $item)
                            <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item['label'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">{{ $item['note'] }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">{{ $item['state'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Boundary</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Yang tetap di admin</h2>
                    </div>
                    <div class="mt-4 grid gap-3">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Pricing dan seat management</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Dosen tidak ubah harga, kuota, dan publish tiket.</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Refund / reschedule</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Kasus operasional peserta tetap ditangani admin.</p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Publish batch dan closing registration</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">Dosen fokus mengajar, bukan pegang lifecycle penjualan.</p>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</x-layouts.dosen>

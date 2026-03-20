@php
    $mentorStats = [
        ['label' => 'Batch Diampu', 'value' => 2, 'helper' => 'aktif minggu ini', 'tone' => 'from-sky-500 to-blue-600'],
        ['label' => 'Peserta Aktif', 'value' => 120, 'helper' => 'gabungan seluruh cohort', 'tone' => 'from-emerald-500 to-teal-600'],
        ['label' => 'Review Tertunda', 'value' => 18, 'helper' => 'submission dan rubrik', 'tone' => 'from-violet-500 to-fuchsia-600'],
        ['label' => 'Sesi Terjadwal', 'value' => 3, 'helper' => 'agenda hari ini', 'tone' => 'from-amber-500 to-orange-500'],
    ];

    $mentorBootcamps = [
        [
            'title' => 'Bootcamp UI/UX Product Sprint',
            'type' => 'Bootcamp',
            'batch' => 'Batch April 2026',
            'role' => 'Lead Mentor',
            'progress' => 68,
            'next_session' => 'Sabtu, 16 Mar 2026 • 09.00',
            'students' => 72,
            'tasks' => '11 submission baru',
            'status' => 'Sedang Jalan',
            'risk' => 'Perlu feedback final untuk cohort A',
            'accent' => 'from-sky-500 to-blue-600',
            'accentBadge' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:border-sky-500/20',
        ],
        [
            'title' => 'Bootcamp Data Analyst Career Track',
            'type' => 'Bootcamp',
            'batch' => 'Batch Mei 2026',
            'role' => 'Mentor Tugas Akhir',
            'progress' => 34,
            'next_session' => 'Senin, 18 Mar 2026 • 19.00',
            'students' => 48,
            'tasks' => '7 review rubrik',
            'status' => 'Persiapan',
            'risk' => 'Mentoring tambahan perlu dibuka',
            'accent' => 'from-violet-500 to-fuchsia-600',
            'accentBadge' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-500/10 dark:text-violet-300 dark:border-violet-500/20',
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
    <div class="bootcamp-shell">
        <section class="bootcamp-hero">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="relative flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                    <div class="max-w-3xl">
                        <span class="bootcamp-badge-dark">
                            Bootcamp Delivery Desk
                        </span>
                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">Panel dosen tetap satu gaya dengan admin, tapi fokus ke delivery</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-gray-400 sm:text-base">
                            Batch yang diampu, sesi terdekat, progress cohort, dan review tugas ditampilkan dengan pola kartu yang sama seperti admin. Bedanya, dosen tidak melihat semua program dan tidak pegang lifecycle tiket.
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-slate-900 px-3 py-1.5 font-semibold text-white dark:bg-white dark:text-slate-900">Assigned Batch View</span>
                            <span class="rounded-full bg-violet-50 px-3 py-1.5 font-semibold text-violet-700 dark:bg-violet-500/10 dark:text-violet-300">Delivery Focus</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1.5 font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">No Pricing / Refund</span>
                        </div>
                    </div>

                    <div class="bootcamp-toolbar xl:w-[420px]">
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

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($mentorStats as $item)
                <article class="bootcamp-kpi-card">
                    <div class="h-1.5 bg-gradient-to-r {{ $item['tone'] }}"></div>
                    <div class="p-5">
                        <p class="bootcamp-label">{{ $item['label'] }}</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $item['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $item['helper'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.25fr_0.85fr]">
            <div class="bootcamp-panel p-5">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="bootcamp-label">Batch Saya</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Bootcamp yang sedang saya ampu</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">Dosen Focused</span>
                </div>

                <div class="mt-5 grid gap-4">
                    @foreach ($mentorBootcamps as $bootcamp)
                        <article class="bootcamp-program-card p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] {{ $bootcamp['accentBadge'] }}">{{ $bootcamp['type'] }}</span>
                                        <span class="text-xs font-medium text-slate-500 dark:text-gray-400">{{ $bootcamp['batch'] }}</span>
                                    </div>
                                    <h2 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $bootcamp['title'] }}</h2>
                                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $bootcamp['role'] }} • {{ $bootcamp['students'] }} peserta</p>
                                </div>
                                <div class="grid gap-2 text-left text-sm lg:min-w-[200px] lg:text-right">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $bootcamp['next_session'] }}</p>
                                    <p class="text-slate-500 dark:text-gray-400">{{ $bootcamp['tasks'] }}</p>
                                    <p class="text-xs font-medium text-amber-600 dark:text-amber-300">{{ $bootcamp['risk'] }}</p>
                                </div>
                            </div>

                            <div class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-gray-700">
                                <div class="h-full rounded-full bg-gradient-to-r {{ $bootcamp['accent'] }}" style="width: {{ $bootcamp['progress'] }}%"></div>
                            </div>

                            <div class="bootcamp-mini-grid mt-4">
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Status</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $bootcamp['status'] }}</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Progress</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $bootcamp['progress'] }}%</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Queue</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $bootcamp['tasks'] }}</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Aksi</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button type="button" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white dark:bg-white dark:text-slate-900">Buka Delivery</button>
                                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Nilai & Review</button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <section class="bootcamp-panel p-5">
                    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="bootcamp-label">Queue Hari Ini</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Sesi dan mentoring yang perlu ditangani</h2>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">Live Delivery</span>
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
                </section>
            </div>
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
            <div class="bootcamp-panel p-5">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="bootcamp-label">Cohort Monitor</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Ringkasan cohort dan progres peserta</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">Per Batch</span>
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
                <section class="bootcamp-panel p-5">
                    <div>
                        <p class="bootcamp-label">Delivery Checklist</p>
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

                <section class="bootcamp-panel p-5">
                    <div>
                        <p class="bootcamp-label">Boundary</p>
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

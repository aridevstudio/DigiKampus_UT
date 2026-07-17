@php
    $mentorStats = $mentorStats ?? [
        ['label' => 'Batch Diampu', 'value' => 2, 'helper' => 'aktif minggu ini', 'tone' => 'from-sky-500 to-blue-600'],
        ['label' => 'Peserta Aktif', 'value' => 120, 'helper' => 'gabungan seluruh cohort', 'tone' => 'from-emerald-500 to-teal-600'],
        ['label' => 'Review Tertunda', 'value' => 18, 'helper' => 'submission dan rubrik', 'tone' => 'from-violet-500 to-fuchsia-600'],
        ['label' => 'Sesi Terjadwal', 'value' => 3, 'helper' => 'agenda hari ini', 'tone' => 'from-amber-500 to-orange-500'],
    ];

    $mentorBootcamps = $mentorBootcamps ?? [
        [
            'id' => 'bootcamp-uiux',
            'slug' => 'bootcamp-uiux-product-sprint',
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
            'id' => 'bootcamp-data-analyst',
            'slug' => 'bootcamp-data-analyst-career-track',
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

    $sessionQueue = $sessionQueue ?? [
        ['id' => 'session-1', 'time' => '09.00', 'session' => 'Critique wireframe cohort A', 'detail' => 'Perlu buka revisi batch 3', 'tag' => 'Live Review'],
        ['id' => 'session-2', 'time' => '11.30', 'session' => 'Office hour final assignment', 'detail' => '6 mahasiswa booked', 'tag' => 'Mentoring'],
        ['id' => 'session-3', 'time' => '19.00', 'session' => 'Data cleaning sprint', 'detail' => 'Module 4 + template notebook', 'tag' => 'Hands-on'],
    ];

    $cohortRows = $cohortRows ?? [
        ['name' => 'Cohort A', 'students' => 24, 'attendance' => '92%', 'completion' => '81%', 'risk' => '2 peserta tertinggal tugas'],
        ['name' => 'Cohort B', 'students' => 28, 'attendance' => '88%', 'completion' => '74%', 'risk' => 'Mentoring tambahan dibutuhkan'],
        ['name' => 'Cohort C', 'students' => 20, 'attendance' => '95%', 'completion' => '86%', 'risk' => 'Aman'],
    ];

    $deliveryChecklist = $deliveryChecklist ?? [
        ['label' => 'Materi sesi minggu ini', 'state' => 'Siap', 'note' => 'Slide, brief, dan template sudah final'],
        ['label' => 'Feedback tugas akhir', 'state' => 'Butuh Review', 'note' => '11 submission baru belum selesai diberi komentar'],
        ['label' => 'Attendance recap', 'state' => 'Sinkron', 'note' => 'Menunggu backend auto attendance'],
        ['label' => 'Sertifikat cohort lulus', 'state' => 'Blocked', 'note' => 'Tergantung logic publish nilai final'],
    ];
@endphp

<x-layouts.dosen title="Bootcamp Saya" active="bootcamp">
    <div
        class="bootcamp-shell"
        x-data="bootcampMentorDesk({
            bootcamps: @js($mentorBootcamps),
            sessions: @js($sessionQueue),
            routes: {
                grades: @js(route('dosen.nilai')),
                progress: @js(route('dosen.progres')),
                chat: @js(route('dosen.pesan')),
                scheduleStore: @js(route('dosen.bootcamp.sessions.store')),
            },
            csrfToken: @js(csrf_token()),
            initialScheduledSessions: @js($mentorStats[3]['value']),
        })"
        @keydown.escape.window="closeAllPanels()"
    >
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
                        <p class="mt-4 text-xs font-medium text-slate-500 dark:text-gray-400">
                            Batch aktif:
                            <span class="font-semibold text-slate-700 dark:text-gray-200" x-text="activeBootcamp ? `${activeBootcamp.title} • ${activeBootcamp.batch}` : 'Belum dipilih'"></span>
                        </p>
                    </div>

                    <div class="bootcamp-toolbar xl:w-[420px]">
                        <button type="button" @click="openScheduleModal()" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Jadwalkan Sesi
                        </button>
                        <button type="button" @click="openReviewPage()" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Review Tugas
                        </button>
                        <button type="button" @click="openBroadcastModal()" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900">
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
                        @if ($item['label'] === 'Sesi Terjadwal')
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white" x-text="scheduledSessions"></p>
                        @else
                            <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $item['value'] }}</p>
                        @endif
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
                    @forelse ($mentorBootcamps as $bootcamp)
                        <article
                            class="bootcamp-program-card p-5 transition"
                            @click="selectBootcamp('{{ $bootcamp['id'] }}')"
                            :class="activeBootcampId === '{{ $bootcamp['id'] }}' ? 'ring-2 ring-sky-200 border-sky-200 dark:ring-sky-500/30 dark:border-sky-500/20' : ''"
                        >
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
                                        <button type="button" @click.stop="openDelivery('{{ $bootcamp['id'] }}')" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white dark:bg-white dark:text-slate-900">Buka Delivery</button>
                                        <button type="button" @click.stop="openReviewPage('{{ $bootcamp['id'] }}')" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Nilai & Review</button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="bootcamp-program-card p-6">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Belum ada bootcamp diampu</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Admin belum melakukan assign mentor ke akun dosen ini.</p>
                        </article>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <section class="bootcamp-panel p-5" x-ref="deliveryQueue">
                    <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="bootcamp-label">Queue Hari Ini</p>
                            <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Sesi dan mentoring yang perlu ditangani</h2>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">Live Delivery</span>
                    </div>

                    <div class="mt-5 grid gap-3">
                        <template x-for="session in sessions" :key="session.id">
                            <article class="rounded-[24px] border border-slate-200 px-4 py-4 dark:border-gray-700">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-900 text-sm font-bold text-white dark:bg-white dark:text-slate-900">
                                            <span x-text="session.time"></span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white" x-text="session.session"></p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" x-text="session.detail"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300" x-text="session.tag"></span>
                                        <button type="button" @click="openSessionChat(session)" class="rounded-full border border-slate-200 px-3 py-1 text-[11px] font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900/50">
                                            Buka Chat
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </section>
            </div>
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.2fr_0.8fr]">
            <div class="bootcamp-panel p-5" x-ref="cohortMonitor">
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

        <div x-cloak x-show="showScheduleModal" x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/55 px-4 py-8">
            <div @click.outside="showScheduleModal = false" class="w-full max-w-2xl rounded-[28px] bg-white p-6 shadow-2xl dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="bootcamp-label">Schedule Session</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Jadwalkan sesi delivery baru</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Sesi baru akan disimpan ke backend dan langsung masuk ke queue dosen.</p>
                    </div>
                    <button type="button" @click="showScheduleModal = false" class="rounded-full border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Tutup</button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-[minmax(0,1.35fr)_minmax(220px,0.65fr)]">
                    <label class="grid min-w-0 gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Bootcamp</span>
                        <select x-model="draftSchedule.bootcampId" class="min-w-0 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                            <template x-for="bootcamp in bootcamps" :key="bootcamp.id">
                                <option :value="bootcamp.id" x-text="`${bootcamp.title} • ${bootcamp.batch}`"></option>
                            </template>
                        </select>
                    </label>
                    <label class="grid min-w-0 gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Jenis Sesi</span>
                        <select x-model="draftSchedule.type" class="min-w-0 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                            <option value="Live Review">Live Review</option>
                            <option value="Mentoring">Mentoring</option>
                            <option value="Hands-on">Hands-on</option>
                            <option value="Office Hour">Office Hour</option>
                        </select>
                    </label>
                    <label class="grid min-w-0 gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Tanggal</span>
                        <input x-model="draftSchedule.date" type="date" class="min-w-0 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                    </label>
                    <label class="grid min-w-0 gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Jam</span>
                        <input x-model="draftSchedule.time" type="time" class="min-w-0 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                    </label>
                    <label class="grid min-w-0 gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Link Zoom / Google Meet</span>
                        <input x-model="draftSchedule.meetingUrl" type="url" placeholder="https://meet.google.com/..." class="min-w-0 rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                    </label>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button type="button" @click="showScheduleModal = false" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Batal</button>
                    <button type="button" @click="submitSchedule()" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">Simpan Jadwal</button>
                </div>
            </div>
        </div>

        <div x-cloak x-show="showBroadcastModal" x-transition.opacity class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/55 px-4 py-8">
            <div @click.outside="showBroadcastModal = false" class="w-full max-w-2xl rounded-[28px] bg-white p-6 shadow-2xl dark:bg-slate-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="bootcamp-label">Broadcast Cohort</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Kirim pengumuman ke cohort aktif</h3>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Dipakai untuk reminder sesi, revisi tugas, atau info office hour tanpa pindah halaman.</p>
                    </div>
                    <button type="button" @click="showBroadcastModal = false" class="rounded-full border border-slate-200 px-3 py-1.5 text-sm font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Tutup</button>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <label class="grid gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Bootcamp</span>
                        <select x-model="draftBroadcast.bootcampId" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                            <template x-for="bootcamp in bootcamps" :key="`${bootcamp.id}-broadcast`">
                                <option :value="bootcamp.id" x-text="`${bootcamp.title} • ${bootcamp.batch}`"></option>
                            </template>
                        </select>
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Audiens</span>
                        <select x-model="draftBroadcast.audience" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200">
                            <option value="Semua peserta">Semua peserta</option>
                            <option value="Peserta tertinggal">Peserta tertinggal</option>
                            <option value="Peserta tugas akhir">Peserta tugas akhir</option>
                            <option value="Peserta hadir hari ini">Peserta hadir hari ini</option>
                        </select>
                    </label>
                </div>

                <label class="mt-4 grid gap-2">
                    <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Pesan</span>
                    <textarea x-model="draftBroadcast.message" rows="5" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 dark:border-gray-700 dark:bg-slate-950 dark:text-gray-200" placeholder="Contoh: Reminder live review malam ini jam 19.00, siapkan file revisi terbaru."></textarea>
                </label>

                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <button type="button" @click="showBroadcastModal = false" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Batal</button>
                    <button type="button" @click="submitBroadcast()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white dark:bg-white dark:text-slate-900">Kirim Broadcast</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bootcampMentorDesk(config) {
            return {
                bootcamps: Array.isArray(config.bootcamps) ? config.bootcamps : [],
                sessions: Array.isArray(config.sessions) ? config.sessions : [],
                routes: config.routes || {},
                csrfToken: config.csrfToken || '',
                scheduledSessions: Number(config.initialScheduledSessions || 0),
                activeBootcampId: (config.bootcamps && config.bootcamps[0] ? config.bootcamps[0].id : null),
                showScheduleModal: false,
                showBroadcastModal: false,
                draftSchedule: {
                    bootcampId: (config.bootcamps && config.bootcamps[0] ? config.bootcamps[0].id : ''),
                    type: 'Live Review',
                    date: '',
                    time: '09:00',
                    meetingUrl: '',
                },
                draftBroadcast: {
                    bootcampId: (config.bootcamps && config.bootcamps[0] ? config.bootcamps[0].id : ''),
                    audience: 'Semua peserta',
                    message: '',
                },

                get activeBootcamp() {
                    return this.bootcamps.find((item) => item.id === this.activeBootcampId) || this.bootcamps[0] || null;
                },

                selectBootcamp(id) {
                    this.activeBootcampId = id;
                },

                closeAllPanels() {
                    this.showScheduleModal = false;
                    this.showBroadcastModal = false;
                },

                openScheduleModal(id = null) {
                    const targetId = id || this.activeBootcampId || (this.bootcamps[0] ? this.bootcamps[0].id : '');
                    this.selectBootcamp(targetId);
                    this.draftSchedule = {
                        bootcampId: targetId,
                        type: 'Live Review',
                        date: '',
                        time: '09:00',
                        meetingUrl: '',
                    };
                    this.showScheduleModal = true;
                },
                async submitSchedule() {
                    const bootcamp = this.bootcamps.find((item) => item.id === this.draftSchedule.bootcampId);

                    if (!bootcamp || !this.draftSchedule.date || !this.draftSchedule.time || !this.draftSchedule.meetingUrl) {
                        this.notify('Lengkapi bootcamp, tanggal, jam, dan link Zoom/Google Meet terlebih dulu.', 'warning', 'Form Belum Lengkap');
                        return;
                    }

                    if (!this.routes.scheduleStore) {
                        this.notify('Endpoint jadwal bootcamp belum tersedia.', 'error', 'Aksi Gagal');
                        return;
                    }

                    try {
                        const response = await fetch(this.routes.scheduleStore, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({
                                id_bootcamp: Number(this.draftSchedule.bootcampId),
                                session_type: this.draftSchedule.type,
                                date: this.draftSchedule.date,
                                time: this.draftSchedule.time,
                                meeting_url: this.draftSchedule.meetingUrl,
                            }),
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok || !payload?.success) {
                            const validationError = payload?.errors
                                ? Object.values(payload.errors).flat().join(' ')
                                : '';
                            throw new Error(validationError || payload?.message || 'Gagal menyimpan jadwal sesi.');
                        }

                        if (payload?.data?.session) {
                            this.sessions.unshift(payload.data.session);
                        }

                        this.scheduledSessions += 1;
                        this.activeBootcampId = bootcamp.id;
                        this.showScheduleModal = false;

                        this.notify(payload?.message || `Sesi baru untuk ${bootcamp.title} berhasil disimpan.`, 'success', 'Jadwal Disimpan');

                        this.$nextTick(() => {
                            this.$refs.deliveryQueue?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });
                    } catch (error) {
                        this.notify(error?.message || 'Gagal menyimpan jadwal sesi.', 'error', 'Aksi Gagal');
                    }
                },

                openBroadcastModal(id = null) {
                    const targetId = id || this.activeBootcampId || (this.bootcamps[0] ? this.bootcamps[0].id : '');
                    this.selectBootcamp(targetId);
                    this.draftBroadcast = {
                        bootcampId: targetId,
                        audience: 'Semua peserta',
                        message: '',
                    };
                    this.showBroadcastModal = true;
                },

                submitBroadcast() {
                    const bootcamp = this.bootcamps.find((item) => item.id === this.draftBroadcast.bootcampId);

                    if (!bootcamp || !this.draftBroadcast.message.trim()) {
                        this.notify('Isi pesan broadcast sebelum dikirim.', 'warning', 'Pesan Masih Kosong');
                        return;
                    }

                    this.activeBootcampId = bootcamp.id;
                    this.showBroadcastModal = false;

                    this.notify(
                        `Broadcast untuk ${this.draftBroadcast.audience.toLowerCase()} di ${bootcamp.batch} siap dikirim.`,
                        'success',
                        'Broadcast Dibuat'
                    );
                },

                openDelivery(id) {
                    const bootcamp = this.bootcamps.find((item) => item.id === id);

                    if (!bootcamp) {
                        return;
                    }

                    this.activeBootcampId = bootcamp.id;
                    if (!this.routes.progress) {
                        this.notify('Halaman delivery belum tersedia.', 'error', 'Aksi Gagal');
                        return;
                    }

                    const url = new URL(this.routes.progress, window.location.origin);
                    url.searchParams.set('source', 'bootcamp');
                    url.searchParams.set('mode', 'delivery');
                    url.searchParams.set('batch', bootcamp.slug || bootcamp.id);
                    url.searchParams.set('bootcamp_id', bootcamp.id);
                    window.location.href = url.toString();
                },

                openReviewPage(id = null) {
                    const bootcamp = this.bootcamps.find((item) => item.id === (id || this.activeBootcampId)) || this.bootcamps[0];

                    if (!bootcamp || !this.routes.grades) {
                        this.notify('Halaman review nilai belum tersedia.', 'error', 'Aksi Gagal');
                        return;
                    }

                    const url = new URL(this.routes.grades, window.location.origin);
                    url.searchParams.set('source', 'bootcamp');
                    url.searchParams.set('batch', bootcamp.slug || bootcamp.id);
                    url.searchParams.set('bootcamp_id', bootcamp.id);
                    window.location.href = url.toString();
                },

                openSessionChat(session) {
                    if (!this.routes.chat) {
                        this.notify(`Chat untuk sesi ${session.session} belum tersedia.`, 'info', 'Info');
                        return;
                    }

                    const url = new URL(this.routes.chat, window.location.origin);
                    url.searchParams.set('source', 'bootcamp');
                    url.searchParams.set('session', session.id);

                    const sessionBootcamp = this.bootcamps.find((item) =>
                        (session.id_bootcamp && item.id === session.id_bootcamp) || item.id === this.activeBootcampId
                    );
                    if (sessionBootcamp) {
                        url.searchParams.set('batch', sessionBootcamp.slug || sessionBootcamp.id);
                        url.searchParams.set('bootcamp_id', sessionBootcamp.id);
                    }
                    if (session.session) {
                        url.searchParams.set('session_title', session.session);
                    }

                    window.location.href = url.toString();
                },

                formatDate(rawDate) {
                    const date = new Date(rawDate);

                    if (Number.isNaN(date.getTime())) {
                        return rawDate;
                    }

                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                    });
                },

                notify(message, icon = 'info', title = 'Informasi', options = {}) {
                    if (typeof window.showAppAlert === 'function') {
                        return window.showAppAlert(message, icon, title, options);
                    }

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        return window.Swal.fire({
                            title,
                            text: message,
                            icon,
                            confirmButtonText: 'Oke',
                            ...options,
                        });
                    }

                    window.alert(message);
                    return Promise.resolve();
                },
            };
        }
    </script>
</x-layouts.dosen>


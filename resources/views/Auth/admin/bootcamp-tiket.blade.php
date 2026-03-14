@php
    $bootcampStats = [
        ['label' => 'Bootcamp Aktif', 'value' => 6, 'helper' => '3 batch berjalan minggu ini', 'tone' => 'from-sky-500 to-blue-600'],
        ['label' => 'Kuota Terisi', 'value' => '428/560', 'helper' => '76% seat occupancy', 'tone' => 'from-emerald-500 to-teal-600'],
        ['label' => 'Mentor Aktif', 'value' => 18, 'helper' => 'dosen dan mentor eksternal', 'tone' => 'from-violet-500 to-fuchsia-600'],
        ['label' => 'Pending Approval', 'value' => 9, 'helper' => 'draft, publish, dan refund', 'tone' => 'from-amber-500 to-orange-500'],
    ];

    $bootcampPrograms = [
        [
            'title' => 'Bootcamp UI/UX Product Sprint',
            'type' => 'Bootcamp',
            'batch' => 'Batch April 2026',
            'status' => 'Open Registration',
            'mentor' => '3 mentor',
            'seats' => '72 / 90 kursi',
            'price' => 'Rp 1.450.000',
            'schedule' => 'Sabtu & Minggu, 09.00 - 12.00',
            'risk' => 'Mentor cadangan belum ditetapkan',
            'accent' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:border-sky-500/20',
        ],
        [
            'title' => 'Bootcamp Data Analyst Career Track',
            'type' => 'Bootcamp',
            'batch' => 'Batch Mei 2026',
            'status' => 'Internal Review',
            'mentor' => '4 mentor',
            'seats' => '48 / 60 kursi',
            'price' => 'Rp 1.950.000',
            'schedule' => 'Senin, Rabu, Jumat, 19.00 - 21.00',
            'risk' => 'Silabus final assignment belum dikunci',
            'accent' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-500/10 dark:text-violet-300 dark:border-violet-500/20',
        ],
        [
            'title' => 'Tiket EduTech Summit 2026',
            'type' => 'Tiket Event',
            'batch' => 'Event 24 Mei 2026',
            'status' => 'Published',
            'mentor' => 'Speaker 6 sesi',
            'seats' => '310 / 400 tiket',
            'price' => 'Rp 275.000',
            'schedule' => 'Hybrid event, 08.00 - 17.00',
            'risk' => 'QR gate dan check-in onsite belum diuji',
            'accent' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:border-emerald-500/20',
        ],
    ];

    $opsBoard = [
        ['label' => 'Draft Baru', 'count' => 4, 'helper' => 'Perlu review admin sebelum publish', 'tone' => 'bg-slate-50 dark:bg-gray-900/40'],
        ['label' => 'Butuh Mentor', 'count' => 2, 'helper' => 'Batch baru belum lengkap pengajar', 'tone' => 'bg-amber-50 dark:bg-amber-500/10'],
        ['label' => 'Refund / Reschedule', 'count' => 3, 'helper' => 'Kasus peserta perlu tindak lanjut', 'tone' => 'bg-rose-50 dark:bg-rose-500/10'],
        ['label' => 'Siap Publish', 'count' => 5, 'helper' => 'Konten, jadwal, kuota sudah lengkap', 'tone' => 'bg-emerald-50 dark:bg-emerald-500/10'],
    ];

    $ticketFlows = [
        ['name' => 'Landing -> Checkout', 'value' => '64%', 'note' => 'Butuh voucher + countdown seat'],
        ['name' => 'Checkout -> Paid', 'value' => '41%', 'note' => 'Payment reminder belum otomatis'],
        ['name' => 'Paid -> Attend', 'value' => '82%', 'note' => 'Attendance gate cukup sehat'],
        ['name' => 'Attend -> Certificate', 'value' => '67%', 'note' => 'Masih tunggu logic backend publish'],
    ];

    $mentorRows = [
        ['name' => 'Rafi Akbar', 'role' => 'Lead Mentor UI/UX', 'load' => '2 batch aktif', 'status' => 'Aman'],
        ['name' => 'Anisa Paramita', 'role' => 'Data Analyst Mentor', 'load' => '1 batch + 1 webinar', 'status' => 'Perlu Backup'],
        ['name' => 'Dimas Prakoso', 'role' => 'Facilitator Onsite Event', 'load' => 'EduTech Summit', 'status' => 'Siap'],
    ];
@endphp

<x-layouts.admin title="Bootcamp & Tiket" active="bootcamp">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="absolute inset-y-0 right-0 hidden w-2/5 bg-[radial-gradient(circle_at_top_right,_rgba(59,130,246,0.18),_transparent_58%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.16),_transparent_50%)] lg:block"></div>
                <div class="relative flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-white dark:bg-white dark:text-slate-900">
                            Ticket Ops Center
                        </span>
                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">Bootcamp dan tiket dikelola dari panel admin</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-gray-400 sm:text-base">
                            Halaman ini jadi pusat operasional untuk batch, kuota, publish, mentor, dan alur penjualan. Dosen tetap fokus ke delivery dan kelas yang mereka ampu.
                        </p>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3 xl:w-[420px]">
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Bootcamp
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Buka Penjualan Tiket
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-2 8l-4-4m0 0l4-4m-4 4h14" />
                            </svg>
                            Export Batch
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($bootcampStats as $item)
                <article class="overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div class="h-1.5 bg-gradient-to-r {{ $item['tone'] }}"></div>
                    <div class="p-5">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $item['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $item['helper'] }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.45fr_0.95fr]">
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Program Aktif</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Bootcamp dan tiket yang sedang dipantau</h2>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">Frontend Preview</span>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">Admin Owned</span>
                    </div>
                </div>

                <div class="mt-5 grid gap-4">
                    @foreach ($bootcampPrograms as $program)
                        <article class="rounded-[24px] border border-slate-200 p-5 transition hover:border-slate-300 dark:border-gray-700 dark:hover:border-gray-600">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] {{ $program['accent'] }}">{{ $program['type'] }}</span>
                                        <span class="text-xs font-medium text-slate-500 dark:text-gray-400">{{ $program['batch'] }}</span>
                                    </div>
                                    <h3 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $program['title'] }}</h3>
                                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $program['schedule'] }}</p>
                                </div>
                                <div class="grid gap-2 text-right text-sm lg:min-w-[180px]">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $program['price'] }}</p>
                                    <p class="text-slate-500 dark:text-gray-400">{{ $program['seats'] }}</p>
                                    <p class="text-xs font-medium text-amber-600 dark:text-amber-300">{{ $program['risk'] }}</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Status</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $program['status'] }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Mentor</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">{{ $program['mentor'] }}</p>
                                </div>
                                <div class="rounded-2xl bg-slate-50 px-4 py-3 dark:bg-gray-900/40">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Operasi</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button type="button" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white dark:bg-white dark:text-slate-900">Kelola Batch</button>
                                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Assign Mentor</button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="space-y-6">
                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Ops Board</p>
                            <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Prioritas Operasional</h2>
                        </div>
                        <span class="rounded-full bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">9 action items</span>
                    </div>

                    <div class="mt-4 grid gap-3">
                        @foreach ($opsBoard as $item)
                            <div class="rounded-2xl border border-slate-200 {{ $item['tone'] }} px-4 py-3 dark:border-gray-700">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $item['label'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">{{ $item['helper'] }}</p>
                                    </div>
                                    <span class="text-xl font-bold text-slate-900 dark:text-white">{{ $item['count'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Funnel Tiket</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Revenue Conversion</h2>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($ticketFlows as $flow)
                            <div class="rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $flow['name'] }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">{{ $flow['note'] }}</p>
                                    </div>
                                    <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $flow['value'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm dark:border-gray-700/60 dark:bg-gray-800">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Mentor Roster</p>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">Distribusi Pengampu</h2>
                    </div>
                    <div class="mt-4 space-y-3">
                        @foreach ($mentorRows as $mentor)
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3 dark:border-gray-700">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $mentor['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-gray-400">{{ $mentor['role'] }}</p>
                                    <p class="mt-2 text-xs font-medium text-slate-600 dark:text-gray-300">{{ $mentor['load'] }}</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">{{ $mentor['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </section>
    </div>
</x-layouts.admin>

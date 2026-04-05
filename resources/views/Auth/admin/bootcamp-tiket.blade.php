@php
    $bootcampStats = $bootcampStats ?? [
        ['label' => 'Bootcamp Aktif', 'value' => 6, 'helper' => '3 batch berjalan minggu ini', 'tone' => 'from-sky-500 to-blue-600'],
        ['label' => 'Kuota Terisi', 'value' => '428/560', 'helper' => '76% seat occupancy', 'tone' => 'from-emerald-500 to-teal-600'],
        ['label' => 'Mentor Aktif', 'value' => 18, 'helper' => 'dosen dan mentor eksternal', 'tone' => 'from-violet-500 to-fuchsia-600'],
        ['label' => 'Pending Approval', 'value' => 9, 'helper' => 'draft, publish, dan refund', 'tone' => 'from-amber-500 to-orange-500'],
    ];

    $bootcampPrograms = $bootcampPrograms ?? [
        [
            'id' => 'bootcamp-uiux',
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
            'id' => 'bootcamp-data-analyst',
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
            'id' => 'tiket-edutech-summit',
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

    $opsBoard = $opsBoard ?? [
        ['label' => 'Draft Baru', 'count' => 4, 'helper' => 'Perlu review admin sebelum publish', 'tone' => 'bg-slate-50 dark:bg-gray-900/40'],
        ['label' => 'Butuh Mentor', 'count' => 2, 'helper' => 'Batch baru belum lengkap pengajar', 'tone' => 'bg-amber-50 dark:bg-amber-500/10'],
        ['label' => 'Refund / Reschedule', 'count' => 3, 'helper' => 'Kasus peserta perlu tindak lanjut', 'tone' => 'bg-rose-50 dark:bg-rose-500/10'],
        ['label' => 'Siap Publish', 'count' => 5, 'helper' => 'Konten, jadwal, kuota sudah lengkap', 'tone' => 'bg-emerald-50 dark:bg-emerald-500/10'],
    ];

    $ticketFlows = $ticketFlows ?? [
        ['name' => 'Landing -> Checkout', 'value' => '64%', 'note' => 'Butuh voucher + countdown seat'],
        ['name' => 'Checkout -> Paid', 'value' => '41%', 'note' => 'Payment reminder belum otomatis'],
        ['name' => 'Paid -> Attend', 'value' => '82%', 'note' => 'Attendance gate cukup sehat'],
        ['name' => 'Attend -> Certificate', 'value' => '67%', 'note' => 'Masih tunggu logic backend publish'],
    ];

    $mentorRows = $mentorRows ?? [
        ['name' => 'Rafi Akbar', 'role' => 'Lead Mentor UI/UX', 'load' => '2 batch aktif', 'status' => 'Aman'],
        ['name' => 'Anisa Paramita', 'role' => 'Data Analyst Mentor', 'load' => '1 batch + 1 webinar', 'status' => 'Perlu Backup'],
        ['name' => 'Dimas Prakoso', 'role' => 'Facilitator Onsite Event', 'load' => 'EduTech Summit', 'status' => 'Siap'],
    ];

    $availableMentors = $availableMentors ?? collect();
@endphp

<x-layouts.admin title="Bootcamp & Tiket" active="bootcamp">
    <div class="bootcamp-shell" id="bootcamp-ticket-page">
        <section class="bootcamp-hero">
            <div class="relative px-6 py-7 sm:px-8">
                <div class="relative flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="max-w-3xl">
                        <span class="bootcamp-badge-dark">
                            Ticket Ops Center
                        </span>
                        <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">Pusat operasional bootcamp dan tiket</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500 dark:text-gray-400 sm:text-base">
                            Semua program, batch, publish, kuota, mentor, dan alur penjualan dipantau dari sini. Tampilan dosen dibuat seragam, tetapi panel admin tetap menampung data seluruh bootcamp dan tiket yang aktif.
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full bg-slate-900 px-3 py-1.5 font-semibold text-white dark:bg-white dark:text-slate-900">All Program View</span>
                            <span class="rounded-full bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">Admin Owned</span>
                            <span class="rounded-full bg-emerald-50 px-3 py-1.5 font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">Publish + Sales</span>
                        </div>
                    </div>

                    <div class="bootcamp-toolbar xl:w-[420px]">
                        <button id="bootcampCreateBtn" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Bootcamp
                        </button>
                        <button id="ticketSalesToggleBtn" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Buka Penjualan Tiket
                        </button>
                        <button id="bootcampExportBtn" type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200 dark:hover:bg-gray-900">
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

        <section class="grid gap-6 2xl:grid-cols-[1.45fr_0.95fr]">
            <div class="bootcamp-panel p-5">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="bootcamp-label">Program Aktif</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Semua bootcamp dan tiket yang sedang dipantau</h2>
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 font-semibold text-slate-600 dark:bg-gray-900/60 dark:text-gray-300">Frontend Preview</span>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">Admin Owned</span>
                    </div>
                </div>

                <div class="mt-5 grid gap-4" id="bootcampProgramList">
                    @forelse ($bootcampPrograms as $program)
                        <article
                            class="bootcamp-program-card p-5"
                            data-bootcamp-card="true"
                            data-program-id="{{ $program['id'] }}"
                            data-program-type="{{ $program['type'] }}"
                            data-title="{{ $program['title'] }}"
                            data-batch="{{ $program['batch'] }}"
                            data-status="{{ $program['status'] }}"
                            data-status-key="{{ $program['status_key'] ?? 'draft' }}"
                            data-mentor="{{ $program['mentor'] }}"
                            data-seats="{{ $program['seats'] }}"
                            data-price="{{ $program['price'] }}"
                            data-schedule="{{ $program['schedule'] }}"
                            data-risk="{{ $program['risk'] }}"
                            data-accent="{{ $program['accent'] }}"
                            data-update-url="{{ is_numeric($program['id']) ? route('admin.bootcamp-tiket.batch.update', $program['id']) : '' }}"
                            data-assign-url="{{ is_numeric($program['id']) ? route('admin.bootcamp-tiket.mentor.assign', $program['id']) : '' }}"
                        >
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] {{ $program['accent'] }}">{{ $program['type'] }}</span>
                                        <span class="text-xs font-medium text-slate-500 dark:text-gray-400">{{ $program['batch'] }}</span>
                                    </div>
                                    <h3 class="mt-3 text-lg font-semibold text-slate-900 dark:text-white">{{ $program['title'] }}</h3>
                                    <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">{{ $program['schedule'] }}</p>
                                </div>
                                <div class="grid gap-2 text-left text-sm lg:min-w-[180px] lg:text-right">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $program['price'] }}</p>
                                    <p class="text-slate-500 dark:text-gray-400" data-card-seats="true">{{ $program['seats'] }}</p>
                                    <p class="text-xs font-medium text-amber-600 dark:text-amber-300" data-card-risk="true">{{ $program['risk'] }}</p>
                                </div>
                            </div>

                            <div class="bootcamp-mini-grid mt-4">
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Status</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white" data-card-status="true">{{ $program['status'] }}</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Mentor</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white" data-card-mentor="true">{{ $program['mentor'] }}</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Seat</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900 dark:text-white" data-card-seats="true">{{ $program['seats'] }}</p>
                                </div>
                                <div class="bootcamp-mini-stat">
                                    <p class="bootcamp-label">Operasi</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button type="button" data-action="manage-batch" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white dark:bg-white dark:text-slate-900">Kelola Batch</button>
                                        <button type="button" data-action="assign-mentor" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 dark:border-gray-700 dark:text-gray-300">Assign Mentor</button>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <article class="bootcamp-program-card p-6">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Belum ada data bootcamp</p>
                            <p class="mt-2 text-sm text-slate-500 dark:text-gray-400">Gunakan tombol <span class="font-semibold">Buat Bootcamp</span> untuk menambahkan program pertama.</p>
                        </article>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <section class="bootcamp-panel p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="bootcamp-label">Ops Board</p>
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

                <section class="bootcamp-panel p-5">
                    <div>
                        <p class="bootcamp-label">Funnel Tiket</p>
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

                <section class="bootcamp-panel p-5">
                    <div>
                        <p class="bootcamp-label">Mentor Roster</p>
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

    <div id="bootcampCreateModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"></div>
        <div class="relative flex min-h-full items-center justify-center px-4 py-6">
            <div class="w-full max-w-2xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-gray-700">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Frontend Draft</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Buat Bootcamp Baru</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Data akan ditambahkan ke daftar program secara lokal di halaman ini.</p>
                    </div>
                    <button id="bootcampModalCloseBtn" type="button" class="rounded-2xl border border-slate-200 p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="bootcampCreateForm" method="POST" action="{{ route('admin.bootcamp-tiket.store') }}" class="space-y-5 px-6 py-6">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Nama Program</span>
                            <input name="title" type="text" required placeholder="Bootcamp Product Management" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Tipe</span>
                            <select name="program_type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                                <option value="bootcamp">Bootcamp</option>
                                <option value="ticketed_event">Tiket Event</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Batch / Event</span>
                            <input name="batch" type="text" required placeholder="Batch Juni 2026" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Harga</span>
                            <input name="price" type="text" required placeholder="Rp 1.250.000" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Mentor</span>
                            <input name="mentor" type="text" required placeholder="2 mentor" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Seat</span>
                            <input name="seats" type="text" required placeholder="0 / 40 kursi" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Jadwal</span>
                        <input name="schedule" type="text" required placeholder="Selasa & Kamis, 19.00 - 21.00" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Catatan Risiko</span>
                        <textarea name="risk" rows="3" required placeholder="Contoh: mentor cadangan belum ditentukan" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white"></textarea>
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Assign Dosen (Opsional)</span>
                            <select name="mentor_user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                                <option value="">Belum di-assign</option>
                                @foreach ($availableMentors as $mentorOption)
                                    <option value="{{ $mentorOption->id }}">{{ $mentorOption->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Role Mentor</span>
                            <input name="mentor_role" type="text" placeholder="lead mentor / mentor / speaker" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
                        <button id="bootcampModalCancelBtn" type="button" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900/60">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Simpan Bootcamp
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="bootcampBatchModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"></div>
        <div class="relative flex min-h-full items-center justify-center px-4 py-6">
            <div class="w-full max-w-2xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-gray-700">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Batch Ops</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Kelola Batch</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Ubah status batch dan catatan risiko langsung dari halaman admin.</p>
                    </div>
                    <button id="bootcampBatchModalCloseBtn" type="button" class="rounded-2xl border border-slate-200 p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="bootcampBatchForm" method="POST" class="space-y-5 px-6 py-6">
                    @csrf
                    @method('PUT')
                    <div class="rounded-2xl bg-slate-50 px-4 py-4 dark:bg-gray-900/50">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white" id="batchModalProgramTitle">Program</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" id="batchModalProgramMeta">Batch</p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Status Batch</span>
                            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                                <option value="draft">Draft</option>
                                <option value="internal_review">Internal Review</option>
                                <option value="open_registration">Open Registration</option>
                                <option value="published">Published</option>
                                <option value="registration_closed">Registration Closed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Seat Batch</span>
                            <input name="seats" type="text" placeholder="72 / 90 kursi" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Catatan Risiko</span>
                        <textarea name="risk" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white"></textarea>
                    </label>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
                        <button id="bootcampBatchModalCancelBtn" type="button" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900/60">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                            Simpan Update Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="bootcampMentorModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"></div>
        <div class="relative flex min-h-full items-center justify-center px-4 py-6">
            <div class="w-full max-w-xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5 dark:border-gray-700">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Mentor Ops</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900 dark:text-white">Assign Mentor</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">Update jumlah mentor atau assign speaker untuk program yang dipilih.</p>
                    </div>
                    <button id="bootcampMentorModalCloseBtn" type="button" class="rounded-2xl border border-slate-200 p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form id="bootcampMentorForm" method="POST" class="space-y-5 px-6 py-6">
                    @csrf
                    <div class="rounded-2xl bg-slate-50 px-4 py-4 dark:bg-gray-900/50">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white" id="mentorModalProgramTitle">Program</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-gray-400" id="mentorModalProgramMeta">Batch</p>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Label Mentor</span>
                        <input name="mentor" type="text" placeholder="4 mentor" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Pilih Dosen</span>
                            <select name="mentor_user_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                                <option value="">Tidak assign user</option>
                                @foreach ($availableMentors as $mentorOption)
                                    <option value="{{ $mentorOption->id }}">{{ $mentorOption->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Role Mentor</span>
                            <input name="mentor_role" type="text" placeholder="mentor / lead mentor / speaker" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white">
                        </label>
                    </div>

                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Catatan Penugasan</span>
                        <textarea name="risk" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900/40 dark:text-white"></textarea>
                    </label>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 dark:border-gray-700 sm:flex-row sm:justify-end">
                        <button id="bootcampMentorModalCancelBtn" type="button" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-900/60">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Simpan Mentor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const createBtn = document.getElementById('bootcampCreateBtn');
                const salesToggleBtn = document.getElementById('ticketSalesToggleBtn');
                const exportBtn = document.getElementById('bootcampExportBtn');
                const modal = document.getElementById('bootcampCreateModal');
                const modalCloseBtn = document.getElementById('bootcampModalCloseBtn');
                const modalCancelBtn = document.getElementById('bootcampModalCancelBtn');
                const createForm = document.getElementById('bootcampCreateForm');
                const programList = document.getElementById('bootcampProgramList');
                const pageRoot = document.getElementById('bootcamp-ticket-page');
                const batchModal = document.getElementById('bootcampBatchModal');
                const batchModalCloseBtn = document.getElementById('bootcampBatchModalCloseBtn');
                const batchModalCancelBtn = document.getElementById('bootcampBatchModalCancelBtn');
                const batchForm = document.getElementById('bootcampBatchForm');
                const batchModalProgramTitle = document.getElementById('batchModalProgramTitle');
                const batchModalProgramMeta = document.getElementById('batchModalProgramMeta');
                const mentorModal = document.getElementById('bootcampMentorModal');
                const mentorModalCloseBtn = document.getElementById('bootcampMentorModalCloseBtn');
                const mentorModalCancelBtn = document.getElementById('bootcampMentorModalCancelBtn');
                const mentorForm = document.getElementById('bootcampMentorForm');
                const mentorModalProgramTitle = document.getElementById('mentorModalProgramTitle');
                const mentorModalProgramMeta = document.getElementById('mentorModalProgramMeta');

                if (!createBtn || !salesToggleBtn || !exportBtn || !modal || !createForm || !programList || !pageRoot || !batchModal || !batchForm || !mentorModal || !mentorForm) {
                    return;
                }

                let ticketSalesOpen = false;

                const getCards = () => Array.from(programList.querySelectorAll('[data-bootcamp-card="true"]'));

                const openModal = (targetModal) => {
                    targetModal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                };

                const closeModal = (targetModal) => {
                    targetModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                };

                const getCardData = (card) => ({
                    type: card.dataset.programType || '',
                    title: card.dataset.title || '',
                    batch: card.dataset.batch || '',
                    status: card.dataset.statusKey || 'draft',
                    mentor: card.dataset.mentor || '',
                    seats: card.dataset.seats || '',
                    risk: card.dataset.risk || '',
                    updateUrl: card.dataset.updateUrl || '',
                    assignUrl: card.dataset.assignUrl || '',
                });

                const updateTicketSalesButton = () => {
                    salesToggleBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-600/20', 'bg-amber-500', 'hover:bg-amber-600', 'shadow-amber-500/20');
                    if (ticketSalesOpen) {
                        salesToggleBtn.classList.add('bg-amber-500', 'hover:bg-amber-600', 'shadow-amber-500/20');
                        salesToggleBtn.innerHTML = `
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M9 9l6 6" />
                            </svg>
                            Tutup Penjualan Tiket
                        `;
                        return;
                    }

                    salesToggleBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-emerald-600/20');
                    salesToggleBtn.innerHTML = `
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Buka Penjualan Tiket
                    `;
                };

                const updateTicketCards = () => {
                    getCards()
                        .filter((card) => card.dataset.programType === 'Tiket Event')
                        .forEach((card) => {
                            const statusNode = card.querySelector('[data-card-status="true"]');
                            if (statusNode) {
                                statusNode.textContent = ticketSalesOpen ? 'Published' : 'Registration Closed';
                            }
                        });
                };

                const openBatchModal = (card) => {
                    const data = getCardData(card);
                    if (!data.updateUrl) {
                        return;
                    }

                    batchModalProgramTitle.textContent = data.title;
                    batchModalProgramMeta.textContent = `${data.batch} • ${data.type}`;
                    batchForm.action = data.updateUrl;
                    batchForm.elements.status.value = data.status;
                    batchForm.elements.seats.value = data.seats;
                    batchForm.elements.risk.value = data.risk;
                    openModal(batchModal);
                };

                const openMentorModal = (card) => {
                    const data = getCardData(card);
                    if (!data.assignUrl) {
                        return;
                    }

                    mentorModalProgramTitle.textContent = data.title;
                    mentorModalProgramMeta.textContent = `${data.batch} • ${data.type}`;
                    mentorForm.action = data.assignUrl;
                    mentorForm.elements.mentor.value = data.mentor;
                    mentorForm.elements.risk.value = data.risk;
                    openModal(mentorModal);
                };

                const exportPrograms = () => {
                    const rows = getCards().map((card) => ({
                        title: card.dataset.title || '',
                        type: card.dataset.programType || '',
                        batch: card.dataset.batch || '',
                        status: card.dataset.status || '',
                        mentor: card.dataset.mentor || '',
                        seats: card.dataset.seats || '',
                        price: card.dataset.price || '',
                        schedule: card.dataset.schedule || '',
                        risk: card.dataset.risk || '',
                    }));

                    const header = ['Program', 'Tipe', 'Batch', 'Status', 'Mentor', 'Seat', 'Harga', 'Jadwal', 'Risiko'];
                    const csvLines = [
                        header.join(','),
                        ...rows.map((row) => [
                            row.title,
                            row.type,
                            row.batch,
                            row.status,
                            row.mentor,
                            row.seats,
                            row.price,
                            row.schedule,
                            row.risk,
                        ].map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')),
                    ];

                    const blob = new Blob([csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `bootcamp-batch-${new Date().toISOString().slice(0, 10)}.csv`;
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    URL.revokeObjectURL(link.href);

                    
                };

                createBtn.addEventListener('click', () => openModal(modal));
                modalCloseBtn?.addEventListener('click', () => closeModal(modal));
                modalCancelBtn?.addEventListener('click', () => closeModal(modal));
                batchModalCloseBtn?.addEventListener('click', () => closeModal(batchModal));
                batchModalCancelBtn?.addEventListener('click', () => closeModal(batchModal));
                mentorModalCloseBtn?.addEventListener('click', () => closeModal(mentorModal));
                mentorModalCancelBtn?.addEventListener('click', () => closeModal(mentorModal));

                [modal, batchModal, mentorModal].forEach((targetModal) => {
                    targetModal.addEventListener('click', (event) => {
                        if (event.target === targetModal || event.target === targetModal.firstElementChild) {
                            closeModal(targetModal);
                        }
                    });
                });

                programList.addEventListener('click', (event) => {
                    const button = event.target.closest('button[data-action]');
                    const card = event.target.closest('[data-bootcamp-card="true"]');

                    if (!button || !card) {
                        return;
                    }

                    if (button.dataset.action === 'manage-batch') {
                        openBatchModal(card);
                    }

                    if (button.dataset.action === 'assign-mentor') {
                        openMentorModal(card);
                    }
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key !== 'Escape') {
                        return;
                    }

                    [modal, batchModal, mentorModal].forEach((targetModal) => {
                        if (!targetModal.classList.contains('hidden')) {
                            closeModal(targetModal);
                        }
                    });
                });

                salesToggleBtn.addEventListener('click', () => {
                    ticketSalesOpen = !ticketSalesOpen;
                    updateTicketSalesButton();
                    updateTicketCards();

                    
                });

                exportBtn.addEventListener('click', exportPrograms);

                updateTicketSalesButton();
                updateTicketCards();
            });
        </script>
    @endpush
</x-layouts.admin>




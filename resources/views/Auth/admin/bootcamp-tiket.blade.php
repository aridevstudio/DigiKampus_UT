@php
    $bootcampStats = $bootcampStats ?? [];
    $bootcampPrograms = $bootcampPrograms ?? [];
    $opsBoard = $opsBoard ?? [];
    $ticketFlows = $ticketFlows ?? [];
    $mentorRows = $mentorRows ?? [];
    $availableMentors = $availableMentors ?? collect();
    $externalMentors = $externalMentors ?? collect();
    $statusOptions = [
        'draft' => 'Draft',
        'internal_review' => 'Internal Review',
        'open_registration' => 'Open Registration',
        'published' => 'Published',
        'registration_closed' => 'Registration Closed',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'archived' => 'Archived',
    ];
@endphp

<x-layouts.admin title="Bootcamp & Tiket" active="bootcamp">
    <div class="space-y-6" id="bootcamp-ticket-page" data-action-base="/admin/bootcamp-tiket" data-external-mentor-url="{{ route('admin.bootcamp-tiket.external-mentors.store', [], false) }}">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <span class="inline-flex rounded-full bg-slate-900 px-3 py-1 text-xs font-bold uppercase tracking-[0.22em] text-white">Ticket Ops Center</span>
                    <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Pusat operasional bootcamp dan tiket</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                        Kelola program, batch/event, publish, kuota, mentor, dan export operasional dari data backend. Tiket atau bootcamp dapat dibuka untuk penjualan setelah statusnya Open Registration atau Published.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 xl:w-[460px]">
                    <button type="button" data-modal-open="create" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">+ Buat Bootcamp</button>
                    <button type="button" data-focus-ticket class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">Atur Tiket</button>
                    <a href="{{ route('admin.bootcamp-tiket.export', [], false) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white">Export Batch</a>
                </div>
            </div>
        </section>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-semibold">Data belum bisa disimpan.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($bootcampStats as $item)
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-1.5 bg-gradient-to-r {{ $item['tone'] ?? 'from-blue-500 to-blue-600' }}"></div>
                    <div class="p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">{{ $item['label'] ?? '-' }}</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ $item['value'] ?? 0 }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $item['helper'] ?? '' }}</p>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 2xl:grid-cols-[1.45fr_0.95fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 pb-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">Program Aktif</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Semua bootcamp dan tiket yang sedang dipantau</h2>
                    </div>
                    <p class="text-sm text-slate-500">Data berasal dari backend.</p>
                </div>
                <div class="mt-5 grid gap-4" id="bootcampProgramList">
                    @forelse ($bootcampPrograms as $program)
                        <article class="bootcamp-program-card rounded-3xl border border-slate-200 bg-white p-5 transition hover:border-blue-200 hover:shadow-md"
                            data-bootcamp-card="true"
                            data-program-id="{{ $program['id'] }}"
                            data-program-type="{{ $program['program_type'] ?? 'bootcamp' }}"
                            data-title="{{ $program['title'] }}"
                            data-batch="{{ $program['batch'] }}"
                            data-status-key="{{ $program['status_key'] ?? 'draft' }}"
                            data-status="{{ $program['status'] }}"
                            data-mentor="{{ $program['mentor'] }}"
                            data-seats="{{ $program['seats'] }}"
                            data-seat-total="{{ $program['seat_total'] ?? 0 }}"
                            data-price="{{ $program['price'] }}"
                            data-schedule="{{ $program['schedule'] }}"
                            data-risk="{{ $program['risk'] }}"
                            data-mentor-ids='@json($program["mentor_ids"] ?? [])'
                            data-external-mentor-ids='@json($program["external_mentor_ids"] ?? [])'>
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] {{ $program['accent'] ?? 'bg-sky-50 text-sky-700 border-sky-200' }}">{{ $program['type'] }}</span>
                                        <span class="text-xs font-medium text-slate-500">{{ $program['batch'] }}</span>
                                    </div>
                                    <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $program['title'] }}</h3>
                                    <p class="mt-2 text-sm text-slate-500">{{ $program['schedule'] }}</p>
                                    @if (!empty($program['badges']))
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach ($program['badges'] as $badge)
                                                <span class="rounded-full px-3 py-1 text-[11px] font-semibold {{ $badge['tone'] ?? 'bg-slate-100 text-slate-600' }}">{{ $badge['label'] }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="grid gap-2 text-left text-sm lg:min-w-[180px] lg:text-right">
                                    <p class="font-semibold text-slate-900">{{ $program['price'] }}</p>
                                    <p class="text-slate-500">{{ $program['seats'] }}</p>
                                    <p class="text-xs font-medium text-amber-600 whitespace-pre-line">{{ $program['risk'] }}</p>
                                </div>
                            </div>
                            <div class="mt-4 grid gap-3 md:grid-cols-4">
                                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Status</p><p class="mt-2 text-sm font-semibold text-slate-900">{{ $program['status'] }}</p></div>
                                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Mentor</p><p class="mt-2 text-sm font-semibold text-slate-900">{{ $program['mentor'] }}</p></div>
                                <div class="rounded-2xl bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Seat</p><p class="mt-2 text-sm font-semibold text-slate-900">{{ $program['seats'] }}</p></div>
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Operasi</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <button type="button" data-modal-open="batch" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Kelola Batch</button>
                                        <button type="button" data-modal-open="mentor" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600">Assign Mentor</button>
                                        @if (in_array($program['status_key'] ?? 'draft', ['open_registration', 'published'], true))
                                            <form method="POST" action="{{ route('admin.bootcamp-tiket.sales.close', $program['id'], false) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white">Tutup Penjualan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.bootcamp-tiket.sales.open', $program['id'], false) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Buka Penjualan</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.bootcamp-tiket.delete', $program['id'], false) }}" onsubmit="return confirmDeleteProgram(event, this);">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                            <p class="font-semibold text-slate-900">Belum ada program bootcamp atau tiket.</p>
                            <p class="mt-2 text-sm text-slate-500">Klik Buat Bootcamp untuk membuat draft pertama dari backend.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">Ops Board</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Prioritas Operasional</h2>
                    <div class="mt-4 grid gap-3">
                        @foreach ($opsBoard as $item)
                            <div class="rounded-2xl border border-slate-200 {{ $item['tone'] ?? 'bg-slate-50' }} px-4 py-3">
                                <div class="flex items-start justify-between gap-3"><div><p class="text-sm font-semibold text-slate-900">{{ $item['label'] }}</p><p class="mt-1 text-xs text-slate-500">{{ $item['helper'] }}</p></div><span class="text-xl font-bold text-slate-900">{{ $item['count'] }}</span></div>
                            </div>
                        @endforeach
                    </div>
                </section>
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">Funnel Tiket</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Revenue Conversion</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($ticketFlows as $flow)
                            <div class="rounded-2xl border border-slate-200 px-4 py-3"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-semibold text-slate-900">{{ $flow['name'] }}</p><p class="mt-1 text-xs text-slate-500">{{ $flow['note'] }}</p></div><span class="text-lg font-bold text-slate-900">{{ $flow['value'] }}</span></div></div>
                        @endforeach
                    </div>
                </section>
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">Mentor Roster</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">Distribusi Pengampu</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($mentorRows as $mentor)
                            <div class="flex items-start justify-between gap-3 rounded-2xl border border-slate-200 px-4 py-3"><div><p class="text-sm font-semibold text-slate-900">{{ $mentor['name'] }}</p><p class="mt-1 text-xs text-slate-500">{{ $mentor['role'] }}</p><p class="mt-2 text-xs font-medium text-slate-600">{{ $mentor['load'] }}</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">{{ $mentor['status'] }}</span></div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">Belum ada mentor yang ditugaskan.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </div>
    <div id="bootcampCreateModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" data-modal-close="create"></div>
        <div class="relative flex min-h-full items-start justify-center px-4 py-4 sm:items-center sm:py-6">
            <div class="flex w-full max-w-3xl max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Backend Form</p><h2 class="mt-2 text-xl font-semibold text-slate-900">Buat Bootcamp / Tiket Baru</h2><p class="mt-1 text-sm text-slate-500">Data disimpan ke database sebagai draft.</p></div><button type="button" data-modal-close="create" class="rounded-2xl border border-slate-200 p-2 text-slate-500">X</button></div>
                <form method="POST" action="{{ route('admin.bootcamp-tiket.store', [], false) }}" class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Nama Program</span><input name="title" type="text" required value="{{ old('title') }}" placeholder="Bootcamp Product Management" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tipe</span><select name="program_type" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"><option value="bootcamp" @selected(old('program_type') === 'bootcamp')>Bootcamp</option><option value="ticketed_event" @selected(old('program_type') === 'ticketed_event')>Tiket Event</option></select></label>
                        <label class="block"><span data-batch-label class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Nama Batch</span><input name="batch" data-batch-input type="text" required value="{{ old('batch') }}" placeholder="Batch Juni 2026" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"><p class="mt-1 text-xs text-slate-500">Label otomatis menyesuaikan tipe program.</p></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Harga</span><input name="price" type="text" required value="{{ old('price') }}" data-rupiah-input placeholder="Rp 1.250.000" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Kapasitas Seat</span><input name="seat_capacity" type="number" required min="1" max="100000" value="{{ old('seat_capacity') }}" placeholder="40" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"><p class="mt-1 text-xs text-slate-500">Hanya angka. Disimpan sebagai 0 / kapasitas kursi.</p></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tanggal</span><input name="schedule_date" type="date" required value="{{ old('schedule_date') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Jam Mulai</span><input name="schedule_start_time" type="time" required value="{{ old('schedule_start_time') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label>
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Jam Selesai</span><input name="schedule_end_time" type="time" value="{{ old('schedule_end_time') }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label>
                        <label class="block sm:col-span-2"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Template Sertifikat</span><select name="certificate_template_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"><option value="">-- Pilih Template Sertifikat --</option>@foreach($certificateTemplates as $template)<option value="{{ $template->id }}" @selected(old('certificate_template_id') == $template->id)>{{ $template->name }}</option>@endforeach</select></label>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-4"><div class="grid gap-4 md:grid-cols-2">
                        <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mentor Internal</span><select name="mentor_user_ids[]" multiple size="6" data-enhanced-multiselect data-placeholder="Cari dosen aktif..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">@foreach ($availableMentors as $mentor)<option value="{{ $mentor->id }}" @selected(in_array($mentor->id, old('mentor_user_ids', [])))>{{ $mentor->name }}</option>@endforeach</select><p class="mt-1 text-xs text-slate-500">Sumber data dari user role dosen. Admin tidak ditampilkan.</p></label>
                        <div class="space-y-4"><label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Role Mentor</span><input name="mentor_role" type="text" value="{{ old('mentor_role', 'mentor') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm"></label><label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mentor Eksternal Tersimpan</span><select name="external_mentor_ids[]" multiple size="4" data-enhanced-multiselect data-placeholder="Cari mentor eksternal..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">@foreach ($externalMentors as $mentor)<option value="{{ $mentor->id_external_mentor }}" @selected(in_array($mentor->id_external_mentor, old('external_mentor_ids', [])))>{{ $mentor->name }}{{ $mentor->expertise ? ' - ' . $mentor->expertise : '' }}</option>@endforeach</select></label><div class="rounded-2xl border border-dashed border-slate-300 bg-white p-3" data-external-mentor-form><div class="mb-3 flex items-center justify-between gap-3"><p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tambah Mentor Eksternal Baru</p><button type="button" data-external-mentor-save class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Tambah</button></div><div class="grid gap-2"><input data-external-field="external_name" type="text" placeholder="Nama mentor luar" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_email" type="email" placeholder="Email opsional" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_expertise" type="text" placeholder="Keahlian / topik" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_institution" type="text" placeholder="Institusi opsional" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"></div><p data-external-mentor-message class="mt-2 hidden text-xs font-medium"></p></div></div>
                    </div></div>
                    <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Catatan Risiko</span><textarea name="risk" rows="3" placeholder="Contoh: mentor cadangan belum ditentukan" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">{{ old('risk') }}</textarea></label>
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"><button type="button" data-modal-close="create" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button><button type="submit" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Simpan Draft</button></div>
                </form>
            </div>
        </div>
    </div>
    <div id="bootcampBatchModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" data-modal-close="batch"></div>
        <div class="relative flex min-h-full items-start justify-center px-4 py-4 sm:items-center sm:py-6"><div class="flex w-full max-w-xl max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Batch Ops</p><h2 class="mt-2 text-xl font-semibold text-slate-900">Kelola Batch</h2><p class="mt-1 text-sm text-slate-500" id="batchModalProgramMeta">Program</p></div><button type="button" data-modal-close="batch" class="rounded-2xl border border-slate-200 p-2 text-slate-500">X</button></div>
            <form id="bootcampBatchForm" method="POST" class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Status Batch</span><select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">@foreach ($statusOptions as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></label>
                    <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Kapasitas Seat</span><input name="seat_capacity" type="number" required min="1" max="100000" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label>
                </div>
                <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Catatan Risiko</span><textarea name="risk" rows="4" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></textarea></label>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"><button type="button" data-modal-close="batch" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button><button type="submit" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white">Simpan Update Batch</button></div>
            </form>
        </div></div>
    </div>

    <div id="bootcampMentorModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" data-modal-close="mentor"></div>
        <div class="relative flex min-h-full items-start justify-center px-4 py-4 sm:items-center sm:py-6"><div class="flex w-full max-w-2xl max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Mentor Ops</p><h2 class="mt-2 text-xl font-semibold text-slate-900">Assign Mentor</h2><p class="mt-1 text-sm text-slate-500" id="mentorModalProgramMeta">Program</p></div><button type="button" data-modal-close="mentor" class="rounded-2xl border border-slate-200 p-2 text-slate-500">X</button></div>
            <form id="bootcampMentorForm" method="POST" class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                @csrf
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mentor Internal</span><select name="mentor_user_ids[]" multiple size="7" data-enhanced-multiselect data-placeholder="Cari dosen aktif..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">@foreach ($availableMentors as $mentor)<option value="{{ $mentor->id }}">{{ $mentor->name }}</option>@endforeach</select><p class="mt-1 text-xs text-slate-500">Hanya role dosen. Mentor luar isi sebelah kanan.</p></label>
                    <div class="space-y-4"><label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Role Mentor</span><input name="mentor_role" type="text" value="mentor" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></label><label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mentor Eksternal Tersimpan</span><select name="external_mentor_ids[]" multiple size="4" data-enhanced-multiselect data-placeholder="Cari mentor eksternal..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">@foreach ($externalMentors as $mentor)<option value="{{ $mentor->id_external_mentor }}">{{ $mentor->name }}{{ $mentor->expertise ? ' - ' . $mentor->expertise : '' }}</option>@endforeach</select></label><div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-3" data-external-mentor-form><div class="mb-3 flex items-center justify-between gap-3"><p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tambah Mentor Eksternal Baru</p><button type="button" data-external-mentor-save class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">Tambah</button></div><div class="grid gap-2"><input data-external-field="external_name" type="text" placeholder="Nama mentor luar" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_email" type="email" placeholder="Email opsional" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_expertise" type="text" placeholder="Keahlian / topik" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"><input data-external-field="external_institution" type="text" placeholder="Institusi opsional" class="rounded-xl border border-slate-200 px-3 py-2 text-sm"></div><p data-external-mentor-message class="mt-2 hidden text-xs font-medium"></p></div></div>
                </div>
                <label class="block"><span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Catatan Penugasan</span><textarea name="risk" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></textarea></label>
                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end"><button type="button" data-modal-close="mentor" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button><button type="submit" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Simpan Mentor</button></div>
            </form>
        </div></div>
    </div>
    @push('scripts')
        <script>
            function confirmDeleteProgram(event, form) {
                event.preventDefault();
                if (window.Swal && typeof window.Swal.fire === 'function') {
                    window.Swal.fire({
                        title: 'Hapus program ini?',
                        text: 'Data yang sudah punya peserta atau transaksi tidak bisa dihapus.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Hapus bootcamp/tiket ini? Data yang sudah punya peserta atau transaksi tidak bisa dihapus.')) {
                        form.submit();
                    }
                }
                return false;
            }

            document.addEventListener('DOMContentLoaded', () => {
                const root = document.getElementById('bootcamp-ticket-page');
                if (!root) return;
                const actionBase = root.dataset.actionBase;
                const externalMentorUrl = root.dataset.externalMentorUrl;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                const modals = { create: document.getElementById('bootcampCreateModal'), batch: document.getElementById('bootcampBatchModal'), mentor: document.getElementById('bootcampMentorModal') };
                const batchForm = document.getElementById('bootcampBatchForm');
                const mentorForm = document.getElementById('bootcampMentorForm');
                const batchMeta = document.getElementById('batchModalProgramMeta');
                const mentorMeta = document.getElementById('mentorModalProgramMeta');
                const openModal = (name) => { modals[name]?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
                const closeModal = (name) => { modals[name]?.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };
                const refreshEnhancedSelect = (select) => {
                    if (!select || typeof select._refreshEnhancedSelect !== 'function') return;
                    select._refreshEnhancedSelect();
                };
                const initEnhancedSelect = (select) => {
                    if (select.dataset.enhancedReady === '1') return;
                    select.dataset.enhancedReady = '1';

                    const wrapper = document.createElement('div');
                    wrapper.dataset.enhancedSelect = 'true';
                    wrapper.className = 'space-y-2';
                    const chips = document.createElement('div');
                    chips.className = 'flex min-h-10 flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2';
                    const search = document.createElement('input');
                    search.type = 'search';
                    search.placeholder = select.dataset.placeholder || 'Cari dan pilih data...';
                    search.className = 'w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20';
                    const optionsBox = document.createElement('div');
                    optionsBox.className = 'max-h-52 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm';
                    wrapper.append(chips, search, optionsBox);
                    select.insertAdjacentElement('afterend', wrapper);
                    select.classList.add('hidden');

                    const render = () => {
                        const selectedOptions = Array.from(select.selectedOptions);
                        chips.innerHTML = '';
                        if (selectedOptions.length === 0) {
                            const empty = document.createElement('span');
                            empty.className = 'text-xs text-slate-400';
                            empty.textContent = 'Belum ada mentor dipilih';
                            chips.appendChild(empty);
                        }
                        selectedOptions.forEach((option) => {
                            const chip = document.createElement('button');
                            chip.type = 'button';
                            chip.dataset.removeValue = option.value;
                            chip.className = 'inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700';
                            chip.textContent = option.textContent.trim();
                            const close = document.createElement('span');
                            close.textContent = 'x';
                            close.className = 'text-blue-400';
                            chip.appendChild(close);
                            chips.appendChild(chip);
                        });

                        const term = search.value.trim().toLowerCase();
                        const options = Array.from(select.options).filter((option) => option.textContent.toLowerCase().includes(term));
                        optionsBox.innerHTML = '';
                        if (options.length === 0) {
                            const empty = document.createElement('p');
                            empty.className = 'px-3 py-2 text-sm text-slate-400';
                            empty.textContent = 'Tidak ada data yang cocok';
                            optionsBox.appendChild(empty);
                            return;
                        }
                        options.forEach((option) => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.dataset.optionValue = option.value;
                            item.className = `mb-1 flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm transition ${option.selected ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-50'}`;
                            const label = document.createElement('span');
                            label.textContent = option.textContent.trim();
                            const marker = document.createElement('span');
                            marker.textContent = option.selected ? 'Dipilih' : '+';
                            marker.className = 'text-xs font-semibold';
                            item.append(label, marker);
                            optionsBox.appendChild(item);
                        });
                    };

                    wrapper.addEventListener('click', (event) => {
                        const optionButton = event.target.closest('[data-option-value]');
                        const removeButton = event.target.closest('[data-remove-value]');
                        if (optionButton) {
                            const option = Array.from(select.options).find((item) => item.value === optionButton.dataset.optionValue);
                            if (option) option.selected = !option.selected;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                        if (removeButton) {
                            const option = Array.from(select.options).find((item) => item.value === removeButton.dataset.removeValue);
                            if (option) option.selected = false;
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                    search.addEventListener('input', render);
                    select.addEventListener('change', render);
                    select._refreshEnhancedSelect = render;
                    render();
                };

                document.querySelectorAll('select[data-enhanced-multiselect]').forEach(initEnhancedSelect);

                const setExternalMentorMessage = (panel, message, isError = false) => {
                    const messageBox = panel?.querySelector('[data-external-mentor-message]');
                    if (!messageBox) return;
                    messageBox.textContent = message;
                    messageBox.classList.remove('hidden', 'text-emerald-600', 'text-rose-600');
                    messageBox.classList.add(isError ? 'text-rose-600' : 'text-emerald-600');
                };

                const addExternalMentorToSelects = (mentor, sourcePanel) => {
                    const value = String(mentor.id);
                    const label = mentor.label || mentor.name;
                    document.querySelectorAll('select[name="external_mentor_ids[]"]').forEach((select) => {
                        let option = Array.from(select.options).find((item) => item.value === value);
                        if (!option) {
                            option = new Option(label, value, false, false);
                            select.add(option);
                        } else {
                            option.textContent = label;
                        }

                        if (sourcePanel?.closest('.space-y-4')?.contains(select)) {
                            option.selected = true;
                        }

                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                };

                document.querySelectorAll('[data-external-mentor-save]').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const panel = button.closest('[data-external-mentor-form]');
                        if (!panel || !externalMentorUrl) return;

                        const payload = {};
                        panel.querySelectorAll('[data-external-field]').forEach((input) => {
                            payload[input.dataset.externalField] = input.value.trim();
                        });

                        if (!payload.external_name) {
                            setExternalMentorMessage(panel, 'Nama mentor wajib diisi.', true);
                            return;
                        }

                        button.disabled = true;
                        const originalLabel = button.textContent;
                        button.textContent = 'Menyimpan...';

                        try {
                            const response = await fetch(externalMentorUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify(payload),
                            });
                            const data = await response.json().catch(() => ({}));

                            if (!response.ok) {
                                const errors = data.errors ? Object.values(data.errors).flat() : [];
                                throw new Error(errors[0] || data.message || 'Mentor eksternal gagal disimpan.');
                            }

                            addExternalMentorToSelects(data.mentor, panel);
                            panel.querySelectorAll('[data-external-field]').forEach((input) => input.value = '');
                            setExternalMentorMessage(panel, data.message || 'Mentor eksternal berhasil ditambahkan.');
                        } catch (error) {
                            setExternalMentorMessage(panel, error.message || 'Mentor eksternal gagal disimpan.', true);
                        } finally {
                            button.disabled = false;
                            button.textContent = originalLabel;
                        }
                    });
                });

                document.querySelectorAll('[data-modal-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const name = button.dataset.modalOpen;
                        const card = button.closest('[data-bootcamp-card="true"]');
                        if (name === 'batch' && card && batchForm) {
                            batchForm.action = `${actionBase}/${card.dataset.programId}/batch`;
                            batchForm.status.value = card.dataset.statusKey || 'draft';
                            batchForm.seat_capacity.value = card.dataset.seatTotal || '';
                            batchForm.risk.value = card.dataset.risk || '';
                            batchMeta.textContent = `${card.dataset.title || 'Program'} - ${card.dataset.batch || ''}`;
                        }
                        if (name === 'mentor' && card && mentorForm) {
                            mentorForm.action = `${actionBase}/${card.dataset.programId}/assign-mentor`;
                            mentorForm.risk.value = card.dataset.risk || '';

                            mentorMeta.textContent = `${card.dataset.title || 'Program'} - ${card.dataset.batch || ''}`;
                            const selectedIds = JSON.parse(card.dataset.mentorIds || '[]').map(String);
                            Array.from(mentorForm.querySelectorAll('select[name="mentor_user_ids[]"] option')).forEach((option) => option.selected = selectedIds.includes(option.value));
                            const selectedExternalIds = JSON.parse(card.dataset.externalMentorIds || '[]').map(String);
                            Array.from(mentorForm.querySelectorAll('select[name="external_mentor_ids[]"] option')).forEach((option) => option.selected = selectedExternalIds.includes(option.value));
                            mentorForm.querySelectorAll('select[data-enhanced-multiselect]').forEach(refreshEnhancedSelect);
                        }
                        openModal(name);
                    });
                });

                document.querySelectorAll('[data-modal-close]').forEach((button) => button.addEventListener('click', () => closeModal(button.dataset.modalClose)));
                document.querySelectorAll('[data-rupiah-input]').forEach((input) => input.addEventListener('input', () => {
                    const number = input.value.replace(/\D/g, '');
                    input.value = number ? `Rp ${new Intl.NumberFormat('id-ID').format(Number(number))}` : '';
                }));
                document.querySelectorAll('select[name="program_type"]').forEach((select) => {
                    const form = select.closest('form');
                    const label = form?.querySelector('[data-batch-label]');
                    const input = form?.querySelector('[data-batch-input]');
                    const updateLabel = () => {
                        const isTicket = select.value === 'ticketed_event';
                        if (label) label.textContent = isTicket ? 'Nama Event' : 'Nama Batch';
                        if (input) input.placeholder = isTicket ? 'Seminar EduTech 2026' : 'Batch Juni 2026';
                    };
                    select.addEventListener('change', updateLabel);
                    updateLabel();
                });
                document.querySelector('[data-focus-ticket]')?.addEventListener('click', () => {
                    const ticketCard = document.querySelector('[data-program-type="ticketed_event"]');
                    if (!ticketCard) {
                        const createForm = modals.create?.querySelector('form');
                        const typeSelect = createForm?.querySelector('select[name="program_type"]');
                        if (typeSelect) {
                            typeSelect.value = 'ticketed_event';
                            typeSelect.dispatchEvent(new Event('change'));
                        }
                        openModal('create');
                        return;
                    }
                    ticketCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    ticketCard.classList.add('ring-4', 'ring-emerald-200');
                    window.setTimeout(() => ticketCard.classList.remove('ring-4', 'ring-emerald-200'), 1800);
                });
            });
        </script>
    @endpush
</x-layouts.admin>

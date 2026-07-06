@php
    use Carbon\Carbon;
    $sessions = $sessions ?? collect();
    $course = $course ?? null;
    $linkedBootcamp = $linkedBootcamp ?? null;
    $tipeOptions = $tipeOptions ?? [];
    $modeOptions = $modeOptions ?? [];
@endphp

<x-layouts.admin title="Kelola Sesi Bootcamp" active="bootcamp">
    <div class="space-y-6" id="kelola-sesi-page">
        {{-- Page Header --}}
        <header class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="max-w-3xl">
                    <a href="{{ route('admin.bootcamp-tiket', [], false) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">← Kembali ke Bootcamp & Tiket</a>
                    <span class="mt-3 inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.22em] text-indigo-700 border border-indigo-200">Session Builder</span>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $course->nama_course ?? 'Bootcamp' }}</h1>
                    @if ($linkedBootcamp)
                        <p class="mt-1 text-sm text-slate-500">Linked event: <span class="font-semibold">{{ $linkedBootcamp->title }}</span></p>
                    @endif
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                        Setiap sesi bersifat individual: judul, jadwal, link meeting, materi. Status sesi otomatis terhitung dari tanggal & jam (upcoming / live / ended).
                    </p>
                    <p class="mt-2 max-w-2xl text-xs leading-5 text-slate-500">
                        Mode default sesi: <span class="font-semibold">{{ strtoupper($currentMode ?? 'online') }}</span> — diwariskan dari Pengaturan Event. Klik "Tambah Sesi" lalu gunakan dropdown Mode Sesi untuk override per-sesi bila diperlukan.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 xl:w-[460px]">
                    <a href="{{ route('admin.bootcamp-tiket', [], false) }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-white text-center">Kembali</a>
                    <button type="button" data-modal-open="course-settings" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:bg-slate-800">Pengaturan Event</button>
                    <button type="button" data-modal-open="new-sesi" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700">+ Tambah Sesi</button>
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                <p class="font-semibold">Data sesi belum bisa disimpan.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Sessions Table --}}
        <section class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 border-b border-slate-200 p-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.20em] text-slate-400">Daftar Sesi</p>
                    <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $sessions->count() }} sesi terdaftar</h2>
                </div>
                <p class="text-xs text-slate-500">Drag handle untuk reorder. Klik aksi untuk edit / toggle / hapus.</p>
            </div>

            @forelse ($sessions as $sesi)
                @php
                    $status = $sesi->status();
                    $tone = match ($status) {
                        'upcoming' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'live'     => 'bg-rose-50 text-rose-700 border-rose-200 animate-pulse',
                        'ended'    => 'bg-slate-100 text-slate-600 border-slate-200',
                        'inactive' => 'bg-amber-50 text-amber-700 border-amber-200',
                    };
                    $label = ucfirst($status);
                    $startAt = $sesi->start_at;
                    $endAt = $sesi->end_at;
                @endphp
                <article class="sesi-row flex flex-col gap-4 border-b border-slate-200 p-5 last:border-0 lg:flex-row lg:items-center lg:justify-between"
                    data-sesi-id="{{ $sesi->id_bootcamp_session }}"
                    data-mode-event="{{ $sesi->mode_event ?? 'online' }}"
                    data-link-zoom="{{ $sesi->link_zoom ?? '' }}"
                    data-link-meet="{{ $sesi->link_meet ?? '' }}"
                    data-lokasi-event="{{ $sesi->lokasi_event ?? '' }}"
                    data-peta-event="{{ $sesi->peta_event ?? '' }}"
                    data-kapasitas-sesi="{{ $sesi->kapasitas_sesi ?? '' }}">
                    <div class="flex items-start gap-4">
                        <span class="mt-1 cursor-grab text-slate-300" title="Drag untuk reorder">⋮⋮</span>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $tone }}">{{ $label }}</span>
                                <span class="text-[10px] font-medium text-slate-400">Urutan #{{ $sesi->urutan }}</span>
                                @if (! $sesi->is_active)
                                    <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700">Nonaktif</span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-slate-900">{{ $sesi->judul_sesi }}</h3>
                            <p class="text-xs text-slate-500">
                                {{ $startAt->translatedFormat('d F Y') }} • {{ $startAt->format('H:i') }}
                                @if ($sesi->jam_selesai) - {{ $endAt->format('H:i') }} @else - selesai @endif
                                WIB
                            </p>
                            <div class="flex flex-wrap items-center gap-2 pt-1 text-[11px] text-slate-500">
                                @if ($sesi->isOffline())
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-700 border border-amber-200">Offline</span>
                                @else
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-700 border border-blue-200">Online</span>
                                @endif
                                @if ($sesi->link_zoom)
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-blue-700 border border-blue-200">Zoom</span>
                                @endif
                                @if ($sesi->link_meet)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700 border border-emerald-200">Google Meet</span>
                                @endif
                                @if ($sesi->lokasi_event)
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-700 border border-amber-200">Lokasi</span>
                                @endif
                                @if ($sesi->materi_file || $sesi->materi_url)
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-700 border border-amber-200">Materi</span>
                                @endif
                                @if ($sesi->link_rekaman)
                                    <span class="rounded-full bg-purple-50 px-2 py-0.5 text-purple-700 border border-purple-200">Recording</span>
                                @endif
                                @if ($sesi->deskripsi_sesi)
                                    <span class="line-clamp-1 max-w-xs italic">"{{ \Illuminate\Support\Str::limit($sesi->deskripsi_sesi, 80) }}"</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 lg:justify-end">
                        <button type="button" data-edit-sesi="{{ $sesi->id_bootcamp_session }}" class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Edit</button>
                        <form method="POST" action="{{ route('admin.bootcamp.sesi.toggle', ['courseId' => $course->id_course, 'sesiId' => $sesi->id_bootcamp_session], false) }}" class="inline">
                            @csrf @method('PUT')
                            <button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                {{ $sesi->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.bootcamp.sesi.destroy', ['courseId' => $course->id_course, 'sesiId' => $sesi->id_bootcamp_session], false) }}" onsubmit="return confirm('Hapus sesi ini? Materi yang diunggah juga akan dihapus.');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="rounded-xl border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="p-10 text-center">
                    <p class="font-semibold text-slate-900">Belum ada sesi untuk bootcamp ini.</p>
                    <p class="mt-2 text-sm text-slate-500">Klik "Tambah Sesi" untuk membuat sesi pertama. Sesi lama otomatis dianggap sebagai 1 event default.</p>
                </div>
            @endforelse
        </section>
    </div>

    {{-- Modal: Pengaturan Event (course-level) --}}
    <div id="courseSettingsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" data-modal-close="course-settings"></div>
        <div class="relative flex min-h-full items-start justify-center px-4 py-4 sm:items-center sm:py-6">
            <div class="flex w-full max-w-2xl max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Course Event Settings</p>
                        <h2 class="mt-2 text-xl font-semibold text-slate-900">Pengaturan Level Event</h2>
                        <p class="mt-1 text-sm text-slate-500">Tipe event, mode, lokasi, kapasitas. Berlaku untuk semua sesi.</p>
                    </div>
                    <button type="button" data-modal-close="course-settings" class="rounded-2xl border border-slate-200 p-2 text-slate-500">X</button>
                </div>
                <form method="POST" action="{{ route('admin.bootcamp.sesi.course-settings', ['courseId' => $course->id_course], false) }}" class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                    @csrf @method('PUT')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tipe Event</span>
                            <select name="tipe_event" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                                @foreach ($tipeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('tipe_event', $course->tipe_event) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mode Event</span>
                            <select name="mode_event" id="course_mode_event_select" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                                @foreach ($modeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('mode_event', $currentMode ?? 'online') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500" id="course_mode_event_helper">Pilih mode untuk menentukan field event yang relevan.</p>
                        </label>

                        {{-- ONLINE-only fields: link meeting wajib. Wrapper pake `contents` (sama
                             dgn offline_fields) supaya children jadi direct grid child → konsistensi
                             layout antara mode online dan offline. --}}
                        <div id="course_online_fields" class="contents hidden">
                            <label class="block sm:col-span-2">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Meeting (Zoom / Google Meet)</span>
                                <input name="online_link" type="url" maxlength="500" value="{{ old('online_link', $course->online_link) }}" placeholder="https://zoom.us/j/... atau https://meet.google.com/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                                <span class="mt-1 block text-xs text-slate-500">Event online wajib memiliki link meeting.</span>
                            </label>
                        </div>

                        {{-- OFFLINE-only fields: lokasi + kapasitas + check-in --}}
                        <div id="course_offline_fields" class="contents">
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Lokasi Event</span>
                                <input name="lokasi_event" type="text" maxlength="255" value="{{ old('lokasi_event', $course->lokasi_event) }}" placeholder="Gedung Rektorat Lt. 5, Jakarta" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Peta (opsional)</span>
                                <input name="peta_event" type="url" maxlength="500" value="{{ old('peta_event', $course->peta_event) }}" placeholder="https://maps.app.goo.gl/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Kapasitas Maksimal</span>
                                <input name="kapasitas_maksimal" type="number" min="1" max="100000" value="{{ old('kapasitas_maksimal', $course->kapasitas_maksimal) }}" placeholder="40" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="flex items-center gap-3 self-end">
                                <input type="hidden" name="checkin_required" value="0">
                                <input type="checkbox" name="checkin_required" value="1" @checked(old('checkin_required', $course->checkin_required)) class="h-4 w-4 rounded border-slate-300">
                                <span class="text-xs font-semibold text-slate-700">Wajib check-in onsite</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                        <button type="button" data-modal-close="course-settings" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Tambah/Edit Sesi --}}
    @php
        $sesiFormAction = route('admin.bootcamp.sesi.store', ['courseId' => $course->id_course], false);
    @endphp
    <div id="newSesiModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm" data-modal-close="new-sesi"></div>
        <div class="relative flex min-h-full items-start justify-center px-4 py-4 sm:items-center sm:py-6">
            <div class="flex w-full max-w-3xl max-h-[calc(100dvh-2rem)] flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Session Form</p>
                        <h2 id="sesi-modal-title" class="mt-2 text-xl font-semibold text-slate-900">Tambah Sesi Baru</h2>
                    </div>
                    <button type="button" data-modal-close="new-sesi" class="rounded-2xl border border-slate-200 p-2 text-slate-500">X</button>
                </div>
                <form id="sesiForm" method="POST" action="{{ $sesiFormAction }}" enctype="multipart/form-data" class="flex-1 space-y-5 overflow-y-auto px-6 py-6">
                    @csrf
                    <div id="sesi-method-slot"></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Judul Sesi</span>
                            <input name="judul_sesi" required maxlength="120" placeholder="Contoh: Sesi 1 - Onboarding & Setup Environment" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Tanggal</span>
                            <input name="tanggal_sesi" required type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Jam Mulai</span>
                            <input name="jam_mulai" required type="time" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Jam Selesai (opsional)</span>
                            <input name="jam_selesai" type="time" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>

                        {{-- Mode Event dropdown — drives dynamic field rendering below --}}
                        <div class="sm:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Mode Sesi</span>
                                <select name="mode_event" id="mode_event_select" required data-default-mode="{{ $currentMode ?? 'online' }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800">
                                    <option value="online" @selected(old('mode_event', $currentMode ?? 'online') === 'online')>Online</option>
                                    <option value="offline" @selected(old('mode_event', $currentMode ?? 'online') === 'offline')>Offline</option>
                                </select>
                            </div>
                        </div>

                        {{-- Online fields --}}
                        <div id="online_fields" class="contents">
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Zoom</span>
                                <input name="link_zoom" type="url" maxlength="500" placeholder="https://zoom.us/j/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Google Meet (alternatif)</span>
                                <input name="link_meet" type="url" maxlength="500" placeholder="https://meet.google.com/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                        </div>

                        {{-- Offline fields — hidden by default, shown when mode=offline --}}
                        <div id="offline_fields" class="contents hidden">
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Lokasi Event</span>
                                <input name="lokasi_event" type="text" maxlength="255" placeholder="Gedung Rektorat Lt. 5, Jakarta" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Kapasitas Sesi</span>
                                <input name="kapasitas_sesi" type="number" min="1" max="100000" placeholder="40" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                            <label class="block sm:col-span-2">
                                <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Peta (opsional)</span>
                                <input name="peta_event" type="url" maxlength="500" placeholder="https://maps.app.goo.gl/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                            </label>
                        </div>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Rekaman (opsional)</span>
                            <input name="link_rekaman" type="url" maxlength="500" placeholder="https://drive.google.com/file/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Upload Materi Sesi (PDF / ZIP / PPT / DOC, maks 50MB)</span>
                            <input name="materi_file" type="file" accept=".pdf,.zip,.ppt,.pptx,.doc,.docx" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-blue-700">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Link Materi Alternatif (jika tidak upload)</span>
                            <input name="materi_url" type="url" maxlength="500" placeholder="https://drive.google.com/..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Deskripsi Sesi</span>
                            <textarea name="deskripsi_sesi" rows="3" placeholder="Topik yang akan dibahas, prasyarat peserta, dll." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm"></textarea>
                        </label>
                    </div>
                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                        <button type="button" data-modal-close="new-sesi" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700">Batal</button>
                        <button type="submit" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">Simpan Sesi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modals = {
                    'course-settings': document.getElementById('courseSettingsModal'),
                    'new-sesi': document.getElementById('newSesiModal'),
                };
                const openModal = (name) => { modals[name]?.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); };
                const closeModal = (name) => { modals[name]?.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); };

                document.querySelectorAll('[data-modal-open]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const name = button.dataset.modalOpen;
                        if (name === 'new-sesi') {
                            const form = document.getElementById('sesiForm');
                            const methodSlot = document.getElementById('sesi-method-slot');
                            const title = document.getElementById('sesi-modal-title');
                            form.action = '{{ $sesiFormAction }}';
                            methodSlot.innerHTML = '';
                            title.textContent = 'Tambah Sesi Baru';
                            form.reset();
                        }
                        openModal(name);
                    });
                });
                document.querySelectorAll('[data-modal-close]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(button.dataset.modalClose));
                });

                // Edit sesi — populate modal from session data
                const sesiRows = document.querySelectorAll('[data-edit-sesi]');
                const buildMethodInput = () => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_method';
                    input.value = 'PUT';
                    return input;
                };
                const buildSessionKeyInput = (id) => {
                    // No-op: kept for future-proofing if we ever need an inline edit id payload.
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_sesi_id';
                    input.value = String(id);
                    return input;
                };

                // Mode Event dynamic field visibility toggle
                const modeSelect = document.getElementById('mode_event_select');
                const onlineFields = document.getElementById('online_fields');
                const offlineFields = document.getElementById('offline_fields');
                const modeHelper = document.getElementById('mode-event-helper');
                const sesiForm = document.getElementById('sesiForm'); // hoisted so setFieldMode can use it from any caller
                const setFieldMode = (mode) => {
                    if (!modeSelect) return;
                    if (mode === 'offline') {
                        onlineFields?.classList.add('hidden');
                        offlineFields?.classList.remove('hidden');
                        if (modeHelper) modeHelper.textContent = 'Sesi dilakukan di lokasi fisik (lokasi + kapasitas + maps).';
                        // Clear online-only fields to prevent mixed data on submit
                        const lz = sesiForm?.querySelector('[name="link_zoom"]');
                        const lm = sesiForm?.querySelector('[name="link_meet"]');
                        if (lz) lz.value = '';
                        if (lm) lm.value = '';
                    } else {
                        onlineFields?.classList.remove('hidden');
                        offlineFields?.classList.add('hidden');
                        if (modeHelper) modeHelper.textContent = 'Sesi akan menggunakan Zoom/Google Meet (link meeting wajib).';
                        // Clear offline-only fields to prevent mixed data on submit
                        const lok = sesiForm?.querySelector('[name="lokasi_event"]');
                        const pet = sesiForm?.querySelector('[name="peta_event"]');
                        const kap = sesiForm?.querySelector('[name="kapasitas_sesi"]');
                        if (lok) lok.value = '';
                        if (pet) pet.value = '';
                        if (kap) kap.value = '';
                    }
                };
                if (modeSelect) {
                    modeSelect.addEventListener('change', (e) => setFieldMode(e.target.value));
                }

                sesiRows.forEach((button) => {
                    button.addEventListener('click', () => {
                        const id = button.dataset.editSesi;
                        const row = button.closest('.sesi-row');
                        const title = row?.querySelector('h3')?.textContent?.trim() ?? '';
                        const scheduleText = row?.querySelector('p.text-xs.text-slate-500')?.textContent?.trim() ?? '';
                        const form = document.getElementById('sesiForm');
                        const methodSlot = document.getElementById('sesi-method-slot');
                        const modalTitle = document.getElementById('sesi-modal-title');

                        form.action = '{{ url('/admin/bootcamp/' . $course->id_course . '/sesi') }}/' + id;
                        methodSlot.replaceChildren();
                        methodSlot.appendChild(buildMethodInput());
                        methodSlot.appendChild(buildSessionKeyInput(id));
                        modalTitle.textContent = 'Edit Sesi';

                        form.querySelector('[name="judul_sesi"]').value = title;
                        // Best-effort parsing from "DD Month YYYY • HH:MM - HH:MM WIB" — degrade gracefully if pattern mismatches
                        const dateMatch = scheduleText.match(/(\d{1,2}\s+[A-Za-z]+\s+\d{4})/);
                        const timeMatch = scheduleText.match(/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})?/);
                        if (dateMatch) {
                            const parsed = new Date(dateMatch[1]);
                            if (!isNaN(parsed)) {
                                const yyyy = parsed.getFullYear();
                                const mm = String(parsed.getMonth() + 1).padStart(2, '0');
                                const dd = String(parsed.getDate()).padStart(2, '0');
                                form.querySelector('[name="tanggal_sesi"]').value = `${yyyy}-${mm}-${dd}`;
                            }
                        }
                        if (timeMatch) {
                            form.querySelector('[name="jam_mulai"]').value = timeMatch[1];
                            if (timeMatch[2]) form.querySelector('[name="jam_selesai"]').value = timeMatch[2];
                        }

                        // Pre-populate per-sesi mode_event + dependent fields
                        const mode = row?.dataset.modeEvent || 'online';
                        if (modeSelect) modeSelect.value = mode;
                        const lz = form.querySelector('[name="link_zoom"]');
                        const lm = form.querySelector('[name="link_meet"]');
                        if (lz) lz.value = row?.dataset.linkZoom || '';
                        if (lm) lm.value = row?.dataset.linkMeet || '';
                        const lok = form.querySelector('[name="lokasi_event"]');
                        const pet = form.querySelector('[name="peta_event"]');
                        const kap = form.querySelector('[name="kapasitas_sesi"]');
                        if (lok) lok.value = row?.dataset.lokasiEvent || '';
                        if (pet) pet.value = row?.dataset.petaEvent || '';
                        if (kap) kap.value = row?.dataset.kapasitasSesi || '';

                        setFieldMode(mode);
                        openModal('new-sesi');
                    });
                });

                // When opening the modal in "Tambah Sesi" mode, reset to Course.mode_event
                // default (bukan hardcoded 'online'). Default di-bind via data-default-mode
                // attribute yang nilainya dari $defaultSesiMode di controller. Mengikuti
                // inheritance: kalau Course.mode_event = Offline, tambah sesi default = Offline.
                // User tetap bisa override per-sesi via dropdown tanpa propagasi ke Course.
                document.querySelector('[data-modal-open="new-sesi"]')?.addEventListener('click', () => {
                    setTimeout(() => {
                        const defaultMode = modeSelect?.dataset.defaultMode || 'online';
                        if (modeSelect) modeSelect.value = defaultMode;
                        setFieldMode(defaultMode);
                    }, 0);
                });

                // ─────────────────────────────────────────────────────
                // COURSE-LEVEL Mode Event toggle (courseSettingsModal)
                // Single source of truth: option value dari controller (modeOptions = ONLINE|OFFLINE).
                // Initial state di-render sesuai $course->mode_event (dari backend); legacy 'onsite'/'hybrid'
                // dinormalisasi ke 'offline' di controller sebelum sampai ke view.
                // ─────────────────────────────────────────────────────
                const courseModeSelect = document.getElementById('course_mode_event_select');
                const courseOnlineFields = document.getElementById('course_online_fields');
                const courseOfflineFields = document.getElementById('course_offline_fields');
                const courseModeHelper = document.getElementById('course_mode_event_helper');
                const courseForm = document.querySelector('#courseSettingsModal form');

                const setCourseFieldMode = (mode) => {
                    if (!courseModeSelect) return;
                    const normalized = (mode === 'offline') ? 'offline' : 'online';
                    // Sync the select UI (in case caller didn't already)
                    courseModeSelect.value = normalized;
                    if (normalized === 'online') {
                        courseOnlineFields?.classList.remove('hidden');
                        courseOfflineFields?.classList.add('hidden');
                        if (courseModeHelper) courseModeHelper.textContent = 'Event online: wajib isi link meeting. Lokasi/kapasitas disembunyikan.';
                        // Auto-clear offline-only fields to prevent mixed data on submit
                        if (courseForm) {
                            ['lokasi_event', 'peta_event', 'kapasitas_maksimal'].forEach((n) => {
                                const el = courseForm.querySelector(`[name="${n}"]`);
                                if (el) el.value = '';
                            });
                            const cb = courseForm.querySelector('[name="checkin_required"]');
                            if (cb) cb.checked = false;
                        }
                    } else {
                        courseOnlineFields?.classList.add('hidden');
                        courseOfflineFields?.classList.remove('hidden');
                        if (courseModeHelper) courseModeHelper.textContent = 'Event offline: wajib isi lokasi + kapasitas. Link meeting disembunyikan.';
                        // Auto-clear online-only field to prevent mixed data on submit
                        if (courseForm) {
                            const ol = courseForm.querySelector('[name="online_link"]');
                            if (ol) ol.value = '';
                        }
                    }
                };

                if (courseModeSelect) {
                    courseModeSelect.addEventListener('change', (e) => setCourseFieldMode(e.target.value));
                }

                // Initialize on modal open dari backend state (single source of truth: option value).
                // Legitimate nilai di sini hanya 'online' atau 'offline' karena modeOptions source dari
                // AccessMode::userCases() (lihat controller show()). Default ke 'online' jika null/undefined.
                document.querySelectorAll('[data-modal-open="course-settings"]').forEach((btn) => {
                    btn.addEventListener('click', () => setCourseFieldMode(courseModeSelect?.value || 'online'));
                });

                // Inisiasi awal saat DOM ready (untuk mencegah FOUC ketika modal auto-open dari validation error)
                setCourseFieldMode(courseModeSelect?.value || 'online');
            });
        </script>
    @endpush
</x-layouts.admin>

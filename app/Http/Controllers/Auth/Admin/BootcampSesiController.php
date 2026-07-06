<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bootcamp;
use App\Models\BootcampSession;
use App\Models\Course;
use App\Support\Bootcamp\AccessMode;
use App\Support\Bootcamp\BootcampType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Sesi bootcamp CRUD khusus.
 *
 * Memisahkan logic sesi dari AdminController monolith. Setiap method
 * menerima courseId (id_course) dan parameter sesi sesuai kebutuhan.
 *
 * Backward compat: course yang di-link dari tabel bootcamps (Bootcamp model
 * punya linked_course_id) akan diteruskan sebagai page sub-title di view
 * agar admin tahu event mana yang sedang diedit sesinya.
 */
class BootcampSesiController extends Controller
{
    /**
     * Halaman utama kelola sesi untuk satu course/bootcamp.
     */
    public function show(int $courseId)
    {
        $course = Course::with(['allSessions'])->findOrFail($courseId);
        $linkedBootcamp = $course->id_course
            ? Bootcamp::where('linked_course_id', $course->id_course)->first()
            : null;
        $tipeOptions = collect(BootcampType::cases())
            ->mapWithKeys(fn (BootcampType $t) => [$t->value => $t->label()])
            ->all();
        // HANYA user-facing cases (Online / Offline). JANGAN pakai AccessMode::cases()
        // karena ONSITE/HYBRID deprecated label-nya collapse ke "Offline" → duplikat.
        $modeOptions = collect(AccessMode::userCases())
            ->mapWithKeys(fn (AccessMode $m) => [$m->value => $m->label()])
            ->all();

        // Normalize legacy 'onsite'/'hybrid' → 'offline' supaya dropdown preselect
        // benar (lihat AccessMode::userCases() — hanya ada online|offline options).
        // Tanpa normalisasi, mode_event='onsite' tidak match satupun option dan
        // browser default ke option pertama (Online) — mismatch dengan realitas data.
        //
        // $currentMode dipakai di dua tempat:
        //  1. Preselect dropdown modal "Pengaturan Event" (course-level).
        //  2. Pre-fill dropdown modal "Tambah Sesi" (inherit dari Course, bisa override
        //     per-sesi via dropdown sesi) — dahulu hardcoded 'online', sekarang inherit.
        $currentMode = AccessMode::fromNullable($course->mode_event)->value;

        return view('Auth.admin.kelola-sesi-bootcamp', [
            'course' => $course,
            'linkedBootcamp' => $linkedBootcamp,
            'tipeOptions' => $tipeOptions,
            'modeOptions' => $modeOptions,
            'currentMode' => $currentMode,
            'sessions' => $course->allSessions,
        ]);
    }

    /**
     * Update tipe_event / mode_event / lokasi / kapasitas course-level fields.
     * Dipakai untuk konfigurasi awal sebelum admin menambah sesi individual.
     *
     * Mode event binary (online|offline). Untuk online → wajib online_link.
     * Untuk offline → wajib lokasi_event + kapasitas_maksimal. Mixed data
     * ditolak dan field yang tidak relevan di-auto-null agar DB tetap bersih.
     */
    public function updateCourseSettings(Request $request, int $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);

        // Normalize legacy 'onsite'/'hybrid' ke 'offline' supaya validasi konsisten.
        $rawMode = (string) $request->input('mode_event', '');
        $normalizedMode = in_array($rawMode, ['online', 'offline'], true)
            ? $rawMode
            : (in_array($rawMode, ['onsite', 'hybrid'], true) ? 'offline' : 'online');
        $request->merge(['mode_event' => $normalizedMode]);

        $rules = [
            'tipe_event'          => ['nullable', Rule::in(['bootcamp','webinar','workshop','seminar'])],
            'mode_event'          => ['required', 'in:online,offline'],
            'lokasi_event'        => ['nullable', 'string', 'max:255', 'required_if:mode_event,offline'],
            'peta_event'          => ['nullable', 'string', 'max:500', 'url'],
            'kapasitas_maksimal'  => ['nullable', 'integer', 'min:1', 'max:100000', 'required_if:mode_event,offline'],
            'checkin_required'    => ['nullable', 'boolean'],
        ];

        if ($normalizedMode === 'online') {
            $rules['online_link'] = ['required', 'string', 'max:500', 'url'];
        } else {
            $rules['online_link'] = ['nullable', 'string', 'max:500', 'url'];
        }

        $messages = [
            'mode_event.required' => 'Mode event wajib dipilih (online/offline).',
            'mode_event.in' => 'Mode event harus bernilai online atau offline.',
            'online_link.required' => 'Link meeting (Zoom / Google Meet) wajib diisi untuk event online.',
            'online_link.url' => 'Link meeting harus berupa URL valid.',
            'lokasi_event.required_if' => 'Lokasi event wajib diisi untuk event offline.',
            'kapasitas_maksimal.required_if' => 'Kapasitas maksimal wajib diisi untuk event offline.',
            'peta_event.url' => 'Link peta harus berupa URL valid.',
        ];

        $validated = $request->validate($rules, $messages);

        // Hard rejection of mixed data (defense in depth).
        if ($normalizedMode === 'offline') {
            if (!empty($validated['online_link'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'online_link' => 'Event offline tidak boleh memiliki link meeting. Kosongkan field link meeting.',
                ]);
            }
        } else {
            // mode = online
            if (!empty($validated['lokasi_event']) || !empty($validated['peta_event']) || !empty($validated['kapasitas_maksimal'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'lokasi_event' => 'Event online tidak boleh memiliki lokasi/kapasitas. Kosongkan field lokasi dan kapasitas.',
                ]);
            }
        }

        // Auto-null: field yang tidak relevan untuk mode di-clear.
        if ($normalizedMode === 'offline') {
            $validated['online_link'] = null;
        } else {
            $validated['lokasi_event'] = null;
            $validated['peta_event'] = null;
            $validated['kapasitas_maksimal'] = null;
            $validated['checkin_required'] = false;
        }

        $payload = [];
        $payload['tipe_event']         = $validated['tipe_event'] ?? $course->tipe_event;
        $payload['mode_event']         = $validated['mode_event'];
        $payload['lokasi_event']       = $validated['lokasi_event'] ?? null;
        $payload['peta_event']         = $validated['peta_event'] ?? null;
        $payload['kapasitas_maksimal'] = $validated['kapasitas_maksimal'] ?? null;
        $payload['checkin_required']   = (bool) ($validated['checkin_required'] ?? false);
        $payload['online_link']        = $validated['online_link'] ?? null;

        $course->update($payload);

        return back()->with('success', 'Pengaturan event berhasil diperbarui.');
    }

    /**
     * Tambah sesi baru untuk course.
     */
    public function store(Request $request, int $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $type = BootcampType::fromNullable($course->tipe_event ?? 'bootcamp');

        $validated = $this->validateSesiPayload($request, null, $type);

        $materiFilePath = $this->handleMateriUpload($request, 'materi_file', $course->id_course);

        $nextUrutan = (int) ($course->allSessions()->max('urutan') ?? 0) + 1;

        BootcampSession::create([
            'id_course'      => $course->id_course,
            'judul_sesi'     => $validated['judul_sesi'],
            'tanggal_sesi'   => $validated['tanggal_sesi'],
            'jam_mulai'      => $validated['jam_mulai'],
            'jam_selesai'    => $validated['jam_selesai'] ?? null,
            'mode_event'     => $validated['mode_event'],
            'lokasi_event'   => $validated['lokasi_event'] ?? null,
            'peta_event'     => $validated['peta_event'] ?? null,
            'kapasitas_sesi' => $validated['kapasitas_sesi'] ?? null,
            'link_zoom'      => $validated['link_zoom'] ?? null,
            'link_meet'      => $validated['link_meet'] ?? null,
            'link_rekaman'   => $validated['link_rekaman'] ?? null,
            'materi_file'    => $materiFilePath,
            'materi_url'     => $validated['materi_url'] ?? null,
            'deskripsi_sesi' => $validated['deskripsi_sesi'] ?? null,
            'urutan'         => $nextUrutan,
            'is_active'      => true,
        ]);

        return back()->with('success', 'Sesi baru berhasil ditambahkan.');
    }

    /**
     * Update sesi yang sudah ada.
     */
    public function update(Request $request, int $courseId, int $sesiId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $sesi = BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();
        $type = BootcampType::fromNullable($course->tipe_event ?? 'bootcamp');

        $validated = $this->validateSesiPayload($request, $sesi, $type);
        $materiFilePath = $this->handleMateriUpload(
            $request,
            'materi_file',
            $course->id_course,
            $sesi->materi_file
        );

        $sesi->update([
            'judul_sesi'     => $validated['judul_sesi'],
            'tanggal_sesi'   => $validated['tanggal_sesi'],
            'jam_mulai'      => $validated['jam_mulai'],
            'jam_selesai'    => $validated['jam_selesai'] ?? null,
            'mode_event'     => $validated['mode_event'],
            'lokasi_event'   => $validated['lokasi_event'] ?? null,
            'peta_event'     => $validated['peta_event'] ?? null,
            'kapasitas_sesi' => $validated['kapasitas_sesi'] ?? null,
            'link_zoom'      => $validated['link_zoom'] ?? null,
            'link_meet'      => $validated['link_meet'] ?? null,
            'link_rekaman'   => $validated['link_rekaman'] ?? null,
            'materi_file'    => $materiFilePath,
            'materi_url'     => $validated['materi_url'] ?? null,
            'deskripsi_sesi' => $validated['deskripsi_sesi'] ?? null,
        ]);

        return back()->with('success', 'Sesi berhasil diperbarui.');
    }

    /**
     * Hapus sesi (soft via cascade aman karena migration menambahkan FK
     * cascadeOnDelete ke courses. Kita eksplisit hapus agar tidak bergantung
     * ke FK level).
     */
    public function destroy(int $courseId, int $sesiId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $sesi = BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();
        // Cleanup materi file
        if ($sesi->materi_file && Storage::disk('public')->exists($sesi->materi_file)) {
            Storage::disk('public')->delete($sesi->materi_file);
        }
        $sesi->delete();

        return back()->with('success', 'Sesi berhasil dihapus.');
    }

    /**
     * Toggle aktif/non-aktif sesi (admin dapat menyembunyikan sesi tanpa hapus).
     */
    public function toggleActive(int $courseId, int $sesiId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $sesi = BootcampSession::where('id_course', $course->id_course)
            ->where('id_bootcamp_session', $sesiId)
            ->firstOrFail();
        $sesi->is_active = !$sesi->is_active;
        $sesi->save();

        return back()->with('success', 'Status sesi diperbarui.');
    }

    /**
     * Reorder sesi — menerima array id_bootcamp_session sesuai urutan baru.
     * Wrap dalam transaction supaya konsisten.
     */
    public function reorder(Request $request, int $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $validated = $request->validate([
            'order' => ['required', 'string'], // JSON array of id_bootcamp_session
        ]);
        $order = json_decode($validated['order'], true);
        if (!is_array($order)) {
            return back()->withErrors(['order' => 'Format order tidak valid.']);
        }
        DB::transaction(function () use ($course, $order) {
            foreach (array_values($order) as $i => $sesiId) {
                BootcampSession::where('id_course', $course->id_course)
                    ->where('id_bootcamp_session', (int) $sesiId)
                    ->update(['urutan' => $i + 1]);
            }
        });

        return back()->with('success', 'Urutan sesi berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Validate per-sesi payload dengan PER-SESI mode_event enforcement.
     *
     * Mode_event yang dipakai adalah dari REQUEST (per-sesi), bukan dari
     * course.mode_event. Auto-null rules: kalau mode_event=offline, link
     * meeting di-clear; kalau mode_event=online, lokasi_event/peta_event/
     * kapasitas_sesi di-clear. Mixed data ditolak.
     */
    private function validateSesiPayload(Request $request, ?BootcampSession $existing, BootcampType $type): array
    {
        $mode = $request->input('mode_event', 'online');

        $rules = [
            'judul_sesi'     => ['required', 'string', 'max:120'],
            'tanggal_sesi'   => ['required', 'date'],
            'jam_mulai'      => ['required', 'date_format:H:i'],
            'jam_selesai'    => ['nullable', 'date_format:H:i', 'after:jam_mulai'],
            'mode_event'     => ['required', 'in:online,offline'],
            'lokasi_event'   => ['nullable', 'string', 'max:255', 'required_if:mode_event,offline'],
            'peta_event'     => ['nullable', 'string', 'max:500', 'url'],
            'kapasitas_sesi' => ['nullable', 'integer', 'min:1', 'max:100000', 'required_if:mode_event,offline'],
            'link_rekaman'   => ['nullable', 'string', 'max:500', 'url'],
            'materi_url'     => ['nullable', 'string', 'max:500', 'url'],
            'materi_file'    => ['nullable', 'file', 'mimes:pdf,zip,ppt,pptx,doc,docx', 'max:51200'],
            'deskripsi_sesi' => ['nullable', 'string', 'max:5000'],
        ];

        if ($mode === 'online') {
            // Sesi online WAJIB punya minimal satu meeting link.
            $rules['link_zoom'] = ['required_without:link_meet', 'nullable', 'string', 'max:500', 'url'];
            $rules['link_meet'] = ['nullable', 'string', 'max:500', 'url'];
        } else {
            // Sesi offline: lokasi + kapasitas sudah required_if di atas.
            // Link meeting harus kosong (mixed data → reject).
            $rules['link_zoom'] = ['nullable', 'string', 'max:500', 'url', 'prohibits:link_meet'];
            $rules['link_meet'] = ['nullable', 'string', 'max:500', 'url'];
        }

        $messages = [
            'judul_sesi.required' => 'Judul sesi wajib diisi.',
            'tanggal_sesi.required' => 'Tanggal sesi wajib diisi.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'mode_event.required' => 'Mode event sesi wajib dipilih (online/offline).',
            'mode_event.in' => 'Mode event harus bernilai online atau offline.',
            'lokasi_event.required_if' => 'Lokasi wajib diisi untuk sesi offline.',
            'kapasitas_sesi.required_if' => 'Kapasitas sesi wajib diisi untuk sesi offline.',
            'link_zoom.required_without' => 'Link Zoom atau Google Meet wajib diisi untuk sesi online.',
            'link_zoom.prohibits' => 'Sesi offline tidak boleh memiliki link meeting.',
            'materi_file.max' => 'Ukuran materi maksimal 50MB.',
        ];

        $validated = $request->validate($rules, $messages);

        // Hard rejection of mixed data (defense in depth — also caught by 'prohibits' rule).
        if ($mode === 'offline') {
            $hasZoom = !empty($validated['link_zoom']);
            $hasMeet = !empty($validated['link_meet']);
            if ($hasZoom || $hasMeet) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'link_zoom' => 'Sesi offline tidak boleh memiliki link meeting (Zoom/Meet). Kosongkan field meeting link.',
                ]);
            }
        } else {
            $hasLokasi = !empty($validated['lokasi_event']);
            $hasKapasitas = !empty($validated['kapasitas_sesi']);
            if ($hasLokasi || $hasKapasitas) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'lokasi_event' => 'Sesi online tidak boleh memiliki lokasi/kapasitas. Kosongkan field lokasi.',
                ]);
            }
        }

        // Auto-null the irrelevant fields after validation so DB stays clean.
        if ($mode === 'offline') {
            $validated['link_zoom'] = null;
            $validated['link_meet'] = null;
        } else {
            $validated['lokasi_event'] = null;
            $validated['peta_event'] = null;
            $validated['kapasitas_sesi'] = null;
        }

        return $validated;
    }

    private function handleMateriUpload(Request $request, string $field, int $courseId, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile($field)) {
            return $oldPath;
        }
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
        $file = $request->file($field);
        $filename = 'sesi_' . $courseId . '_' . time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
        return $file->storeAs('bootcamp-sesi-materi', $filename, 'public');
    }
}

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
        $modeOptions = collect(AccessMode::cases())
            ->mapWithKeys(fn (AccessMode $m) => [$m->value => $m->label()])
            ->all();

        return view('Auth.admin.kelola-sesi-bootcamp', [
            'course' => $course,
            'linkedBootcamp' => $linkedBootcamp,
            'tipeOptions' => $tipeOptions,
            'modeOptions' => $modeOptions,
            'sessions' => $course->allSessions,
        ]);
    }

    /**
     * Update tipe_event / mode_event / lokasi / kapasitas course-level fields.
     * Dipakai untuk konfigurasi awal sebelum admin menambah sesi individual.
     */
    public function updateCourseSettings(Request $request, int $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);

        $validated = $request->validate([
            'tipe_event'    => ['nullable', Rule::in(['bootcamp','webinar','workshop','seminar'])],
            'mode_event'    => ['nullable', Rule::in(['online','onsite','hybrid'])],
            'lokasi_event'  => ['nullable', 'string', 'max:255'],
            'peta_event'    => ['nullable', 'string', 'max:500'],
            'kapasitas_maksimal' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'checkin_required' => ['nullable', 'boolean'],
        ]);

        $payload = [];
        if ($request->filled('tipe_event'))      $payload['tipe_event'] = $validated['tipe_event'];
        if ($request->filled('mode_event'))      $payload['mode_event'] = $validated['mode_event'];
        if ($request->has('lokasi_event'))       $payload['lokasi_event'] = $validated['lokasi_event'] ?? null;
        if ($request->has('peta_event'))         $payload['peta_event'] = $validated['peta_event'] ?? null;
        if ($request->filled('kapasitas_maksimal')) $payload['kapasitas_maksimal'] = (int) $validated['kapasitas_maksimal'];
        $payload['checkin_required'] = (bool) ($validated['checkin_required'] ?? false);

        if (!empty($payload)) {
            $course->update($payload);
        }

        return back()->with('success', 'Pengaturan event berhasil diperbarui.');
    }

    /**
     * Tambah sesi baru untuk course.
     */
    public function store(Request $request, int $courseId): RedirectResponse
    {
        $course = Course::findOrFail($courseId);
        $type = BootcampType::fromNullable($course->tipe_event ?? 'bootcamp');
        $mode = AccessMode::fromNullable($course->mode_event ?? 'online');

        $validated = $this->validateSesiPayload($request, null, $type, $mode);

        $materiFilePath = $this->handleMateriUpload($request, 'materi_file', $course->id_course);

        $nextUrutan = (int) ($course->allSessions()->max('urutan') ?? 0) + 1;

        BootcampSession::create([
            'id_course'      => $course->id_course,
            'judul_sesi'     => $validated['judul_sesi'],
            'tanggal_sesi'   => $validated['tanggal_sesi'],
            'jam_mulai'      => $validated['jam_mulai'],
            'jam_selesai'    => $validated['jam_selesai'] ?? null,
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
        $mode = AccessMode::fromNullable($course->mode_event ?? 'online');

        $validated = $this->validateSesiPayload($request, $sesi, $type, $mode);
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

    private function validateSesiPayload(Request $request, ?BootcampSession $existing, BootcampType $type, AccessMode $mode): array
    {
        $rules = [
            'judul_sesi'     => ['required', 'string', 'max:120'],
            'tanggal_sesi'   => ['required', 'date'],
            'jam_mulai'      => ['required', 'date_format:H:i'],
            'jam_selesai'    => ['nullable', 'date_format:H:i', 'after:jam_mulai'],
            'link_zoom'      => ['nullable', 'string', 'max:500', 'url'],
            'link_meet'      => ['nullable', 'string', 'max:500', 'url'],
            'link_rekaman'   => ['nullable', 'string', 'max:500', 'url'],
            'materi_url'     => ['nullable', 'string', 'max:500', 'url'],
            'materi_file'    => ['nullable', 'file', 'mimes:pdf,zip,ppt,pptx,doc,docx', 'max:51200'],
            'deskripsi_sesi' => ['nullable', 'string', 'max:5000'],
        ];

        // Tipe yang rencananya butuh meeting link per sesi
        if ($type->requiresLiveClassLink() && !$mode->equals(AccessMode::ONSITE)) {
            $rules['link_zoom'] = ['required_without:link_meet', 'nullable', 'string', 'max:500', 'url'];
        }

        return $request->validate($rules, [
            'judul_sesi.required' => 'Judul sesi wajib diisi.',
            'tanggal_sesi.required' => 'Tanggal sesi wajib diisi.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'link_zoom.required_without' => 'Link Zoom atau Google Meet wajib diisi untuk event ini.',
            'materi_file.max' => 'Ukuran materi maksimal 50MB.',
        ]);
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

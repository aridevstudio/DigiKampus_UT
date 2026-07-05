<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Bukti kehadiran live-class bootcamp (kategori='tiket').
 *
 * Resource tunggal untuk state machine:
 *   pending → verified (oleh admin/dosen)
 *   pending → rejected (oleh admin/dosen, dengan catatan_reviewer wajib)
 *   rejected → pending (mahasiswa boleh upload ulang bukti baru)
 *
 * Setelah verified, mesin sertifikat bisa membaca data ini untuk gate
 * final-project dan kelulusan.
 */
class BootcampLiveClassAttendance extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_REJECTED = 'rejected';

    /**
     * ADR-0001 (docs/Kerjain/adr-0001-live-class-attendance-schema.md) -
     * Mapping kolom DB ke nama kolom PRD agar JSON/API contract conformance
     * tanpa rename migration.
     *
     * - PRD `bootcamp_id`   → kolom `id_course` (Course.kategori='tiket' = bootcamp)
     * - PRD `live_class_id` → kolom `session_key` (computed timeline string)
     */
    public const PRD_BOOTCAMP_COLUMN = 'id_course';
    public const PRD_LIVE_CLASS_COLUMN = 'session_key';

    protected $table = 'bootcamp_live_class_attendances';
    protected $primaryKey = 'id_bootcamp_live_class_attendance';

    protected $fillable = [
        'id_user',
        'id_course',
        'session_key',
        'proof_file',
        'catatan_mahasiswa',
        'status',
        'catatan_reviewer',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function isVerified(): bool
    {
        return $this->status === self::STATUS_VERIFIED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Human-readable label untuk session_key — diekspos agar admin UI dan
     * notifikasi punya string yang konsisten (bukan menampilkan "qa_3_..."
     * mentah-mentah).
     *
     * Pattern session_key di ekosistem ini (lihat
     * CourseController::collectLiveClassSessionKeys()):
     *   - "primary"             → sesi utama bootcamp (Course.tanggal_webinar)
     *   - "qa_<moduleId>_<iso>" → sesi Live Q&A yang dibangkitkan per module.
     *
     * @return string Label ringkas untuk dirender di tabel, badge, notifikasi.
     */
    public function humanSessionLabel(): string
    {
        $key = (string) ($this->attributes['session_key'] ?? '');

        if ($key === 'primary') {
            return 'Sesi Utama Bootcamp';
        }

        if (str_starts_with($key, 'qa_')) {
            // "qa_<moduleId>_<startIso>" → tangkap moduleId untuk label yang helpful.
            // Contoh: "qa_3_2026-07-15T10:00" → "Live Q&A Modul #3"
            $parts = explode('_', $key, 3);
            $moduleId = $parts[1] ?? null;
            return $moduleId !== null && $moduleId !== ''
                ? "Live Q&A Modul #{$moduleId}"
                : 'Live Q&A';
        }

        // Fallback untuk session_key baru yang mungkin ditambahkan di masa depan
        // tanpa harus mengubah helper ini.
        return 'Sesi Live';
    }

    /**
     * Magic accessor — exposes PRD `bootcamp_id` di serialized response.
     * Tidak men-shadow kolom asli; Eloquent skip magic method ini jika
     * kolom `bootcamp_id` diminta via `getAttribute()` direct.
     */
    public function getBootcampIdAttribute(): int
    {
        return (int) $this->attributes[self::PRD_BOOTCAMP_COLUMN] ?? 0;
    }

    /**
     * Magic accessor — exposes PRD `live_class_id` di serialized response.
     */
    public function getLiveClassIdAttribute(): string
    {
        return (string) ($this->attributes[self::PRD_LIVE_CLASS_COLUMN] ?? '');
    }
}

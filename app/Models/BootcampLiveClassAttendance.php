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
}

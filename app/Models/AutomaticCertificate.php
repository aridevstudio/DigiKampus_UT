<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomaticCertificate extends Model
{
    use HasFactory;

    protected $table = 'automatic_certificates';

    protected $fillable = [
        'certificate_template_id',
        'id_mahasiswa',
        'id_course',
        'nomor_sertifikat',
        'nama_peserta',
        'nama_program',
        'tanggal_terbit',
        'source',
    ];

    protected $casts = [
        'id_mahasiswa' => 'integer',
        'id_course' => 'integer',
        'tanggal_terbit' => 'date',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'id_course', 'id_course');
    }
}

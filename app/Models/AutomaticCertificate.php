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
        'nomor_sertifikat',
        'nama_peserta',
        'nama_program',
        'tanggal_terbit',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
}

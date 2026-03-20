<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $table = 'certificate_templates';

    protected $fillable = [
        'name',
        'background_type',
        'background_image_path',
        'background_gradient',
        'nomor_x',
        'nomor_y',
        'nomor_size',
        'nama_x',
        'nama_y',
        'nama_size',
        'program_x',
        'program_y',
        'program_size',
        'tanggal_x',
        'tanggal_y',
        'tanggal_size',
    ];

    protected $casts = [
        'nomor_x' => 'float',
        'nomor_y' => 'float',
        'nomor_size' => 'integer',
        'nama_x' => 'float',
        'nama_y' => 'float',
        'nama_size' => 'integer',
        'program_x' => 'float',
        'program_y' => 'float',
        'program_size' => 'integer',
        'tanggal_x' => 'float',
        'tanggal_y' => 'float',
        'tanggal_size' => 'integer',
    ];

    public function certificates(): HasMany
    {
        return $this->hasMany(AutomaticCertificate::class, 'certificate_template_id');
    }
}

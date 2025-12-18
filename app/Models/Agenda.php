<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';
    protected $primaryKey = 'id_agenda';

    protected $fillable = [
        'id_mahasiswa',
        'judul',
        'deskripsi',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'tipe',
        'warna'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the mahasiswa that owns the agenda.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa', 'id');
    }

    /**
     * Scope by month.
     */
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year);
    }

    /**
     * Get color based on type.
     */
    public static function getColorByType($tipe)
    {
        return match ($tipe) {
            'webinar' => '#3B82F6',   // Blue
            'deadline' => '#EF4444',  // Red
            'workshop' => '#F59E0B',  // Orange
            default => '#3B82F6'
        };
    }
}

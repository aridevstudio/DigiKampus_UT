<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bootcamp extends Model
{
    use HasFactory;

    protected $table = 'bootcamps';
    protected $primaryKey = 'id_bootcamp';

    protected $fillable = [
        'program_type',
        'title',
        'batch_label',
        'status',
        'mentor_label',
        'seats_label',
        'price_label',
        'schedule_label',
        'risk_note',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function mentorAssignments()
    {
        return $this->hasMany(BootcampMentor::class, 'id_bootcamp', 'id_bootcamp');
    }
}


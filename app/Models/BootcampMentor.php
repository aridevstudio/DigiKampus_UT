<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BootcampMentor extends Model
{
    use HasFactory;

    protected $table = 'bootcamp_mentors';
    protected $primaryKey = 'id_bootcamp_mentor';

    protected $fillable = [
        'id_bootcamp',
        'mentor_type',
        'id_user',
        'id_external_mentor',
        'role_label',
        'assignment_note',
    ];

    public function bootcamp()
    {
        return $this->belongsTo(Bootcamp::class, 'id_bootcamp', 'id_bootcamp');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function externalMentor()
    {
        return $this->belongsTo(ExternalMentor::class, 'id_external_mentor', 'id_external_mentor');
    }
}

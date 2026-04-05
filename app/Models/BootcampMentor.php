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
        'id_user',
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
}


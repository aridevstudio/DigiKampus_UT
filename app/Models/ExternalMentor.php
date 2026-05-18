<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalMentor extends Model
{
    use HasFactory;

    protected $table = 'external_mentors';
    protected $primaryKey = 'id_external_mentor';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'expertise',
        'institution',
        'status',
        'notes',
    ];

    public function bootcampAssignments()
    {
        return $this->hasMany(BootcampMentor::class, 'id_external_mentor', 'id_external_mentor');
    }
}

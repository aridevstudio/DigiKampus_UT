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
        'seat_capacity',
        'price_label',
        'price',
        'schedule_label',
        'schedule_date',
        'start_time',
        'end_time',
        'risk_note',
        'sales_opened_at',
        'published_at',
        'linked_course_id',
        'created_by',
    ];

    protected $casts = [
        'price' => 'integer',
        'seat_capacity' => 'integer',
        'schedule_date' => 'date',
        'sales_opened_at' => 'datetime',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function mentorAssignments()
    {
        return $this->hasMany(BootcampMentor::class, 'id_bootcamp', 'id_bootcamp');
    }

    public function linkedCourse()
    {
        return $this->belongsTo(Course::class, 'linked_course_id', 'id_course');
    }
}

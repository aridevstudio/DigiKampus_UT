<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $table = 'import_logs';

    protected $fillable = [
        'admin_id',
        'type',
        'filename',
        'total_rows',
        'imported',
        'skipped',
        'updated',
        'duplicate_strategy',
        'status',
        'errors',
        'preview_data',
    ];

    protected $casts = [
        'errors' => 'array',
        'preview_data' => 'array',
        'total_rows' => 'integer',
        'imported' => 'integer',
        'skipped' => 'integer',
        'updated' => 'integer',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

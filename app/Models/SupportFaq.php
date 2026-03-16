<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportFaq extends Model
{
    use HasFactory;

    protected $table = 'support_faqs';
    protected $primaryKey = 'id_support_faq';

    protected $fillable = [
        'question',
        'answer',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppRegistryOverride extends Model
{
    protected $table = 'app_registry_overrides';

    protected $fillable = [
        'slug',
        'display_name',
        'description',
        'external_url',
        'is_active',
        'access_roles',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'access_roles' => 'array',
    ];
}

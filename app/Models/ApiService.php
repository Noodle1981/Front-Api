<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiService extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'required_fields',
    ];

    protected $casts = [
        'required_fields' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessRule extends Model
{
    protected $fillable = ['name', 'description', 'python_script', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

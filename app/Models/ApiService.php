<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiService extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'logo_url',
        'required_fields',
    ];

    protected $casts = [
        'required_fields' => 'array',
    ];

    public function endpoints()
    {
        return $this->hasMany(Endpoint::class);
    }

    public function clientCredentials()
    {
        return $this->hasMany(ClientCredential::class);
    }
}

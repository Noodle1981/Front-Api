<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    protected $fillable = ['api_service_id', 'method', 'url', 'description', 'parameters'];

    protected $casts = [
        'parameters' => 'array',
    ];

    public function apiService()
    {
        return $this->belongsTo(ApiService::class);
    }

    public function clientCredentials()
    {
        return $this->belongsToMany(ClientCredential::class, 'client_credential_endpoint')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }
}

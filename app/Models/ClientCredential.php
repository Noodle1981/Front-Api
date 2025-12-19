<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCredential extends Model
{
    protected $fillable = [
        'client_id',
        'api_service_id',
        'name',
        'credentials',
        'is_active',
        'execution_frequency',
        'alert_email',
    ];

    protected $casts = [
        'credentials' => AsEncryptedArrayObject::class,
        'is_active' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function apiService(): BelongsTo
    {
        return $this->belongsTo(ApiService::class);
    }

    public function endpoints()
    {
        return $this->belongsToMany(Endpoint::class, 'client_credential_endpoint')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }
}

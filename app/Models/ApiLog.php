<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'client_id',
        'api_service_id',
        'status',
        'event_type',
        'details',
        'happened_at',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function apiService()
    {
        return $this->belongsTo(ApiService::class);
    }
}

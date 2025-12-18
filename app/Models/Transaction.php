<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'client_id',
        'api_service_id',
        'date_at',
        'amount',
        'currency',
        'type',
        'description',
        'status',
        'raw_data',
    ];

    protected $casts = [
        'date_at' => 'datetime',
        'amount' => 'decimal:2',
        'raw_data' => 'array',
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

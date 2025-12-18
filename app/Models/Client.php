<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\NormalizesPhone;
use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, NormalizesPhone, SoftDeletes, BelongsToUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'cuit',
        'company', // Razón Social
        'fantasy_name',
        'tax_condition',
        'industry',
        'employees_count',
        'parent_id',
        'branch_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zip_code',
        'internal_notes',
        'external_reference_id',
        'user_id',
        'active',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'active' => 1, // <-- ¡LA LÍNEA MÁGICA!
    ];

    /**
     * Casts por defecto para este modelo
     */
    protected $casts = [
        'active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Normaliza email (trim + lowercase)
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    // ----- RELACIONES -----

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Client::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Client::class, 'parent_id');
    }

    public function isAnexo()
    {
        return !is_null($this->parent_id);
    }

    public function credentials(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClientCredential::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::creating(function ($client) {
            if (empty($client->uuid)) {
                $client->uuid = (string) Str::uuid();
            }
        });
    }
}
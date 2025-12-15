<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\NormalizesPhone;
use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use HasFactory, NormalizesPhone, SoftDeletes, BelongsToUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company',
        'cuit',
        'website',
        'email',
        'phone',
        'fiscal_address_street',
        'fiscal_address_zip_code',
        'fiscal_address_city',
        'fiscal_address_state',
        'fiscal_address_country',
        'economic_activity',
        'art_provider',
        'art_registration_date',
        'hs_manager_name',
        'hs_manager_contact',
        'notes',
        'active',
        'user_id',
        'hs_platform_empresa_id', // <-- Importante para la integración
        'client_status', // <-- Permitir cambio de estado
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        // ... logic removed
    }
}
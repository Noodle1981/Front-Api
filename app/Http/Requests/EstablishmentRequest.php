<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstablishmentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajustar según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'address_street' => 'nullable|string|max:255',
            'address_city' => 'nullable|string|max:100',
            'address_zip_code' => 'nullable|string|max:10',
            'address_state' => 'nullable|string|max:100',
            'address_country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'active' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'client_id.required' => 'El cliente es obligatorio',
            'client_id.exists' => 'El cliente seleccionado no existe',
            'latitude.between' => 'La latitud debe estar entre -90 y 90',
            'longitude.between' => 'La longitud debe estar entre -180 y 180',
        ];
    }
}
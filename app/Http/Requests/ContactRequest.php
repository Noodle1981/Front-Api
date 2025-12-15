<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajustar según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'establishment_id' => 'nullable|exists:establishments,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'client_id.required' => 'El cliente es obligatorio',
            'client_id.exists' => 'El cliente seleccionado no existe',
            'establishment_id.exists' => 'El establecimiento seleccionado no existe',
            'email.email' => 'El email debe ser una dirección válida',
        ];
    }
}
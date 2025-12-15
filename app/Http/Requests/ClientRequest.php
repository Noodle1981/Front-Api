<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Ajustar según tu lógica de autorización
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'cuit' => 'nullable|string|max:15',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'fiscal_address_street' => 'nullable|string|max:255',
            'fiscal_address_zip_code' => 'nullable|string|max:10',
            'fiscal_address_city' => 'nullable|string|max:100',
            'fiscal_address_state' => 'nullable|string|max:100',
            'fiscal_address_country' => 'nullable|string|max:100',
            'economic_activity' => 'nullable|string|max:255',
            'art_provider' => 'nullable|string|max:255',
            'art_registration_date' => 'nullable|date',
            'hs_manager_name' => 'nullable|string|max:255',
            'hs_manager_contact' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.email' => 'El email debe ser una dirección válida',
            'website.url' => 'El sitio web debe ser una URL válida',
            'user_id.exists' => 'El usuario asignado no existe',
        ];
    }
}
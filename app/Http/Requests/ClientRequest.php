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
            'fantasy_name' => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'cuit' => 'required|string|size:11',
            'tax_condition' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'employees_count' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:clients,id',
            'branch_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'internal_notes' => 'nullable|string',
            'active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'company.required' => 'La Razón Social es obligatoria',
            'cuit.required' => 'El CUIT es obligatorio',
            'cuit.size' => 'El CUIT debe tener 11 caracteres',
            'email.email' => 'El email debe ser una dirección válida',
            'website.url' => 'El sitio web debe ser una URL válida',
            'user_id.exists' => 'El usuario asignado no existe',
        ];
    }
}
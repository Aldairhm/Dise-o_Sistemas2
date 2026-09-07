<?php
// app/Http/Requests/StoreProveedorRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'    => ['required', 'string', 'max:255'],
            'correo'    => ['nullable', 'email', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:9'],
            'direccion' => ['nullable', 'string', 'max:500'],
            ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
        ];
    }
}
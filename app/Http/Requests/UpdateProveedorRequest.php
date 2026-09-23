<?php
// app/Http/Requests/UpdateProveedorRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // El parámetro de la ruta puede ser el modelo o el ID.
        $proveedor = $this->route('proveedor');
        $proveedorId = $proveedor instanceof \App\Models\Proveedor ? $proveedor->id : $proveedor;

        return [
            'nombre'    => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('proveedors', 'nombre')->ignore($proveedorId)],
            'correo'    => ['nullable', 'email', 'max:255'],
            'telefono'  => ['nullable', 'string', 'max:9'],
            'direccion' => ['nullable', 'string', 'max:500'],
            'catalogo'  => ['nullable', 'string'],
            'archivo'   => ['nullable', 'file', 'mimes:pdf,xls,xlsx,png,jpg,jpeg', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'nombre.unique'   => 'Ya existe otro proveedor registrado con ese nombre.',
            'archivo.mimes'   => 'El archivo debe ser un PDF, Excel o Imagen.',
            'archivo.max'     => 'El archivo no debe pesar más de 5MB.',
        ];
    }
}
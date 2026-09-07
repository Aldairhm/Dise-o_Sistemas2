<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogoRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'id_proveedor'      => 'required|exists:proveedors,id',
            'nombre_referencia' => 'required|string|max:150',
            'tipo'              => 'required|in:enlace,archivo',
            'ruta_destino'      => 'required_if:tipo,enlace|nullable|url|max:255',
            'archivo'           => 'required_if:tipo,archivo|file|mimes:pdf,xls,xlsx,png,jpg,jpeg|max:5120',
        ];
    }
    
    public function messages()
    {
        return [
            'archivo.mimes' => 'El archivo debe ser un PDF, Excel o Imagen.',
            'archivo.max' => 'El archivo no debe pesar más de 5MB.',
        ];
    }
}
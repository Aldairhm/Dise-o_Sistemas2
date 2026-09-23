<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productoId = $this->route('producto')?->id ?? $this->route('producto');

        return [
            'nombre' => 'required|string|max:150|unique:producto,nombre,' . $productoId,
            'marca' => 'nullable|string|max:100',
            'id_categoria' => 'required|exists:categoria,id',
            'descripcion' => 'nullable|string|max:5000',
            'comision' => 'required|numeric|min:0|max:100',
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sku' => 'nullable|string|max:50|unique:producto,sku,' . $productoId,
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max'      => 'El nombre no puede superar 150 caracteres.',
            'nombre.unique'   => 'Ya existe otro producto con ese nombre.',
            'id_categoria.required' => 'Debes seleccionar una categoría.',
            'id_categoria.exists' => 'La categoría seleccionada no existe.',
            'comision.required' => 'La comisión es obligatoria.',
            'comision.min' => 'La comisión no puede ser negativa.',
            'comision.max' => 'La comisión no puede superar el 100%.',
            'imagen_principal.image' => 'El archivo debe ser una imagen.',
            'imagen_principal.max' => 'La imagen no puede pesar más de 2MB.',
            'sku.unique' => 'Este SKU ya está en uso por otro producto.',
        ];
    }
}

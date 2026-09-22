<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVarianteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $varianteId = $this->route('id');

        // Obtener el id_producto de la variante actual para la regla unique
        $idProducto = \App\Models\Variante::where('id', $varianteId)->value('id_producto');

        return [
            'nombre_variante' => [
                'required',
                'string',
                'max:200',
                Rule::unique('variante', 'nombre_variante')
                    ->where(fn($q) => $q->where('id_producto', $idProducto))
                    ->ignore($varianteId),
            ],
            'costo_promedio'      => 'required|numeric|min:0',
            'porcentaje_ganancia' => 'required|numeric|min:0',
            'precio_venta'        => 'required|numeric|min:0.01',
            'sku'                 => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('variante', 'sku')->ignore($varianteId),
            ],
            'imagenes'             => 'nullable|array|max:10',
            'imagenes.*'           => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'imagenes_eliminadas'  => 'nullable|array',
            'imagenes_eliminadas.*'=> 'integer|exists:variante_imagen,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_variante.required'     => 'El nombre de la variante es obligatorio.',
            'nombre_variante.max'          => 'El nombre no puede superar 200 caracteres.',
            'nombre_variante.unique'       => 'Ya existe una variante con ese nombre para este producto.',
            'precio_venta.required'        => 'El precio de venta es obligatorio.',
            'precio_venta.min'             => 'El precio debe ser mayor a 0.',
            'sku.unique'                   => 'Este SKU ya está en uso por otra variante.',
            'imagenes.max'                 => 'No puedes subir más de 10 imágenes por variante.',
            'imagenes.*.image'             => 'Cada archivo debe ser una imagen válida.',
            'imagenes.*.max'               => 'Cada imagen no puede pesar más de 2MB.',
            'imagenes_eliminadas.*.exists' => 'Una imagen a eliminar no existe o no pertenece a esta variante.',
        ];
    }
}

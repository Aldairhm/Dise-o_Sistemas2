<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /** Mostrar la página de configuración de perfil. */
    public function show()
    {
        $user = Auth::user();
        return view('perfil.index', compact('user'));
    }

    /** Actualizar datos personales: nombre, teléfono, correo. */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nombre_real' => ['required', 'string', 'max:255'],
            'telefono'    => ['nullable', 'string', 'regex:/^[267]\d{3}-\d{4}$/'],
            'username'    => ['required', 'string', 'email', 'max:255', Rule::unique('usuario', 'username')->ignore($user->id)],
        ], [
            'nombre_real.required' => 'El nombre real es obligatorio.',
            'telefono.regex'       => 'El teléfono debe tener el formato XXXX-XXXX (ej: 7890-1234). El primer dígito debe ser 2, 6 o 7.',
            'username.required'    => 'El correo electrónico es obligatorio.',
            'username.email'       => 'Ingresa un correo electrónico válido.',
            'username.unique'      => 'Este correo ya está registrado en otra cuenta.',
        ]);

        $user->update([
            'nombre_real' => $validated['nombre_real'],
            'telefono'    => $validated['telefono'] ?? null,
            'username'    => $validated['username'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente.',
        ]);
    }

    /** Cambiar contraseña del usuario autenticado. */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'string', 'min:8', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.min'              => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'La confirmación de contraseña no coincide.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual no es correcta.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => '¡Contraseña actualizada correctamente!',
        ]);
    }
}

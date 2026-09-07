<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Mostrar la lista de usuarios con filtros y estadísticas.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro de búsqueda por nombre_real o username
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(nombre_real) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(username) like ?', ["%{$search}%"]);
            });
        }

        // Filtro por rol
        if ($request->filled('rol') && $request->rol !== 'todos') {
            $query->where('rol', $request->rol);
        }

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', (int) $request->estado);
        }

        // Ordenamiento
        $users = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();

        // Estadísticas para las tarjetas KPI
        $stats = [
            'total'        => User::count(),
            'admins'       => User::where('rol', 'admin')->count(),
            'vendedores'   => User::where('rol', 'vendedor')->count(),
            'activos'      => User::where('estado', 1)->count(),
            'inactivos'    => User::where('estado', 0)->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html'    => view('usuarios.partials.table', compact('users'))->render(),
                'cards'   => view('usuarios.partials.cards', compact('users'))->render(),
                'stats'   => $stats,
            ]);
        }

        return view('usuarios.index', compact('users', 'stats'));
    }

    /**
     * Registrar un nuevo usuario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_real' => ['required', 'string', 'max:255'],
            'telefono'    => ['nullable', 'string', 'regex:/^[267]\d{3}-\d{4}$/'],
            'username'    => ['required', 'string', 'email', 'max:255', Rule::unique('usuario', 'username')],
            'rol'         => ['required', 'string', Rule::in(['admin', 'vendedor'])],
            'estado'      => ['required', 'integer', Rule::in([0, 1])],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre_real.required' => 'El nombre real es obligatorio.',
            'telefono.regex'       => 'El teléfono debe tener el formato de El Salvador: XXXX-XXXX (ej: 7890-1234). El primer dígito debe ser 2, 6 o 7.',
            'username.required'    => 'El correo electrónico es obligatorio.',
            'username.email'       => 'Debes ingresar un correo electrónico válido (ej: usuario@correo.com).',
            'username.unique'      => 'Este correo ya se encuentra registrado.',
            'rol.required'         => 'Debes seleccionar un rol para el usuario.',
            'estado.required'      => 'El estado de la cuenta es obligatorio.',
            'password.required'    => 'La contraseña es obligatoria.',
            'password.min'         => 'La contraseña debe contener al menos 8 caracteres.',
            'password.confirmed'   => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'nombre_real' => $validated['nombre_real'],
            'telefono'    => $validated['telefono'] ?? null,
            'username'    => $validated['username'],
            'rol'         => $validated['rol'],
            'estado'      => (int) $validated['estado'],
            'password'    => Hash::make($validated['password']),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '¡Usuario registrado exitosamente!',
                'user'    => $user,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', '¡Usuario registrado exitosamente!');
    }

    /**
     * Actualizar datos del usuario existente.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nombre_real' => ['required', 'string', 'max:255'],
            'telefono'    => ['nullable', 'string', 'regex:/^[267]\d{3}-\d{4}$/'],
            'username'    => ['required', 'string', 'email', 'max:255', Rule::unique('usuario', 'username')->ignore($user->id)],
            'rol'         => ['required', 'string', Rule::in(['admin', 'vendedor'])],
            'estado'      => ['required', 'integer', Rule::in([0, 1])],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre_real.required' => 'El nombre real es obligatorio.',
            'telefono.regex'       => 'El teléfono debe tener el formato de El Salvador: XXXX-XXXX (ej: 7890-1234). El primer dígito debe ser 2, 6 o 7.',
            'username.required'    => 'El correo electrónico es obligatorio.',
            'username.email'       => 'Debes ingresar un correo electrónico válido (ej: usuario@correo.com).',
            'username.unique'      => 'Este correo ya se encuentra registrado por otra cuenta.',
            'rol.required'         => 'Debes seleccionar un rol.',
            'estado.required'      => 'El estado es obligatorio.',
            'password.min'         => 'La contraseña debe contener al menos 8 caracteres.',
            'password.confirmed'   => 'La confirmación de contraseña no coincide.',
        ]);

        $updateData = [
            'nombre_real' => $validated['nombre_real'],
            'telefono'    => $validated['telefono'] ?? null,
            'username'    => $validated['username'],
            'rol'         => $validated['rol'],
            'estado'      => (int) $validated['estado'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '¡Usuario actualizado exitosamente!',
                'user'    => $user,
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', '¡Usuario actualizado exitosamente!');
    }

    /**
     * Alternar el estado activo / inactivo de un usuario.
     */
    public function toggleStatus(Request $request, User $user)
    {
        // No permitir desactivarse a sí mismo si es el usuario logueado
        if (Auth::id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propia cuenta activa.',
            ], 422);
        }

        $user->estado = $user->estado == 1 ? 0 : 1;
        $user->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Estado del usuario actualizado a ' . ($user->estado == 1 ? 'Activo' : 'Inactivo'),
            'new_state' => $user->estado,
        ]);
    }

    /**
     * Eliminar un usuario.
     */
    public function destroy(Request $request, User $user)
    {
        // No permitir eliminarse a sí mismo
        if (Auth::id() === $user->id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta en sesión.',
                ], 422);
            }
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $userName = $user->nombre_real ?? $user->username;
        $user->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "El usuario '{$userName}' ha sido desactivado y removido de la lista.",
            ]);
        }

        return redirect()->route('usuarios.index')->with('success', "El usuario '{$userName}' ha sido removido.");
    }
}

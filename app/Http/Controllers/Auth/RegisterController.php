<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /** Mostrar formulario de registro */
    public function show()
    {
        return view('auth.register');
    }

    /** Procesar envío del formulario */
    public function store(Request $request)
    {
        // 1) Validación
        $data = $request->validate([
            'username'              => 'required|string|max:50|unique:usuarios,username',
            'nombre'                => 'required|string|max:255',
            'email'                 => 'required|email|max:191|unique:usuarios,email',
            'password'              => 'required|string|min:8|confirmed',
            'rol_id'                => 'required|integer|exists:roles,id',
        ]);

        // 2) Hashear contraseña
        $data['password'] = Hash::make($data['password']);

        // 3) Insertar en la tabla 'usuarios'
        $id = DB::table('usuarios')->insertGetId([
            'username'       => $data['username'],
            'nombre'         => $data['nombre'],
            'email'          => $data['email'],
            'password'       => $data['password'],
            'rol_id'         => $data['rol_id'],
            'activo'         => 1,
            'remember_token' => null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 4) Redirigir al login con mensaje de éxito
        return redirect()
            ->route('login')   // Asumiendo que tienes ruta 'login'
            ->with('success', 'Registro exitoso. Por favor, inicia sesión.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;      // Para Query Builder
use Illuminate\Support\Facades\Hash;   // Para encriptar contraseñas
use Illuminate\Support\Facades\Auth;   // Para manejar autenticación (login, logout, check)
use Illuminate\Support\Facades\Validator; // Para validar formularios
use Illuminate\Validation\Rules\Password; // Reglas de contraseña más robustas

class AuthController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register'); // Crearemos esta vista
    }

    public function register(Request $request)
    {
        // 1. Validación
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:50|unique:usuarios,username', // unique en tabla 'usuarios', columna 'username'
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:usuarios,email', // unique en tabla 'usuarios', columna 'email'
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()], // Requiere confirmación (password_confirmation) y complejidad
            'rol_id' => 'required|integer|exists:roles,id' // Asegura que el rol exista en la tabla roles
        ]);

        if ($validator->fails()) {
            return redirect()->route('register.form')
                        ->withErrors($validator) // Reenvía los errores a la vista
                        ->withInput(); // Reenvía los datos ingresados (excepto password)
        }

        // 2. Verificar si rol existe (doble chequeo, aunque 'exists' ya lo hace)
        $rolExists = DB::table('roles')->where('id', $request->input('rol_id'))->exists();
        if (!$rolExists) {
             return redirect()->route('register.form')
                        ->withErrors(['rol_id' => 'El rol seleccionado no es válido.'])
                        ->withInput();
        }

        try {
            $userId = DB::table('usuarios')->insertGetId([ // insertGetId devuelve el ID del nuevo registro
                'username' => $request->input('username'),
                'nombre' => $request->input('nombre'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')), // ¡Encriptar contraseña!
                'rol_id' => $request->input('rol_id'),
                'activo' => 1, // Activo por defecto
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. (Opcional pero recomendado) Loguear al usuario recién registrado
            Auth::loginUsingId($userId);
            $request->session()->regenerate(); // Regenerar sesión por seguridad

            // 5. Redirigir a una página post-registro (ej. dashboard)
            return redirect()->route('dashboard')->with('success', '¡Registro exitoso!');

        } catch (\Exception $e) {
            // Manejo básico de errores (puedes loggear el error $e->getMessage())
            return redirect()->route('register.form')
                        ->withErrors(['error_inesperado' => 'Ocurrió un error durante el registro. Inténtalo de nuevo.'])
                        ->withInput();
        }
    }
    public function showLoginForm()
    {
        return view('auth.login'); // Crearemos esta vista
    }

    public function login(Request $request)
    {
        // 1. Validación
        $credentials = $request->validate([
            // Permitir login con email o username
            'login_identificador' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Determinar si el identificador es email o username
        $fieldType = filter_var($request->input('login_identificador'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Intentar autenticar usando la fachada Auth
        // Auth::attempt busca el usuario, hashea el password ingresado y lo compara con el hash en la BD
        if (Auth::attempt([$fieldType => $credentials['login_identificador'], 'password' => $credentials['password']], $request->filled('remember'))) {
            // Autenticación exitosa...

            // Regenerar la sesión para prevenir session fixation
            $request->session()->regenerate();

            // Redirigir al dashboard o a la ruta intentada antes del login
            return redirect()->intended(route('dashboard'))->with('success', '¡Inicio de sesión exitoso!');
        }

        // 4. Autenticación fallida...
        return back()->withErrors([
            'login_identificador' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('login_identificador'); // Solo rellenar el campo identificador, no el password
    }

    // --- Logout ---

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Cierra la sesión

        $request->session()->invalidate(); // Invalida la sesión actual

        $request->session()->regenerateToken(); // Regenera el token CSRF

        return redirect()->route('login.form')->with('success', 'Has cerrado sesión correctamente.');
    }
}

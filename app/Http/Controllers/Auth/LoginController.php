<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    function showLoginForm()
    {
        return view('auth.login');
    }

    //Login

    public function login(Request $request)
    {
        try {
            // 1. Validar los datos de entrada
            $validatedData = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
    
            // 2. Verificar si el usuario existe antes de autenticar
            $user = DB::table('usuarios')
                ->where('username', $validatedData['username'])
                ->where('activo', 1)
                ->first();
    
            if (!$user) {
                return back()
                    ->withInput($request->only('username'))
                    ->withErrors(['username' => 'Usuario no encontrado o inactivo.']);
            }
    
            // 3. Preparar las credenciales
            $credentials = [
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
                'activo' => 1,
            ];
    
            // 4. Intentar autenticar al usuario
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();
    
                // 5. Redirigir según el rol_id
                switch ($user->rol_id) {
                    case 1: // Administrador
                        echo "admin";
                        return redirect()->intended(route('admin.dashboard'));
                    case 4: // Consulta
                        return redirect()->intended(route('consulta.dashboard'));
                    default:
                        Auth::logout();
                        return redirect('/login')->with('error', 'Rol de usuario no autorizado para acceder.');
                }
            }
    
            // 6. Contraseña incorrecta
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['password' => 'Contraseña incorrecta.']);
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Loguear el error específico para depuración
            \Log::error('Error de base de datos durante login: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['error' => 'Error de conexión a la base de datos: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Error durante login: ' . $e->getMessage());
            
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['error' => 'Errorrrr: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

}

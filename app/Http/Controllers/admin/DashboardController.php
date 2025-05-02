<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Obtiene el usuario autenticado (el objeto de DB::table si usas driver database)

        // Verificar si el usuario tiene el rol_id de Administrador (ej: 1)
        if ($user->rol_id !== 1) { // Ajusta el ID si es diferente
            // Si no es administrador, redirigir o mostrar un error
            return redirect('/')->with('error', 'No tienes permisos para acceder a esta área.');
        }
        \Log::info('Acceso al dashboard de administrador por el usuario: ' . $user->nombre);

        // Si es administrador, cargar la vista del dashboard
        return view('admin.dashboard'); // Asegúrate de que esta vista exista
    }

    public function editProfile()
    {
        $user = Auth::user();

        // Validaciones básicas (Rol, Vinculación)
        if ($user->rol_id !== 4 || is_null($user->responsable_id)) {
             return redirect('/')->with('error', 'Acceso no autorizado o perfil no configurado.');
        }

        // Cargar datos necesarios con relaciones
        try {
            $user->load([
                'responsable' => function($query) {
                    $query->with([
                        'direccion' => function($q_dir) {
                            $q_dir->with(['localidad' => function($q_loc) {
                                $q_loc->with(['municipio' => function ($q_mun){
                                    $q_mun->with('departamento'); // Carga anidada hasta departamento
                                }]);
                            }]);
                        }
                    ]);
                }
            ]);

            // Obtener todos los departamentos para el primer select
            $departamentos = Departamento::orderBy('nombre')->get();
            // Obtener municipios y localidades iniciales (si hay dirección)
            $municipios = collect(); // Colección vacía por defecto
            $localidades = collect(); // Colección vacía por defecto

            if ($user->responsable && $user->responsable->direccion && $user->responsable->direccion->localidad && $user->responsable->direccion->localidad->municipio) {
                $currentMunicipio = $user->responsable->direccion->localidad->municipio;
                $currentDepartamentoId = $currentMunicipio->departamento_id;
                // Cargar municipios del departamento actual
                $municipios = Municipio::where('departamento_id', $currentDepartamentoId)->orderBy('nombre')->get();
                // Cargar localidades del municipio actual
                $localidades = Localidad::where('municipio_id', $currentMunicipio->id)->orderBy('nombre')->get();
            }


            return view('consulta.edit', compact('user', 'departamentos', 'municipios', 'localidades'));

        } catch (\Exception $e) {
           
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'No se pudo cargar la información de tu perfil.'. $e->getMessage());
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validaciones básicas (Rol, Vinculación)
        if ($user->rol_id !== 4 || is_null($user->responsable_id)) {
             return redirect('/')->with('error', 'Acceso no autorizado o perfil no configurado.');
        }

        // Obtener el responsable asociado
        $responsable = Responsable::find($user->responsable_id);
        if (!$responsable) {
             return redirect()->route('consulta.perfil.edit')->with('error', 'No se encontró el registro responsable asociado.');
        }

        // 1. Validar los datos del formulario
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                // ***** LÍNEA CORREGIDA *****
                Rule::unique('usuarios', 'email')->ignore($user->id), // Usar Rule::unique
                // ***** FIN CORRECCIÓN *****
            ],
            'telefono' => 'nullable|string|max:25',
            // Dirección
            'calle_numero' => 'nullable|string|max:255',
            'colonia_barrio' => 'nullable|string|max:150',
            'codigo_postal' => 'nullable|string|max:10',
            'departamento_id' => 'required|integer|exists:departamentos,id',
            'municipio_id' => 'required|integer|exists:municipios,id',
            'localidad_id' => 'required|integer|exists:localidades,id',
            'referencia_adicional' => 'nullable|string|max:65535',
        
        ]);

        // Iniciar transacción
        DB::beginTransaction();
        try {
            // 2. Actualizar datos del Usuario
            $user->nombre = $validated['nombre'];
            $user->email = $validated['email'];
            if (!empty($validated['new_password'])) {
                $user->password = Hash::make($validated['new_password']);
            }
            $user->save();

            // 3. Actualizar datos del Responsable
            $responsable->telefono = $validated['telefono'];
            $responsable->save(); // Guardar teléfono

            // 4. Actualizar o Crear Dirección
            $direccionData = [
                'calle_numero' => $validated['calle_numero'],
                'colonia_barrio' => $validated['colonia_barrio'],
                'codigo_postal' => $validated['codigo_postal'],
                'localidad_id' => $validated['localidad_id'],
                'referencia_adicional' => $validated['referencia_adicional'],
                'pais' => 'Guatemala',
            ];

            if ($responsable->direccion_id) {
                $direccion = Direccion::find($responsable->direccion_id);
                if ($direccion) {
                    $direccion->update($direccionData);
                } else {
                    $newDireccion = Direccion::create($direccionData);
                    $responsable->direccion_id = $newDireccion->id;
                    $responsable->save();
                }
            } else {
                $newDireccion = Direccion::create($direccionData);
                $responsable->direccion_id = $newDireccion->id;
                $responsable->save();
            }

            // Confirmar transacción
            DB::commit();

            return redirect()->route('consulta.perfil.edit')->with('success', 'Perfil actualizado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
             DB::rollBack();
            return redirect()->route('consulta.perfil.edit')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar perfil (Usuario ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('consulta.perfil.edit')->with('error', 'Ocurrió un error al actualizar tu perfil.');
        }
    }

    
}

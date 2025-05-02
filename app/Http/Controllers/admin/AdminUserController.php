<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rol;
use App\Models\Responsable; // <-- IMPORTAR Responsable
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Direccion; // <-- IMPORTAR Direccion
use App\Models\Departamento; // <-- IMPORTAR Departamento
use App\Models\Municipio; // <-- IMPORTAR Municipio
use App\Models\Localidad; // <-- IMPORTAR Localidad
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Muestra la lista de usuarios.
     */
    public function index(Request $request)
    {
        try {
            // Obtener usuarios con su rol, paginados
            // Añadir búsqueda si se desea
            $query = User::with('rol')->orderBy('nombre');
            
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }
             if ($request->filled('rol_id')) {
                $query->where('rol_id', $request->input('rol_id'));
            }
             if ($request->filled('activo')) {
                 $activoValue = $request->input('activo') === '1' ? 1 : 0;
                 $query->where('activo', $activoValue);
             }

            $users = $query->paginate(15)->withQueryString(); // withQueryString mantiene filtros en paginación
            $roles = Rol::orderBy('nombre')->get(); // Para el filtro

            return view('admin.usuarios.index', compact('users', 'roles'));

        } catch (\Exception $e) {
            Log::error("Error al listar usuarios admin: " . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'No se pudieron cargar los usuarios.');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        try {
            $roles = Rol::orderBy('nombre')->pluck('nombre', 'id');
            $departamentos = Departamento::orderBy('nombre')->get(); // <-- AÑADIR ESTO
            $municipios = Municipio::orderBy('nombre')->get(); // <-- AÑADIR ESTO
            return view('admin.usuarios.create', compact('roles', 'departamentos','municipios')); // <-- PASAR DEPARTAMENTOS
        } catch (\Exception $e) {
            Log::error("Error al mostrar form crear usuario admin: " . $e->getMessage());
            return redirect()->route('admin.usuarios.index')->with('error', 'No se pudo abrir el formulario de creación.');
        }
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        
        
        // 1. Validación Base
        $baseRules = [
            'rol_id' => 'required|integer|exists:roles,id',
            'email' => 'required|string|email|max:191|unique:usuarios,email',
            'password' => ['required', 'confirmed', Password::min(3)],
        ];
        \Log::info("Entrando al store de usuarios adminnnnnnnnnnn", ['rol_id' => $request->input('rol_id')]);
        // 2. Validación Condicional
        $conditionalRules = [];
        if ($request->input('rol_id') == 4) { // Rol de Consulta
            \Log::info("Entrando al store de usuarios admin2", ['rol_id' => $request->input('rol_id')]);
            $conditionalRules = [
                // Usuario (Consulta)
                'nombre' => 'required|string|max:255', // Nombre Familia/General
                'username' => 'required|string|max:50|unique:usuarios,username',

                // Responsable (Obligatorio)
                'resp_nombres' => 'required|string|max:150',
                'resp_apellidos' => 'required|string|max:150',
                'resp_dpi' => 'required|string|max:20|unique:responsables,dpi',
                'resp_telefono' => 'nullable|string|max:25',
                'resp_correo_electronico' => 'nullable|string|email|max:191|unique:responsables,correo_electronico',

                // Dirección (Obligatoria selección, otros campos opcionales)
                'resp_addr_departamento_id' => 'required|integer|exists:departamentos,id',
                'resp_addr_municipio_id' => 'required|integer|exists:municipios,id',
                'resp_addr_calle_numero' => 'nullable|string|max:255',
                'resp_addr_colonia_barrio' => 'nullable|string|max:150',
                'resp_addr_codigo_postal' => 'nullable|string|max:10',
                'resp_addr_referencia' => 'nullable|string',
            ];
        } else { // OTROS ROLES
            \Log::info("Entrando al store de usuarios admin3", ['rol_id' => $request->input('rol_id')]);
            $conditionalRules = [
                'nombre' => 'required|string|max:255', // Nombre normal
                'username' => 'nullable|string|max:50|unique:usuarios,username',
            ];
        }

        // 3. Combinar y Validar
        $validated = $request->validate(array_merge($baseRules, $conditionalRules), [
            // ... (mensajes existentes) ...
            'resp_addr_departamento_id.required' => 'El departamento es requerido para la dirección del responsable.',
            'resp_addr_municipio_id.required' => 'El municipio es requerido para la dirección del responsable.',
            'resp_addr_localidad_id.required' => 'La localidad/zona es requerida para la dirección del responsable.',
        ]);

        DB::beginTransaction();
        try {
            $responsableIdParaUsuario = null;
            $nombreUsuarioFinal = $validated['nombre'];
            Log::info("STORE: Dentro del TRY, antes del IF de rol_id=4");
    
            if ($validated['rol_id'] == 4) {
                Log::info("STORE: Rol es 4, procesando Dirección y Responsable...");
    
                // a. Crear la Dirección
                $direccionData = [
                    'calle_numero' => $validated['resp_addr_calle_numero'] ?? null, // Usa null coalescing por si acaso
                    'colonia_barrio' => $validated['resp_addr_colonia_barrio'] ?? null,
                    'codigo_postal' => $validated['resp_addr_codigo_postal'] ?? null,
                    'municipio_id' => $validated['resp_addr_municipio_id'], // Ya validado como required
                    // 'localidad_id' => $validated['resp_addr_localidad_id'], // Quitado
                    'referencia_adicional' => $validated['resp_addr_referencia'] ?? null,
                    'pais' => 'Guatemala',
                ];
                Log::info("STORE: Datos para Dirección:", $direccionData);
                $nuevaDireccion = Direccion::create($direccionData);
                Log::info("STORE: Dirección creada OK con ID: " . $nuevaDireccion->id);
    
                // b. Crear el Responsable
                $responsableData = [
                    'nombres' => $validated['resp_nombres'],
                    'apellidos' => $validated['resp_apellidos'],
                    'dpi' => $validated['resp_dpi'],
                    'telefono' => $validated['resp_telefono'] ?? null,
                    'correo_electronico' => $validated['resp_correo_electronico'] ?? null,
                    'direccion_id' => $nuevaDireccion->id,
                ];
                 Log::info("STORE: Datos para Responsable:", $responsableData);
                $nuevoResponsable = Responsable::create($responsableData);
                Log::info("STORE: Responsable creado OK con ID: " . $nuevoResponsable->id);
                $responsableIdParaUsuario = $nuevoResponsable->id;
            }
    
            // c. Crear el Usuario
            $userData = [
                'nombre' => $nombreUsuarioFinal,
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'rol_id' => $validated['rol_id'],
                'password' => Hash::make($validated['password']),
                'responsable_id' => $responsableIdParaUsuario,
                'activo' => true,
            ];
             Log::info("STORE: Datos para Usuario:", $userData);
            $newUser = User::create($userData);
            Log::info("STORE: Usuario creado OK con ID: " . $newUser->id);
    
            Log::info("STORE: Intentando hacer commit...");
            DB::commit();
            Log::info("STORE: Commit exitoso.");
            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    
        } catch (\Illuminate\Validation\ValidationException $e) { // Captura específica para Validación
            DB::rollBack();
            Log::error("STORE: Error de Validación al guardar usuario: " . $e->getMessage(), ['errors' => $e->errors()]);
            return redirect()->route('admin.usuarios.create')
                             ->withErrors($e->errors())
                             ->withInput();
        } catch (\Exception $e) { // Captura genérica para OTROS errores
            DB::rollBack();
            Log::error("STORE: Error GENERAL al guardar usuario/responsable/direccion: TIPO[" . get_class($e) . "] MENSAJE[" . $e->getMessage() . "] ARCHIVO[" . $e->getFile() . "] LINEA[" . $e->getLine() . "]", [
                // 'trace' => $e->getTraceAsString(), // Descomenta para full trace
                'request' => $request->except('password', 'password_confirmation')
            ]);
            // Mensaje para el usuario más genérico
            return redirect()->route('admin.usuarios.create')
                             ->with('error', 'No se pudo crear el usuario debido a un error interno. Por favor, contacte al soporte.')
                             ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * Usamos Route Model Binding para obtener el usuario.
     */
    public function edit(User $user) // Laravel inyecta el User automáticamente por el {user} en la ruta
    {
        try {
            $roles = Rol::orderBy('nombre')->pluck('nombre', 'id');
            // Aquí podríamos cargar responsables si quisiéramos editar la vinculación
            return view('admin.usuarios.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
             Log::error("Error al mostrar form editar usuario admin (ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('admin.usuarios.index')->with('error', 'No se pudo abrir el formulario de edición.');
        }
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     */
    public function update(Request $request, User $user)
    {
         $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'username' => ['nullable','string','max:50', Rule::unique('usuarios', 'username')->ignore($user->id)],
            'email' => ['required','string','email','max:191', Rule::unique('usuarios', 'email')->ignore($user->id)],
            'rol_id' => 'required|integer|exists:roles,id',
            'activo' => 'required|boolean', // Para editar el estado activo/inactivo
            
            'password' => ['nullable', 'confirmed', Password::min(3)],
            'responsable_id' => 'nullable|integer|exists:responsables,id', // Solo si el rol es Consulta
        ]);

         try {
            $updateData = [
                'nombre' => $validated['nombre'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'rol_id' => $validated['rol_id'],
                'activo' => $validated['activo'],
                // Solo asignar responsable si el rol es Consulta, si no, null
                'responsable_id' => ($validated['rol_id'] == 4 && isset($validated['responsable_id'])) ? $validated['responsable_id'] : null,
            ];

            // Actualizar contraseña solo si se proporcionó una nueva
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');

         } catch (\Exception $e) {
             Log::error("Error al actualizar usuario admin (ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('admin.usuarios.edit', $user)->with('error', 'No se pudo actualizar el usuario.')->withInput();
        }
    }

    /**
     * Cambia el estado activo/inactivo de un usuario.
     */
    public function toggleStatus(User $user)
    {
        // Evitar que el admin se desactive a sí mismo
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.usuarios.index')->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        try {
            $user->activo = !$user->activo;
            $user->save();
            $status = $user->activo ? 'activado' : 'desactivado';
            return redirect()->route('admin.usuarios.index')->with('success', "Usuario {$status} correctamente.");
        } catch (\Exception $e) {
             Log::error("Error al cambiar estado usuario admin (ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('admin.usuarios.index')->with('error', 'No se pudo cambiar el estado del usuario.');
        }
    }
}
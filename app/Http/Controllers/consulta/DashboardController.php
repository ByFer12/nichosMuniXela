<?php

namespace App\Http\Controllers\Consulta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contrato;
use App\Models\Nicho;
use App\Models\Exhumacion;
use App\Models\Ocupante;
use App\Models\Solicitud;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Localidad;  
use Carbon\Carbon;
use App\Models\Responsable;
use App\Models\Direccion;
use App\Models\Rol;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DashboardController extends Controller
{
    // El método index que ya tenías para el dashboard
       public function index()
    {
        
        $user = Auth::user();

        // 1. Validación del Rol
        if ($user->rol_id !== 4) { // Ajusta el ID 4 si es diferente para Consulta
            Auth::logout();
            return redirect('/login')->with('error', 'Acceso no autorizado.');
        }

        // 2. Validación de Vinculación con Responsable
        if (is_null($user->responsable_id)) {
             return redirect('/') // Redirige a home como alternativa
                      ->with('warning', 'Tu usuario no está vinculado a ningún responsable para consultar información. Contacta al administrador.');
        }

        // 3. Obtener TODOS los contratos para el Select del Modal
        $contratosParaSelect = []; // Inicializa
        try {
            
            // Obtener TODOS los contratos (activos e inactivos) del responsable
            $contratosParaSelect = Contrato::where('responsable_id', $user->responsable_id)
                // Cargar relaciones necesarias para mostrar info útil en el select
                // Seleccionar solo columnas necesarias de las relaciones es más eficiente
                ->with([
                    'nicho:id,codigo', // Solo id y codigo de nicho
                    'ocupante:id,nombres,apellidos' // Solo id y nombres/apellidos de ocupante
                ])
                // Seleccionar columnas del contrato principal
                ->select('id', 'nicho_id', 'ocupante_id', 'fecha_fin_original', 'activo')
                ->orderBy('activo', 'desc') // Mostrar activos primero (opcional)
                ->orderBy('fecha_fin_original', 'desc') // Luego ordenar por fecha
                ->get(); // Quitamos ->where('activo', true) para obtener TODOS

        } catch (\Exception $e) {
            Log::error("Error al obtener contratos para select modal (Usuario ID: {$user->id}): " . $e->getMessage());
            
             return view('consulta.dashboard', compact('user', 'contratosParaSelect'))
                      ->with('error_interno', 'No se pudieron cargar los datos de tus contratos para la solicitud. Intenta recargar la página.');
        }

        
        return view('consulta.dashboard', compact('user', 'contratosParaSelect'));
    }

    // --- NUEVO MÉTODO ---
    public function showMyContracts()
    {
        $user = Auth::user();
        
        // Doble verificación (aunque el middleware ya debería proteger)
        if ($user->rol_id !== 4) {
            Auth::logout();
            return redirect('/login')->with('error', 'Acceso no autorizado.');
        }

        // **Obtener el ID del responsable asociado al usuario**
        $responsableId = $user->responsable_id;

        // Si por alguna razón no está vinculado, mostrar error
        if (is_null($responsableId)) {
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'Tu cuenta no está asociada a un responsable.');
        }

        // **Consultar los contratos del responsable**
        // Carga ansiosa (eager loading) para evitar N+1 queries en la vista
        $contratos = Contrato::where('responsable_id', $responsableId)
            ->with([
                'nicho' => function ($query) {
                    // Cargar también las relaciones de Nicho (Tipo y Estado)
                    $query->with(['tipoNicho', 'estadoNicho']);
                },
                'ocupante' // Cargar la información del ocupante
                // Podrías cargar pagos aquí si necesitaras mostrar estado de pago
                // 'pagos' => function($q) { $q->orderBy('fecha_emision', 'desc'); }
            ])
            ->orderBy('fecha_inicio', 'desc') // Ordenar por fecha de inicio descendente (más nuevos primero)
            ->get();

        // Pasar los contratos encontrados a la nueva vista
        return view('consulta.mis_contratos', compact('contratos'));
    }


    public function requestBoletaFromModal(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'contrato_id_modal' => 'required|integer|exists:contratos,id',
            'observaciones_usuario_modal' => 'nullable|string|max:500',
        ]);

        $contratoId = $validated['contrato_id_modal'];
        /** @var Contrato|null $contrato */ // Type hint para autocompletado
        $contrato = Contrato::find($contratoId);

  
        if (!$contrato || $user->rol_id !== 4 || $contrato->responsable_id !== $user->responsable_id) {
            
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'No tienes permiso o el contrato no existe.');
        }

        
        if ($contrato->activo) {
             return redirect()->route('consulta.dashboard')
                     ->with('error', 'No puedes solicitar la boleta de renovación aún. El contrato está vigente hasta el' . $fechaFinOriginal->format('d/m/Y') . ". Podrás solicitarla a partir del " . $fechaInicioSolicitud->format('d/m/Y') . ".");
        }

        // --- Validación 3: Lógica de Tiempo para Solicitud (Renovación) ---
        $hoy = Carbon::today();
        $fechaFinOriginal = Carbon::parse($contrato->fecha_fin_original);
        $fechaFinGracia = Carbon::parse($contrato->fecha_fin_gracia);
        // Período permitido: Desde X meses antes del fin original hasta el fin del período de gracia
        $mesesAntesParaSolicitar = 3; // Puedes ajustar este valor
        $fechaInicioSolicitud = $fechaFinOriginal->copy()->subMonths($mesesAntesParaSolicitar);

        // Comprobamos si HOY está DESPUÉS de la fecha de fin de gracia (ya expiró totalmente)
        if ($hoy->gt($fechaFinGracia)) { // gt() significa "greater than" (mayor que)
             return redirect()->route('consulta.dashboard')
                      ->with('error', "El período para solicitar la boleta de este contrato (incluyendo gracia) finalizó el " . $fechaFinGracia->format('d/m/Y') . ". Contacta a la administración.");
        }
        // Si llegamos aquí, estamos dentro del período permitido (desde $fechaInicioSolicitud hasta $fechaFinGracia)
        // --- Fin Validación 3 ---


        // --- Validación 4: Solicitud Pendiente Existente ---
        $existeSolicitudPendiente = Solicitud::where('contrato_id', $contratoId)
                                            ->where('tipo_solicitud', 'boleta')
                                            ->whereIn('estado', ['pendiente', 'en_proceso']) // Considerar también 'en_proceso'
                                            ->exists();
        if ($existeSolicitudPendiente) {
            return redirect()->route('consulta.dashboard')
                     ->with('warning', 'Ya existe una solicitud de boleta pendiente o en proceso para este contrato.');
        }

        // --- Crear la solicitud ---
        try {
            Solicitud::create([
                'tipo_solicitud' => 'boleta',
                'contrato_id' => $contratoId,
                'usuario_solicitante_id' => $user->id,
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'observaciones_usuario' => $validated['observaciones_usuario_modal'],
            ]);

            // Redirigir (puedes elegir el dashboard o la lista de contratos)
            return redirect()->route('consulta.dashboard')
                     ->with('success', "Solicitud de boleta (Renovación/Pago Contrato #{$contratoId}) enviada correctamente.");

        } catch (\Exception $e) {
            Log::error("Error al crear solicitud de boleta desde modal: " . $e->getMessage(), [
                'usuario_id' => $user->id,
                'contrato_id' => $contratoId, // Usar la variable que tienes
            ]);
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'Ocurrió un error inesperado al procesar tu solicitud.');
        }
    }

    public function requestExhumacionFromModal(Request $request)
    {
        $user = Auth::user();

        // 1. Validar la entrada del formulario modal
        $validated = $request->validate([
            // Usamos un nombre diferente para evitar colisiones si ambos modales están en la misma página
            'contrato_id_exh_modal' => 'required|integer|exists:contratos,id',
            'motivo_exhumacion_modal' => 'required|string|max:1000', // Motivo es requerido
        ]);

        $contratoId = $validated['contrato_id_exh_modal'];
        $motivo = $validated['motivo_exhumacion_modal'];

        /** @var Contrato|null $contrato */
        // Cargar relaciones necesarias para las validaciones
        $contrato = Contrato::with(['nicho', 'ocupante'])->find($contratoId);

        // 2. Validación: Autorización y Existencia
        if (!$contrato || $user->rol_id !== 4 || $contrato->responsable_id !== $user->responsable_id) {
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'No tienes permiso o el contrato seleccionado no existe.');
        }

        // 3. Validación: ¿Contrato tiene Ocupante? (Necesario para exhumar)
        if (is_null($contrato->ocupante_id) || is_null($contrato->ocupante)) {
             return redirect()->route('consulta.dashboard')
                      ->with('error', 'El contrato seleccionado no parece tener un ocupante registrado para exhumar.');
        }

        // 4. Validación: ¿Nicho es Histórico?
        if ($contrato->nicho && $contrato->nicho->es_historico) {
             return redirect()->route('consulta.dashboard')
                     ->with('error', 'No se puede solicitar la exhumación para un nicho marcado como histórico.');
        }
        if ($contrato->activo) {
            return redirect()->route('consulta.dashboard')
                    ->with('error', 'No se puede solicitar exhumaciones para un contrato inactivo.');
       }

        // 5. Validación: ¿Ya existe una Solicitud o Proceso de Exhumación en curso?
        // Buscar en tabla 'solicitudes'
        $existeSolicitudExhPendiente = Solicitud::where('contrato_id', $contratoId)
                                                ->where('tipo_solicitud', 'exhumacion')
                                                ->whereIn('estado', ['pendiente', 'en_proceso'])
                                                ->exists();
        // Buscar en tabla 'exhumaciones' (procesos ya iniciados/aprobados por admin)
        // Comparamos por ocupante_id ya que es lo que se exhuma
        $existeProcesoExhEnCurso = Exhumacion::where('ocupante_exhumado_id', $contrato->ocupante_id)
                                             ->whereNotIn('estado_exhumacion_id', [
                                                 /* IDs de estados 'Rechazada', 'Realizada', 'Cancelada' */
                                                 // Debes obtener estos IDs de tu tabla cat_estados_exhumacion
                                                 // Ejemplo: Suponiendo que 4=Rechazada, 5=Realizada, 6=Cancelada
                                                 4, 5, 6
                                             ])
                                             ->exists();

        if ($existeSolicitudExhPendiente || $existeProcesoExhEnCurso) {
            return redirect()->route('consulta.dashboard')
                     ->with('warning', 'Ya existe una solicitud o proceso de exhumación en curso para este contrato/ocupante.');
        }

     
        try {
            Solicitud::create([
                'tipo_solicitud' => 'exhumacion', // <--- Tipo cambiado
                'contrato_id' => $contratoId,
                'usuario_solicitante_id' => $user->id,
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'observaciones_usuario' => $motivo, // Guardamos el motivo aquí
            ]);

            return redirect()->route('consulta.dashboard')
                     ->with('success', "Solicitud de exhumación para el ocupante del Contrato #{$contratoId} enviada correctamente. La administración revisará tu petición.");

        } catch (\Exception $e) {
            Log::error("Error al crear solicitud de exhumación desde modal: " . $e->getMessage(), [
                'usuario_id' => $user->id,
                'contrato_id' => $contratoId,
            ]);
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'Ocurrió un error inesperado al procesar tu solicitud de exhumación.');
        }
    }

    public function showMyRequests()
    {
        $user = Auth::user();

        // 1. Validaciones (Rol, Vinculación Responsable)
        if ($user->rol_id !== 4) {
            Auth::logout();
            return redirect('/login')->with('error', 'Acceso no autorizado.');
        }
        if (is_null($user->responsable_id)) {
             return redirect('/')
                      ->with('warning', 'Tu usuario no está vinculado a ningún responsable para consultar información.');
        }

        try {
            $solicitudes = Solicitud::where('usuario_solicitante_id', $user->id)
                ->with([
                    'contrato' => function($query) {
                        $query->with([
                            'nicho:id,codigo',
                            'ocupante:id,nombres,apellidos',
                            // *** NUEVO: Cargar el ÚLTIMO pago PENDIENTE asociado al contrato ***
                            'pagos' => function ($q_pago) {
                                $q_pago->where('estado_pago_id', 1) // Asumiendo ID 1 = Pendiente
                                       ->orderBy('fecha_emision', 'desc')
                                       ->select('id', 'contrato_id', 'numero_boleta'); // Solo lo necesario
                                       // ->limit(1); // Opcional si solo quieres el más reciente
                            }
                        ])->select('id', 'nicho_id', 'ocupante_id');
                    },
                ])
                ->orderBy('fecha_solicitud', 'desc')
                ->paginate(15);

            return view('consulta.mis_solicitudes', compact('solicitudes'));

        } catch (\Exception $e) {
            Log::error("Error al obtener solicitudes para seguimiento (Usuario ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('consulta.dashboard')
                     ->with('error', 'No se pudieron cargar tus solicitudes pendientes. Intenta más tarde.');
        }
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
             return redirect()->route('perfil.edit')->with('error', 'No se encontró el registro responsable asociado.');
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

            return redirect()->route('perfil.edit')->with('success', 'Perfil actualizado correctamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
             DB::rollBack();
            return redirect()->route('perfil.edit')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar perfil (Usuario ID: {$user->id}): " . $e->getMessage());
            return redirect()->route('perfil.edit')->with('error', 'Ocurrió un error al actualizar tu perfil.');
        }
    }


}

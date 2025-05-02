<?php

namespace App\Http\Controllers\Consulta; // O donde decidas ponerlo

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\Localidad;
use Illuminate\Support\Facades\Log; // Asegúrate de importar el Log
class LocationWebController extends Controller
{
    /**
     * Devuelve los municipios de un departamento específico en formato JSON.
     * Usado por el JavaScript del formulario de perfil.
     */
      public function getMunicipios(Departamento $departamento) // Usa Route Model Binding
    {
        
        Log::info("LocationWebController: Solicitando municipios para Depto ID: {$departamento->id} ({$departamento->nombre})");

        try {
            // 1. Verifica que la relación 'municipios' exista en el Modelo Departamento
            if (!method_exists($departamento, 'municipios')) {
                 Log::error("LocationWebController: La relación 'municipios' no existe en el modelo Departamento.");
                 return response()->json(['error' => 'Error interno del servidor: Relación no definida.'], 500);
            }

            // 2. Ejecuta la consulta para obtener municipios
            $municipios = $departamento->municipios() // Llama a la relación
                                     ->orderBy('nombre')      // Ordena por nombre
                                     ->select('id', 'nombre') // Selecciona solo las columnas necesarias
                                     ->get();                // Obtiene la colección

            // 3. Log opcional para ver cuántos municipios se encontraron
            Log::info("LocationWebController: Encontrados " . $municipios->count() . " municipios para Depto ID: {$departamento->id}");

            // 4. Devuelve la respuesta JSON
            return response()->json($municipios);

        } catch (\Exception $e) {
            // 5. Registra cualquier excepción que ocurra durante la consulta
            Log::error("LocationWebController: Error al obtener municipios para Depto ID {$departamento->id}: " . $e->getMessage(), [
                'exception' => $e // Opcional: Loguear la excepción completa para más detalle
            ]);
            // 6. Devuelve una respuesta de error genérica en formato JSON
            return response()->json(['error' => 'Error interno al cargar municipios.'], 500);
        }
    }

    /**
     * Devuelve las localidades de un municipio específico en formato JSON.
     * Usado por el JavaScript del formulario de perfil.
     */
    public function getLocalidades(Municipio $municipio) // Usa Route Model Binding
    {
        try {
             // Obtener solo id y nombre, ordenados
            $localidades = $municipio->localidades()
                                    ->orderBy('nombre')
                                    ->select('id', 'nombre')
                                    ->get();

            // Devolver la respuesta JSON
            return response()->json($localidades);

        } catch (\Exception $e) {
             // Devolver un error JSON si algo falla
            \Log::error("Error API web getLocalidades para Municipio ID {$municipio->id}: " . $e->getMessage());
            return response()->json(['error' => 'Error al cargar localidades'], 500);
        }
    }
}
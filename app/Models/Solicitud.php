<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    use HasFactory; // Puedes quitarlo si no usas factories

    protected $table = 'solicitudes';

    // Timestamps automáticos (created_at, updated_at) habilitados por defecto
    public $timestamps = true;

    // Define los campos que SÍ se pueden asignar masivamente (más seguro)
    protected $fillable = [
        'tipo_solicitud',
        'contrato_id',
        'usuario_solicitante_id',
        'fecha_solicitud', // Aunque tiene default, puede ser útil asignarlo
        'estado',
        'observaciones_usuario',
        // Los campos de procesamiento los llenará el admin
    ];

    // Opcional: Define casts para tipos de datos específicos
    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_procesamiento' => 'datetime',
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el contrato asociado a esta solicitud.
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    /**
     * Obtiene el usuario que realizó la solicitud.
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_solicitante_id');
    }

    /**
     * Obtiene el usuario (Admin/Ayudante) que procesó la solicitud.
     */
    public function procesador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_procesador_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exhumacion extends Model
{
    use HasFactory;

    protected $table = 'exhumaciones';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $guarded = [];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * @var array
     */
    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_aprobacion_rechazo' => 'date',
        'fecha_exhumacion_programada' => 'date',
        'fecha_exhumacion_realizada' => 'date',
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el ocupante que fue exhumado.
     */
    public function ocupanteExhumado(): BelongsTo
    {
        return $this->belongsTo(Ocupante::class, 'ocupante_exhumado_id');
    }

    /**
     * Obtiene el nicho de donde se realizó la exhumación.
     */
    public function nichoOrigen(): BelongsTo
    {
        return $this->belongsTo(Nicho::class, 'nicho_origen_id');
    }

    /**
     * Obtiene el contrato que estaba asociado (si aplica).
     */
    public function contratoAsociado(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_asociado_id');
    }

    /**
     * Obtiene el estado actual del proceso de exhumación.
     */
    public function estadoExhumacion(): BelongsTo
    {
        return $this->belongsTo(CatEstadoExhumacion::class, 'estado_exhumacion_id');
    }

    /**
     * Obtiene el usuario que aprobó o rechazó la solicitud.
     */
    public function usuarioAprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_aprobador_id');
    }

    /**
     * Obtiene el destino final de los restos.
     */
    public function destinoRestos(): BelongsTo
    {
        return $this->belongsTo(CatDestinoResto::class, 'destino_resto_id');
    }

    /**
     * Obtiene el nuevo ocupante (si hubo reinhumación).
     */
    public function nuevoOcupante(): BelongsTo
    {
        return $this->belongsTo(Ocupante::class, 'nuevo_ocupante_id');
    }

    /**
     * Obtiene el nicho destino (si hubo reinhumación en otro nicho).
     */
    public function nichoDestino(): BelongsTo
    {
        return $this->belongsTo(Nicho::class, 'nicho_destino_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
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
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_registro_pago' => 'datetime', // O 'timestamp'
        'monto' => 'decimal:2', // Para manejar correctamente los decimales
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el contrato al que pertenece este pago/boleta.
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    /**
     * Obtiene el estado del pago (Pendiente, Pagada, Anulada).
     */
    public function estadoPago(): BelongsTo
    {
        return $this->belongsTo(CatEstadoPago::class, 'estado_pago_id');
    }

    /**
     * Obtiene el usuario que registró este pago.
     */
    public function usuarioRegistrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registro_pago_id');
    }
}
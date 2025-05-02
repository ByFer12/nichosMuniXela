<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use HasFactory;
    protected $dates = [
        'fecha_fin_original',
        'fecha_fin_gracia',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'fecha_fin_original' => 'date',    // o 'datetime'
        'fecha_fin_gracia'   => 'date',
    ];
    /**
     * La tabla asociada con el modelo.
     * (Laravel intentaría 'contratos' por defecto, pero es bueno ser explícito)
     * @var string
     */
    protected $table = 'contratos';

    /**
     * La clave primaria asociada a la tabla.
     * (Laravel asume 'id' por defecto)
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la ID es auto-incremental.
     * (Laravel asume true por defecto)
     * @var bool
     */
    public $incrementing = true;

    /**
     * El tipo de dato de la clave primaria auto-incremental.
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe tener timestamps (created_at, updated_at).
     * (Laravel asume true por defecto)
     * @var bool
     */
    public $timestamps = true; // Tu tabla tiene created_at y updated_at

    /**
     * Los atributos que NO son asignables masivamente.
     * Usar [] permite toda asignación masiva (¡cuidado!),
     * o define $fillable con los campos permitidos (más seguro).
     * Por ahora, para empezar:
     * @var array
     */
    protected $guarded = [];

    // --- DEFINICIÓN DE RELACIONES ---
    // Necesario para que $contrato->nicho funcione

    /**
     * Obtiene el nicho asociado al contrato.
     */
    public function nicho(): BelongsTo
    {
        // El primer argumento es la clase del modelo relacionado.
        // El segundo (opcional) es la clave foránea en ESTA tabla ('contratos').
        // El tercero (opcional) es la clave primaria en la tabla relacionada ('nichos').
        return $this->belongsTo(Nicho::class, 'nicho_id', 'id');
    }

    /**
     * Obtiene el ocupante asociado al contrato.
     */
    public function ocupante(): BelongsTo
    {
        return $this->belongsTo(Ocupante::class, 'ocupante_id', 'id');
    }

    /**
     * Obtiene el responsable asociado al contrato.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'responsable_id', 'id');
    }

    /**
     * Obtiene los pagos asociados a este contrato.
     */
    public function pagos(): HasMany
    {
        // El primer argumento es la clase del modelo relacionado.
        // El segundo (opcional) es la clave foránea en la tabla RELACIONADA ('pagos').
        // El tercero (opcional) es la clave primaria en ESTA tabla ('contratos').
        return $this->hasMany(Pago::class, 'contrato_id', 'id');
    }

     /**
     * Obtiene el contrato anterior (si es una renovación).
     */
    public function contratoAnterior(): BelongsTo
    {
         return $this->belongsTo(Contrato::class, 'contrato_anterior_id', 'id');
    }

     /**
     * Obtiene el contrato siguiente (si este fue renovado).
     * Necesitas definir la relación inversa si quieres usar esto fácilmente.
     */
     // public function contratoSiguiente(): HasOne
     // {
     //    return $this->hasOne(Contrato::class, 'contrato_anterior_id', 'id');
     // }
}


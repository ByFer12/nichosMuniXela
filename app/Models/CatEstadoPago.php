<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatEstadoPago extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * @var string
     */
    protected $table = 'cat_estados_pago';

    /**
     * Indica si el modelo debe tener timestamps.
     * Tu tabla SÓLO tiene created_at.
     * @var bool
     */
    public $timestamps = true;

    /**
     * Nombre de la columna "updated at".
     * Establecido a null porque la tabla no tiene esta columna.
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Los atributos que NO son asignables masivamente.
     * @var array
     */
    protected $guarded = [];

    // --- RELACIONES (Inversa) ---

    /**
     * Obtiene todos los pagos asociados a este estado.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'estado_pago_id');
    }
}
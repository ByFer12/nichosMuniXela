<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatDestinoResto extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * @var string
     */
    protected $table = 'cat_destinos_restos'; // Ojo: 'restos' en plural en SQL

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
     * Obtiene todas las exhumaciones asociadas a este destino de restos.
     */
    public function exhumaciones(): HasMany
    {
        return $this->hasMany(Exhumacion::class, 'destino_resto_id');
    }
}
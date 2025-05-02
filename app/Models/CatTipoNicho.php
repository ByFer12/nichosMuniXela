<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatTipoNicho extends Model
{
    // HasFactory es opcional para modelos simples si no usarás factories
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     * Es importante especificarla si no sigue la convención plural de Laravel.
     *
     * @var string
     */
    protected $table = 'cat_tipos_nicho'; // Coincide con tu CREATE TABLE

    /**
     * La clave primaria asociada a la tabla.
     * (Laravel asume 'id' por defecto, así que esto es opcional)
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la ID es auto-incremental.
     * (Laravel asume true por defecto)
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * El tipo de dato de la clave primaria auto-incremental.
     * (Laravel asume 'int' por defecto)
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe tener timestamps.
     * Tu tabla SÓLO tiene created_at.
     * Para manejar esto, desactivamos updated_at específicamente.
     *
     * @var bool
     */
    public $timestamps = true; // Indica que sí gestionamos timestamps (al menos created_at)

    /**
     * Nombre de la columna "created at".
     * (Laravel asume 'created_at' por defecto)
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * Nombre de la columna "updated at".
     * ¡IMPORTANTE! Lo ponemos a null porque tu tabla no tiene updated_at.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Los atributos que NO son asignables masivamente.
     * [] permite todo (más fácil para empezar).
     * Alternativa segura: $fillable = ['nombre'];
     *
     * @var array
     */
    protected $guarded = [];

    // --- RELACIONES (Inversa, opcional pero útil) ---

    /**
     * Obtiene todos los nichos que tienen este tipo.
     */
    public function nichos(): HasMany
    {
        // El segundo argumento es la clave foránea en la tabla 'nichos'
        return $this->hasMany(Nicho::class, 'tipo_nicho_id');
    }
}
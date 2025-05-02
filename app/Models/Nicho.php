<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Nicho extends Model
{
    use HasFactory; // Puedes quitar esto si no usarás factories

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'nichos';

    /**
     * La clave primaria asociada a la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indica si la ID es auto-incremental.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * El tipo de dato de la clave primaria auto-incremental.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo debe tener timestamps (created_at, updated_at).
     * Tu tabla 'nichos' SÍ los tiene.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Los atributos que NO son asignables masivamente.
     * Usar [] permite toda asignación masiva (más fácil para empezar).
     * Alternativa: usa $fillable para listar los campos permitidos.
     *
     * @var array
     */
    protected $guarded = [];

    // --- RELACIONES ---

    /**
     * Obtiene el tipo de nicho (Adulto, Niño) asociado.
     * Relación necesaria para ->with(['tipoNicho']) en el controlador.
     */
    public function tipoNicho(): BelongsTo
    {
        // Eloquent asume 'cat_tipo_nicho_id' por defecto si el método se llama tipoNicho()
        // pero tu clave foránea es 'tipo_nicho_id', así que la especificamos.
        return $this->belongsTo(CatTipoNicho::class, 'tipo_nicho_id');
    }

    /**
     * Obtiene el estado actual del nicho (Disponible, Ocupado, etc.).
     * Relación necesaria para ->with(['estadoNicho']) en el controlador.
     */
    public function estadoNicho(): BelongsTo
    {
        return $this->belongsTo(CatEstadoNicho::class, 'estado_nicho_id');
    }

    public function contratoActivo(): HasOne
{
    // Busca el contrato con activo=true, ordenado por ID descendente (el más nuevo)
    // y toma solo uno (HasOne)
    return $this->hasOne(Contrato::class, 'nicho_id')
                ->where('activo', true)
                ->latest('id'); // O latest('fecha_inicio')
}
    /**
     * Obtiene los contratos asociados a este nicho.
     * Un nicho puede tener un historial de contratos.
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'nicho_id');
    }

    /**
     * Obtiene las exhumaciones donde este nicho fue el origen.
     */
    public function exhumacionesOrigen(): HasMany
    {
        return $this->hasMany(Exhumacion::class, 'nicho_origen_id');
    }

    /**
     * Obtiene las exhumaciones donde este nicho fue el destino.
     */
    public function exhumacionesDestino(): HasMany
    {
        return $this->hasMany(Exhumacion::class, 'nicho_destino_id');
    }
}

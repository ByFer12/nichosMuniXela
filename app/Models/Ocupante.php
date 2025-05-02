<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ocupante extends Model
{
    use HasFactory; // Puedes quitar esto si no usarás factories
    public function getNombreCompletoAttribute(): string
{
    return "{$this->nombres} {$this->apellidos}";
}

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'ocupantes';

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
     * Tu tabla 'ocupantes' SÍ los tiene.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Los atributos que NO son asignables masivamente.
     *
     * @var array
     */
    protected $guarded = [];

    // --- RELACIONES ---

    /**
     * Obtiene el tipo de género asociado al ocupante.
     */
    public function tipoGenero(): BelongsTo
    {
        return $this->belongsTo(CatTipoGenero::class, 'tipo_genero_id');
    }

    /**
     * Obtiene la dirección asociada al ocupante (última conocida o procedencia).
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    /**
     * Obtiene los contratos asociados a este ocupante.
     * Relación necesaria para ->with(['ocupante']) en el controlador (usada desde Contrato).
     * Aunque aquí defines la inversa (un ocupante puede tener varios contratos, raro pero posible).
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'ocupante_id');
    }

    /**
     * Obtiene el registro de personaje histórico (si existe) para este ocupante.
     */
    public function personajeHistorico(): HasOne
    {
        // Un ocupante solo puede ser UN personaje histórico
        return $this->hasOne(PersonajeHistorico::class, 'ocupante_id');
    }

    /**
     * Obtiene las exhumaciones realizadas a este ocupante.
     */
    public function exhumacionesRealizadas(): HasMany
    {
        return $this->hasMany(Exhumacion::class, 'ocupante_exhumado_id');
    }

     /**
     * Obtiene las exhumaciones donde este ocupante fue el nuevo inquilino.
     * (Cuando se usa 'nuevo_ocupante_id' en la tabla exhumaciones)
     */
    public function exhumacionesComoNuevoOcupante(): HasMany
    {
         return $this->hasMany(Exhumacion::class, 'nuevo_ocupante_id');
    }

}

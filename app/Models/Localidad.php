<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Localidad extends Model
{
    use HasFactory;

    protected $table = 'localidades';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Tu tabla no tiene timestamps

    protected $guarded = [];

    /**
     * Obtiene el municipio al que pertenece esta localidad.
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    /**
     * Obtiene las direcciones asociadas a esta localidad.
     */
    public function direcciones(): HasMany
    {
        return $this->hasMany(Direccion::class, 'localidad_id');
    }
}
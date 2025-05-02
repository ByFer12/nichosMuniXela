<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Tu tabla no tiene timestamps

    protected $guarded = [];

    /**
     * Obtiene el departamento al que pertenece este municipio.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    /**
     * Obtiene las localidades pertenecientes a este municipio.
     */
    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class, 'municipio_id');
    }
}
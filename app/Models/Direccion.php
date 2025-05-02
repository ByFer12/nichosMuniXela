<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // Tu tabla tiene created_at y updated_at

    protected $guarded = [];
    protected $fillable = [
        'calle_numero',
        'colonia_barrio',
        'codigo_postal',
        'municipio_id', // <-- Asegúrate que esté si cambiaste de localidad_id
        // 'localidad_id', // <-- Quítalo si ya no existe
        'referencia_adicional',
        'pais',
    ];


    /**
     * Obtiene la localidad a la que pertenece esta dirección.
     */
    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function getResumenAttribute(): string
{
    $parts = [];
    if ($this->calle_numero) $parts[] = $this->calle_numero;
    if ($this->colonia_barrio) $parts[] = $this->colonia_barrio;
    if ($this->municipio) $parts[] = $this->municipio->nombre; // Asume relación cargada
    if ($this->municipio && $this->municipio->departamento) $parts[] = $this->municipio->departamento->nombre; // Asume relación cargada
    return implode(', ', $parts);
}

    /**
     * Obtiene los ocupantes cuya dirección es esta.
     */
    public function ocupantes(): HasMany
    {
        return $this->hasMany(Ocupante::class, 'direccion_id');
    }
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
    /**
     * Obtiene los responsables cuya dirección es esta.
     */
    public function responsables(): HasMany
    {
        return $this->hasMany(Responsable::class, 'direccion_id');
    }

    /**
     * Accesor para obtener la dirección completa formateada (Ejemplo útil)
     * Puedes llamarlo con $direccion->direccion_completa
     */
    public function getDireccionCompletaAttribute(): string
    {
        $parts = [
            $this->calle_numero,
            $this->colonia_barrio,
            $this->localidad->nombre ?? null, // Accede a través de la relación
            $this->localidad->municipio->nombre ?? null,
            $this->localidad->municipio->departamento->nombre ?? null,
            $this->codigo_postal,
            $this->pais
        ];
        // Filtra partes vacías y las une con comas
        return implode(', ', array_filter($parts));
    }
}
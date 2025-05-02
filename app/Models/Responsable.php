<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Responsable extends Model
{
    use HasFactory;

    protected $table = 'responsables';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $guarded = [];
    protected $fillable = [
        'nombres',
        'apellidos',
        'dpi',
        'telefono',
        'correo_electronico',
        'direccion_id', // <-- Importante permitir este
    ];

    // --- RELACIONES ---

    /**
     * Obtiene la dirección de contacto del responsable.
     */
    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    /**
     * Obtiene los contratos donde esta persona es la responsable.
     */
    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'responsable_id');
    }

    /**
     * Obtiene la cuenta de usuario asociada a este responsable (si existe).
     */
    public function usuario(): HasOne
    {
        // La FK está en la tabla 'usuarios'
        return $this->hasOne(User::class, 'responsable_id');
    }

     /**
     * Accesor para obtener el nombre completo.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }
}
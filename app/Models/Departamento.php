<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamentos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // Tu tabla no tiene timestamps

    protected $guarded = [];

    /**
     * Obtiene los municipios pertenecientes a este departamento.
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'departamento_id', 'id');
    }


}
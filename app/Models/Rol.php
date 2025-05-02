<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // Tu tabla tiene created_at y updated_at

    protected $guarded = [];

    /**
     * Obtiene los usuarios que tienen este rol.
     */
    public function usuarios(): HasMany
    {
        // Asegúrate que el modelo de usuario se llama 'User'
        return $this->hasMany(User::class, 'rol_id');
    }
}
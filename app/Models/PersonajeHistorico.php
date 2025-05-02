<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonajeHistorico extends Model
{
    use HasFactory;

    protected $table = 'personajes_historicos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $guarded = [];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * @var array
     */
    protected $casts = [
        'fecha_declaracion' => 'date',
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el ocupante que es considerado personaje histórico.
     */
    public function ocupante(): BelongsTo
    {
        return $this->belongsTo(Ocupante::class, 'ocupante_id');
    }

    /**
     * Obtiene el usuario que realizó la declaración.
     */
    public function usuarioDeclarador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_declarador_id');
    }
}
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Descomenta si usas verificación de email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Asegúrate que extiende Authenticatable
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Si usas Sanctum para APIs
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Cambia 'User' por 'Usuario' si prefieres ese nombre de clase, pero 'User' es la convención de Laravel
class User extends Authenticatable
{
    // HasApiTokens y Notifiable son traits comunes de Laravel
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * La tabla asociada con el modelo.
     * Especificar aunque Laravel puede inferirlo.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Los atributos que son asignables masivamente.
     * ¡IMPORTANTE incluir 'responsable_id' aquí si lo añadiste!
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'username', // Añadido según tu SQL
        'email',
        'password',
        'rol_id',
        'responsable_id', // ¡Asegúrate que esto está aquí!
        'activo',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime', // Si usas verificación
        'password' => 'hashed', // Asegura que la contraseña se hashea automáticamente
        'activo' => 'boolean', // Convierte TINYINT(1) a true/false
    ];

    // --- RELACIONES ---

    /**
     * Obtiene el rol asociado a este usuario.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Obtiene el registro de responsable asociado a este usuario (si es un Usuario Consulta).
     * Será null para otros roles si la FK es nullable.
     */
    public function responsable(): BelongsTo
    {
        // Asegúrate que el modelo se llama Responsable
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }

    /**
     * Obtiene los pagos registrados por este usuario.
     */
    public function pagosRegistrados(): HasMany
    {
        // Clave foránea en la tabla 'pagos'
        return $this->hasMany(Pago::class, 'usuario_registro_pago_id');
    }

    /**
     * Obtiene los registros de personajes históricos declarados por este usuario.
     */
    public function personajesDeclarados(): HasMany
    {
        // Clave foránea en la tabla 'personajes_historicos'
        return $this->hasMany(PersonajeHistorico::class, 'usuario_declarador_id');
    }

    /**
     * Obtiene las exhumaciones aprobadas/rechazadas por este usuario.
     */
    public function exhumacionesAprobadas(): HasMany
    {
        // Clave foránea en la tabla 'exhumaciones'
        return $this->hasMany(Exhumacion::class, 'usuario_aprobador_id');
    }

    // --- Métodos Helper (Ejemplos) ---

    /**
     * Verifica si el usuario tiene un rol específico por nombre.
     * @param string $nombreRol
     * @return bool
     */
    public function hasRole(string $nombreRol): bool
    {
        // Accede a la relación 'rol' y luego a su atributo 'nombre'
        return $this->rol && strtolower($this->rol->nombre) === strtolower($nombreRol);
    }

    /**
     * Verifica si el usuario es Administrador.
     * @return bool
     */
    public function isAdmin(): bool
    {
        // Asumiendo que el rol de Admin se llama 'Administrador'
        return $this->hasRole('Administrador');
    }

     /**
     * Verifica si el usuario es de Consulta.
     * @return bool
     */
    public function isConsulta(): bool
    {
        // Asumiendo que el rol de Consulta se llama 'Consulta' o similar
        return $this->hasRole('Consulta'); // Ajusta el nombre si es diferente
    }
}
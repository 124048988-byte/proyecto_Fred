<?php

namespace App\Models;

// use Laravel\Sanctum\HasApiTokens; // <--- ESTA LÍNEA DEBE SER ELIMINADA SI NO USAS SANCTUM
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    // QUITAMOS 'use HasApiTokens' del cuerpo de la clase si no está instalado
    use HasFactory, Notifiable; 

    /**
     * The attributes that are mass assignable.
     * Añadimos 'puesto', 'departamento_id', 'rol_id' y 'foto_perfil'
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'puesto', // Nuevo
        'departamento_id', // Nuevo
        'rol_id', // Nuevo
        'foto_perfil', // Nuevo
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the departamento associated with the user.
     */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Get the rol associated with the user.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }
}
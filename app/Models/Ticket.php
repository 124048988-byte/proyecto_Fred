<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa (Mass Assignable).
     * Esto es importante para el método store del controlador.
     * Incluimos 'adjunto_path' ya que el controlador se encarga de subir el archivo.
     */
    protected $fillable = [
        'title',           // <--- ¡Añadido el campo 'title' aquí!
        'descripcion',
        'estatus',
        'departamento_id',
        'usuario_id',
        'auxiliar_id',
        'adjunto_path',
    ];

    // --- RELACIONES ---

    /**
     * Relación: Un ticket pertenece a un usuario (el creador).
     */
    public function creador(): BelongsTo
    {
        // Usamos 'usuario_id' como clave foránea
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Un ticket pertenece a un auxiliar asignado (puede ser nulo).
     */
    public function auxiliar(): BelongsTo
    {
        // Usamos 'auxiliar_id' como clave foránea
        return $this->belongsTo(User::class, 'auxiliar_id');
    }

    /**
     * Relación: Un ticket pertenece a un departamento.
     */
    public function departamento(): BelongsTo
    {
        // Necesitas tener importado el modelo Departamento al inicio si no está
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Relación: Un ticket tiene muchos comentarios.
     */
    public function comentarios(): HasMany
    {
        // Necesitas importar el modelo Comentario al inicio si no está
        return $this->hasMany(Comentario::class);
    }
}

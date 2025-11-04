<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentario extends Model
{
    use HasFactory;

    // Nombre de la tabla si no es el plural de 'Comentario'
    // protected $table = 'comentarios'; 

    // Campos que pueden ser llenados de forma masiva
    protected $fillable = [
        'contenido',
        'user_id',
        'ticket_id',
    ];

    /**
     * Define la relación: Un comentario pertenece a un usuario (el creador).
     */
    public function creador(): BelongsTo
    {
        // Asume que tu modelo de usuario es 'User'
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define la relación: Un comentario pertenece a un ticket.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}

<?php
//------------------------------ESTE CÓDIGO ME LO DIÓ CHAT----------------------------------------
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'auxiliar_id',
        'title',
        'description',
        'status',
        'finished_at',
    ];

    /**
     * Relación con el usuario que creó el ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el usuario auxiliar asignado al ticket
     */
    public function auxiliar()
    {
        return $this->belongsTo(User::class, 'auxiliar_id');
    }
}


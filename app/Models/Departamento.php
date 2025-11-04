<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;
    
    // Indica el nombre de la tabla (opcional si sigue convención)
    protected $table = 'departamentos'; 

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'nombre',
    ];

    /**
     * Obtiene los usuarios que pertenecen a este departamento.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
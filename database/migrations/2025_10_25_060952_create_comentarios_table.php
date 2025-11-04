<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            
            // Contenido del comentario
            $table->text('contenido');
            
            // Clave foránea al usuario que creó el comentario
            // Asume que tienes una tabla 'users'
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Clave foránea al ticket al que pertenece el comentario
            // Asume que tienes una tabla 'tickets'
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};

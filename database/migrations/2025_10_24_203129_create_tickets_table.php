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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // CAMPO AÑADIDO: Título del ticket. NO puede ser nulo.
            $table->string('title');
            
            // Descripción del problema
            $table->text('descripcion');

            // Estatus del ticket: Abierto, En Progreso, Cerrado
            // Usamos un valor por defecto de 'Abierto'
            $table->string('estatus')->default('Abierto'); 
            
            // Campo para saber a qué departamento se asigna (puede ser nulo inicialmente)
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->onDelete('set null');

            // Clave foránea del usuario que crea el ticket (no puede ser nulo)
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');

            // Clave foránea del usuario auxiliar/jefe al que se le asigna el ticket (puede ser nulo al inicio)
            $table->foreignId('auxiliar_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Adjunto opcional
            $table->string('adjunto_path')->nullable();
            
            // Fechas de creación y actualización (fecha_de_creacion)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

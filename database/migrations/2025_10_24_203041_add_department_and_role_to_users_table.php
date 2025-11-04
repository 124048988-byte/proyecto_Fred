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
        // Esta migración solo debe añadir las restricciones de clave foránea, 
        // asumiendo que las columnas (puesto, foto_perfil, departamento_id, rol_id) 
        // ya fueron creadas en la migración base de users.
        Schema::table('users', function (Blueprint $table) {
            
            // Si la columna ya se creó en la migración base, la eliminamos de aquí
            // $table->string('puesto')->nullable()->after('email');
            
            // Añadimos las restricciones de clave foránea
            $table->foreign('departamento_id')
                  ->references('id')
                  ->on('departamentos')
                  ->onDelete('set null');

            $table->foreign('rol_id')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos las claves foráneas en caso de rollback
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['rol_id']);
            
            // No eliminamos las columnas puesto y foto_perfil aquí ya que fueron creadas en otro lugar
        });
    }
};
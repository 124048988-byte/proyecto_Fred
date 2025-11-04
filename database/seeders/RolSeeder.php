<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol; // Asegúrate de importar tu modelo Rol

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Jefe'],
            ['nombre' => 'Auxiliar'],
            ['nombre' => 'Usuario'],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate($rol);
        }
    }
}
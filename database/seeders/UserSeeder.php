<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que los roles y departamentos existan
        $jefeRol = Rol::where('nombre', 'Jefe')->first();
        $soporteDepto = Departamento::where('nombre', 'Soporte Técnico')->first();

        // Crear un usuario administrador de ejemplo
        User::firstOrCreate(
            ['email' => 'admin@fred.com'],
            [
                'name' => 'Admin Fred',
                'puesto' => 'apoyo técnico',
                'password' => Hash::make('password'), // Contraseña simple para desarrollo
                'email_verified_at' => now(),
                'rol_id' => $jefeRol ? $jefeRol->id : null,
                'departamento_id' => $soporteDepto ? $soporteDepto->id : null,
            ]
        );
        User::firstOrCreate(
            ['email' => 'guillermolapc@gmail.com'],
            [
                'name' => 'Guille',
                'puesto' => 'Jefe de Soporte',
                'password' => Hash::make('MaLllermo'), // Contraseña simple para desarrollo
                'email_verified_at' => now(),
                'rol_id' => $jefeRol ? $jefeRol->id : null,
                'departamento_id' => $soporteDepto ? $soporteDepto->id : null,
            ]
        );
    }
}

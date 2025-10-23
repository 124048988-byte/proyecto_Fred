<?php

namespace Database\Seeders;

use App\Models\User; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; 

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Fred',
            'email' => 'admin@tiendafred.com',
            'password' => Hash::make('password'), // Contraseña simple: "password"
        ]);
        User::create([
            'name' => 'Guille',
            'email' => 'guillermolapc@gmail.com',
            'password' => Hash::make('MaLlermo'),
        ]);
    }
}
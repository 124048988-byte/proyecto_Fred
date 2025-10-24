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
        User::create([
            'name' => 'Esme',
            'email' => 'esme@gmail.com',
            'password' => Hash::make('Danna1q2w'),
        ]);
        User::create([
            'name' => 'Ruben',
            'email' => 'ruben@gmail.com',
            'password' => Hash::make('ruben123'),
        ]);
        User::create([
            'name' => 'Moni',
            'email' => 'moni@gmail.com',
            'password' => Hash::make('moni456'),
        ]);
        User::create([
            'name' => 'Fer',
            'email' => 'fer@gmail.com',
            'password' => Hash::make('fer789'),
        ]);
    }
}
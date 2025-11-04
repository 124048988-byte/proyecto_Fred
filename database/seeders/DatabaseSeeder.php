<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class, // Debe ir primero
            DepartamentoSeeder::class, // Debe ir segundo
            UserSeeder::class, // Debe ir tercero (depende de los IDs anteriores)
        ]);
    }
}
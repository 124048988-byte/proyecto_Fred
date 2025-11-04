<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento; // Asegúrate de importar tu modelo Departamento

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['nombre' => 'Recursos Humanos'],
            ['nombre' => 'Soporte Técnico'],
            ['nombre' => 'Contabilidad'],
            ['nombre' => 'Ventas'],
        ];

        foreach ($departamentos as $departamento) {
            Departamento::firstOrCreate($departamento);
        }
    }
}
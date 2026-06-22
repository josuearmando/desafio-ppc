<?php

namespace Database\Seeders;

use App\Models\UnidadeAcademica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadeAcademicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnidadeAcademica::create([
            'nome' => 'Faculdade de Computação e Telecomunicações',
            'senha' => 'FCT2026'
        ]);

        UnidadeAcademica::create([
            'nome' => 'Faculdade de Computação',
            'senha' => 'FACOMP2026'
        ]);
    }
}

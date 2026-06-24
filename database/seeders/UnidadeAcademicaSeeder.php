<?php

namespace Database\Seeders;

use App\Models\UnidadeAcademica;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UnidadeAcademicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnidadeAcademica::create([
            'nome' => 'Faculdade de Computação e Telecomunicações',
            'password' => Hash::make('FCT2026')
        ]);

        UnidadeAcademica::create([
            'nome' => 'Faculdade de Computação',
            'password' => Hash::make('FACOMP2026')
        ]);
    }
}

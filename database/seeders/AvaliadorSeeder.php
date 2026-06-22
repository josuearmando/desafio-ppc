<?php

namespace Database\Seeders;

use App\Models\Avaliador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AvaliadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Avaliador::create([
            'nome' => 'Victor Sales',
            'email' => 'victor.sales@ufpa.br',
            'senha' => 'senha123'
        ]);

        Avaliador::create([
            'nome' => 'Euripedes Santos',
            'email' => 'euripedes.santos@ufpa.br',
            'senha' => 'senha123'
        ]);
        
    }
}

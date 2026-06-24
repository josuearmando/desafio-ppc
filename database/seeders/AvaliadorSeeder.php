<?php

namespace Database\Seeders;

use App\Models\Avaliador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'password' => Hash::make('senha123'),
        ]);

        Avaliador::create([
            'nome' => 'Euripedes Santos',
            'email' => 'euripedes.santos@ufpa.br',
            'password' => Hash::make('senha123'),
        ]);
        
    }
}

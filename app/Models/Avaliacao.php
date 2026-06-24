<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';

    protected $fillable = [
        'proposta_id',
        'avaliador_id',
        'observacoes',
        'status_resultado'
    ];
}

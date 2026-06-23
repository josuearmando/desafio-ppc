<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    protected $table = 'propostas';

    protected $fillable = [
        'unidade_academica_id',
        'avaliador_id',
        'nome',
        'carga_horaria_total',
        'quantidade_semestres',
        'justificativa',
        'impacto_social',
        'status'
    ];

    public function disciplinas()
    {
        return $this->hasMany(Disciplina::class);
    }
}

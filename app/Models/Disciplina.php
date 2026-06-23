<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    protected $table = 'disciplinas';

    protected $fillable = [
        'proposta_id',
        'nome',
        'carga_horaria',
        'semestre'
    ];
}

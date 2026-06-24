<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UnidadeAcademica extends Authenticatable
{
    use HasApiTokens, Notifiable;
    
    protected $table = 'unidades_academicas';
    
    protected $fillable = [
        'nome',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

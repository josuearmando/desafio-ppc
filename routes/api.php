<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AvaliacaoController;
use App\Http\Controllers\API\PropostaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// rotas publicas autenticao
Route::post('/login/avaliador', [AuthController::class, 'loginAvaliador']);
Route::post('/login/unidade', [AuthController::class, 'loginUnidade']);

// enviar ppc para avaliacao
Route::post('/propostas', [PropostaController::class, 'store']);

// rotas Camara de Educacao (sem autenticacao)
Route::get('/camara/pauta', [PropostaController::class, 'pautaCamara']);
Route::post('/camara/propostas/{id}/decisao', [PropostaController::class, 'decisaoCamara'])->whereNumber('id');
    
Route::middleware('auth:sanctum')->group(function () {

    // rotas privadas do avaliador
    Route::get('/propostas', [PropostaController::class, 'index']);
    Route::get('/propostas/{id}', [PropostaController::class, 'show'])->whereNumber('id');
    Route::post('/avaliacoes', [AvaliacaoController::class, 'store']);
    
    Route::post('/logout', [AuthController::class, 'logout']);
    });


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

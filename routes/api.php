<?php

use App\Http\Controllers\API\AvaliacaoController;
use App\Http\Controllers\API\PropostaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/propostas', [PropostaController::class, 'store']);

Route::get('/propostas', [PropostaController::class, 'index']);
Route::get('/propostas/{id}', [PropostaController::class, 'show'])->whereNumber('id');

Route::post('/avaliacoes', [AvaliacaoController::class, 'store']);

Route::get('/camara/pauta', [PropostaController::class, 'pautaCamara']);
Route::post('/camara/propostas/{id}/decisao', [PropostaController::class, 'decisaoCamara'])->whereNumber('id');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

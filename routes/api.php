<?php

use App\Http\Controllers\API\PropostaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/propostas', [PropostaController::class, 'store']);

Route::get('/propostas', [PropostaController::class, 'index']);
Route::get('/propostas/{id}', [PropostaController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

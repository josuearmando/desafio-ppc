<?php

use App\Models\Avaliador;
use App\Models\Proposta;
use App\Models\UnidadeAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'loginIdentifier' => 'required|string',
        'password' => 'required|string',
        'perfil_selecionado' => 'required|string',
    ]);

    // unidade academica
    if ($request->perfil_selecionado === 'unidade') {
        $unidade = UnidadeAcademica::whereNome($request->loginIdentifier)->first();

        if ($unidade && Hash::check($request->password, $unidade->password)) {
            Auth::login($unidade);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }
    }

    // avaliador
    if ($request->perfil_selecionado === 'avaliador') {
        $avaliador = Avaliador::whereEmail($request->loginIdentifier)->first();
        
        if ($avaliador && Hash::check($request->password, $avaliador->password)) {
            Auth::login($avaliador);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }
    }

    return back()->withErrors([
        'loginIdentifier' => 'As credenciais fornecidas não correspondem aos nossos registros para este perfil.',
    ]);
});

Route::get('/login', function () {
    return Inertia::render('auth/Login', [
        'unidades' => UnidadeAcademica::select(['id', 'nome'])->orderBy('nome', 'asc')->get()
    ]);
})->name('login'); 

Route::get('/camara', function () {
    $propostas = Proposta::with('disciplinas')
        ->where('status', 'enviado_camara')
        ->orderBy('updated_at', 'asc')
        ->get();

    return Inertia::render('camara/PautaVotacao', [
        'propostasInicial' => $propostas
    ]);
});

Route::middleware(['auth:unidade,avaliador'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::post('/logoutPortal', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
});

/*
Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
*/
require __DIR__.'/settings.php';

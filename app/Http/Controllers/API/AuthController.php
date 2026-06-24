<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Avaliador;
use App\Models\UnidadeAcademica;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginAvaliador(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $avaliador = Avaliador::whereEmail($request->email)->first();
        
        if (!$avaliador || !Hash::check($request->password, $avaliador->password)) {
            return response()->json(['message' => 'Credenciais incorretas. Verifique seu e-mail e senha.'], 401);
        }

        $token = $avaliador->createToken('avaliador_token')->plainTextToken;

        return response()->json([
            'message' => 'Login de Avaliador realizado com sucesso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'perfil' => 'avaliador',
            'user' => $avaliador
        ], 200);
        
    }

    public function loginUnidade(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'password' => 'required'
        ]);
        
        $unidade = UnidadeAcademica::whereNome($request->nome)->first();
        
        if (!$unidade || !Hash::check($request->password, $unidade->password)) {
            return response()->json(['message' => 'Credenciais incorretas. Verifique sua unidade e senha.'], 401);
        }

        $token = $unidade->createToken('unidade_token')->plainTextToken;

        return response()->json([
            'message' => 'Login de Unidade Acadêmica realizado com sucesso.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'perfil' => 'unidade',
            'user' => $unidade
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso.'
        ], 200);
    }

    


}

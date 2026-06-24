<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Avaliacao;
use App\Models\Proposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AvaliacaoController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'proposta_id' => 'required|exists:propostas,id',
            'avaliador_id' => 'required|exists:avaliadores,id',
            'observacoes' => 'required|string|min:10',
            'status_resultado' => 'required|in:retornado_correcao,enviado_camara',
        ]);

        DB::beginTransaction();

        try {
            $proposta = Proposta::findOrFail($validatedData['proposta_id']);
            
            $avaliacao = Avaliacao::create([
                'proposta_id' => $validatedData['proposta_id'],
                'avaliador_id' => $validatedData['avaliador_id'],
                'observacoes' => $validatedData['observacoes'],
                'status_resultado' => $validatedData['status_resultado'],
            ]);

            $proposta->update([
                'status' => $validatedData['status_resultado'],
                'avaliador_id' => $validatedData['avaliador_id'],
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Avaliação registrada com sucesso.',
                'avaliacao' => $avaliacao,
                'proposta' => $proposta,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocorreu um erro ao registrar avaliação.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

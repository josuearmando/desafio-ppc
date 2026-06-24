<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Disciplina;
use App\Models\Proposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropostaController extends Controller
{
    public function store(Request $request) {
        $validatedData = $request->validate([
            'unidade_academica_id' => 'required|exists:unidades_academicas,id',
            'nome' => 'required|string|max:255',
            'carga_horaria_total' => 'required|integer|min:1',
            'quantidade_semestres' => 'required|integer|min:1',
            'justificativa' => 'required|string',
            'impacto_social' => 'required|string',
            'disciplinas' => 'required|array|min:1',
            'disciplinas.*.nome' => 'required|string|max:255',
            'disciplinas.*.carga_horaria' => 'required|integer|min:1',
            'disciplinas.*.semestre' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $proposta = Proposta::create([
                'unidade_academica_id' => $validatedData['unidade_academica_id'],
                'nome' => $validatedData['nome'],
                'carga_horaria_total' => $validatedData['carga_horaria_total'],
                'quantidade_semestres' => $validatedData['quantidade_semestres'],
                'justificativa' => $validatedData['justificativa'],
                'impacto_social' => $validatedData['impacto_social'],
                'status' => 'em_analise',
            ]);

            $disciplinasValidadas = $validatedData['disciplinas'];

            foreach ($disciplinasValidadas as $disciplina) {
                Disciplina::create([
                    'proposta_id' => $proposta->id,
                    'nome' => $disciplina['nome'],
                    'carga_horaria' => $disciplina['carga_horaria'],
                    'semestre' => $disciplina['semestre'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Proposta de PPC submetida com sucesso.',
                'proposta' => $proposta->load('disciplinas')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Ocorreu um erro ao salvar a proposta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $propostas = Proposta::with('disciplinas')
            ->where('status', 'em_analise')
            ->whereNull('avaliador_id')
            ->orderBy('created_at', 'desc')
            ->get();
        
            return response()->json($propostas, 200);
    }

    public function show(Request $request, int $id)
    {
        $avaliadorId = $request->query('avaliador_id');

        DB::beginTransaction();

        try {
            $proposta = Proposta::with('disciplinas')->lockForUpdate()->find($id);

            if (!$proposta) {
                DB::rollBack();
                return response()->json(['message' => 'Proposta não encontrada.'], 404);
            }

            if ($proposta->avaliador_id !== null && $proposta->avaliador_id != $avaliadorId) {
                DB::rollBack();
                return response()->json(['message' => 'Essa proposta já está sendo avaliada por outro profissional.'], 423);
            }

            if ($proposta->avaliador_id === null && $avaliadorId) {
                $proposta->update([
                    'avaliador_id' => $avaliadorId
                ]);
            }

            DB::commit();
            return response()->json($proposta, 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao abrir proposta',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }

    public function pautaCamara()
    {
        $propostas = Proposta::with('disciplinas')
            ->where('status', 'enviado_camara')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($propostas, 200);
    }

    public function decisaoCamara(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:aprovado,reprovado',
        ]);

        DB::beginTransaction();

        try {
            $proposta = Proposta::lockForUpdate()->find($id);

            if (!$proposta) {
                DB::rollBack();
                return response()->json(['message' => 'Proposta não encontrada.'], 404);
            }

            if ($proposta->status !== 'enviado_camara') {
                DB::rollBack();
                return response()->json(['message' => 'A proposta não está em análise na câmara.'], 400);
            }

            $proposta->fill([
                'status' => $validatedData['status']
            ])->save();

            DB::commit();
            return response()->json([
                'message' => 'Decisão da Câmara registrada com sucesso.',
                'proposta' => $proposta
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao registrar decisão da câmara.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

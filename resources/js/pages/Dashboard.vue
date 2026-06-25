<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const guard = computed(() => page.props.auth.guard);
const isAvaliador = computed(() => guard.value === 'avaliador');

const formPpc = ref({ nome: '', carga_horaria_total: 0, quantidade_semestres: 1, justificativa: '', impacto_social: '', disciplinas: [] });
const novaDisciplina = ref({ nome: '', carga_horaria: null, semestre: 1 });

const adicionarDisciplina = () => {
    if (novaDisciplina.value.nome && novaDisciplina.value.carga_horaria > 0) {
        formPpc.value.disciplinas.push({ ...novaDisciplina.value });
        novaDisciplina.value = { nome: '', carga_horaria: null, semestre: 1 };
    } else {
        alert('Preencha o nome e a carga horária da disciplina.');
    }
};

const removerDisciplina = (index) => formPpc.value.disciplinas.splice(index, 1);

const submeterPpc = () => {
    const payload = {
        unidade_academica_id: user.value.id,
        nome: formPpc.value.nome,
        carga_horaria_total: formPpc.value.disciplinas.reduce((a, d) => a + (d.carga_horaria || 0), 0),
        quantidade_semestres: formPpc.value.quantidade_semestres,
        justificativa: formPpc.value.justificativa,
        impacto_social: formPpc.value.impacto_social,
        disciplinas: formPpc.value.disciplinas,
    };

    window.axios.post('/api/propostas', payload)
        .then(() => {
            alert('PPC submetido com sucesso para a fila de avaliação!');
            formPpc.value = { nome: '', carga_horaria_total: 0, quantidade_semestres: 1, justificativa: '', impacto_social: '', disciplinas: [] };
        })
        .catch(err => {
            const errors = err.response?.data?.errors;
            if (errors) {
                alert('Erros de validação:\n' + Object.values(errors).flat().join('\n'));
            } else {
                alert('Erro ao submeter o PPC.');
            }
        });
};

const propostasFila = ref([]);
const propostaSelecionada = ref(null);
const formParecer = ref({ observacoes: '', status_resultado: '' });

const carregarFila = () => {
    window.axios.get('/api/propostas').then(res => { propostasFila.value = res.data; });
};

const calcularCargaTotal = (disciplinas) =>
    disciplinas?.reduce((a, d) => a + (d.carga_horaria || 0), 0) ?? 0;

const abrirParaAvaliacao = (prop) => {
    window.axios.get(`/api/propostas/${prop.id}?avaliador_id=${user.value.id}`)
        .then(res => {
            propostaSelecionada.value = res.data;
            formParecer.value = { observacoes: '', status_resultado: '' };
        })
        .catch(() => {
            alert('Esta proposta já está reservada por outro avaliador.');
            carregarFila();
        });
};

const enviarParecer = (statusResultado) => {
    if (!formParecer.value.observacoes) {
        alert('Insira o parecer descritivo antes de enviar.');
        return;
    }

    window.axios.post('/api/avaliacoes', {
        proposta_id: propostaSelecionada.value.id,
        avaliador_id: user.value.id,
        observacoes: formParecer.value.observacoes,
        status_resultado: statusResultado, // 'retornado_correcao' ou 'enviado_camara'
    })
    .then(() => {
        alert('Parecer registrado com sucesso!');
        propostaSelecionada.value = null;
        carregarFila();
    })
    .catch(() => alert('Erro ao registrar o parecer.'));
};

const logout = () => router.post('/logoutPortal');

onMounted(() => { if (isAvaliador.value) carregarFila(); });
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-slate-100">
        <nav class="bg-slate-800 border-b border-slate-700 px-6 py-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="text-xl font-bold tracking-wider text-white">Simulador PPC</span>
                    <span :class="['text-xs px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border',
                                   isAvaliador ? 'bg-emerald-950/50 text-emerald-400 border-emerald-800' : 'bg-blue-950/50 text-blue-400 border-blue-800']">
                        {{ isAvaliador ? 'Avaliador Técnico' : 'Unidade Acadêmica' }}
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-slate-300">Olá, {{ user?.nome }}</span>
                    <button @click="logout" class="text-xs bg-slate-700 hover:bg-slate-600 text-slate-200 px-3 py-1.5 rounded-lg border border-slate-600">Sair</button>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto p-6 md:p-8">

            <!-- PAINEL UNIDADE ACADÊMICA -->
            <div v-if="!isAvaliador" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-xl h-fit space-y-4">
                    <h2 class="text-xl font-bold text-white">Novo PPC</h2>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Nome do Curso</label>
                        <input type="text" v-model="formPpc.nome" class="mt-1 block w-full rounded-md bg-slate-700 text-white p-2.5 text-sm" placeholder="Ex: Engenharia de Computação" required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Quantidade de Semestres</label>
                        <input type="number" v-model.number="formPpc.quantidade_semestres" class="mt-1 block w-full rounded-md bg-slate-700 text-white p-2.5 text-sm" min="1" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Justificativa</label>
                        <textarea v-model="formPpc.justificativa" rows="3" class="mt-1 block w-full rounded-md bg-slate-700 text-white p-2.5 text-sm" placeholder="Justificativa do PPC..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Impacto Social</label>
                        <textarea v-model="formPpc.impacto_social" rows="3" class="mt-1 block w-full rounded-md bg-slate-700 text-white p-2.5 text-sm" placeholder="Impacto social esperado..."></textarea>
                    </div>
                    <button @click="submeterPpc" :disabled="formPpc.disciplinas.length === 0" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2.5 rounded-lg font-semibold transition-colors disabled:opacity-40 text-sm">
                        Submeter PPC para Avaliação
                    </button>
                </div>

                <div class="lg:col-span-2 bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-xl">
                    <h2 class="text-xl font-bold text-white mb-1">Grade Curricular</h2>
                    <p class="text-slate-400 text-sm mb-6">Adicione as disciplinas que compõem este PPC.</p>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-slate-900/40 p-4 rounded-lg border border-slate-700/50 mb-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs text-slate-400 mb-1">Nome da Disciplina</label>
                            <input type="text" v-model="novaDisciplina.nome" class="block w-full rounded-md bg-slate-700 text-white p-2 text-xs" placeholder="Ex: Algoritmos" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Carga Horária</label>
                            <input type="number" v-model.number="novaDisciplina.carga_horaria" class="block w-full rounded-md bg-slate-700 text-white p-2 text-xs" placeholder="Ex: 60" />
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Semestre</label>
                            <input type="number" v-model.number="novaDisciplina.semestre" class="block w-full rounded-md bg-slate-700 text-white p-2 text-xs" placeholder="Ex: 1" min="1" />
                        </div>
                        <div class="md:col-span-4 flex justify-end">
                            <button @click="adicionarDisciplina" class="bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold py-2 px-6 rounded border border-slate-600 transition-colors">
                                + Inserir na Grade
                            </button>
                        </div>
                    </div>

                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-700 text-slate-400 text-xs uppercase">
                                <th class="py-3 px-4">Disciplina</th>
                                <th class="py-3 px-4">Semestre</th>
                                <th class="py-3 px-4">Carga Horária</th>
                                <th class="py-3 px-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            <tr v-for="(disc, index) in formPpc.disciplinas" :key="index" class="hover:bg-slate-700/20">
                                <td class="py-3 px-4 text-white font-medium">{{ disc.nome }}</td>
                                <td class="py-3 px-4 text-slate-300">{{ disc.semestre }}º</td>
                                <td class="py-3 px-4 text-slate-300">{{ disc.carga_horaria }}h</td>
                                <td class="py-3 px-4 text-center">
                                    <button @click="removerDisciplina(index)" class="text-rose-400 hover:text-rose-300 text-xs font-bold">Excluir</button>
                                </td>
                            </tr>
                            <tr v-if="formPpc.disciplinas.length === 0">
                                <td colspan="4" class="py-8 text-center text-slate-500 italic">Nenhuma disciplina adicionada ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PAINEL AVALIADOR -->
            <div v-else class="space-y-8">
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-xl">
                    <h2 class="text-xl font-bold text-white mb-2">Fila de PPCs Pendentes</h2>
                    <p class="text-slate-400 text-sm mb-6">Selecione uma proposta para reservar e avaliar.</p>

                    <div v-if="propostasFila.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="prop in propostasFila" :key="prop.id" class="bg-slate-900/50 border border-slate-700 rounded-lg p-5 flex flex-col justify-between hover:border-slate-600 transition-all">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] uppercase font-bold tracking-widest bg-emerald-950 text-emerald-400 px-2 py-0.5 rounded border border-emerald-900">ID: {{ prop.id }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-white">{{ prop.nome }}</h3>
                                <div class="mt-3 space-y-1 text-xs text-slate-400">
                                    <p>• Disciplinas: <strong class="text-slate-200">{{ prop.disciplinas?.length || 0 }}</strong></p>
                                    <p>• Carga Total: <strong class="text-slate-200">{{ calcularCargaTotal(prop.disciplinas) }}h</strong></p>
                                    <p>• Semestres: <strong class="text-slate-200">{{ prop.quantidade_semestres }}</strong></p>
                                </div>
                            </div>
                            <button @click="abrirParaAvaliacao(prop)" class="mt-5 w-full bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white py-2 rounded font-medium transition-colors text-xs">
                                Abrir e Reservar para Análise →
                            </button>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 text-slate-500 italic">
                        Nenhuma proposta aguardando avaliação no momento.
                    </div>
                </div>

                <div v-if="propostaSelecionada" class="bg-slate-800 border-2 border-emerald-500/30 rounded-xl p-6 shadow-2xl space-y-6">
                    <div class="flex justify-between items-start border-b border-slate-700 pb-4">
                        <div>
                            <span class="text-xs font-bold uppercase text-emerald-400 bg-emerald-950/40 px-2 py-1 rounded border border-emerald-900">Análise Ativa</span>
                            <h3 class="text-2xl font-bold text-white mt-2">{{ propostaSelecionada.nome }}</h3>
                            <p class="text-sm text-slate-400 mt-1">{{ propostaSelecionada.justificativa }}</p>
                        </div>
                        <button @click="propostaSelecionada = null" class="text-slate-400 hover:text-white text-sm">Fechar ✕</button>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Grade Curricular:</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            <div v-for="d in propostaSelecionada.disciplinas" :key="d.id" class="bg-slate-900/60 p-3 rounded border border-slate-700/50 text-sm flex justify-between">
                                <span class="text-slate-200 font-medium">{{ d.nome }} <span class="text-slate-500 text-xs">({{ d.semestre }}º)</span></span>
                                <span class="text-emerald-400 font-semibold">{{ d.carga_horaria }}h</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-700/60 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400">Parecer Técnico</label>
                            <textarea v-model="formParecer.observacoes" rows="4" class="mt-1 block w-full rounded-md bg-slate-700 text-white p-2.5 text-sm focus:ring-1 focus:ring-emerald-500" placeholder="Descreva os pontos fortes, fracos e justificativas..." required></textarea>
                        </div>

                        <div class="flex justify-end gap-4">
                            <button type="button" @click="enviarParecer('retornado_correcao')" class="bg-rose-900/40 border border-rose-700 text-rose-300 font-medium px-5 py-2 rounded-lg hover:bg-rose-950 transition-colors text-sm">
                                Retornar para Correção
                            </button>
                            <button type="button" @click="enviarParecer('enviado_camara')" class="bg-emerald-600 text-white font-medium px-5 py-2 rounded-lg hover:bg-emerald-500 transition-colors text-sm">
                                Enviar para a Câmara ✓
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>
<script setup>
    import { ref } from 'vue';
    import { router } from '@inertiajs/vue3';

    defineOptions({ layout: false });

    const props = defineProps({
        propostasInicial: Array
    });

    const propostas = ref(props.propostasInicial || []);

    const votar = (id, resultado) => {
        if (confirm(`Deseja confirmar o veredito como: ${resultado.toUpperCase()}?`)) {
            // Dispara uma chamada utilizando o Axios que já vem embutido no projeto para bater no seu endpoint de API robusto
            window.axios.post(`/api/camara/propostas/${id}/decisao`, { status: resultado })
                .then(res => {
                    alert('Decisão da Câmara registrada com sucesso!');
                    // Remove da tela a proposta votada localmente para dar o efeito de tempo real
                    propostas.value = propostas.value.filter(p => p.id !== id);
                })
                .catch(err => {
                    alert('Erro ao registrar decisão.');
                });
        }
    };
</script>

<template>
    <div class="min-h-screen bg-slate-900 text-slate-100 p-8">
        <div class="max-w-5xl mx-auto">
            
            <div class="flex justify-between items-center mb-8 border-b border-slate-800 pb-6">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Câmara de Graduação</h1>
                    <p class="text-slate-400 mt-1">Pauta do Dia - Julgamento Final de Projetos Pedagógicos</p>
                </div>
                <a href="/" class="text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-lg transition-colors">
                    ← Voltar ao Portal
                </a>
            </div>

            <div v-if="propostas.length > 0" class="space-y-6">
                <div v-for="proposta in propostas" :key="proposta.id" class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-400 bg-purple-950/50 px-2.5 py-1 rounded border border-purple-900">
                                ID: {{ proposta.id }} - Aguardando Voto
                            </span>
                            <h2 class="text-xl font-semibold mt-2 text-white">Proposta de Curso</h2>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="votar(proposta.id, 'aprovado')" class="bg-emerald-600 hover:bg-emerald-500 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                Aprovar PPC
                            </button>
                            <button @click="votar(proposta.id, 'rejeitado')" class="bg-rose-600 hover:bg-rose-500 text-white font-medium px-4 py-2 rounded-lg transition-colors text-sm">
                                Rejeitar
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-700/50">
                        <h3 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-2">Estrutura Curricular Enviada:</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div v-for="disc in proposta.disciplinas" :key="disc.id" class="bg-slate-900/50 p-2.5 rounded border border-slate-700/40 text-sm flex justify-between">
                                <span class="text-slate-200 font-medium">{{ disc.nome }}</span>
                                <span class="text-slate-500">{{ disc.carga_horaria }}h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-16 bg-slate-800/50 border border-slate-800 rounded-xl">
                <p class="text-slate-400">Nenhum projeto pedagógico encontra-se na pauta de votação da Câmara no momento.</p>
            </div>

        </div>
    </div>
</template>

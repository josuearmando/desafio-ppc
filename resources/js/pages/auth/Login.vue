<template>
    <div class="min-h-screen flex flex-col justify-center items-center bg-slate-900 p-6">
        <div class="w-full max-w-md bg-slate-800 rounded-xl shadow-2xl p-8 border border-slate-700">
            
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-white tracking-wide">Simulador de PPC - UFPA</h1>
                <p class="text-sm text-slate-400 mt-2">Selecione seu perfil para acessar o sistema</p>
            </div>

            <div class="space-y-4">
                
                <button
                    type="button"
                    @click="selecionarPerfil('unidade')"
                    :class="[
                        'w-full py-3 px-4 rounded-lg font-medium border transition-all flex justify-between items-center cursor-pointer',
                        perfilSelecionado === 'unidade'
                            ? 'border-blue-500 bg-blue-950/50 text-blue-400'
                            : 'border-slate-700 text-slate-300 hover:bg-slate-700/50'
                    ]"
                >
                    <span>Submeter PPC (Unidade Acadêmica)</span>
                    <span class="text-xs bg-blue-900/50 text-blue-300 px-2 py-0.5 rounded border border-blue-800">Restrito</span>
                </button>

                <button
                    type="button"
                    @click="selecionarPerfil('avaliador')"
                    :class="[
                        'w-full py-3 px-4 rounded-lg font-medium border transition-all flex justify-between items-center cursor-pointer',
                        perfilSelecionado === 'avaliador'
                            ? 'border-emerald-500 bg-emerald-950/50 text-emerald-400'
                            : 'border-slate-700 text-slate-300 hover:bg-slate-700/50'
                    ]"
                >
                    <span>Avaliar PPC (Avaliador Técnico)</span>
                    <span class="text-xs bg-emerald-900/50 text-emerald-300 px-2 py-0.5 rounded border border-emerald-800">Restrito</span>
                </button>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-700"></div></div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-2 bg-slate-800 text-slate-500 font-medium">Acesso Facilitado</span>
                    </div>
                </div>

                <a href="/camara" class="w-full py-3 px-4 rounded-lg font-medium border border-purple-500/30 bg-purple-950/40 text-purple-400 hover:bg-purple-900/30 transition-all flex justify-between items-center text-center">
                    <span>Painel da Câmara de Graduação</span>
                    <span class="text-xs bg-purple-900/60 text-purple-300 px-2 py-0.5 rounded font-bold border border-purple-700">Livre →</span>
                </a>
            </div>

            <Transition name="slide-down">
                <form
                    v-if="perfilSelecionado"
                    @submit.prevent="submit"
                    class="mt-6 pt-6 border-t border-slate-700 space-y-4"
                >
                    <div v-if="perfilSelecionado === 'unidade'">
                        <label class="block text-sm font-medium text-slate-300">Unidade Acadêmica</label>
                        <div class="pr-2 rounded-md border-slate-600 shadow-sm bg-slate-700">
                            <select
                                v-model="form.loginIdentifier"
                                class="mt-1 block w-full rounded-md text-white outline-none bg-slate-700 focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2.5 cursor-pointer"
                                required
                            >
                                <option value="" disabled selected>Selecione a sua faculdade...</option>
                                <option 
                                    v-for="unidade in unidades" 
                                    :key="unidade.id" 
                                    :value="unidade.nome"
                                >
                                    {{ unidade.nome }}
                                </option>
                            </select>

                        </div>
                        
                    </div>

                    <div v-if="perfilSelecionado === 'avaliador'">
                        <label class="block text-sm font-medium text-slate-300">E-mail institucional</label>
                        <input
                            type="email"
                            v-model="form.loginIdentifier"
                            class="mt-1 block w-full rounded-md border-slate-600 bg-slate-700 text-white shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm p-2.5"
                            placeholder="usuario@ufpa.br"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300">Senha de Acesso</label>
                        <input
                            type="password"
                            v-model="form.password"
                            class="mt-1 block w-full rounded-md border-slate-600 bg-slate-700 text-white shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm p-2.5"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-white text-slate-900 py-2.5 rounded-lg font-semibold hover:bg-slate-100 transition-colors mt-2 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Autenticando...' : 'Entrar no Painel' }}
                    </button>
                </form>
            </Transition>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: false });

// Recebe o array enviado pelo backend através do Inertia
defineProps({
    unidades: {
        type: Array,
        default: () => []
    }
});


const perfilSelecionado = ref(null);

const form = useForm({
    loginIdentifier: '',
    password: '',
    perfil_selecionado: ''
});

const selecionarPerfil = (perfil) => {
    perfilSelecionado.value = perfil;
    form.loginIdentifier = '';
    form.password = '';
    form.perfil_selecionado = perfil;
};

const submit = () => {
    form.post('/login');
};
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
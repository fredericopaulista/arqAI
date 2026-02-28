<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    settings: Array,
});

const form = useForm({
    settings: props.settings.map(s => ({ key: s.key, value: s.value || '' }))
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Configurações salvas com sucesso!'),
    });
};
</script>

<template>
    <Head title="Configurações de IA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-black text-2xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-tighter">
                   Configurações <span class="text-indigo-600">SaaS AI</span>
                </h2>
                <Link :href="route('admin.dashboard')" class="text-sm font-bold text-gray-500 hover:text-gray-800 transition">
                    ← Voltar ao Painel
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                        
                        <div class="grid grid-cols-1 gap-8">
                            <div v-for="(setting, index) in form.settings" :key="setting.key" class="space-y-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-500/70">
                                    {{ 
                                        setting.key === 'agent_prompt' ? 'Prompt do Agente (Briefing Chat)' : 
                                        (setting.key === 'image_prompt_template' ? 'Template do Prompt de Geração' : 
                                        (setting.key === 'replicate_api_key' ? 'API Key (Replicate - Geração de Imagem)' : 
                                        (setting.key === 'ai_api_key' ? 'API Key (OpenAI - Chat)' : 
                                        (setting.key === 'asaas_api_key' ? 'API Key (Asaas - Pagamentos)' : 
                                        (setting.key === 'asaas_mode' ? 'Modo Asaas (sandbox/production)' : setting.key.replace(/_/g, ' '))))))
                                    }}
                                </label>
                                
                                <textarea v-if="setting.key === 'agent_prompt' || setting.key === 'image_prompt_template'" 
                                    v-model="form.settings[index].value" 
                                    :rows="setting.key === 'agent_prompt' ? 10 : 6"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono"
                                ></textarea>
                                
                                <input v-else-if="setting.key === 'ai_api_key' || setting.key === 'replicate_api_key' || setting.key === 'asaas_api_key'"
                                    type="password"
                                    v-model="form.settings[index].value"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="••••••••••••••••"
                                />

                                <select v-else-if="setting.key === 'ai_model' || setting.key === 'asaas_mode'"
                                    v-model="form.settings[index].value"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <template v-if="setting.key === 'ai_model'">
                                        <option value="gpt-4o">GPT-4o (Standard)</option>
                                        <option value="gpt-4-turbo">GPT-4 Turbo</option>
                                        <option value="claude-3-5-sonnet">Claude 3.5 Sonnet</option>
                                        <option value="gemini-1.5-pro">Gemini 1.5 Pro</option>
                                    </template>
                                    <template v-else>
                                        <option value="sandbox">Sandbox (Teste)</option>
                                        <option value="production">Produção (Real)</option>
                                    </template>
                                </select>

                                <input v-else
                                    type="text"
                                    v-model="form.settings[index].value"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                        </div>

                        <div class="mt-10 flex justify-end">
                            <button 
                                type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-black uppercase text-xs tracking-widest shadow-lg shadow-indigo-200 transition-all active:scale-95"
                                :disabled="form.processing"
                            >
                                Salvar Configurações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

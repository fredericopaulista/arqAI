<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';

const props = defineProps({
    project: Object
});

const projectStatus = ref(props.project.status);

const form = useForm({
    message: '',
});

const chatContainer = ref(null);

const scrollToBottom = () => {
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const checkStatus = async () => {
    if (projectStatus.value === 'collecting_data') return;

    try {
        const response = await fetch(route('projects.status', props.project.id));
        const data = await response.json();
        
        if (data.status === 'completed') {
            window.location.href = route('projects.show', props.project.id);
        } else {
            projectStatus.value = data.status;
            setTimeout(checkStatus, 3000);
        }
    } catch (e) {
        console.error("Erro ao verificar status", e);
        setTimeout(checkStatus, 5000);
    }
};

onMounted(() => {
    scrollToBottom();
    if (props.project.status === 'generating') {
        checkStatus();
    }
});

const submit = () => {
    if (!form.message.trim()) return;
    
    form.post(route('projects.send', props.project.id), {
        onSuccess: () => {
            form.reset();
            nextTick(scrollToBottom);
            // If the backend changed status to 'generating', start polling
            if (props.project.status !== 'collecting_data') {
                projectStatus.value = 'generating';
                checkStatus();
            }
        },
    });
};
</script>

<template>
    <Head title="Consultoria com IA" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Consultoria ArqAI: Projeto #{{ project.id }}
                </h2>
                <div class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 text-xs font-bold rounded-full uppercase tracking-wider">
                    {{ projectStatus === 'collecting_data' ? 'Briefing' : 'Gerando Proposta' }}
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-6">
                <!-- Image Preview -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase">Ambiente Original</h3>
                            <img :src="'/storage/' + project.original_image_path" class="w-full rounded-lg shadow-sm" />
                        </div>
                    </div>
                </div>

                <!-- Chat Interface -->
                <div class="w-full md:w-2/3 flex flex-col h-[600px]">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-full">
                        <div ref="chatContainer" class="flex-1 p-6 overflow-y-auto space-y-4 bg-gray-50 dark:bg-gray-900/50">
                            <div v-for="message in project.conversations" :key="message.id" 
                                class="flex" :class="message.role === 'assistant' ? 'justify-start' : 'justify-end'">
                                <div class="max-w-[80%] rounded-2xl px-4 py-2 shadow-sm"
                                    :class="message.role === 'assistant' 
                                        ? 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700' 
                                        : 'bg-indigo-600 text-white'">
                                    <p class="text-sm leading-relaxed">{{ message.message }}</p>
                                    <span class="text-[10px] opacity-70 mt-1 block">{{ new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
                                </div>
                            </div>

                            <div v-if="projectStatus === 'generating'" class="flex justify-start">
                                <div class="bg-white dark:bg-gray-800 rounded-2xl px-4 py-3 border border-gray-100 dark:border-gray-700">
                                    <div class="flex space-x-2">
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce [animation-delay:-.5s]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <form @submit.prevent="submit" class="flex gap-2">
                                <input 
                                    v-model="form.message"
                                    :disabled="form.processing || projectStatus !== 'collecting_data'"
                                    type="text" 
                                    placeholder="Digite sua resposta..." 
                                    class="flex-1 rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <button 
                                    type="submit"
                                    :disabled="form.processing || !form.message.trim() || projectStatus !== 'collecting_data'"
                                    class="p-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 focus:outline-none transition-colors disabled:opacity-50"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                </button>
                            </form>
                            <p v-if="projectStatus === 'generating'" class="text-center text-xs text-indigo-600 mt-2 font-medium">
                                Proposta sendo gerada... você será avisado quando estiver pronto.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

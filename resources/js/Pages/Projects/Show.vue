<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    project: Object
});

const isProcessing = ref(props.project.status === 'generating');
let interval = null;

onMounted(() => {
    if (isProcessing.value) {
        interval = setInterval(() => {
            router.reload({
                only: ['project'],
                onSuccess: (page) => {
                    if (page.props.project.status === 'completed') {
                        isProcessing.value = false;
                        clearInterval(interval);
                    }
                }
            });
        }, 5000);
    }
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>

<template>
    <Head title="Proposta Arquitetônica" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Proposta ArqAI: Projeto #{{ project.id }}
                </h2>
                <Link :href="route('dashboard')" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium font-bold">Voltar ao Painel</Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Loading State -->
                <div v-if="project.status === 'generating'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600 mb-4"></div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Criando sua proposta...</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Nossa IA está analisando seu briefing e gerando um design exclusivo.</p>
                </div>

                <!-- Final Result -->
                <div v-else-if="project.status === 'completed'" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Images -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Antes & Depois</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Original</p>
                                        <img :src="'/storage/' + project.original_image_path" class="w-full rounded-lg shadow-sm" />
                                    </div>
                                    <div class="relative">
                                        <p class="text-xs font-semibold text-indigo-600 uppercase mb-1">Proposta ArqAI Studio</p>
                                        <img :src="'/storage/' + project.generated_image_path" class="w-full rounded-lg shadow-2xl border-4 border-indigo-500/20" />
                                        <div class="absolute top-8 right-4 bg-indigo-600 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-widest shadow-lg">IA Generated</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Detalhes da Proposta</h3>
                                <div class="space-y-4">
                                    <div v-for="(val, key) in project.briefing_json" :key="key" class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider mb-1">{{ key.replace('_', ' ') }}</p>
                                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ val }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-8 flex flex-col gap-3">
                                    <a :href="'/storage/' + project.generated_image_path" download 
                                        class="w-full text-center bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition-colors shadow-lg">
                                        Baixar Imagem em Alta
                                    </a>
                                    <Link :href="route('projects.create')" 
                                        class="w-full text-center bg-white dark:bg-gray-800 text-indigo-600 border border-indigo-600/30 py-3 rounded-lg font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        Novo Projeto
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

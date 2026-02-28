<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    projects: Object,
});
</script>

<template>
    <Head title="Oversight de Projetos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-black text-2xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-tighter">
                   Oversight de <span class="text-indigo-600">Projetos</span>
                </h2>
                <Link :href="route('admin.dashboard')" class="text-sm font-bold text-gray-500 hover:text-gray-800 transition">
                    ← Voltar ao Painel
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="p-8">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] text-gray-400 uppercase tracking-widest border-b dark:border-gray-700">
                                        <th class="pb-4 px-4">Preview</th>
                                        <th class="pb-4 px-4">Detalhes do Projeto</th>
                                        <th class="pb-4 px-4">Proprietário / Tenant</th>
                                        <th class="pb-4 px-4">Status</th>
                                        <th class="pb-4 px-4">Data</th>
                                        <th class="pb-4 px-4 text-right">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="project in projects.data" :key="project.id" class="border-b last:border-0 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="py-6 px-4">
                                            <div class="w-16 h-16 rounded-xl overflow-hidden shadow-lg transform hover:scale-110 transition duration-300">
                                                <img :src="'/storage/' + project.original_image_path" class="w-full h-full object-cover" />
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="flex flex-col">
                                                <span class="font-black text-gray-900 dark:text-white uppercase tracking-tight text-sm">PROJETO #{{ project.id }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Proposta Arquitetônica</span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ project.user.name }}</span>
                                                <span class="text-[10px] text-indigo-500 font-black uppercase tracking-widest">{{ project.tenant.name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-black tracking-tighter"
                                                :class="{
                                                    'bg-amber-100 text-amber-800': project.status === 'collecting_data',
                                                    'bg-blue-100 text-blue-800': project.status === 'generating',
                                                    'bg-emerald-100 text-emerald-800': project.status === 'completed'
                                                }">
                                                {{ project.status }}
                                            </span>
                                        </td>
                                        <td class="py-6 px-4 text-xs font-bold text-gray-500">{{ new Date(project.created_at).toLocaleDateString() }}</td>
                                        <td class="py-6 px-4 text-right">
                                            <Link :href="route('projects.show', project.id)" class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-4 py-2 rounded-lg text-xs font-black uppercase hover:bg-indigo-600 hover:text-white transition">
                                                Inspecionar
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Premium Pagination -->
                        <div class="mt-8 flex items-center justify-between">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Página {{ projects.current_page }} de {{ projects.last_page }}
                            </div>
                            <div class="flex gap-2">
                                <Link v-if="projects.prev_page_url" :href="projects.prev_page_url" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-xs font-bold uppercase transition hover:bg-gray-200">Anterior</Link>
                                <Link v-if="projects.next_page_url" :href="projects.next_page_url" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold uppercase transition hover:bg-indigo-700 shadow-md">Próxima</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

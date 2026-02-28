<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    tenants: Array,
});

const adjustCredits = (user) => {
    // This would ideally be a modal, but keeping it simple for now
    const credits = prompt('Quantidade de créditos:', user.credits);
    if (credits !== null) {
        useForm({ credits }).post(route('admin.users.adjust-credits', user.id));
    }
};
</script>

<template>
    <Head title="Gestão de Clientes" />

    <AuthenticatedLayout>
        <template #header>
             <div class="flex justify-between items-center">
                <h2 class="font-black text-2xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-tighter">
                   Gestão de <span class="text-indigo-600">Clientes</span>
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
                                        <th class="pb-4 px-4 text-center w-12">ID</th>
                                        <th class="pb-4 px-4">Nome da Equipe</th>
                                        <th class="pb-4 px-4 text-center">Usuários</th>
                                        <th class="pb-4 px-4 text-center">Projetos</th>
                                        <th class="pb-4 px-4">Data Cadastro</th>
                                        <th class="pb-4 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tenant in tenants" :key="tenant.id" class="border-b last:border-0 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="py-6 px-4 font-mono text-xs text-gray-400 text-center">{{ tenant.id }}</td>
                                        <td class="py-6 px-4">
                                            <div class="flex flex-col">
                                                <span class="font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ tenant.name }}</span>
                                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">Membro SaaS</span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4 text-center">
                                            <span class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 px-3 py-1 rounded-full text-xs font-black">{{ tenant.users_count }}</span>
                                        </td>
                                        <td class="py-6 px-4 text-center">
                                             <span class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-black">{{ tenant.projects_count }}</span>
                                        </td>
                                        <td class="py-6 px-4 text-sm font-medium text-gray-500 whitespace-nowrap">{{ new Date(tenant.created_at).toLocaleDateString() }}</td>
                                        <td class="py-6 px-4 text-right">
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-lg uppercase">Ativo</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

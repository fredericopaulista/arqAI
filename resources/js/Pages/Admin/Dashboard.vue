<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    recent_projects: Array,
});
</script>

<template>
    <Head title="Painel de Super Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-black text-2xl text-gray-800 dark:text-gray-100 leading-tight uppercase tracking-tighter">
                    SaaS <span class="text-indigo-600">Central de Controle</span>
                </h2>
                <div class="flex gap-2">
                    <Link :href="route('admin.tenants')" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                        Gerenciar Tenants
                    </Link>
                    <Link :href="route('admin.projects')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 transition shadow-md">
                        Oversight de Projetos
                    </Link>
                    <Link :href="route('admin.settings')" class="bg-amber-500 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-amber-600 transition shadow-md">
                        Configurações Gerais
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- KPI Dashboard -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                            <svg class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tenants Ativos</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ stats.total_tenants }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform text-indigo-500">
                             <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-1">Total Usuários</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ stats.total_users }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform text-emerald-500">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-1">Projetos Gerados</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ stats.total_projects }}</h3>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group">
                         <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform text-amber-500">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1">Tempo Médio (IA)</p>
                        <h3 class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ stats.avg_generation_time }}s</h3>
                    </div>
                </div>

                <!-- Recent Activities Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Table -->
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">Projetos em Tempo Real</h3>
                            <Link :href="route('admin.projects')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Ver Todos</Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] text-gray-400 uppercase tracking-widest border-b dark:border-gray-700">
                                        <th class="pb-3 px-4">Projeto</th>
                                        <th class="pb-3 px-4">Equipe</th>
                                        <th class="pb-3 px-4">Status</th>
                                        <th class="pb-3 px-4">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="project in recent_projects" :key="project.id" class="border-b last:border-0 dark:border-gray-700 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                                        <td class="py-4 px-4 font-bold text-sm">#{{ project.id }}</td>
                                        <td class="py-4 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">{{ project.tenant.name }}</td>
                                        <td class="py-4 px-4">
                                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-black"
                                                :class="{
                                                    'bg-amber-100 text-amber-800': project.status === 'collecting_data',
                                                    'bg-blue-100 text-blue-800': project.status === 'generating',
                                                    'bg-emerald-100 text-emerald-800': project.status === 'completed'
                                                }">
                                                {{ project.status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-xs text-gray-400">{{ new Date(project.created_at).toLocaleDateString() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Right Column: Quick Actions & Alerts -->
                    <div class="space-y-6">
                        <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                            <svg class="absolute -bottom-6 -right-6 w-32 h-32 opacity-20" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                            <h4 class="text-xl font-black mb-2 uppercase italic tracking-tighter">Manutenção Rápida</h4>
                            <p class="text-indigo-100 text-sm mb-4">Gerenciamento global de infraestrutura SaaS e suporte.</p>
                            <div class="space-y-2">
                                <Link :href="route('admin.tenants')" class="w-full flex items-center justify-between bg-white/10 hover:bg-white/20 p-3 rounded-xl transition backdrop-blur-sm border border-white/10">
                                    <span class="text-sm font-bold">Listar Todos Clientes</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </Link>
                                <button class="w-full flex items-center justify-between bg-white/10 hover:bg-white/20 p-3 rounded-xl transition backdrop-blur-sm border border-white/10">
                                    <span class="text-sm font-bold text-white/50">Configurações do Sistema</span>
                                    <span class="text-[8px] bg-white/20 px-1 rounded">Soon</span>
                                </button>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 px-6">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-4">Log de Sistema</h4>
                            <div class="space-y-4">
                                <div class="flex gap-3">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Sincronização de IA completa para todos os tenants.</p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Novo tenant registrado: <span class="text-gray-900 dark:text-gray-100 font-bold">Design Studio A</span></p>
                                </div>
                                <div class="flex gap-3">
                                    <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Picos de carga detectados no workers queue 1.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

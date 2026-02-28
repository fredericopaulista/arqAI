<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    projects: Array
});

const searchQuery = ref('');
const statusFilter = ref('all');

const filteredProjects = computed(() => {
    return props.projects.filter(p => {
        const matchesSearch = p.id.toString().includes(searchQuery.value) || 
                             (p.briefing_json?.tipo_ambiente?.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesStatus = statusFilter.value === 'all' || p.status === statusFilter.value;
        
        return matchesSearch && matchesStatus;
    });
});

const getStatusLabel = (status) => {
    const labels = {
        'collecting_data': 'Briefing',
        'generating': 'Gerando Proposta',
        'completed': 'Finalizado'
    };
    return labels[status] || status;
};
</script>

<template>
    <Head title="Meu Estúdio - ArqAI" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div class="space-y-2">
                    <h2 class="font-black text-4xl lg:text-5xl leading-tight tracking-tighter uppercase italic">
                        Meu <span class="text-amber-500">Estúdio</span>
                    </h2>
                    <p class="text-white/40 font-light text-lg">Gerencie e visualize suas transformações arquitetônicas.</p>
                </div>

                <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                    <!-- Global Search -->
                    <div class="relative flex-1 md:flex-none md:w-64 group">
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Buscar projeto..." 
                            class="w-full bg-white/5 border-white/10 rounded-2xl px-5 py-3 text-sm focus:ring-amber-500 focus:border-amber-500 transition-all placeholder:text-white/20"
                        />
                        <svg class="absolute right-4 top-3.5 w-4 h-4 text-white/20 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <Link :href="route('projects.create')" class="bg-amber-500 text-black px-6 py-3.5 rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-amber-400 transition-all shadow-xl shadow-amber-500/20 active:scale-95 whitespace-nowrap">
                        + Novo Projeto
                    </Link>
                </div>
            </div>
        </template>

        <!-- Filters & Stats Sub-header -->
        <div class="flex flex-wrap items-center justify-between gap-6 mb-12 animate-fade-in">
            <div class="flex items-center gap-2 p-1 bg-white/5 border border-white/5 rounded-2xl overflow-hidden">
                <button 
                    v-for="filter in ['all', 'collecting_data', 'generating', 'completed']" 
                    :key="filter"
                    @click="statusFilter = filter"
                    :class="[
                        'px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all',
                        statusFilter === filter ? 'bg-amber-500 text-black shadow-lg shadow-amber-500/20' : 'text-white/40 hover:text-white'
                    ]"
                >
                    {{ filter === 'all' ? 'Todos' : getStatusLabel(filter) }}
                </button>
            </div>

            <div class="text-[10px] font-black uppercase tracking-widest text-white/30">
                Exibindo <span class="text-white">{{ filteredProjects.length }}</span> de <span class="text-white">{{ projects.length }}</span> projetos
            </div>
        </div>

        <div class="relative">
            <!-- Empty State -->
            <div v-if="filteredProjects.length === 0" class="py-32 flex flex-col items-center text-center animate-fade-in">
                <div class="w-24 h-24 bg-white/5 rounded-[2rem] flex items-center justify-center mb-8 border border-white/10">
                    <svg class="w-10 h-10 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-black uppercase tracking-tighter mb-2 italic">Nenhum projeto encontrado</h3>
                <p class="text-white/40 font-light max-w-sm mb-8">Parece que você ainda não iniciou nenhuma obra com a nossa IA.</p>
                <Link :href="route('projects.create')" class="text-amber-500 font-black uppercase text-xs tracking-widest border-b-2 border-amber-500/30 hover:border-amber-500 transition-all pb-1">
                    Iniciar minnha primeira geração →
                </Link>
            </div>

            <!-- Bento Projects Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="(project, index) in filteredProjects" :key="project.id" 
                    class="group relative animate-slide-up"
                    :style="{ animationDelay: (index * 0.1) + 's' }">
                    
                    <div class="relative bg-[#121214] border border-white/5 rounded-[2.5rem] overflow-hidden transition-all duration-500 hover:border-amber-500/40 hover:-translate-y-2 shadow-2xl group flex flex-col h-full">
                        
                        <!-- Image Container -->
                        <Link :href="project.status === 'completed' ? route('projects.show', project.id) : route('projects.chat', project.id)" class="block aspect-[4/3] relative overflow-hidden bg-zinc-900">
                            <img :src="'/storage/' + project.original_image_path" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 opacity-70 group-hover:opacity-90" />
                            
                            <!-- Static/Dynamic Overlays -->
                            <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-[#121214] to-transparent"></div>
                            
                            <!-- Floating Badge -->
                            <div class="absolute top-6 right-6">
                                <div :class="[
                                    'px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border shadow-2xl backdrop-blur-md',
                                    project.status === 'collecting_data' ? 'bg-amber-500/10 border-amber-500/30 text-amber-500' : 
                                    (project.status === 'generating' ? 'bg-blue-500/10 border-blue-500/30 text-blue-400 animate-pulse' : 
                                    'bg-emerald-500/10 border-emerald-500/30 text-emerald-400')
                                ]">
                                    {{ getStatusLabel(project.status) }}
                                </div>
                            </div>

                            <!-- Hover Prompt Detail -->
                            <div class="absolute inset-x-0 bottom-0 p-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Briefing</p>
                                <p class="text-xs text-white/60 line-clamp-2 italic">
                                    "{{ project.briefing_json?.estilo || 'Estilo não definido' }} para {{ project.briefing_json?.tipo_ambiente || 'Ambiente' }}"
                                </p>
                            </div>
                        </Link>

                        <!-- Content info -->
                        <div class="p-8 space-y-4 flex-1 flex flex-col justify-between">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-xl font-bold tracking-tighter uppercase italic">Obra #{{ project.id }}</h4>
                                    <p class="text-[10px] font-medium text-white/30 uppercase tracking-[0.2em] mt-1">{{ new Date(project.created_at).toLocaleDateString() }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-white/5 flex items-center justify-between">
                                <div class="flex -space-x-2">
                                     <div v-for="i in 3" :key="i" class="w-6 h-6 rounded-full border border-[#121214] bg-zinc-800 flex items-center justify-center overflow-hidden">
                                        <div class="w-full h-full bg-gradient-to-br from-zinc-700 to-zinc-900"></div>
                                     </div>
                                </div>
                                <span class="text-[8px] font-black text-white/20 uppercase tracking-widest">Render de Alta Precisão</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.animate-slide-up {
    animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out forwards;
}
</style>

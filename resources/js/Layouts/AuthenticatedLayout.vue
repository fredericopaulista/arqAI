<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const scrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
});

const user = usePage().props.auth.user;
</script>

<template>
    <div class="min-h-screen bg-[#070708] text-white font-sans selection:bg-amber-200 selection:text-black">
        <!-- Premium Navigation -->
        <nav :class="[
            'fixed w-full z-50 transition-all duration-500 px-6 py-4 flex justify-between items-center',
            scrolled || showingNavigationDropdown ? 'bg-black/80 backdrop-blur-xl py-3 border-b border-white/5' : 'bg-transparent'
        ]">
            <div class="flex items-center gap-10">
                <!-- Logo -->
                <Link :href="route('dashboard')" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-600 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-xl leading-none">A</span>
                    </div>
                    <span class="text-lg font-bold tracking-tighter uppercase">ArqAI <span class="text-amber-500">Studio</span></span>
                </Link>

                <!-- Desktop Links -->
                <div class="hidden md:flex items-center gap-6">
                    <Link :href="route('dashboard')" 
                        :class="[
                            'text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-amber-500',
                            route().current('dashboard') ? 'text-amber-500' : 'text-white/40'
                        ]">
                        Painel
                    </Link>
                    <Link :href="route('projects.create')" 
                        :class="[
                            'text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-amber-500',
                            route().current('projects.create') ? 'text-amber-500' : 'text-white/40'
                        ]">
                        Novo Projeto
                    </Link>
                    <Link :href="route('users.index')" 
                        :class="[
                            'text-[10px] font-black uppercase tracking-[0.2em] transition-all hover:text-amber-500',
                            route().current('users.index') ? 'text-amber-500' : 'text-white/40'
                        ]">
                        Minha Equipe
                    </Link>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Credits Display -->
                <Link :href="route('billing.index')" class="hidden sm:flex items-center gap-3 px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl transition-all group">
                    <div class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white/50 group-hover:text-white">Créditos:</span>
                    <span class="text-sm font-black text-white">{{ user.credits }}</span>
                </Link>

                <!-- Admin Badge if applicable -->
                <Link v-if="user.is_admin" :href="route('admin.dashboard')" class="hidden md:block text-[8px] font-black uppercase tracking-widest border border-red-500/50 text-red-400 px-2 py-1 rounded-md hover:bg-red-500 hover:text-white transition-all">
                    Super Admin
                </Link>

                <!-- User Dropdown Toggle -->
                <div class="relative items-center gap-4 flex">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-zinc-800 to-zinc-900 border border-white/10 flex items-center justify-center text-xs font-bold uppercase overflow-hidden group-hover:border-amber-500/50 transition-all">
                            {{ user.name.charAt(0) }}
                        </div>
                        <svg class="w-4 h-4 text-white/30 group-hover:text-amber-500 transition-colors" :class="{'rotate-180': showingNavigationDropdown}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown Menu -->
                    <div v-if="showingNavigationDropdown" class="absolute top-14 right-0 w-48 bg-zinc-900/95 backdrop-blur-2xl border border-white/10 rounded-[1.5rem] shadow-2xl p-2 z-[60] animate-fade-in">
                        <Link :href="route('profile.edit')" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 text-[10px] font-black uppercase tracking-widest transition-all">
                            Meu Perfil
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-red-500/10 text-red-400 text-[10px] font-black uppercase tracking-widest transition-all">
                            Sair do ArqAI
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="pt-24 min-h-screen relative overflow-hidden">
            <!-- Ambient Background Decor -->
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-[120px] pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-orange-600/5 rounded-full blur-[100px] pointer-events-none"></div>

            <header v-if="$slots.header" class="px-6 py-10 max-w-7xl mx-auto">
                <slot name="header" />
            </header>

            <main class="relative z-10 px-6 max-w-7xl mx-auto pb-20">
                <slot />
            </main>
        </div>

        <!-- Footer Decoration -->
        <footer class="py-12 px-6 border-t border-white/5 opacity-20">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-widest">ArqAI Studio &copy; 2026</span>
                <div class="flex gap-6">
                    <span class="text-[8px] font-bold uppercase tracking-tighter">Tecnologia ControlNet</span>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;600;700;800;900&display=swap');

.font-sans {
    font-family: 'Outfit', sans-serif;
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

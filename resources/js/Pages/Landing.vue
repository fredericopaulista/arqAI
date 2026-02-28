<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

defineProps({
    plans: Array,
    canLogin: Boolean,
    canRegister: Boolean,
});

const scrolled = ref(false);
const activeFaq = ref(null);

const faqs = [
    {
        q: "Como funcionam os créditos?",
        a: "Diferente de sistemas de assinatura, aqui você compra pacotes de créditos. Cada crédito dá direito a uma geração completa de proposta (briefing via chat/voz + renderização IA). Seus créditos não expiram e você usa quando precisar."
    },
    {
        q: "A IA preserva as medidas do meu projeto?",
        a: "Sim! Utilizamos tecnologia ControlNet que analisa a estrutura da sua foto original. Isso garante que janelas, portas e vigas permaneçam no lugar exato, mudando apenas o revestimento, iluminação e decoração."
    },
    {
        q: "Quais formas de pagamento são aceitas?",
        a: "Aceitamos PIX para liberação imediata dos créditos e Cartão de Crédito via checkout seguro do Asaas."
    },
    {
        q: "Posso usar em reuniões com clientes?",
        a: "Com certeza. O ArqAI Studio foi desenhado para ser uma ferramenta de venda. Você pode subir a foto da obra e gerar propostas conceituais em segundos, encantando o cliente no ato."
    }
];

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 50;
    });
});
</script>

<template>
    <Head title="ArqAI Studio - Projetos Instantâneos com IA para Arquitetos" />

    <div class="min-h-screen bg-[#070708] text-white font-sans selection:bg-amber-200 selection:text-black scroll-smooth">
        <!-- Navigation -->
        <nav :class="[
            'fixed w-full z-50 transition-all duration-500 px-6 py-4 flex justify-between items-center',
            scrolled ? 'bg-black/80 backdrop-blur-md py-3 border-b border-white/10' : 'bg-transparent'
        ]">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-600 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <span class="text-white font-bold text-xl">A</span>
                </div>
                <span class="text-xl font-display font-bold tracking-tight">ArqAI <span class="text-amber-500">Studio</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-white/70">
                <a href="#how-it-works" class="hover:text-white transition-colors">Como Funciona</a>
                <a href="#tech" class="hover:text-white transition-colors">Tecnologia</a>
                <a href="#pricing" class="hover:text-white transition-colors">Créditos</a>
                <a href="#faq" class="hover:text-white transition-colors">FAQ</a>
            </div>

            <div class="flex items-center gap-4">
                <Link v-if="canLogin" :href="route('login')" class="text-sm font-medium hover:text-amber-500 transition-colors">Entrar</Link>
                <Link v-if="canRegister" :href="route('register')" class="bg-white text-black px-6 py-2.5 rounded-full text-sm font-bold hover:bg-amber-500 hover:text-white transition-all transform hover:scale-105 active:scale-95">Começar Agora</Link>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-40 pb-32 px-6 overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[800px] bg-gradient-to-b from-amber-500/10 to-transparent opacity-40 blur-3xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center relative z-10">
                <div class="space-y-10">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-amber-500 text-[10px] font-black uppercase tracking-[0.2em] animate-pulse">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        IA Arquitetônica de Alta Performance
                    </div>
                    
                    <h1 class="text-6xl lg:text-[5rem] font-display font-black leading-[0.95] tracking-tighter text-white">
                        Do briefing ao <br/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 to-orange-500">render final</span> <br/> em segundos.
                    </h1>
                    
                    <p class="text-xl text-white/50 leading-relaxed max-w-xl font-light">
                        Chega de noites em claro renderizando. Suba uma foto, converse com nossa IA sobre o conceito e receba propostas fotorrealistas preservando cada detalhe estrutural original.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-5 pt-4">
                        <Link :href="route('register')" class="bg-amber-500 text-black px-10 py-5 rounded-2xl font-black text-lg flex items-center justify-center gap-3 hover:bg-amber-400 transition-all shadow-2xl shadow-amber-500/30 active:scale-95 group">
                            Criar Projeto Grátis
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5 group-hover:translate-x-1 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </Link>
                        <div class="flex items-center gap-4 px-6 text-sm text-white/40">
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-full border-2 border-[#070708] bg-zinc-800 flex items-center justify-center text-[10px] font-bold">+500</div>
                            </div>
                            <span class="font-medium">Arquitetos transformando a produtividade</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Showcase -->
                <div class="relative group perspective-1000">
                    <div class="absolute -inset-4 bg-gradient-to-r from-amber-500/20 to-orange-600/20 rounded-[3rem] blur-2xl opacity-40 group-hover:opacity-60 transition duration-1000"></div>
                    <div class="relative bg-[#121214] rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/5 aspect-[4/3] rotate-y-3 group-hover:rotate-y-0 transition-transform duration-700">
                        <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1200" class="w-full h-full object-cover opacity-80" alt="Exemplo de Renderização">
                        
                        <!-- Overlay Labels -->
                        <div class="absolute top-6 left-6 p-3 bg-black/60 backdrop-blur-md rounded-xl border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white/70">
                            Antes: Obra Cinza
                        </div>
                        <div class="absolute bottom-6 right-6 p-3 bg-amber-500/90 backdrop-blur-md rounded-xl text-[10px] font-black uppercase tracking-widest text-black">
                            Depois: Proposta ArqAI
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works Section -->
        <section id="how-it-works" class="py-32 px-6 border-y border-white/5 bg-zinc-950/20">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-3 gap-12">
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center border border-white/10 group-hover:bg-amber-500 group-hover:text-black transition-all duration-500">
                            <span class="text-2xl font-black">01</span>
                        </div>
                        <h4 class="text-2xl font-bold">Suba a Foto Original</h4>
                        <p class="text-white/40 font-light leading-relaxed text-lg">
                            Tire uma foto do ambiente real, mesmo que esteja vazio ou em obra. Nossa IA usará isso como fundação.
                        </p>
                    </div>
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center border border-white/10 group-hover:bg-amber-500 group-hover:text-black transition-all duration-500">
                            <span class="text-2xl font-black">02</span>
                        </div>
                        <h4 class="text-2xl font-bold">Defina o Conceito</h4>
                        <p class="text-white/40 font-light leading-relaxed text-lg">
                            Converse com nosso agente especialista sobre estilos, cores e materiais. Ele criará o resumo criativo ideal.
                        </p>
                    </div>
                    <div class="space-y-6 group">
                        <div class="w-16 h-16 bg-white/5 rounded-3xl flex items-center justify-center border border-white/10 group-hover:bg-amber-500 group-hover:text-black transition-all duration-500">
                            <span class="text-2xl font-black">03</span>
                        </div>
                        <h4 class="text-2xl font-bold">Gere Instantaneamente</h4>
                        <p class="text-white/40 font-light leading-relaxed text-lg">
                            Em menos de 10 segundos, receba uma proposta fotorrealista pronta para encantar seu cliente.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tech / ControlNet Showcase -->
        <section id="tech" class="py-32 px-6 relative">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-20">
                <div class="lg:w-1/2 space-y-8">
                    <h2 class="text-amber-500 font-black uppercase tracking-[0.3em] text-xs">Poder Tecnológico</h2>
                    <h3 class="text-4xl lg:text-6xl font-display font-medium leading-tight">Precisão Milimétrica com <span class="italic text-white">IA Estrutural</span>.</h3>
                    <p class="text-xl text-white/40 leading-relaxed font-light">
                        Diferente de IAs generativas convencionais que criam imagens aleatórias, o ArqAI Studio utiliza o **ControlNet**. Isso significa que a IA "lê" a geometria do seu espaço real e mantém janelas, paredes e fluxos exatamente onde eles estão.
                    </p>
                    <ul class="space-y-4 pt-4">
                        <li class="flex items-center gap-4 text-white/70">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            Preservação total de layout original
                        </li>
                        <li class="flex items-center gap-4 text-white/70">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            Detecção inteligente de profundidade e arestas
                        </li>
                        <li class="flex items-center gap-4 text-white/70">
                            <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                            Iluminação realista baseada em context aware
                        </li>
                    </ul>
                </div>
                <!-- Interactive Comparison Tool Mock -->
                <div class="lg:w-1/2 relative p-4 bg-white/5 border border-white/10 rounded-[3rem] overflow-hidden group">
                    <div class="aspect-[4/5] bg-zinc-900 rounded-[2.5rem] overflow-hidden flex relative">
                        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800" class="absolute inset-0 w-full h-full object-cover grayscale opacity-20" alt="Camada estrutural">
                        <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=800" class="absolute inset-0 w-full h-full object-cover clip-half opacity-100 transition-all duration-700" alt="Render final">
                        
                        <!-- Center Line -->
                        <div class="absolute inset-y-0 left-1/2 w-0.5 bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.5)] z-20"></div>
                        
                        <!-- Badge -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-amber-500 text-black px-4 py-2 rounded-full font-black text-[10px] z-30 shadow-2xl">
                            COMPARAÇÃO REAL
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing / Credit Packs -->
        <section id="pricing" class="py-32 px-6 bg-white text-black relative -rotate-1 rounded-[4rem] z-10 mx-2">
            <div class="max-w-7xl mx-auto rotate-1">
                <div class="text-center space-y-6 mb-24">
                    <h2 class="text-zinc-400 font-black uppercase tracking-[0.2em] text-[10px]">Preços Transparentes</h2>
                    <h3 class="text-5xl lg:text-7xl font-display font-black leading-tight tracking-tighter">
                        Sem assinaturas. <br/> Apenas <span class="bg-amber-400 px-4">créditos</span>.
                    </h3>
                    <p class="text-zinc-500 max-w-xl mx-auto text-lg font-medium">Compre uma vez, use quando quiser. Seus créditos nunca expiram e você só paga pelo que gera.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-10">
                    <div v-for="(plan, index) in plans" :key="plan.id" :class="[
                        'relative p-12 rounded-[3.5rem] border-2 transition-all duration-500 group flex flex-col justify-between',
                        index === 1 ? 'bg-black text-white border-black scale-105 z-20 shadow-[-20px_20px_0px_rgba(0,0,0,0.1)]' : 'bg-transparent border-zinc-100 hover:border-amber-400'
                    ]">
                        
                        <div v-if="index === 1" class="absolute -top-5 left-1/2 -translate-x-1/2 bg-amber-400 text-black px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                            PACOTE PROFISSIONAL
                        </div>

                        <div class="space-y-8 text-center md:text-left">
                            <div>
                                <h4 class="text-sm font-black uppercase tracking-widest opacity-60">{{ plan.name }}</h4>
                                <div class="mt-4 flex items-baseline justify-center md:justify-start gap-1">
                                    <span :class="['text-6xl font-black tracking-tighter', index === 1 ? 'text-amber-400' : 'text-black']">R${{ Math.floor(plan.monthly_price) }}</span>
                                    <span class="text-sm opacity-50">.00</span>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-center md:justify-start gap-4">
                                    <div :class="['w-10 h-10 rounded-2xl flex items-center justify-center', index === 1 ? 'bg-white/10 text-white' : 'bg-black/5 text-black']">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6">
                                            <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                                        </svg>
                                    </div>
                                    <p class="text-lg font-bold">{{ plan.image_limit }} Projetos <span class="block text-xs font-medium opacity-50">Válidos por tempo indeterminado</span></p>
                                </div>
                                <div class="flex items-center justify-center md:justify-start gap-4">
                                    <div :class="['w-10 h-10 rounded-2xl flex items-center justify-center', index === 1 ? 'bg-white/10 text-white' : 'bg-black/5 text-black']">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-amber-500">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold uppercase tracking-tight">Preservação de Layout</span>
                                </div>
                            </div>
                        </div>

                        <Link :href="route('register')" :class="[
                            'mt-10 w-full py-5 rounded-[2rem] font-black text-xs uppercase tracking-widest text-center transition-all',
                            index === 1 ? 'bg-amber-400 text-black hover:bg-white' : 'bg-black text-white hover:bg-amber-500'
                        ]">
                            Comprar Agora
                        </Link>
                    </div>
                </div>
                
                <p class="text-center mt-12 text-zinc-400 text-sm font-bold uppercase tracking-widest">
                    Pagamento seguro via <span class="text-black">ASAAS</span>. Liberação imediata após o PIX.
                </p>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-32 px-6 bg-[#070708]">
            <div class="max-w-4xl mx-auto space-y-16">
                <div class="text-center">
                    <h3 class="text-4xl lg:text-5xl font-display font-medium">Dúvidas Comuns</h3>
                </div>

                <div class="space-y-4">
                    <div v-for="(item, i) in faqs" :key="i" class="bg-white/5 border border-white/10 rounded-3xl overflow-hidden active:scale-[0.98] transition-all">
                        <button @click="activeFaq = activeFaq === i ? null : i" class="w-full text-left p-8 flex justify-between items-center group">
                            <span class="text-xl font-bold group-hover:text-amber-500 transition-colors">{{ item.q }}</span>
                            <div class="w-8 h-8 rounded-full border border-white/20 flex items-center justify-center transition-transform duration-300" :class="activeFaq === i ? 'rotate-45 bg-amber-500 border-transparent text-black' : ''">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                        </button>
                        <div v-show="activeFaq === i" class="px-8 pb-8 text-white/50 leading-relaxed text-lg font-light border-t border-white/5 pt-6 animate-fade-in-down">
                            {{ item.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-32 px-6">
            <div class="max-w-7xl mx-auto rounded-[4rem] bg-gradient-to-br from-amber-500 to-orange-700 p-16 lg:p-32 text-center text-black overflow-hidden relative group">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                
                <h3 class="text-5xl lg:text-7xl font-display font-black leading-none mb-10 relative z-10">
                    Sua próxima obra <br/> começa aqui.
                </h3>
                <Link :href="route('register')" class="inline-flex bg-black text-white px-12 py-6 rounded-3xl font-black text-xl hover:scale-105 active:scale-95 transition-all relative z-10 uppercase tracking-widest">
                    Iniciar Renderização Grátis
                </Link>

                <p class="mt-10 text-black/60 font-bold uppercase tracking-widest text-[10px] relative z-10">
                    Junte-se a centenas de arquitetos inovadores.
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-20 px-6 border-t border-white/5 opacity-50">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-white rounded flex items-center justify-center">
                        <span class="text-black font-bold text-xs">A</span>
                    </div>
                    <span class="text-lg font-bold">ArqAI Studio</span>
                </div>
                
                <div class="text-white/30 text-[10px] font-medium uppercase tracking-widest">
                    &copy; 2026 ArqAI Studio. Para quem constrói o futuro.
                </div>

                <div class="flex gap-10 text-white/50 text-[10px] font-black uppercase tracking-[0.2em]">
                    <a href="#" class="hover:text-amber-500 transition-colors">Termos</a>
                    <a href="#" class="hover:text-amber-500 transition-colors">Privacidade</a>
                    <a href="#" class="hover:text-amber-500 transition-colors">Instagram</a>
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

.font-display {
    font-family: 'Outfit', sans-serif;
    letter-spacing: -0.06em;
}

.perspective-1000 {
    perspective: 1000px;
}

.rotate-y-3 {
    transform: rotateY(3deg);
}

.clip-half {
    clip-path: inset(0 0 0 50%);
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: .7;
    }
}

.animate-fade-in-down {
    animation: fadeInDown 0.4s ease-out;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

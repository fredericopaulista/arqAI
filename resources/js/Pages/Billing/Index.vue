<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import CheckoutModal from '@/Components/CheckoutModal.vue';

const props = defineProps({
    plans: Array,
    user_credits: Number
});

const selectedPlan = ref(null);
const paymentMethod = ref('PIX');
const processing = ref(false);
const pixData = ref(null);
const error = ref(null);
const showCheckoutModal = ref(false);
const pendingPlan = ref(null);

const startCheckout = async (plan, extraData = null) => {
    // If it's credit card and no data provided yet, show modal first
    if (paymentMethod.value === 'CREDIT_CARD' && !extraData) {
        pendingPlan.value = plan;
        showCheckoutModal.value = true;
        return;
    }

    selectedPlan.value = plan;
    processing.value = true;
    error.value = null;
    pixData.value = null;

    try {
        const response = await axios.post(route('billing.checkout'), {
            plan_id: plan.id,
            method: paymentMethod.value,
            ...extraData
        });

        if (paymentMethod.value === 'PIX') {
            pixData.value = response.data.pix;
        } else {
            // If credit card was successful, it might return a success message or redirect
            if (response.data.success) {
                alert('Pagamento processado com sucesso! Seus créditos serão liberados em instantes.');
                window.location.reload();
            } else if (response.data.checkout_url) {
                window.location.href = response.data.checkout_url;
            }
        }
        showCheckoutModal.value = false;
    } catch (e) {
        if (e.response?.data?.needs_data) {
            pendingPlan.value = plan;
            showCheckoutModal.value = true;
        } else {
            error.value = e.response?.data?.error || "Erro ao processar pagamento. Verifique os dados e tente novamente.";
        }
        console.error(e);
    } finally {
        processing.value = false;
    }
};

const handleCheckoutSubmit = (extraData) => {
    startCheckout(pendingPlan.value, extraData);
};
</script>

<template>
    <Head title="Meus Créditos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Créditos & Cobrança</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- Credit Balance Header -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <p class="text-white/70 text-sm font-bold uppercase tracking-widest">Saldo Atual</p>
                        <h3 class="text-5xl font-black">{{ user_credits }} <span class="text-2xl font-normal opacity-80">créditos</span></h3>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 max-w-xs">
                        <p class="text-xs font-medium">Cada crédito permite uma geração completa de proposta arquitetônica com IA.</p>
                    </div>
                </div>

                <!-- Plans Grid -->
                <div>
                    <h4 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-amber-600">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.121-.659-1.171-.879-1.171-2.303 0-3.182 1.172-.879 3.07-.879 4.242 0 .417.313.762.709.935 1.151V8" />
                        </svg>
                        Adquirir mais Créditos
                    </h4>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-lg transition-shadow">
                            <div>
                                <h5 class="text-lg font-bold text-gray-500">{{ plan.name }}</h5>
                                <div class="mt-2 flex items-baseline gap-1">
                                    <span class="text-3xl font-black text-gray-900">R${{ plan.monthly_price }}</span>
                                </div>
                                <div class="mt-6 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                                    <p class="text-amber-900 font-bold text-lg text-center">{{ plan.image_limit }} Créditos</p>
                                </div>
                            </div>
                            
                            <div class="mt-8 space-y-4">
                                <div class="flex gap-2">
                                    <button 
                                        @click="paymentMethod = 'PIX'"
                                        :class="['flex-1 py-2 text-xs font-bold rounded-xl border transition-all', paymentMethod === 'PIX' ? 'bg-amber-600 text-white border-amber-600' : 'bg-gray-50 text-gray-500 border-gray-200']"
                                    >PIX (Imediato)</button>
                                    <button 
                                        @click="paymentMethod = 'CREDIT_CARD'"
                                        :class="['flex-1 py-2 text-xs font-bold rounded-xl border transition-all', paymentMethod === 'CREDIT_CARD' ? 'bg-amber-600 text-white border-amber-600' : 'bg-gray-50 text-gray-500 border-gray-200']"
                                    >Cartão</button>
                                </div>

                                <button 
                                    @click="startCheckout(plan)"
                                    :disabled="processing"
                                    class="w-full bg-black text-white py-4 rounded-2xl font-bold hover:bg-gray-800 disabled:opacity-50 transition-all active:scale-95"
                                >
                                    {{ processing && selectedPlan?.id === plan.id ? 'Processando...' : 'Comprar Agora' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PIX Modal (Simplified overlay) -->
                <div v-if="pixData" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div class="bg-white rounded-[2.5rem] p-8 max-w-md w-full text-center space-y-6 shadow-2xl">
                        <h4 class="text-2xl font-black">Pague com PIX</h4>
                        <p class="text-gray-500 text-sm">Escaneie o código abaixo para liberar seus créditos instantaneamente.</p>
                        
                        <div class="p-4 bg-gray-50 rounded-3xl inline-block border-4 border-amber-100">
                            <img :src="'data:image/png;base64,' + pixData.encodedImage" class="w-64 h-64 mx-auto" alt="QR Code PIX">
                        </div>

                        <div class="space-y-4">
                            <p class="text-xs font-mono bg-gray-100 p-3 rounded-xl break-all line-clamp-2 text-gray-400">
                                {{ pixData.payload }}
                            </p>
                            <button @click="pixData = null" class="w-full bg-amber-500 text-black py-4 rounded-2xl font-bold hover:bg-amber-400">
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="error" class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-2xl text-center font-medium">
                    {{ error }}
                </div>

                <!-- Mandatory Data Modal -->
                <CheckoutModal 
                    :show="showCheckoutModal" 
                    :method="paymentMethod"
                    @close="showCheckoutModal = false" 
                    @submit="handleCheckoutSubmit"
                />

            </div>
        </div>
    </AuthenticatedLayout>
</template>

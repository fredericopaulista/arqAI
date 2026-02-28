<script setup>
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    method: {
        type: String,
        default: 'PIX'
    }
});

const emit = defineEmits(['close', 'submit']);

const step = ref(1);

const form = ref({
    // Billing Info
    cpf_cnpj: '',
    phone: '',
    // Address Info (Mandatory for Credit Card)
    postal_code: '',
    address_number: '',
    address_complement: '',
    // Card Details
    holder_name: '',
    card_number: '',
    expiry: '',
    cvv: '',
});

const errors = ref({});

const validateStep1 = () => {
    errors.value = {};
    let valid = true;

    if (!form.value.cpf_cnpj || form.value.cpf_cnpj.length < 11) {
        errors.value.cpf_cnpj = 'CPF ou CNPJ inválido.';
        valid = false;
    }

    if (!form.value.phone || form.value.phone.length < 10) {
        errors.value.phone = 'Telefone inválido.';
        valid = false;
    }

    if (props.method === 'CREDIT_CARD') {
        if (!form.value.postal_code || form.value.postal_code.length < 8) {
            errors.value.postal_code = 'CEP inválido.';
            valid = false;
        }
        if (!form.value.address_number) {
            errors.value.address_number = 'Número é obrigatório.';
            valid = false;
        }
    }

    return valid;
};

const validateStep2 = () => {
    errors.value = {};
    let valid = true;

    if (!form.value.holder_name) {
        errors.value.holder_name = 'Nome no cartão é obrigatório.';
        valid = false;
    }
    if (!form.value.card_number || form.value.card_number.replace(/\s/g, '').length < 13) {
        errors.value.card_number = 'Número do cartão inválido.';
        valid = false;
    }
    if (!form.value.expiry || !form.value.expiry.includes('/')) {
        errors.value.expiry = 'Validade inválida (MM/AA).';
        valid = false;
    }
    if (!form.value.cvv || form.value.cvv.length < 3) {
        errors.value.cvv = 'CVV inválido.';
        valid = false;
    }

    return valid;
};

const next = () => {
    if (validateStep1()) {
        if (props.method === 'CREDIT_CARD') {
            step.value = 2;
        } else {
            submit();
        }
    }
};

const submit = () => {
    if (props.method === 'PIX') {
        emit('submit', { 
            cpf_cnpj: form.value.cpf_cnpj, 
            phone: form.value.phone 
        });
    } else {
        if (validateStep2()) {
            emit('submit', { ...form.value });
        }
    }
};

const close = () => {
    step.value = 1;
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close" max-width="md">
        <div class="p-8 bg-zinc-900 border border-white/10 rounded-[2.5rem] overflow-hidden relative transition-all duration-500">
            <!-- Decorative background -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-500/10 blur-3xl rounded-full"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-orange-500/5 blur-3xl rounded-full"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-black text-white tracking-tighter uppercase">
                        Checkout <span class="text-amber-500">{{ props.method === 'PIX' ? 'PIX' : 'Cartão' }}</span>
                    </h2>
                    <div v-if="props.method === 'CREDIT_CARD'" class="flex gap-1">
                        <div :class="['w-2 h-2 rounded-full transition-colors', step === 1 ? 'bg-amber-500' : 'bg-white/20']"></div>
                        <div :class="['w-2 h-2 rounded-full transition-colors', step === 2 ? 'bg-amber-500' : 'bg-white/20']"></div>
                    </div>
                </div>

                <!-- Step 1: Billing & Address -->
                <div v-if="step === 1" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
                    <p class="text-white/50 text-sm leading-relaxed mb-4">
                        {{ props.method === 'PIX' ? 'Informe seus dados básicos para gerar o QR Code.' : 'Precisamos dos seus dados de faturamento para processar o cartão.' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <InputLabel for="cpf_cnpj" value="CPF ou CNPJ" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                            <TextInput
                                id="cpf_cnpj"
                                type="text"
                                class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                v-model="form.cpf_cnpj"
                                placeholder="000.000.000-00"
                                required
                            />
                            <InputError class="mt-2" :message="errors.cpf_cnpj" />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="phone" value="Celular / WhatsApp" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                            <TextInput
                                id="phone"
                                type="text"
                                class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                v-model="form.phone"
                                placeholder="(00) 00000-0000"
                                required
                            />
                            <InputError class="mt-2" :message="errors.phone" />
                        </div>

                        <template v-if="props.method === 'CREDIT_CARD'">
                            <div>
                                <InputLabel for="postal_code" value="CEP" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                                <TextInput
                                    id="postal_code"
                                    type="text"
                                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                    v-model="form.postal_code"
                                    placeholder="00000-000"
                                />
                                <InputError class="mt-2" :message="errors.postal_code" />
                            </div>
                            <div>
                                <InputLabel for="address_number" value="Número" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                                <TextInput
                                    id="address_number"
                                    type="text"
                                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                    v-model="form.address_number"
                                    placeholder="123"
                                />
                                <InputError class="mt-2" :message="errors.address_number" />
                            </div>
                        </template>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <PrimaryButton 
                            @click="next"
                            class="w-full justify-center py-4 bg-amber-500 text-black hover:bg-amber-400 rounded-2xl font-black uppercase text-xs tracking-widest transition-all active:scale-95"
                        >
                            {{ props.method === 'PIX' ? 'Gerar PIX' : 'Próximo Passo' }}
                        </PrimaryButton>
                        <SecondaryButton 
                            @click="close"
                            class="w-full justify-center py-4 bg-transparent border-white/10 text-white/50 hover:bg-white/5 rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all"
                        >
                            Cancelar
                        </SecondaryButton>
                    </div>
                </div>

                <!-- Step 2: Card Details -->
                <div v-if="step === 2" class="space-y-6 animate-in fade-in slide-in-from-right-4 duration-500">
                    <p class="text-white/50 text-sm leading-relaxed mb-4">
                        Insira os dados do seu cartão de crédito com segurança.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <InputLabel for="holder_name" value="Nome no Cartão" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                            <TextInput
                                id="holder_name"
                                type="text"
                                class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                v-model="form.holder_name"
                                placeholder="NOME COMO NO CARTÃO"
                            />
                            <InputError class="mt-2" :message="errors.holder_name" />
                        </div>

                        <div>
                            <InputLabel for="card_number" value="Número do Cartão" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                            <TextInput
                                id="card_number"
                                type="text"
                                class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                v-model="form.card_number"
                                placeholder="0000 0000 0000 0000"
                            />
                            <InputError class="mt-2" :message="errors.card_number" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="expiry" value="Validade" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                                <TextInput
                                    id="expiry"
                                    type="text"
                                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                    v-model="form.expiry"
                                    placeholder="MM/AA"
                                />
                                <InputError class="mt-2" :message="errors.expiry" />
                            </div>
                            <div>
                                <InputLabel for="cvv" value="CVV" class="text-white/40 uppercase tracking-widest text-[10px] font-black" />
                                <TextInput
                                    id="cvv"
                                    type="password"
                                    maxlength="4"
                                    class="mt-1 block w-full bg-white/5 border-white/10 text-white rounded-2xl focus:ring-amber-500 focus:border-amber-500"
                                    v-model="form.cvv"
                                    placeholder="***"
                                />
                                <InputError class="mt-2" :message="errors.cvv" />
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <PrimaryButton 
                            @click="submit"
                            class="w-full justify-center py-4 bg-amber-500 text-black hover:bg-amber-400 rounded-2xl font-black uppercase text-xs tracking-widest transition-all active:scale-95"
                        >
                            Finalizar Pagamento
                        </PrimaryButton>
                        <SecondaryButton 
                            @click="step = 1"
                            class="w-full justify-center py-4 bg-transparent border-white/10 text-white/50 hover:bg-white/5 rounded-2xl font-bold uppercase text-[10px] tracking-widest transition-all"
                        >
                            Voltar
                        </SecondaryButton>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

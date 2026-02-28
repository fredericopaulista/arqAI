<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    image: null,
});

const imageUrl = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    form.image = file;
    imageUrl.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('projects.store'));
};
</script>

<template>
    <Head title="Novo Projeto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Novo Projeto</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto do Ambiente</label>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-gray-700 px-6 py-10" :class="imageUrl ? 'bg-gray-50 dark:bg-gray-900' : ''">
                                <div class="text-center">
                                    <template v-if="!imageUrl">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 18V6.672c.178.03.35.082.503.15L10.25 10.5l4.747-2.373a.75.75 0 01.674 1.346l-5.082 2.541a.75.75 0 01-.674 0L4.542 9.401 3 10.171V18h18V6.672l-5.082 2.541a.75.75 0 01-.674 0L10.25 7.171 3.503 10.543A2.246 2.246 0 003 10.171V18z" clip-rule="evenodd" />
                                        </svg>
                                        <div class="mt-4 flex text-sm leading-6 text-gray-600 dark:text-gray-400">
                                            <label for="file-upload" class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                                <span>Upload de imagem</span>
                                                <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="onFileChange" accept="image/*">
                                            </label>
                                            <p class="pl-1">ou arraste e solte</p>
                                        </div>
                                        <p class="text-xs leading-5 text-gray-600 dark:text-gray-400">PNG, JPG, GIF até 10MB</p>
                                    </template>
                                    <template v-else>
                                        <img :src="imageUrl" class="mx-auto max-h-64 rounded-lg shadow-md mb-4" />
                                        <button type="button" @click="imageUrl = null; form.image = null" class="text-sm text-red-600 hover:text-red-500">Remover e trocar foto</button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing || !form.image"
                                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-all duration-200"
                            >
                                <span v-if="form.processing">Processando...</span>
                                <span v-else>Iniciar Consultoria IA</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

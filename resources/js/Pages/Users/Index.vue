<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
});

const isCreating = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            isCreating.value = false;
            form.reset();
        },
    });
};

const deleteUser = (id) => {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        useForm({}).delete(route('users.destroy', id));
    }
};

const toggleStatus = (user) => {
    const newStatus = user.status === 'active' ? 'suspended' : 'active';
    useForm({
        name: user.name,
        status: newStatus,
    }).put(route('users.update', user.id));
};
</script>

<template>
    <Head title="Equipe" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gerenciar Equipe</h2>
                <button 
                    @click="isCreating = !isCreating"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm transition"
                >
                    {{ isCreating ? 'Cancelar' : 'Convidar Membro' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Create Form -->
                <div v-if="isCreating" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                            <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Senha</label>
                            <input v-model="form.password" type="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg" :disabled="form.processing">Gravar</button>
                        </div>
                    </form>
                </div>

                <!-- Users List -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 font-bold">
                                        <th class="py-2 px-4">Nome</th>
                                        <th class="py-2 px-4">Email</th>
                                        <th class="py-2 px-4">Status</th>
                                        <th class="py-2 px-4 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users" :key="user.id" class="border-b dark:border-gray-700">
                                        <td class="py-4 px-4">{{ user.name }}</td>
                                        <td class="py-4 px-4">{{ user.email }}</td>
                                        <td class="py-4 px-4">
                                            <span 
                                                class="px-2 py-1 rounded text-xs uppercase font-bold"
                                                :class="user.status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                                            >
                                                {{ user.status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right space-x-2">
                                            <button @click="toggleStatus(user)" class="text-indigo-600 hover:text-indigo-400 text-sm font-medium">
                                                {{ user.status === 'active' ? 'Suspender' : 'Ativar' }}
                                            </button>
                                            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-400 text-sm font-medium" v-if="user.id !== $page.props.auth.user.id">
                                                Excluir
                                            </button>
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

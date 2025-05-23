<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'All Users',
        href: '/users',
    },
];

const users = ref([]);
const newUser = ref({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const editUserId = ref<number | null>(null);
const isLoading = ref(false);
const showModal = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const formErrors = ref<{ [key: string]: string }>({});
const successMessage = ref<string | null>(null);
const showDeleteConfirm = ref(false);
const userIdToDelete = ref<number | null>(null);

onMounted(async () => {
    await fetchUsers();
});

async function fetchUsers(page: number = 1) {
    isLoading.value = true;
    try {
        const response = await axios.get('/users', {
            params: { page },
            headers: { 'Cache-Control': 'no-cache' },
        });
        users.value = response.data.users.data;
        currentPage.value = response.data.users.current_page;
        lastPage.value = response.data.users.last_page;
        total.value = response.data.users.total;
        console.log('Users updated:', users.value, response.data);
    } catch (error: any) {
        console.error('Error fetching users:', error.response?.data || error.message);
    } finally {
        isLoading.value = false;
    }
}

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const openModal = (user: any = null) => {
    if (user) {
        editUserId.value = user.id;
        newUser.value = { ...user, password: '', password_confirmation: '' };
    } else {
        resetForm();
    }
    formErrors.value = {};
    successMessage.value = null;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
    formErrors.value = {};
};

const submitUser = async () => {
    formErrors.value = {};

    if (!editUserId.value && newUser.value.password !== newUser.value.password_confirmation) {
        formErrors.value.password_confirmation = 'Passwords do not match.';
        return;
    }

    try {
        if (editUserId.value) {
            const updateData = { ...newUser.value };
            if (!updateData.password) {
                delete updateData.password;
                delete updateData.password_confirmation;
            }
            await axios.put(`/users/${editUserId.value}`, updateData);
            successMessage.value = 'User updated successfully!';
            console.log('User updated:', newUser.value);
        } else {
            await axios.post('/register', newUser.value);
            successMessage.value = 'User created successfully!';
            console.log('User created:', newUser.value);
        }
        closeModal();
        await fetchUsers(currentPage.value);
    } catch (error: any) {
        if (error.response?.data?.errors) {
            Object.keys(error.response.data.errors).forEach((key) => {
                const messages = error.response.data.errors[key];
                formErrors.value[key] = Array.isArray(messages) ? messages[0] : messages;
            });
        } else {
            formErrors.value.general = error.response?.data?.message || 'Error saving user.';
        }
        console.error('Error saving user:', error.response?.data || error.message);
    }
};

const startEdit = (user: any) => {
    openModal(user);
};

const openDeleteConfirm = (id: number) => {
    userIdToDelete.value = id;
    showDeleteConfirm.value = true;
};

const closeDeleteConfirm = () => {
    showDeleteConfirm.value = false;
    userIdToDelete.value = null;
};

const confirmDelete = async () => {
    if (userIdToDelete.value !== null) {
        try {
            await axios.delete(`/users/${userIdToDelete.value}`);
            successMessage.value = 'User deleted successfully!';
            console.log('User deleted:', userIdToDelete.value);
            await fetchUsers(currentPage.value);
        } catch (error: any) {
            console.error('Error deleting user:', error.response?.data || error.message);
        }
    }
    closeDeleteConfirm();
};

const resetForm = () => {
    editUserId.value = null;
    newUser.value = { first_name: '', last_name: '', email: '', password: '', password_confirmation: '' };
};

const changePage = async (page: number) => {
    if (page >= 1 && page <= lastPage.value) {
        await fetchUsers(page);
    }
};

const clearSuccessMessage = () => {
    setTimeout(() => {
        successMessage.value = null;
    }, 3000);
};

watch(successMessage, (newValue) => {
    if (newValue) {
        clearSuccessMessage();
    }
});
</script>

<template>

    <Head title="All Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Success Message -->
            <div v-if="successMessage"
                class="bg-green-100 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
                <span>{{ successMessage }}</span>
                <button @click="successMessage = null" class="text-green-700 hover:text-green-900 focus:outline-none">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Create Button -->
            <div class="flex justify-end mb-4">
                <button @click="openModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Create User
                </button>
            </div>

            <!-- Users Table -->
            <div class="relative rounded-xl border border-gray-300 dark:border-gray-600 overflow-x-auto">
                <PlaceholderPattern v-if="isLoading || !users.length" />
                <div v-else>
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead>
                            <tr
                                class="bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID</th>
                                <th class="py-3 px-6 text-left">First Name</th>
                                <th class="py-3 px-6 text-left">Last Name</th>
                                <th class="py-3 px-6 text-left">Email</th>
                                <th class="py-3 px-6 text-left">Created At</th>
                                <th class="py-3 px-6 text-center">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 dark:text-gray-300 text-sm font-light">
                            <tr v-for="user in users" :key="user.id"
                                class="border-b border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="py-3 px-6 text-left">{{ user.id }}</td>
                                <td class="py-3 px-6 text-left">{{ user.first_name }}</td>
                                <td class="py-3 px-6 text-left">{{ user.last_name }}</td>
                                <td class="py-3 px-6 text-left">{{ user.email }}</td>
                                <td class="py-3 px-6 text-left">{{ formatDate(user.created_at) }}</td>
                                <td class="py-3 px-6 text-center whitespace-nowrap">
                                    <button @click="startEdit(user)"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded mr-2 hover:bg-yellow-600 transition-colors"
                                        :disabled="isLoading">
                                        Edit
                                    </button>
                                    <button @click="openDeleteConfirm(user.id)"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition-colors"
                                        :disabled="isLoading">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div v-if="users.length" class="flex items-center justify-between mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Showing {{ users.length }} of {{ total }} users
                </p>
                <div class="flex space-x-2">
                    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1 || isLoading"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50">
                        Previous
                    </button>
                    <button @click="changePage(currentPage + 1)" :disabled="currentPage === lastPage || isLoading"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50">
                        Next
                    </button>
                </div>
            </div>

            <!-- Modal for Create/Update User -->
            <div v-if="showModal"
                class="fixed inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-500"
                :class="{ 'opacity-100': showModal, 'opacity-0 pointer-events-none': !showModal }" role="dialog"
                aria-labelledby="modal-title">
                <div class="sm:max-w-lg sm:w-full m-3 sm:mx-auto">
                    <div
                        class="flex flex-col bg-white dark:bg-gray-800 border border-gray-200 shadow-2xl rounded-xl pointer-events-auto">
                        <!-- Modal Header -->
                        <div
                            class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-gray-600">
                            <h3 id="modal-title" class="font-bold text-gray-800 dark:text-gray-200">
                                {{ editUserId ? 'Update User' : 'Create User' }}
                            </h3>
                            <button type="button"
                                class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none"
                                aria-label="Close" @click="closeModal">
                                <span class="sr-only">Close</span>
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"></path>
                                    <path d="m6 6 12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-4 overflow-y-auto">
                            <form @submit.prevent="submitUser" class="space-y-4">
                                <div v-if="formErrors.general" class="bg-red-100 text-red-700 p-2 rounded text-sm">
                                    {{ formErrors.general }}
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First
                                        Name</label>
                                    <input v-model="newUser.first_name" type="text" placeholder="First Name"
                                        class="border p-2 rounded w-full border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.first_name }" required />
                                    <p v-if="formErrors.first_name" class="text-red-500 text-sm mt-1">{{
                                        formErrors.first_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last
                                        Name</label>
                                    <input v-model="newUser.last_name" type="text" placeholder="Last Name"
                                        class="border p-2 rounded w-full border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.last_name }" required />
                                    <p v-if="formErrors.last_name" class="text-red-500 text-sm mt-1">{{
                                        formErrors.last_name }}</p>
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                    <input v-model="newUser.email" type="email" placeholder="Email"
                                        class="border p-2 rounded w-full border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.email }" required />
                                    <p v-if="formErrors.email" class="text-red-500 text-sm mt-1">{{ formErrors.email }}
                                    </p>
                                </div>
                                <div v-if="!editUserId">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                                    <input v-model="newUser.password" type="password" placeholder="Password"
                                        class="border p-2 rounded w-full border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.password }" required />
                                    <p v-if="formErrors.password" class="text-red-500 text-sm mt-1">{{
                                        formErrors.password }}</p>
                                </div>
                                <div v-if="!editUserId">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm
                                        Password</label>
                                    <input v-model="newUser.password_confirmation" type="password"
                                        placeholder="Confirm Password"
                                        class="border p-2 rounded w-full border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :class="{ 'border-red-500': formErrors.password_confirmation }" required />
                                    <p v-if="formErrors.password_confirmation" class="text-red-500 text-sm mt-1">{{
                                        formErrors.password_confirmation }}</p>
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-gray-600">
                            <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none"
                                @click="closeModal">
                                Close
                            </button>
                            <button type="button"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
                                :disabled="isLoading" @click="submitUser">
                                {{ editUserId ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Popup -->
            <div v-if="showDeleteConfirm"
                class="fixed inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-500"
                :class="{ 'opacity-100': showDeleteConfirm, 'opacity-0 pointer-events-none': !showDeleteConfirm }"
                role="dialog" aria-labelledby="delete-confirm-title">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl max-w-sm w-full mx-4 shadow-lg">
                    <h3 id="delete-confirm-title" class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">
                        Confirm Deletion
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">
                        Are you sure you want to delete this user? This action cannot be undone.
                    </p>
                    <div class="flex justify-end space-x-2">
                        <button @click="closeDeleteConfirm"
                            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button @click="confirmDelete"
                            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
table {
    width: 100%;
    table-layout: fixed;
}

th,
td {
    overflow: visible;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 640px) {
    table {
        font-size: 0.875rem;
    }

    th,
    td {
        padding: 0.5rem;
    }
}
</style>

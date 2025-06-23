<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { router } from "@inertiajs/vue3";

const masterData = ref([]);
const isLoading = ref(true);
const showModal = ref(false);
const formMode = ref("create");
const form = ref({
    id: null,
    name: "",
});

const resetForm = () => {
    form.value = {
        id: null,
        name: "",
    };
    formMode.value = "create";
};

const openCreateModal = () => {
    resetForm();
    showModal.value = true;
};

const openEditModal = (master) => {
    form.value = { ...master };
    formMode.value = "edit";
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const fetchMasterData = async () => {
    try {
        const response = await axios.get(
            "http://localhost:8000/api/master-table"
        );
        if (response.data.is_success) {
            masterData.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to fetch master data:", error);
    } finally {
        isLoading.value = false;
    }
};

const handleSubmit = async () => {
    try {
        let response;
        if (formMode.value === "create") {
            response = await axios.post(
                "http://localhost:8000/api/master-table",
                form.value
            );
        } else {
            response = await axios.put(
                `http://localhost:8000/api/master-table/${form.value.id}`,
                form.value
            );
        }

        if (response.data.is_success) {
            await fetchMasterData();
            closeModal();
        }
    } catch (error) {
        console.error("Failed to submit form:", error);
    }
};

const handleDelete = async (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        try {
            const response = await axios.delete(
                `http://localhost:8000/api/master-table/${id}`
            );
            if (response.data.is_success) {
                await fetchMasterData();
            }
        } catch (error) {
            console.error("Failed to delete data:", error);
        }
    }
};

const navigateToColumns = (masterId) => {
    router.visit(`/master-table-column/${masterId}`);
};

const navigateToData = (masterId) => {
    router.visit(`/master-table-data/${masterId}`);
};

onMounted(fetchMasterData);
</script>

<template>
    <Head title="Master Data" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Master Data
            </h2>
        </template>

        <div class="container py-12 mx-auto dark:text-white">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">Daftar Master</h1>
                <PrimaryButton @click="openCreateModal"
                    >Tambah Master</PrimaryButton
                >
            </div>

            <div v-if="isLoading">Loading...</div>

            <div v-else>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full bg-white rounded-lg shadow-sm dark:bg-gray-800"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    ID
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Tanggal Dibuat
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-600"
                        >
                            <tr
                                v-for="master in masterData"
                                :key="master.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ master.id }}
                                </td>
                                <td
                                    class="px-6 py-4 capitalize whitespace-nowrap"
                                >
                                    {{ master.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{
                                        new Date(
                                            master.created_at
                                        ).toLocaleDateString()
                                    }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        @click="openEditModal(master)"
                                        class="mr-2 text-blue-600 hover:text-blue-900"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="handleDelete(master.id)"
                                        class="mr-2 text-red-600 hover:text-red-900"
                                    >
                                        Hapus
                                    </button>
                                    <button
                                        @click="navigateToColumns(master.id)"
                                        class="mr-2 text-green-600 hover:text-green-900"
                                    >
                                        Kolom
                                    </button>
                                    <button
                                        @click="navigateToData(master.id)"
                                        class="text-purple-600 hover:text-purple-900"
                                    >
                                        Data
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{
                        formMode === "create"
                            ? "Tambah Master Baru"
                            : "Edit Master"
                    }}
                </h2>

                <form @submit.prevent="handleSubmit" class="mt-6">
                    <div>
                        <InputLabel for="name" value="Nama" />
                        <TextInput
                            id="name"
                            type="text"
                            class="block w-full mt-1"
                            v-model="form.name"
                            required
                        />
                    </div>

                    <div class="flex justify-end mt-6">
                        <PrimaryButton
                            type="button"
                            class="mr-3"
                            @click="closeModal"
                        >
                            Batal
                        </PrimaryButton>
                        <PrimaryButton type="submit">
                            {{ formMode === "create" ? "Simpan" : "Update" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

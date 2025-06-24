<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, defineProps, watch } from "vue";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import FieldTypeSelect from "@/Components/FieldTypeSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableBody from "@/Components/TableBody.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    master_table_id: {
        type: Number,
        required: true,
    },
    master_table_name: {
        type: String,
        required: true,
    },
});

const masterData = ref([]);
const showModal = ref(false);
const formMode = ref("create");
const form = ref({
    id: null,
    master_table_id: props.master_table_id,
    data: {
        type: "",
        label: "",
        order: 0,
        field_name: "",
        placeholder: "",
        is_visible: true,
        is_editable: true,
        default_value: null,
        component_props: [],
    },
});

// Fungsi untuk konversi ke snake_case
function toSnakeCase(text) {
    return text
        .normalize("NFD") // untuk menghapus aksen karakter
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/\W+/g, " ") // ganti non-word jadi spasi
        .trim()
        .toLowerCase()
        .replace(/\s+/g, "_");
}

// Auto-generate field_name dari label
watch(
    () => form.value.data.label,
    (newLabel) => {
        form.value.data.field_name = toSnakeCase(newLabel);
    }
);

watch(
    () => props.master_table_id,
    (newValue) => {
        if (!newValue) {
            router.visit("/master");
        }
    },
    { immediate: true }
);

const resetForm = () => {
    form.value = {
        id: null,
        master_table_id: props.master_table_id,
        data: {
            type: "",
            label: "",
            order: "0",
            field_name: "",
            placeholder: "",
            is_visible: true,
            is_editable: true,
            default_value: null,
            component_props: [],
        },
    };
    formMode.value = "create";
};

const openCreateModal = () => {
    resetForm();
    showModal.value = true;
};

const openEditModal = (master) => {
    form.value = {
        id: master.id,
        master_table_id: props.master_table_id,
        data: master.data ?? {
            type: "",
            label: "",
            order: 0,
            field_name: "",
            placeholder: "",
            is_visible: true,
            is_editable: true,
            default_value: null,
            component_props: [],
        },
        _method: "put",
    };
    formMode.value = "edit";
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    resetForm();
};

const handleSubmit = async () => {
    try {
        let response;
        if (formMode.value === "create") {
            response = await axios.post(
                `/api/master-table-data/${props.master_table_id}/records`,
                form.value
            );
        } else {
            response = await axios.post(
                `/api/master-table-data/${props.master_table_id}/records/${form.value.id}`,
                form.value
            );
        }

        if (response.data.is_success) {
            // await fetchMasterData();
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
                `/api/master-table-data/${props.master_table_id}/records/${id}`
            );
            if (response.data.is_success) {
                // await fetchMasterData();
            }
        } catch (error) {
            console.error("Failed to delete data:", error);
        }
    }
};

// onMounted(fetchMasterData);

const newProp = ref({
    key: "",
    value: "",
    validation_message: "",
});

const editingPropIndex = ref(null);

const addComponentProp = () => {
    if (!newProp.value.key) return;

    if (editingPropIndex.value !== null) {
        // update prop
        form.value.data.component_props[editingPropIndex.value] = {
            ...newProp.value,
        };
        editingPropIndex.value = null;
    } else {
        // tambah baru
        form.value.data.component_props.push({ ...newProp.value });
    }

    newProp.value = {
        key: "",
        value: "",
        validation_message: "",
    };
};

const editProp = (index) => {
    const prop = form.value.data.component_props[index];
    newProp.value = { ...prop };
    // Simpan index yang sedang diedit
    editingPropIndex.value = index;
};

const deleteProp = (index) => {
    form.value.data.component_props.splice(index, 1);
};

import ValidationSelect from "@/Components/ValidationSelect.vue";

const baseUrl = "http://127.0.0.1:8000/api"; // Ganti dengan URL API Anda
</script>

<template>
    <Head :title="`Master Data Column - ${props.master_table_id}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                {{ props.master_table_name }} Data
            </h2>
        </template>

        <div class="container py-12 mx-auto dark:text-white">
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">
                    Daftar Data {{ props.master_table_name }}
                </h1>
                <PrimaryButton @click="openCreateModal"
                    >Tambah Data {{ props.master_table_name }}</PrimaryButton
                >
            </div>

            <div class="overflow-x-auto">
                <table
                    class="min-w-full bg-white rounded-lg shadow-sm dark:bg-gray-800"
                >
                    <TableHeader :tableId="props.master_table_id" />
                    <TableBody
                        :master_table_id="props.master_table_id"
                        @edit="openEditModal"
                        @delete="handleDelete"
                    />
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showModal" @close="closeModal">
            <div
                class="p-6 bg-white rounded shadow dark:bg-gray-900"
                @click.stop
            >
                <h2
                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{
                        formMode === "create"
                            ? "Tambah Column Baru"
                            : "Edit Column"
                    }}
                </h2>

                <form @submit.prevent="handleSubmit" class="mt-6 space-y-4">
                    <!-- Tambahan untuk mengisi field data -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="label" value="Label" />
                            <TextInput
                                id="label"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.data.label"
                            />
                        </div>
                        <div>
                            <InputLabel for="placeholder" value="placeholder" />
                            <TextInput
                                id="placeholder"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.data.placeholder"
                            />
                        </div>
                        <div>
                            <FieldTypeSelect v-model="form.data.type" />
                        </div>
                        <div>
                            <InputLabel for="order" value="Urutan" />
                            <TextInput
                                id="order"
                                type="number"
                                class="block w-full mt-1"
                                v-model.number="form.data.order"
                            />
                        </div>
                    </div>

                    <!-- Form Tambah component_props -->
                    <ValidationSelect
                        :base-url="baseUrl"
                        :component-props="form.data.component_props"
                        @update:component-props="
                            form.data.component_props = $event
                        "
                    />

                    <!-- Tombol Aksi -->
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

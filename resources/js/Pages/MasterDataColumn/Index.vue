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
const isLoading = ref(true);
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

const fetchMasterData = async () => {
    if (!props.master_table_id) {
        console.error("Master table ID tidak valid");
        return;
    }

    try {
        const response = await axios.get(
            `/api/master-table-column/${props.master_table_id}/records`
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
                `/api/master-table-column/${props.master_table_id}/records`,
                form.value
            );
        } else {
            response = await axios.post(
                `/api/master-table-column/${props.master_table_id}/records/${form.value.id}`,
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
                `/api/master-table-column/${props.master_table_id}/records/${id}`
            );
            if (response.data.is_success) {
                await fetchMasterData();
            }
        } catch (error) {
            console.error("Failed to delete data:", error);
        }
    }
};

onMounted(fetchMasterData);

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
                    Daftar Column {{ props.master_table_name }}
                </h1>
                <PrimaryButton @click="openCreateModal"
                    >Tambah Column {{ props.master_table_name }}</PrimaryButton
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
                                    Label
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Order
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
                                >
                                    Properties
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
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ master.data.label }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded-full"
                                    >
                                        {{ master.data.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ master.data.order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full"
                                            :class="
                                                master.data.is_visible
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'
                                            "
                                        >
                                            {{
                                                master.data.is_visible
                                                    ? "Visible"
                                                    : "Hidden"
                                            }}
                                        </span>

                                        <span
                                            class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium rounded-full"
                                            :class="
                                                master.data.is_editable
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800'
                                            "
                                        >
                                            {{
                                                master.data.is_editable
                                                    ? "Editable"
                                                    : "Read Only"
                                            }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <template
                                            v-for="prop in master.data
                                                .component_props"
                                            :key="prop.key"
                                        >
                                            <div
                                                v-if="prop.validation_message"
                                                class="text-xs"
                                            >
                                                <span class="font-semibold"
                                                    >{{ prop.key }}:</span
                                                >
                                                <span class="ml-1">{{
                                                    prop.value
                                                }}</span>
                                            </div>
                                        </template>
                                    </div>
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
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                            <InputLabel for="field_name" value="Field Name" />
                            <TextInput
                                :readonly="true"
                                id="field_name"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.data.field_name"
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
                    <div class="pt-4 mt-4 border-t">
                        <h3 class="mb-2 font-semibold dark:text-gray-300">
                            Component Props
                        </h3>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <InputLabel value="Key" />
                                <TextInput
                                    v-model="newProp.key"
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <InputLabel value="Value" />
                                <TextInput
                                    v-model="newProp.value"
                                    class="w-full"
                                />
                            </div>
                            <div>
                                <InputLabel value="Pesan Validasi" />
                                <TextInput
                                    v-model="newProp.validation_message"
                                    class="w-full"
                                />
                            </div>
                        </div>
                        <PrimaryButton
                            class="mt-2"
                            type="button"
                            @click="addComponentProp"
                        >
                            + Tambah
                        </PrimaryButton>
                    </div>

                    <!-- List component_props -->
                    <div
                        v-if="form.data.component_props.length"
                        class="p-4 mt-4 bg-gray-100 rounded dark:bg-gray-500"
                    >
                        <ul class="pl-5 space-y-2 text-sm list-disc">
                            <li
                                v-for="(prop, index) in form.data
                                    .component_props"
                                :key="index"
                                class="flex items-start justify-between gap-4 mb-1"
                            >
                                <div>
                                    <strong>{{ prop.key }}:</strong>
                                    {{ prop.value }}
                                    <span v-if="prop.validation_message">
                                        ({{ prop.validation_message }})
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <a
                                        class="text-sm text-blue-600 cursor-pointer hover:underline"
                                        @click.stop="editProp(index)"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        class="text-sm text-red-600 cursor-pointer hover:underline"
                                        @click.stop="deleteProp(index)"
                                    >
                                        Hapus
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>

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

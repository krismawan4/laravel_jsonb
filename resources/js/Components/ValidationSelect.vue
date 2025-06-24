<template>
    <div class="space-y-4">
        <!-- Validation Selection -->
        <div>
            <InputLabel value="Pilih Validasi" />
            <select
                v-model="selectedValidationId"
                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                @change="selectValidation"
            >
                <option value="" disabled>Pilih validasi</option>
                <option
                    v-for="validation in validations"
                    :key="validation.id"
                    :value="validation.id"
                >
                    {{ validation.key }} -
                    {{ validation.message || "No message" }}
                </option>
            </select>
        </div>

        <!-- Selected Validation Details -->
        <div
            v-if="selectedValidation"
            class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
        >
            <h4 class="mb-3 font-medium text-gray-900 dark:text-gray-100">
                Detail Validasi
            </h4>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <InputLabel value="Key" />
                    <TextInput
                        v-model="newProp.key"
                        class="w-full"
                        :placeholder="selectedValidation.key"
                    />
                </div>
                <div>
                    <InputLabel value="Value" />
                    <TextInput
                        v-model="newProp.value"
                        class="w-full"
                        :placeholder="selectedValidation.value"
                    />
                </div>
                <div>
                    <InputLabel value="Pesan Validasi" />
                    <TextInput
                        v-model="newProp.validation_message"
                        class="w-full"
                        :placeholder="selectedValidation.message"
                    />
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end mt-4 space-x-2">
                <button
                    @click="addComponentProp"
                    type="button"
                    class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    {{
                        editingPropIndex !== null
                            ? "Update Validasi"
                            : "Tambah Validasi"
                    }}
                </button>
                <button
                    @click="clearSelection"
                    type="button"
                    class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    Clear
                </button>
            </div>
        </div>

        <!-- List of Added Component Props -->
        <div v-if="props.componentProps.length > 0" class="mt-6">
            <h4 class="mb-3 font-medium text-gray-900 dark:text-gray-100">
                Validasi yang Ditambahkan
            </h4>
            <div class="space-y-2">
                <div
                    v-for="(prop, index) in props.componentProps"
                    :key="index"
                    class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg dark:bg-gray-700 dark:border-gray-600"
                >
                    <div class="flex-1">
                        <div
                            class="text-sm font-medium text-gray-900 dark:text-gray-100"
                        >
                            {{ prop.key }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Value: {{ prop.value }}
                        </div>
                        <div
                            v-if="prop.validation_message"
                            class="text-sm text-gray-500 dark:text-gray-400"
                        >
                            Message: {{ prop.validation_message }}
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="editProp(index)"
                            class="text-sm text-blue-600 hover:text-blue-800"
                        >
                            Edit
                        </button>
                        <button
                            @click="deleteProp(index)"
                            class="text-sm text-red-600 hover:text-red-800"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="py-4 text-center">
            <div
                class="inline-block w-6 h-6 border-b-2 border-indigo-600 rounded-full animate-spin"
            ></div>
            <span class="ml-2 text-gray-600 dark:text-gray-400"
                >Loading validations...</span
            >
        </div>

        <!-- Error State -->
        <div
            v-if="error"
            class="p-4 border border-red-200 rounded-md bg-red-50 dark:bg-red-900/20 dark:border-red-800"
        >
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg
                        class="w-5 h-5 text-red-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3
                        class="text-sm font-medium text-red-800 dark:text-red-200"
                    >
                        Error
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        {{ error }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

// Props
const props = defineProps({
    baseUrl: {
        type: String,
        default: "",
    },
    componentProps: {
        type: Array,
        default: () => [],
    },
});

// Emits
const emit = defineEmits(["update:componentProps"]);

// Reactive data
const validations = ref([]);
const selectedValidationId = ref("");
const loading = ref(false);
const error = ref("");

const newProp = ref({
    key: "",
    value: "",
    validation_message: "",
});

const editingPropIndex = ref(null);

// Computed
const selectedValidation = computed(() => {
    if (!selectedValidationId.value) return null;
    return validations.value.find((v) => v.id === selectedValidationId.value);
});

// Methods
const fetchValidations = async () => {
    loading.value = true;
    error.value = "";

    try {
        const response = await axios.get(
            `${props.baseUrl}/master-table-validation`
        );

        if (response.data.is_success) {
            validations.value = response.data.data;
        } else {
            throw new Error(
                response.data.message || "Failed to fetch validations"
            );
        }
    } catch (err) {
        error.value =
            err.response?.data?.message ||
            err.message ||
            "Gagal mengambil data validasi";
        console.error("Error fetching validations:", err);
    } finally {
        loading.value = false;
    }
};

const selectValidation = () => {
    if (selectedValidation.value) {
        // Auto-fill form with selected validation data
        newProp.value.key = selectedValidation.value.key;
        newProp.value.value = selectedValidation.value.value;
        newProp.value.validation_message = selectedValidation.value.message;
    }
};

const addComponentProp = () => {
    if (!newProp.value.key) return;

    const updatedProps = [...props.componentProps];

    if (editingPropIndex.value !== null) {
        // update prop
        updatedProps[editingPropIndex.value] = {
            ...newProp.value,
        };
        editingPropIndex.value = null;
    } else {
        // tambah baru
        updatedProps.push({ ...newProp.value });
    }

    emit("update:componentProps", updatedProps);
    clearSelection();
};

const editProp = (index) => {
    const prop = props.componentProps[index];
    newProp.value = { ...prop };
    // Simpan index yang sedang diedit
    editingPropIndex.value = index;
};

const deleteProp = (index) => {
    const updatedProps = [...props.componentProps];
    updatedProps.splice(index, 1);
    emit("update:componentProps", updatedProps);
};

const clearSelection = () => {
    selectedValidationId.value = "";
    newProp.value = {
        key: "",
        value: "",
        validation_message: "",
    };
    editingPropIndex.value = null;
};

// Lifecycle
onMounted(() => {
    fetchValidations();
});

// Expose methods for parent component
defineExpose({
    fetchValidations,
    clearSelection,
});
</script>

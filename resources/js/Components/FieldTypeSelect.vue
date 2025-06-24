<!-- FieldTypeSelect.vue (Using defineModel - Vue 3.3+) -->
<template>
    <div>
        <InputLabel for="type" value="Tipe Field" />
        <select
            id="type"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            v-model="model"
        >
            <option value="" disabled>Pilih tipe field</option>
            <option
                v-for="item in validationOptions"
                :key="item.name"
                :value="item.name"
            >
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

// Much simpler with defineModel
const model = defineModel();

const validationOptions = ref([]);

const fetchValidationOptions = async () => {
    try {
        const response = await axios.get("/api/master-table-tipe");
        if (response.data.is_success) {
            validationOptions.value = response.data.data;
        }
    } catch (error) {
        console.error("Gagal mengambil data validasi:", error);
    }
};

onMounted(fetchValidationOptions);
</script>

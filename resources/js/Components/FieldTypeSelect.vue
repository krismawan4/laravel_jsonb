<!-- FieldTypeSelect.vue -->
<template>
    <div>
        <InputLabel for="type" value="Tipe Field" />
        <select
            id="type"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
            v-model="model"
        >
            <option value="" disabled>Pilih tipe field</option>
            <option
                v-for="item in fieldTypeStore.validationOptions"
                :key="item.name"
                :value="item.name"
            >
                {{ item.name }}
            </option>
        </select>
    </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useFieldTypeStore } from "@/stores/fieldTypeStore";
import InputLabel from "@/Components/InputLabel.vue";

// defineModel (Vue 3.3+)
const model = defineModel();

// Pinia store
const fieldTypeStore = useFieldTypeStore();

onMounted(() => {
    if (fieldTypeStore.validationOptions.length === 0) {
        fieldTypeStore.fetchValidationOptions();
    }
});
</script>

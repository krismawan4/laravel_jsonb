<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

const masterData = ref([]);
const isLoading = ref(true);

onMounted(async () => {
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
});
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

        <div class="container mx-auto py-12 dark:text-white">
            <h1 class="text-2xl font-bold mb-4">Daftar Master</h1>

            <div v-if="isLoading">Loading...</div>

            <div v-else>
                <div
                    v-for="master in masterData"
                    :key="master.id"
                    class="mb-8 border-b pb-4"
                >
                    <h2 class="text-lg font-semibold mb-2 capitalize">
                        {{ master.name }}
                    </h2>
                    <table
                        class="table-auto w-full border border-gray-300 dark:border-gray-600"
                    >
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="px-4 py-2 text-left">Field Name</th>
                                <th class="px-4 py-2 text-left">Label</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Required</th>
                                <th class="px-4 py-2 text-left">Visible</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="field in master.data"
                                :key="field.field_name"
                            >
                                <td class="px-4 py-2">
                                    {{ field.field_name }}
                                </td>
                                <td class="px-4 py-2">{{ field.label }}</td>
                                <td class="px-4 py-2">{{ field.type }}</td>
                                <td class="px-4 py-2">
                                    {{ field.is_required ? "Yes" : "No" }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ field.is_visible ? "Yes" : "No" }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

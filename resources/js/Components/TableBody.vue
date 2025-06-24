<template>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        <!-- Loading state -->
        <tr v-if="isLoading">
            <td colspan="99" class="px-6 py-4 text-center text-gray-500">
                <svg
                    class="w-5 h-5 mx-auto text-gray-500 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    />
                </svg>
                <div class="mt-2">Memuat data...</div>
            </td>
        </tr>

        <!-- Data rows dan empty state -->
        <template v-if="!isLoading">
            <tr
                v-for="(row, index) in masterData"
                :key="row.id ?? index"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                <!-- Kolom ID -->
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ row.id }}
                </td>

                <!-- Kolom Dinamis -->
                <td
                    v-for="[key, value] in filteredEntries(row)"
                    :key="key"
                    class="px-6 py-4 whitespace-nowrap"
                >
                    {{ value }}
                </td>

                <!-- Kolom Aksi -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <button
                        @click="$emit('edit', row)"
                        class="mr-2 text-blue-600 hover:text-blue-900"
                    >
                        Edit
                    </button>
                    <button
                        @click="$emit('delete', row.id)"
                        class="mr-2 text-red-600 hover:text-red-900"
                    >
                        Hapus
                    </button>
                </td>
            </tr>

            <!-- Jika tidak ada data -->
            <tr v-if="masterData.length === 0">
                <td colspan="99" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data.
                </td>
            </tr>
        </template>
    </tbody>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
    master_table_id: {
        type: [Number, String],
        required: true,
    },
});

const masterData = ref([]);
const isLoading = ref(true);

const filteredEntries = (row) =>
    Object.entries(row).filter(([key]) => key !== "id");

const fetchMasterData = async () => {
    isLoading.value = true;

    if (!props.master_table_id) {
        console.error("Master table ID tidak valid");
        isLoading.value = false;
        return;
    }

    try {
        const response = await axios.get(
            `/api/master-table-data/${props.master_table_id}/records`
        );
        if (response.data.is_success && Array.isArray(response.data.data)) {
            masterData.value = response.data.data;
        } else {
            masterData.value = [];
        }
    } catch (error) {
        console.error("Failed to fetch master data:", error);
        masterData.value = [];
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchMasterData);
watch(() => props.master_table_id, fetchMasterData);
</script>

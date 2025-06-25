<template>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
        <!-- Loading state -->
        <tr v-if="store.loading">
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
        <template v-if="!store.loading">
            <tr
                v-for="(row, index) in store.data"
                :key="row.id ?? index"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ row.id }}
                </td>

                <td
                    v-for="[key, value] in filteredEntries(row)"
                    :key="key"
                    class="px-6 py-4 whitespace-nowrap"
                >
                    {{ value }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <button
                        @click="$emit('edit', row)"
                        class="mr-2 text-blue-600 hover:text-blue-900"
                    >
                        Edit
                    </button>
                    <button
                        @click="handleDelete(row.id)"
                        class="mr-2 text-red-600 hover:text-red-900"
                    >
                        Hapus
                    </button>
                </td>
            </tr>

            <tr v-if="store.data.length === 0">
                <td colspan="99" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data.
                </td>
            </tr>
        </template>
    </tbody>
</template>

<script setup>
import { onMounted, watch } from "vue";
import { useMasterDataStore } from "@/stores/masterDataStore";

const props = defineProps({
    master_table_id: {
        type: [Number, String],
        required: true,
    },
});

const store = useMasterDataStore();

// Helper untuk menyaring kolom
const filteredEntries = (row) =>
    Object.entries(row).filter(([key]) => key !== "id");

// Fetch saat mount dan saat ID berubah
onMounted(() => store.fetch(props.master_table_id));
watch(
    () => props.master_table_id,
    (newId) => store.fetch(newId)
);

// Hapus data lewat store
const handleDelete = async (id) => {
    if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        try {
            await store.deleteRecord(props.master_table_id, id);
            await store.fetch(props.master_table_id);
        } catch (error) {
            alert("Gagal menghapus data.");
        }
    }
};
</script>

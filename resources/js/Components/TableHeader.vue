<template>
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <!-- Kolom tetap -->
            <th
                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
            >
                ID
            </th>

            <!-- Kolom dari API -->
            <th
                v-for="(label, index) in dynamicHeaders"
                :key="index"
                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
            >
                {{ label }}
            </th>

            <th
                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300"
            >
                Aksi
            </th>
        </tr>
    </thead>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";

// Props
const props = defineProps({
    tableId: {
        type: [String, Number],
        required: true,
    },
});

const dynamicHeaders = ref([]);

// Fungsi untuk ambil data header dari API
const fetchHeaders = async (id) => {
    try {
        const res = await axios.get(
            `/api/master-table-column/${id}/records-header`
        );
        if (res.data?.is_success && Array.isArray(res.data.data)) {
            dynamicHeaders.value = res.data.data;
        }
    } catch (error) {
        console.error("Gagal memuat header:", error);
    }
};

// Ambil data saat komponen dimount dan saat prop berubah
onMounted(() => fetchHeaders(props.tableId));
watch(
    () => props.tableId,
    (newId) => {
        if (newId) fetchHeaders(newId);
    }
);
</script>

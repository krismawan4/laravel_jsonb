<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, onMounted, defineProps, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import FieldTypeSelect from "@/Components/FieldTypeSelect.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TableHeader from "@/Components/TableHeader.vue";
import TableBody from "@/Components/TableBody.vue";
import FormGenerator from "@/Components/FormGenerator.vue";
import { useFormSchemaStore } from "@/stores/formSchemaStore";

const formStore = useFormSchemaStore();
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
                <PrimaryButton @click="formStore.openCreateModal"
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
                        @edit="formStore.openEditModal"
                    />
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="formStore.showModal" @close="formStore.closeModal">
            <div
                class="p-6 bg-white rounded shadow dark:bg-gray-900"
                @click.stop
            >
                <h2
                    class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100"
                >
                    {{
                        formStore.formMode === "create"
                            ? "Tambah Data Baru"
                            : "Edit Data"
                    }}
                </h2>

                <FormGenerator :master_table_id="props.master_table_id" />
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

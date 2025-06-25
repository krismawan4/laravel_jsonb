<script setup>
import { ref, watch, onMounted } from "vue";
import { useFormSchemaStore } from "@/stores/formSchemaStore";

const props = defineProps({
    master_table_id: {
        type: [String, Number],
        required: true,
    },
});

const store = useFormSchemaStore();
const formValues = ref({});

watch(
    () => props.master_table_id,
    (newId) => {
        store.fetchSchema(newId);
        formValues.value = {}; // reset form
    }
);

onMounted(() => {
    store.fetchSchema(props.master_table_id);
});

function getProp(componentProps, key) {
    const found = componentProps.find((p) => p.key === key);
    return found ? found.value : null;
}

function getValidationMessage(componentProps, key) {
    const found = componentProps.find((p) => p.key === key);
    return found ? found.validation_message : "";
}

async function submitForm() {
    try {
        await store.submitData(props.master_table_id, formValues.value);
        alert("Data berhasil dikirim!");
    } catch (err) {
        alert(`Gagal submit: ${store.error}`);
    }
}
</script>

<template>
    <div v-if="store.loading">Loading...</div>
    <div v-else>
        <form @submit.prevent="submitForm" class="space-y-4">
            <div v-for="field in store.schema" :key="field.field_name">
                <label class="block text-sm font-medium text-gray-700">{{
                    field.label
                }}</label>

                <input
                    v-if="field.type === 'text'"
                    type="text"
                    v-model="formValues[field.field_name]"
                    :placeholder="field.placeholder"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    :maxlength="getProp(field.component_props, 'max_length')"
                    :minlength="getProp(field.component_props, 'min_length')"
                    :required="
                        getProp(field.component_props, 'is_required') === 'true'
                    "
                />

                <input
                    v-else-if="field.type === 'number'"
                    type="number"
                    v-model="formValues[field.field_name]"
                    :placeholder="field.placeholder"
                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm"
                    :maxlength="getProp(field.component_props, 'max_length')"
                    :minlength="getProp(field.component_props, 'min_length')"
                    :required="
                        getProp(field.component_props, 'is_required') === 'true'
                    "
                />

                <!-- Tambahkan komponen lain sesuai type jika perlu -->
                <p class="mt-1 text-xs italic text-red-500">
                    {{
                        getValidationMessage(
                            field.component_props,
                            "is_required"
                        )
                    }}
                </p>
            </div>

            <button
                type="submit"
                class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-600"
            >
                Submit
            </button>
        </form>
    </div>
</template>

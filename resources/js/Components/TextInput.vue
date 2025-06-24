<script setup>
import { onMounted, ref } from "vue";

// Bind model
const model = defineModel({
    type: String,
    required: true,
});

// Terima prop readonly
const props = defineProps({
    readonly: Boolean,
});

const input = ref(null);

onMounted(() => {
    if (input.value?.hasAttribute("autofocus")) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        v-model="model"
        :readonly="props.readonly"
        ref="input"
    />
</template>

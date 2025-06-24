// stores/fieldTypeStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useFieldTypeStore = defineStore("fieldType", {
    state: () => ({
        validationOptions: [],
    }),
    actions: {
        async fetchValidationOptions() {
            try {
                const response = await axios.get("/api/master-table-tipe");
                if (response.data.is_success) {
                    this.validationOptions = response.data.data;
                }
            } catch (error) {
                console.error("Gagal mengambil data validasi:", error);
            }
        },
    },
});

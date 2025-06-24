// stores/validationStore.js
import { defineStore } from "pinia";
import axios from "axios";

export const useValidationStore = defineStore("validationStore", {
    state: () => ({
        validations: [],
        loading: false,
        error: "",
    }),
    actions: {
        async fetchValidations(baseUrl) {
            this.loading = true;
            this.error = "";

            try {
                const response = await axios.get(
                    `${baseUrl}/master-table-validation`
                );
                if (response.data.is_success) {
                    this.validations = response.data.data;
                } else {
                    throw new Error(
                        response.data.message || "Failed to fetch validations"
                    );
                }
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    err.message ||
                    "Gagal mengambil data validasi";
                console.error("Error fetching validations:", err);
            } finally {
                this.loading = false;
            }
        },
    },
});

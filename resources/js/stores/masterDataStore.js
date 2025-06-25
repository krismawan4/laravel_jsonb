import { defineStore } from "pinia";
import axios from "axios";

export const useMasterDataStore = defineStore("masterData", {
    state: () => ({
        data: [],
        loading: false,
        error: null,
    }),
    actions: {
        async fetch(masterTableId) {
            this.loading = true;
            this.error = null;

            try {
                const res = await axios.get(
                    `/api/master-table-data/${masterTableId}/records`
                );
                if (res.data.is_success && Array.isArray(res.data.data)) {
                    this.data = res.data.data;
                } else {
                    this.data = [];
                }
            } catch (err) {
                console.error("Failed to fetch:", err);
                this.error = err.message;
                this.data = [];
            } finally {
                this.loading = false;
            }
        },

        async deleteRecord(masterTableId, id) {
            try {
                const res = await axios.delete(
                    `/api/master-table-data/${masterTableId}/records/${id}`
                );
                if (res.data.is_success) {
                    // Hapus dari state lokal
                    this.data = this.data.filter((item) => item.id !== id);
                }
                return res.data;
            } catch (err) {
                console.error("Gagal menghapus:", err);
                throw err;
            }
        },
    },
});

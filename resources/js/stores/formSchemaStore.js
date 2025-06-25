import { defineStore } from "pinia";
import axios from "axios";
import { useMasterDataStore } from "@/stores/masterDataStore";

export const useFormSchemaStore = defineStore("formSchema", {
    state: () => ({
        // Schema-related state
        schema: [],
        loading: false,
        error: null,
        postSuccess: false,

        // Modal-related state
        showModal: false,
        formMode: "create", // atau "edit"
        selectedData: null, // digunakan saat edit
    }),

    actions: {
        // Ambil struktur form
        async fetchSchema(id) {
            this.loading = true;
            this.error = null;
            try {
                const res = await axios.get(
                    `/api/master-table-column/${id}/records-form`
                );
                this.schema = res.data.data || [];
            } catch (err) {
                this.error = err.message;
            } finally {
                this.loading = false;
            }
        },

        // Submit form data
        async submitData(masterTableId, data) {
            this.postSuccess = false;
            this.error = null;
            try {
                const res = await axios.post(
                    `/api/master-table-data/${masterTableId}/records`,
                    {
                        master_table_id: masterTableId,
                        data,
                    }
                );
                this.postSuccess = true;

                const masterStore = useMasterDataStore();
                await masterStore.fetch(masterTableId);

                this.closeModal();

                return res.data;
            } catch (err) {
                this.error = err.response?.data?.message || err.message;
                throw err;
            }
        },

        // Modal controls
        openCreateModal() {
            this.formMode = "create";
            this.selectedData = null;
            this.showModal = true;
        },

        openEditModal(data) {
            this.formMode = "edit";
            this.selectedData = data;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
            this.formMode = "create";
            this.selectedData = null;
        },
    },
});

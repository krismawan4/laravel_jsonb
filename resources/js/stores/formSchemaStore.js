import { ref, watch, onMounted } from "vue";
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
        formValues: {}, // digunakan saat edit
        primaryKey: null,
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
                console.log("Primary Key:", this.primaryKey);

                let res;

                if (this.primaryKey != null) {
                    // Mode Edit
                    res = await axios.put(
                        `/api/master-table-data/${masterTableId}/records/${this.primaryKey}`,
                        {
                            master_table_id: masterTableId,
                            data,
                        }
                    );
                } else {
                    // Mode Create
                    res = await axios.post(
                        `/api/master-table-data/${masterTableId}/records`,
                        {
                            master_table_id: masterTableId,
                            data,
                        }
                    );
                }

                this.postSuccess = true;

                // Refresh data utama
                const masterStore = useMasterDataStore();
                await masterStore.fetch(masterTableId);

                // Reset modal dan primary key
                this.closeModal();
                this.primaryKey = null;

                return res.data;
            } catch (err) {
                this.error = err.response?.data?.message || err.message;
                throw err;
            }
        },
        // Ambil data yang akan diedit (binding data dinamis)
        async fetchEditData(masterTableId, id) {
            this.error = null;
            try {
                const res = await axios.get(
                    `/api/master-table-data/${masterTableId}/records/${id}`
                );
                if (res.data.is_success) {
                    this.formValues = res.data.data;
                } else {
                    this.formValues = {};
                    this.error = "Data tidak ditemukan";
                }
            } catch (err) {
                this.formValues = {};
                this.error = err.response?.data?.message || err.message;
                throw err;
            }
        },

        async generateFormValuesFromSchema() {
            this.schema.forEach((field) => {
                this.formValues[field.field_name] =
                    field.default_value !== undefined
                        ? field.default_value
                        : "";
            });
        },

        async bindEditDataToSchema() {
            if (!this.formValues || this.schema.length === 0) return;

            this.schema = this.schema.map((field) => {
                const boundValue = this.formValues[field.field_name];
                return {
                    ...field,
                    default_value: boundValue !== undefined ? boundValue : null,
                };
            });
            // console.log(
            //     `bindEditDataToSchema: ${JSON.stringify(this.schema, null, 2)}`
            // );
            // console.log(
            //     `formValues: ${JSON.stringify(this.formValues, null, 2)}`
            // );
        },

        // Modal controls
        async openCreateModal(masterTableId) {
            await this.fetchSchema(masterTableId);
            await this.generateFormValuesFromSchema();
            this.formMode = "create";
            this.showModal = true;
            this.loading = false;
        },

        async openEditModal(masterTableId, id) {
            this.formMode = "edit";
            await this.fetchSchema(masterTableId);
            await this.fetchEditData(masterTableId, id);
            await this.bindEditDataToSchema();
            this.showModal = true;
            this.primaryKey = id;
        },

        closeModal() {
            this.showModal = false;
            this.formMode = "create";
        },
    },
});

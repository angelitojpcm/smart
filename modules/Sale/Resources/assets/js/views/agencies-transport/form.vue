<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.description }"
                        >
                            <label class="control-label">Descripción</label>
                            <el-input v-model="form.description"></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.description"
                                v-text="errors.description[0]"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.ubigeo }"
                        >
                            <label class="control-label">Ubigeo</label>
                            <el-cascader
                                :options="locations"
                                v-model="form.ubigeo"
                                filterable
                            ></el-cascader>
                            <small
                                class="text-danger"
                                v-if="errors.ubigeo"
                                v-text="errors.ubigeo[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-12 col-12">
                        <div
                            class="form-group"
                            :class="{ 'has-danger': errors.address }"
                        >
                            <label class="control-label">Dirección</label>
                            <el-input v-model="form.address"></el-input>
                            <small
                                class="text-danger"
                                v-if="errors.address"
                                v-text="errors.address[0]"
                            ></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button
                    type="primary"
                    native-type="submit"
                    :loading="loading_submit"
                    >Guardar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "recordId"],
    data() {
        return {
            loading_submit: false,
            titleDialog: null,
            resource: "agencies-transport",
            errors: {},
            form: {},
            locations: [],
        };
    },
    created() {
        this.initForm();
        this.getLocations();
    },
    methods: {
        getLocations() {
            this.$http.get(`/${this.resource}/locations`).then((response) => {
                this.locations = response.data.locations;
            });
        },
        initForm() {
            this.errors = {};
            this.form = {
                id: null,
                bank_id: null,
                description: null,
                number: null,
                currency_type_id: null,
                cci: null,
                initial_balance: 0,
                show_in_documents: false,
            };
        },
        create() {
            this.titleDialog = this.recordId
                ? "Editar Agencia de transporte"
                : "Nueva Agencia de transporte";
            if (this.recordId) {
                this.$http
                    .get(`/${this.resource}/record/${this.recordId}`)
                    .then((response) => {
                        console.log("🚀 ~ .then ~ response:", response);
                        this.form = response.data;
                    });
            }
        },
        submit() {
            this.loading_submit = true;
            this.$http
                .post(`/${this.resource}`, this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                        this.close();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        close() {
            this.$emit("update:showDialog", false);
            this.initForm();
        },
    },
};
</script>

<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @close="close"
        @open="create"
        class="dialog-import"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="/formats/item_size_lists.xlsx" target="_new"
                            >Descargar formato</a
                        >
                    </div>
                    <div
                        class="col-12 form-group"
                        :class="{ 'has-danger': errors.warehouse_id }"
                    >
                        <label for="warehouse">Almacén</label>
                        <el-select v-model="form.warehouse_id">
                            <el-option
                                v-for="w in warehouses"
                                :key="w.id"
                                :label="w.description"
                                :value="w.id"
                            ></el-option>
                        </el-select>
                        <small
                            class="text-danger"
                            v-if="errors.warehouse_id"
                            v-text="errors.warehouse_id[0]"
                        ></small>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div
                            class="form-group text-center"
                            :class="{ 'has-danger': errors.file }"
                        >
                            <el-upload
                                ref="upload"
                                :headers="headers"
                                action="/items/import/item-size-lists"
                                :show-file-list="true"
                                :auto-upload="false"
                                :multiple="false"
                                :on-error="errorUpload"
                                :limit="1"
                                :data="form"
                                :on-success="successUpload"
                            >
                                <el-button slot="trigger" type="primary"
                                    >Seleccione un archivo (xlsx)</el-button
                                >
                            </el-upload>
                            <small
                                class="text-danger"
                                v-if="errors.file"
                                v-text="errors.file[0]"
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
                    >Procesar</el-button
                >
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog"],
    data() {
        return {
            loading_submit: false,
            headers: headers_token,
            titleDialog: null,
            resource: "items",
            errors: {},
            form: {},
            warehouses: [],
        };
    },
    async created() {
        this.initForm();
        await this.onFetchTables();
    },
    methods: {
        async onFetchTables() {
            this.loading_submit = true;
            await this.$http
                .get("/items/import/tables")
                .then((response) => {
                    this.warehouses = response.data.warehouses;
                })
                .finally(() => (this.loading_submit = false));
        },
        initForm() {
            this.errors = {};
            this.form = {
                file: null,
                warehouse_id: null,
            };
        },
        create() {
            this.titleDialog = "Importar listado de tallas";
        },
        async submit() {
            if (!this.form.warehouse_id) {
                this.$message.warning(
                    "Seleccione un almacén para poder continuar"
                );
                return;
            }
            this.loading_submit = true;
            await this.$refs.upload.submit();
            this.loading_submit = false;
        },
        close() {
            this.$emit("update:showDialog", false);
            this.initForm();
        },
        successUpload(response, file, fileList) {
            if (response.success) {
                this.$message.success(response.message);
                this.$eventHub.$emit("reloadData");
                this.$eventHub.$emit("reloadTables");
                this.$refs.upload.clearFiles();
                this.close();
            } else {
                this.$message({ message: response.message, type: "error" });
            }
        },
        errorUpload(response) {
            console.log(response);
        },
    },
};
</script>

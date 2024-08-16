<template>
    <el-dialog
        :visible="showDialog"
        @close="close"
        @open="open"
        width="40%"
        append-to-body
        :title="titleDialog"
    >
        <div class="row">
            <div class="col">
                <label for="license">Placa</label>
                <el-input v-model="form.license" placeholder="Placa" />
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="submit">Guardar</el-button>
        </span>
    </el-dialog>
</template>
<script>
export default {
    props: ["showDialog"],
    data() {
        return {
            titleDialog: "Agregar Placa",
            form: {},
            resource: "purchases-license",
            loading: false,
        };
    },
    methods: {
        async submit() {
            try {
                this.loading = true;
                const response = await this.$http.post(`/${this.resource}`, this.form);
                if (response.status == 200) {
                    this.$message({
                        type: "success",
                        message: "Datos guardados correctamente",
                    });
                    let id = response.data.id;
                    this.form.id = id;
                    this.$emit("reloadDataLicense", this.form);
                    this.close();
                } else {
                    this.$message({
                        type: "error",
                        message: "Ocurrio un error al guardar los datos",
                    });
                }
            } catch (e) {
                console.log("🚀 ~ file: license_modal.vue:51 ~ submit ~ e:", e)
                this.$message({
                    type: "error",
                    message: "Ocurrio un error al guardar los datos",
                });
            } finally {
                this.loading = false;
            }
        },
        open() {},
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

<template>
    <el-dialog :visible="showDialog" @open="open" @close="close" :title="title">
        <div class="row mt-2">
            <div class="col-md-12">
                <label for="comment">Se envia cuando</label>
                <el-input
                    type="textarea"
                    v-model="form.comment"
                    placeholder="Se envia cuando"
                    rows="2"
                ></el-input>
            </div>
            <div class="col-md-12">
                <label for="message">Mensaje</label>
                <el-input
                    type="textarea"
                    v-model="form.message"
                    placeholder="Mensaje"
                    rows="2"
                ></el-input>
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
    props: ["showDialog", "recordId"],
    data() {
        return {
            showAgencyModal: false,
            fileList2: [],
            defaultFileList2: [],
            form: {},
            title: "EDITAR MENSAJE",
            resource: "message-integrate-system",
        };
    },
    methods: {
        async open() {
            this.form = {
                id: null,
                trigger_event: null,
                comment: null,
                message: null,
            };
            this.getRecord();
        },
        async getRecord() {
            const response = await this.$http.get(
                `/${this.resource}/record/${this.recordId}`
            );
            if (response.status == 200) {
                this.form = response.data;
            }
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        async submit() {
            this.loading = true;
            const response = await this.$http.post(
                `/${this.resource}`,
                this.form
            );
            if (response.status == 200) {
                this.$message({
                    type: "success",
                    message: "Se actualizó correctamente",
                });
                this.$emit("getRecords");
                this.$emit("update:showDialog", false);
            }
            this.loading = false;
        },
    },
};
</script>

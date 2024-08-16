<template>
    <el-dialog
        :visible="showDialog"
        @open="open"
        @close="close"
        append-to-body
        :title="title"
        v-loading="loading"
    >
        <div class="row m-2">
            <div class="col-12">
                <label for="observation">Observación</label>
                <el-input
                    type="textarea"
                    :rows="3"
                    placeholder="Ingrese una observación"
                    v-model="quotation.description"
                ></el-input>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="changeDescription">
                Guardar
            </el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "quotationId"],
    data() {
        return {
            quotation: {},
            title: "",
            loading: false,
        };
    },
    methods: {
        async changeDescription() {
            try {
                this.loading = true;
                const response = await this.$http.post(
                    `/quotations/change-description/${this.quotationId}`,
                    {
                        description: this.quotation.description,
                    }
                );
                if (response.status == 200) {
                    let { data } = response;
                    if (data) {
                        this.$message({
                            type: "success",
                            message: "Observación actualizada correctamente",
                        });
                        this.close();
                    }
                }
            } catch (error) {
                console.log(
                    "🚀 ~ file: quotation_observation.vue:33 ~ getQuotation ~ response:",
                    error
                );
            } finally {
                this.loading = false;
            }
        },
        async getQuotation() {
            const response = await this.$http(
                `/quotations/record2/${this.quotationId}`
            );
            if (response.status == 200) {
                let { data } = response.data;
                if (data) {
                    let { quotation, identifier } = data;
                    this.title = "Observación de la cotización " + identifier;
                    this.quotation = quotation;
                }
            }
            console.log(
                "🚀 ~ file: quotation_observation.vue:33 ~ getQuotation ~ response:",
                response
            );
        },
        open() {
            this.getQuotation();
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

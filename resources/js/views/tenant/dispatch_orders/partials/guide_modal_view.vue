<template>
    <el-dialog :visible="showDialog" @open="open" @close="close" :title="title">
        <div class="row mt-2">
            <div class="col-md-3">
                <label for="date">Fecha</label>
                <div class="col-12">
                    {{ agency.date_of_issue }}
                </div>
            </div>
            <div class="col-md-3">
                <label for="agency">Agencia</label>
                <div class="col-12">
                    {{ agency.agency.description }}
                </div>
            </div>
            <div class="col-md-3">
                <label for="destination">Destino</label>
                <div class="col-12">
                    {{ agency.destination }}
                </div>
            </div>
            <div class="col-md-3">
                <label for="reference">Referencia</label>
                <div class="col-12">
                    {{ agency.reference }}
                </div>
            </div>
        </div>
        <div class="row mt-2" v-if="agency.dispatch_file">
            <div class="col-md-12">
                <label for="image">Imagen</label>
                <div class="col-12">
                    <img :src="agency.dispatch_file" alt="" width="100%" />
                </div>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cerrar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "currentRecord"],
    data() {
        return {
            showAgencyModal: false,
            fileList2: [],
            defaultFileList2: [],
            form: {},
            fullNumber: "",
            title: "GUIA DE TRASLADO",
            agencies: [],
            agency: {
                agency:{}
            },
        };
    },
    methods: {
        async getAngency() {
            const response = await this.$http(
                `/agency/agency-dispatch/${this.currentRecord.id}`
            );
            this.agency = response.data;
        },
        open() {
            this.getAngency();
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

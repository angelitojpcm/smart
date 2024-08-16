<template>
    <el-dialog
        title="Resultados de la importación"
        :visible="showDialog"
        @open="open"
        @close="close"
        width="350px"
        append-to-body
    >
        <div class="row mt-2">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td>Registrados</td>
                        <td>
                            {{ registered }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-danger">No encontrados</td>
                        <td class="text-danger">
                            {{ errors.length }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="row" v-if="errors.length > 0">
                <div class="col-md-12 text-center">
                    <a href="#" @click.prevent="downloadExcelErrors">
                        Descargar formato para subir lo nuevos
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <el-button type="primary" @click="close">Cerrar</el-button>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "registered", "errors","hash"],
    data() {
        return {
            showErrors: false,
        };
    },
    methods: {
        async downloadExcelErrors() {
            
            let url = `/items/items-document-error/${this.hash}`;

            console.log("🚀 ~ file: ResultExcelProducts.vue:56 ~ downloadExcelErrors ~ url:", url)
            window.open(url, "_blank");
                  
        },
        open() {},
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

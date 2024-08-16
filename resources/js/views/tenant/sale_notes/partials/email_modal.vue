<template>
    <el-dialog 
        v-loading="loading" 
    :title="`Enviar comprobante de pago ${fullNumber}`"
    :visible="showDialog" width="50%" @open="open" @close="close"
    
    >
        <div class="row mt-2">
            <div class="col-12">
                <label for="customer_email"> Correo </label>
                <el-input v-model="form.customer_email"></el-input>
            </div>
            <div class="col-12">
                <label for=""> Mensaje </label>
                <el-input type="textarea" v-model="form.message"></el-input>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="sendEmail">Enviar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "saleNoteId", "mail","fullNumber"],
    data() {
        return {
            loading: false,
            form: {
                customer_email: "",
                message: "Su pedido ha sido confirmado. Adjunto a este correo encontrará su comprobante de pago.",
                sale_note_id: "",
            },
            errors: [],
        };
    },
    methods: {
        open() {
            this.form.customer_email = this.mail;
            this.form.sale_note_id = this.saleNoteId;
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        async sendEmail() {
            try{
                this.loading = true;
  const response = await this.$http.post("sale-notes/send_email",{
                id: this.form.sale_note_id,
                customer_email: this.form.customer_email,
                message: this.form.message,
            });

            if (response.data.success) {
                this.$message({
                    type: "success",
                    message: "Correo enviado con éxito",
                });
                this.close();
            } else {
                this.$message({
                    type: "error",
                    message: "Ocurrió un error al enviar el correo",
                });
            }
            }catch(e){
                this.$message({
                    type: "error",
                    message: "Ocurrió un error al enviar el correo",
                });
            }finally{
                this.loading = false;
                }
        },
    },
};
</script>

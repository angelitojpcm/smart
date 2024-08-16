<template>
    <el-dialog
        :title="title"
        @open="open"
        @close="close"
        :visible="showDialog"
        append-to-body
    >
        <div class="row">
            <label class=""
                >Mensaje de bloqueo
                <el-tooltip
                    class="item"
                    effect="dark"
                    content="Mensaje que se mostrará al usuario cuando intente ingresar al sistema."
                    placement="top"
                >
                    <i
                        class="fa fa-info-circle text-primary"
                        aria-hidden="true"
                    ></i>
                </el-tooltip>
            </label>
            <el-input
                v-model="form.message"
                placeholder="Mensaje"
                type="textarea"
                :rows="3"
            >
            </el-input>
        </div>
        <div slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="danger" @click="submit">Bloquear</el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["configuration", "showDialog", "user"],
    data() {
        return {
            form: {},
            record: {},
            loading: false,
            title: "",
            errors: {},
        };
    },
    methods: {
        initForm() {
            this.form = {
                message:
                    "Su cuenta está inactiva, comuníquese con el administrador del sistema.",
            };
        },
        open() {
            this.initForm();
            this.title = "Confirmar bloque de: " + this.user.name;
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        async submit() {
            const response = await this.$http.post("/users/lock", {
                user_id: this.user.id,
                message: this.form.message,
            });
            if (response.data.success) {
                this.$message.success(response.data.message);
                this.$emit("getRecords");
                this.close();
            }
        },
    },
};
</script>

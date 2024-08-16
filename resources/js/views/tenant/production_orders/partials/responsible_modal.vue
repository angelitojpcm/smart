<template>
    <el-dialog
        title="Asignar responsable"
        width="30%"
        :visible="showDialog"
        append-to-body
        @close="close"
        @open="open"
    >
        <div class="row m-2">
            <div class="col-12">
                <label> Responsable </label>
                <el-select
                    v-model="form.responsible_id"
                    placeholder="Seleccione un responsable"
                >
                    <el-option
                        v-for="user in users"
                        :key="user.id"
                        :label="user.name"
                        :value="user.id"
                    ></el-option>
                </el-select>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <el-button @click="close">Cancelar</el-button>
                            <el-button
                                :disabled="form.responsible_id == null"
                                type="primary"
                                @click="changeResponsible"
                                >Guardar</el-button
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "document"],
    data() {
        return {
            form: {
                responsible: null,
                responsible_id: null,
            },
            responsibles: [],
            loading: false,
            errors: {},
            users: [],
        };
    },

    methods: {
        open() {
            this.getUsers();
        },
        close() {
            this.$emit("update:showDialog", false);
        },
        async getUsers() {
            try {
                const { data } = await this.$http("/production-order/users");
                console.log(
                    "🚀 ~ file: responsible_modal.vue:36 ~ getUsers ~ data:",
                    data
                );
                this.users = data.users;
            } catch (error) {
                console.log(error);
            }
        },
        async changeResponsible() {
            const response = await this.$http(
                `/production-order/set-responsible/${this.document.id}/${this.form.responsible_id}`
            );
            if (response.data.success) {
                this.$message.success(response.data.message);
                this.$emit("update");
                this.$emit("update:showDialog", false);
            } else {
                this.$message.error(response.data.message);
            }
        },
    },
};
</script>

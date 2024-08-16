<template>
    <el-dialog
        :close-on-click-modal="false"
        :title="titleDialog"
        :visible="showDialog"
        append-to-body
        @close="close"
        @open="create"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.plate_number }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Nro. de Placa
                                <span class="text-danger">*</span></label
                            >
                            <el-input
                                v-model="form.plate_number"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.plate_number"
                                class="text-danger"
                                v-text="errors.plate_number[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.tuc }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >T.U.C
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Tarjeta Única de Circulación"
                                    placement="top"
                                >
                                    <i class="el-icon-info"></i>
                                </el-tooltip>
                            </label>
                            <el-input v-model="form.tuc" dusk="name"></el-input>
                            <small
                                v-if="errors.tuc"
                                class="text-danger"
                                v-text="errors.tuc[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.auth_plate_primary }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Autorización de Placa principal
                            </label>
                            <el-input
                                v-model="form.auth_plate_primary"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.auth_plate_primary"
                                class="text-danger"
                                v-text="errors.auth_plate_primary[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{
                                'has-danger': errors.secondary_plate_number,
                            }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Nro. de Placa secundaria
                            </label>
                            <el-input
                                v-model="form.secondary_plate_number"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.secondary_plate_number"
                                class="text-danger"
                                v-text="errors.secondary_plate_number[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.tuc_secondary }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >T.U.C (placa secundaria)
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Tarjeta Única de Circulación"
                                    placement="top"
                                >
                                    <i class="el-icon-info"></i>
                                </el-tooltip>
                            </label>
                            <el-input
                                v-model="form.tuc_secondary"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.tuc_secondary"
                                class="text-danger"
                                v-text="errors.tuc_secondary[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{
                                'has-danger': errors.auth_plate_secondary,
                            }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Autorización de Placa secundaria
                            </label>
                            <el-input
                                v-model="form.auth_plate_secondary"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.auth_plate_secondary"
                                class="text-danger"
                                v-text="errors.auth_plate_secondary[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.model }"
                            class="form-group"
                        >
                            <label class="control-label">Modelo</label>
                            <el-input v-model="form.model"></el-input>
                            <small
                                v-if="errors.model"
                                class="text-danger"
                                v-text="errors.model[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.brand }"
                            class="form-group"
                        >
                            <label class="control-label">Marca</label>
                            <el-input v-model="form.brand"></el-input>
                            <small
                                v-if="errors.brand"
                                class="text-danger"
                                v-text="errors.brand[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-top: 32px">
                            <el-switch
                                v-model="form.is_default"
                                active-text="Predeterminado"
                                inactive-text=""
                            ></el-switch>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button
                    :loading="loading_submit"
                    native-type="submit"
                    type="primary"
                    >Guardar
                </el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    name: "DispatchTransportForm",
    props: ["showDialog", "recordId", "external"],
    data() {
        return {
            loading_submit: false,
            titleDialog: null,
            resource: "transports",
            errors: {},
            form: {},
        };
    },
    created() {
        this.initForm();
    },
    methods: {
        initForm() {
            this.errors = {};
            this.form = {
                id: null,
                plate_number: null,
                secondary_plate_number: null,
                model: null,
                brand: null,
                is_default: false,
                is_active: true,
                tuc: null,
            };
        },
        async create() {
            this.initForm();
            this.titleDialog = this.recordId
                ? "Editar Vehículo"
                : "Nuevo Vehículo";
            if (this.recordId) {
                await this.$http
                    .get(`/${this.resource}/record/${this.recordId}`)
                    .then((response) => {
                        this.form = response.data.data;
                    });
            }
        },
        async submit() {
            this.errors = {};
            let {
                plate_number,
                secondary_plate_number,
                tuc,
                tuc_secondary,
                auth_plate_primary,
                auth_plate_secondary,
            } = this.form;
           /* if (plate_number) {
                if (!tuc || !auth_plate_primary) {
                    this.$message.error(
                        "Debe ingresar el T.U.C y la autorización de placa principal"
                    );
                    return;
                }
            }
            if (secondary_plate_number) {
                if (!tuc_secondary || !auth_plate_secondary) {
                    this.$message.error(
                        "Debe ingresar el T.U.C y la autorización de placa secundaria"
                    );
                    return;
                }
            }*/
            this.loading_submit = true;
            await this.$http
                .post(`/${this.resource}`, this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$emit("success", response.data.id);
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
        },
    },
};
</script>

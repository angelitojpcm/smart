<template>
    <el-dialog
        :visible="dialogVisible"
        :title="dialogTitle"
        :width="dialogWidth"
        @close="handleClose"
        @open="handleOpen"
        v-loading="loading"
    >
        <div class="row mt-2">
            <div class="col-md-6">
                <label for="">Fecha de emisión</label>
                <el-date-picker
                    v-model="form.date_of_issue"
                    type="date"
                    placeholder="Fecha de emisión"
                    value-format="yyyy-MM-dd"
                ></el-date-picker>
            </div>
            <div class="col-md-6">
                <label for="">Responsable</label>
                <el-select
                    v-model="form.responsible_id"
                    placeholder="Responsable"
                >
                    <el-option
                        v-for="item in responsibles"
                        :key="item.id"
                        :label="item.name"
                        :value="item.id"
                    ></el-option>
                </el-select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Cantidad</th>
                        <th>Producto</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.quantity }}</td>
                        <td>{{ item.item && item.item.description }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <label for="">Observación</label>
                <el-input
                    type="textarea"
                    v-model="form.observation"
                    placeholder="Observación"
                    rows="2"
                ></el-input>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="handleClose">Cancelar</el-button>
            <el-button type="primary" @click="generateDispatchOrder"
                >Generar</el-button
            >
        </span>
    </el-dialog>
</template>

<script>
import moment from "moment";
export default {
    props: {
        recordId: {
            type: Number,
        },
        dialogVisible: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            loading: false,
            dialogTitle: "Order de despacho OD-",
            dialogWidth: "50%",
            number: null,
            responsibles: [],
            form: {},
            items: [],
        };
    },
    methods: {
        async getResponsibles() {
            const response = await this.$http.get(
                `/dispatch-order/responsibles`
            );
            if (response.status == 200) {
                let { responsibles } = response.data;
                this.responsibles = responsibles;
            }
        },
        initForm() {
            this.form = {
                date_of_issue: moment().format("YYYY-MM-DD"),
                responsible_id: null,
                observation: null,
            };
        },
        async generateDispatchOrder() {
            try {
                this.loading = true;
                const response = await this.$http.post(
                    `/dispatch-order/generate/${this.recordId}`,
                    this.form
                );
                if (response.status == 200) {
                    this.$emit("getRecords");

                    this.$message({
                        type: "success",
                        message: "Se generó la orden de despacho",
                    });
                    this.handleClose();
                }
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },
        async getRecord() {
            try {
                this.loading = true;
                const response = await this.$http(
                    `/production-order/record2/${this.recordId}`
                );
                if (response.status == 200) {
                    let { data } = response.data;
                    let  production_order  = data;
                    this.items = production_order.items;
                    this.form.date_of_issue = production_order.date_of_issue;
                    this.number = data.number;
                    this.dialogTitle = `Order de despacho OD-${this.number}`;
                }
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },
        handleOpen() {
            this.initForm();
            this.getRecord();
            this.getResponsibles();
        },
        handleBeforeClose(done) {
            // Lógica antes de cerrar el diálogo
        },
        handleClose() {
            this.$emit("update:dialogVisible", false);
        },
    },
};
</script>

<template>
    <el-dialog
        :title="titleDialog"
        width="40%"
        :visible="showDialog"
        @open="create"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        append-to-body
        :show-close="false"
    >
        <div class="form-body">
            <div class="row">
                <div class="col-12 text-end">
                    <button class="btn btn-primary btn-sm" @click="addSize">
                        Agregar
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th>Stock real</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in sizes" :key="index">
                                <td v-if="!row.canBeErased">{{ row.size }}</td>
                                <td v-else>
                                    <el-input
                                        v-model="row.size"
                                        size="small"
                                    ></el-input>
                                </td>
                                <td class>{{ row.stock }}</td>
                                <td>
                                    <el-input-number
                                        v-model="row.qty"
                                        :min="0"
                                        :precision="0"
                                        :controls="false"
                                        size="mini"
                                    ></el-input-number>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        v-if="row.canBeErased"
                                        @click="sizes.splice(index, 1)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-actions text-end pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
            <el-button type="primary" @click="submit">Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "sizes", "stock", "recordId", "quantity"],
    data() {
        return {
            titleDialog: "Tallas",
            loading: false,
            errors: {},
            form: {},
            states: ["Activo", "Inactivo", "Desactivado", "Voz", "M2m"],
            idSelected: null,
        };
    },
    async created() {
        // await this.$http.get(`/pos/payment_tables`)
        //     .then(response => {
        //         this.payment_method_types = response.data.payment_method_types
        //         this.cards_brand = response.data.cards_brand
        //         this.clickAddLot()
        //     })
    },
    methods: {
        checkIsHasRepeat(){
            let pass = true;
            let sizes = this.sizes;
            let justSizes = sizes.map((x) => x.size.toUpperCase());
            let sizesRepeat = justSizes.filter(
                (x, i) => justSizes.indexOf(x) !== i
            );
            let error = sizesRepeat.length > 0;
            if (error) {
                this.$message.error("No puede haber tallas repetidas");
                pass = false;
            }
            return pass;

        },
        notRepeatSize(input, idx) {
            //check if size exists, but not in the same idx
            let sizes = this.sizes.filter(
                (x, i) =>
                    x.size.toUpperCase() == input.toUpperCase() && i != idx
            );

            let error = sizes.length == 1;
            if (error) {
                this.$message.error("La talla ya existe");
                this.sizes[idx].size = "";
            }
        },
        addSize() {
          
            this.sizes.push({
                size: "",
                qty: 0,
                stock: 0,
                canBeErased: true,
                deleted: false,
            });
        },
        changeSelect(index, id, quantity_lot) {},
        handleSelectionChange(val) {
            //this.$refs.multipleTable.clearSelection();
            let row = val[val.length - 1];
            this.multipleSelection = [row];
        },
        create() {
            console.log(
                "🚀 ~ file: sizes.vue:76 ~ create ~ this.sizes:",
                this.sizes
            );
        },

        async submit() {
            if (!this.checkIsHasRepeat()) {
                return;
            }
            let sizes = this.sizes;

            await this.$emit("addRowSelectSize", sizes);
            await this.$emit("update:showDialog", false);
        },

        clickCancel(item) {
            //this.lots.splice(index, 1);
            item.deleted = true;
            this.$emit("addRowLotGroup", this.lots);
        },

        async clickCancelSubmit() {
            this.$emit("addRowLotGroup", []);
            await this.$emit("update:showDialog", false);
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

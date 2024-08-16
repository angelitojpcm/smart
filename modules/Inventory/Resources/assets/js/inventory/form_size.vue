<template>
    <el-dialog
        :title="titleDialog"
        width="30%"
        :visible="showDialog"
        @open="create"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        append-to-body
        :show-close="false"
    >
        <div class="form-body">
            <div class="row">
                <div class="col-12 text-end" v-if="type == 'input'">
                    <button class="btn btn-primary btn-sm" @click="addSize">
                        Agregar
                    </button>
                </div>
                <div class="col-lg-12 col-md-12 table-responsive">
                    <table width="100%" class="table">
                        <thead>
                            <tr width="100%">
                                <th>Talla</th>
                                <th>Cantidad</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(row, index) in sizes_"
                                :key="index"
                                width="100%"
                            >
                                <!-- <td>{{index}}</td> -->

                                <td>
                                    <el-input
                                        v-model="row.size"
                                        size="small"
                                    ></el-input>
                                </td>
                                <td>
                                    <el-input-number
                                        v-model="row.quantity"
                                        :min="0"
                                        :step="1"
                                        size="small"
                                    ></el-input-number>
                                </td>
                                <td>
                                    <button
                                        class="btn btn-danger btn-sm"
                                        v-if="row.canBeErased"
                                        @click="sizes_.splice(index, 1)"
                                    >
                                        Eliminar
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
            <!-- <el-button type="primary" @click="submit" >Guardar</el-button> -->
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "sizes", "recordId", "type"],
    data() {
        return {
            titleDialog: "Tallas",
            loading: false,
            errors: {},
            form: {},
            search: "",
            sizes_: [],
        };
    },
    async created() {},
    watch: {
        sizes(val) {
            this.sizes_ = val;
        },
    },
    methods: {
        checkIsHasRepeat() {
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
        addSize() {
            this.sizes_.push({ size: "", quantity: 0, canBeErased: true });
        },
        notRepeatSize(input, idx) {
            //check if size exists, but not in the same idx
            let sizes = this.sizes_.filter(
                (x, i) =>
                    x.size.toUpperCase() == input.toUpperCase() && i != idx
            );

            let error = sizes.length == 1;
            if (error) {
                this.$message.error("La talla ya existe");
                this.sizes_[idx].size = "";
            }
        },
        filter() {
            if (this.search) {
                this.sizes_ = this.sizes.filter((x) =>
                    x.series.toUpperCase().includes(this.search.toUpperCase())
                );
            } else {
                this.sizes_ = this.sizes;
            }
        },
        create() {},
        async submit() {
            // let val_sizes = await this.validateLots()
            // if(!val_sizes.success)
            //     return this.$message.error(val_sizes.message);
            // await this.$emit('addRowLot', this.sizes);
            // await this.$emit('update:showDialog', false)
        },
        checkSizes() {
            //check if all elements are filled his property size
            let sizes = this.sizes_.filter((x) => x.size == "");
        
            return sizes.length == 0;
        },
        close() {
            if (!this.checkSizes()) {
                return this.$message.error(
                    "Debe llenar todos los campos de talla"
                );
            }
            if (!this.checkIsHasRepeat()) {
                return;
            }
            this.$emit("update:showDialog", false);
            this.$emit("addRowSize", this.sizes_);
        },
        async clickCancelSubmit() {
            // this.$emit('addRowLot', []);
            // await this.$emit('update:showDialog', false)
        },
    },
};
</script>

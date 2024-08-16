<template>
    <el-dialog
        :title="titleDialog"
        width="40%"
        :visible="showDialog"
        @open="create"
        @close="close"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        append-to-body
        :show-close="false"
    >
        <div class="form-body">
            <div class="col-md-12 text-end">
                <h5>Cant. Pedida: {{ quantity }}</h5>
                <h5 v-bind:class="{ 'text-danger': toAttend < 0 }">
                    Por Atender: {{ toAttend }}
                </h5>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Talla</th>
                            <th>Stock</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <td>
                                {{ row.size }}
                            </td>
                            <td>
                                {{ row.stock }}
                            </td>
                            <td>
                                <el-input-number
                                    v-model="row.qty"
                                    :min="0"
                                    :max="row.stock"
                                    :precision="0"
                                    :controls="false"
                                    size="mini"
                                    @change="changeHasSale(row, index)"
                                ></el-input-number>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-actions text-end pt-2">
            <el-button @click.prevent="close">Cerrar</el-button>
            <el-button type="primary" :disabled="toAttend < 0" @click="submit"
                >Guardar
            </el-button>
        </div>
    </el-dialog>
</template>

<script>
import queryString from "query-string";

export default {
    props: [
        "showDialog",
        "lotsAll",
        "sizes",
        "stock",
        "itemId",
        "documentItemId",
        "quantity",
        "saleNoteItemId",
        "warehouseId",
    ],
    data() {
        return {
            titleDialog: "Tallas",
            resource: "documents",
            search_series_by_barcode: false,
            loading: false,
            errors: {},
            form: {},
            sizesSelected: [],
            records: [],
            pagination: {},
            search: {
                input: null,
                item_id: null,
            },
        };
    },
    computed: {
        toAttend() {
            let total = this.sizesSelected.reduce(
                (acc, obj) => acc + obj.qty,
                0
            );
            return this.quantity - total;
        },
    },
    async mounted() {},

    methods: {
          calculateToAttend() {
      let total = this.sizesSelected.reduce(
        (acc, obj) => acc + obj.qty,
        0
      );
      return this.quantity - total;
    },
        initForm() {
            this.search = {
                input: null,
                item_id: null,
                document_item_id: this.documentItemId,
                sale_note_item_id: this.saleNoteItemId,
                warehouse_id: this.warehouseId,
            };
        },
        async create() {
            this.initForm();
            this.sizesSelected = this.sizes;

            await this.getRecords();
            if (this.sizesSelected.length > 0) {
                _.forEach(this.sizesSelected, (row) => {
                    let size = _.find(this.records, { size: row.size });
                    if (size) {
                        size.qty = row.qty;
                    }
                });
              
               this.calculateToAttend();
            }
          
        },

        async getRecords() {
            this.search.item_id = this.itemId;
            this.records = [];

            this.loading = true;
            await this.$http
                .post(
                    `/${this.resource}/item_sizes?${this.getQueryParameters()}`,
                    this.search
                )
                .then((response) => {
                    const {
                        data: { data },
                    } = response;
                    this.records = data;
                })
                .catch((error) => {
                    this.$message.error(error.response.data.message);
                })
                .then(() => {
                    this.loading = false;
                });
            // await this.checkedLot();
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page,
            });
        },
        changeSearchSeriesBarcode() {
            this.cleanInput();
        },
        cleanInput() {
            this.search.input = null;
        },
        // async searchSeriesBarcode() {
        //     await this.getRecords(true)
        //     await this.checkedSeries()
        // },
        // async checkedSeries() {
        //     if (this.search_series_by_barcode) {
        //         if (this.records.length == 1) {
        //             let lot = await _.find(this.lots, {id: this.records[0].id})
        //             if (!lot) {
        //                 this.records[0].has_sale = true
        //                 this.addLot(this.records[0])
        //             }
        //         }
        //         this.cleanInput();
        //     }
        // },
        changeHasSale(row, index) {
            let sizeIndex = _.findIndex(this.sizesSelected, { size: row.size });
            if (sizeIndex > -1) {
                this.sizesSelected[sizeIndex].qty = row.qty;
            } else {
                this.sizesSelected.push(row);
            }
            // this.calculateToAttend();
        },
        customIndex(index) {
            return (
                this.pagination.per_page * (this.pagination.current_page - 1) +
                index +
                1
            );
        },
        checkedLot() {
            _.forEach(this.sizesSelected, (row) => {
                let lot = _.find(this.records, { id: row.id });
                console.log(row);
                if (lot) {
                    lot.has_sale = true;
                }
            });
        },
        submit() {
            // console.log(this.sizesSelected);
            // if (this.toAttend < 0) {
            //     return this.$message.error(
            //         "La cantidad de series seleccionadas no es la correcta"
            //     );
            // }
            //has una copia profunda de sizesSelected
         let sizesSelected = _.cloneDeep(this.sizesSelected);


            this.$emit("addRowSelectSize", sizesSelected);
            this.close();
        },
        close() {
            // this.sizesSelected = [];
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

<template>
    <el-dialog
        :title="titleDialog"
        :visible="showDialog"
        @open="create"
        @close="close"
        append-to-body
        top="7vh"
        :close-on-click-modal="false"
    >
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12" v-if="item">
                        <div
                            class="col-12"
                            v-for="(factoryCode, idx) in itemsFactoryCodes"
                            :key="idx"
                        >
                            <el-divider content-position="left">
                                {{ factoryCode.description }}
                            </el-divider>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Producto</th>
                                        <th class="text-end">Precio</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, idx) in factoryCode.items"
                                        :key="`${idx}_`"
                                    >
                                        <td width="10%">
                                            <el-checkbox
                                                @change="selectItem(item)"
                                                v-model="item.selected"
                                                :disabled="item.stock <= 0"
                                            ></el-checkbox>
                                        </td>
                                        <td width="50%">
                                            {{ item.description }}
                                        </td>
                                        <td class="text-end" width="20%">
                                            {{ item.sale_unit_price }}
                                        </td>
                                        <td width="20%">
                                            {{ item.stock }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end pt-2">
                <el-button @click.prevent="close()">Cerrar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "item"],
    data() {
        return {
            showImportDialog: false,
            resource: "items",
            recordId: null,
            titleDialog: "Código de fábricas/Stock",
            my_warehouses: [],
            warehouse_id: null,
            items: [],
            factoryCodes: [],
            itemsFactoryCodes: [],
        };
    },
    created() {
        // console.log(this.typeUser)
    },
    methods: {
        selectItem(item) {
            // this.itemsFactoryCodes = this.itemsFactoryCodes.map((fc) => ({
            //     ...fc,
            //     items: fc.items.map((i) => ({
            //         ...i,
            //         selected: i.id === item.id,
            //     })),
            // }));
            this.$eventHub.$emit("reloadDataItems", item.id);
            this.close();
        },
        async create() {
            this.itemsFactoryCodes = [];
            let { factory_code } = this.item;
            this.factoryCodes = factory_code.split(",");
            await this.getFactoryCodes();
        },
        async getFactoryCodes() {
            //search-items-factory-codes/{id}
            const response = await this.$http.get(
                `/documents/search-items-factory-codes/${this.item.id}`
            );

            const {
                data: { items },
            } = response;
            this.items = items.map((i) => ({
                ...i,
                factory_code: i.factory_code.split(","),
            }));
            console.log("🚀 ~ file: select_factory_code.vue:117 ~ this.items=items.map ~ this.items :", this.items )
            this.itemsFactoryCodes = this.factoryCodes.map((fc) => ({
                description: fc,
                items: [...this.items.filter((i) =>{
                    let f = fc.toLowerCase().trim()
                    console.log("🚀 ~ file: select_factory_code.vue:122 ~ items:[...this.items.filter ~ i:", i)
                    let factory_code = i.factory_code.map(f => f.toLowerCase().trim())
                    console.log("🚀 ~ file: select_factory_code.vue:121 ~ items:[...this.items.filter ~ f:", f)
                    console.log("🚀 ~ file: select_factory_code.vue:121 ~ items:[...this.items.filter ~ factory_code:", factory_code)
                    console.log("🚀 ~ file: select_factory_code.vue:127 ~ items:[...this.items.filter ~ factory_code.includes(f):", factory_code.includes(f))
                    
                    return factory_code.includes(f)
                })],
            }));
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

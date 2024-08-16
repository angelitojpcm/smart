<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <!--<div class="card-header">
            <h3 class="my-0">Consulta kardex</h3>
        </div> -->
        <div class="card mb-0">
            <div class="card-body">
                <data-table
                    :resource="resource"
                    @setAllWarehouses="setAllWarehouses"
                >
                    <tr slot="heading">
                        <th>#</th>
                        <th v-if="!item_id">Producto</th>
                        <th>Fecha y hora transacción</th>
                        <th
                            v-if="
                                configuration.purchases_control &&
                                all_warehouses
                            "
                        >
                            Almacén
                        </th>
                        <th>Tipo transacción</th>
                        <th>Número</th>
                        <th>NV. Asociada</th>
                        <th>Pedido</th>
                        <th>Doc. Asociado</th>
                        <th>Fecha emisión</th>
                        <th>Fecha registro</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th v-if="item_id">Saldo</th>
                        <th v-if="configuration.purchases_control">Placa</th>
                        <th v-if="configuration.purchases_control">
                            Responsable
                        </th>
                        <th v-if="configuration.purchases_control">
                            Precio unitario
                        </th>
                        <th>Referencia</th>
                        <th>Cliente/Proveedor</th>
                        <th></th>
                        <!--
                        <th >Almacen </th>
                        <th >Precio de almacen</th>
                    --></tr>

                    <tr></tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td v-if="!item_id">{{ row.item_name }}</td>
                        <td>{{ row.date_time }}</td>
                        <td
                            v-if="
                                configuration.purchases_control &&
                                all_warehouses
                            "
                        >
                            {{ row.warehouse }}
                        </td>
                        <td>{{ row.type_transaction }}</td>
                        <td>{{ row.number }}</td>
                        <td>{{ row.sale_note_asoc }}</td>
                        <td>{{ row.order_note_asoc }}</td>
                        <td>{{ row.doc_asoc }}</td>
                        <td>{{ row.date_of_issue }}</td>
                        <td>{{ row.date_of_register }}</td>
                        <!-- <td>{{ row.inventory }}</td> -->
                        <td>{{ row.input }}</td>
                        <td>{{ row.output }}</td>
                        <td v-if="item_id">{{ row.balance }}</td>
                        <td v-if="configuration.purchases_control">
                            {{ row.license }}
                        </td>
                        <td v-if="configuration.purchases_control">
                            {{ row.responsible }}
                        </td>
                        <td v-if="configuration.purchases_control">
                            <template v-if="row.unit_price">
                                {{ Number(row.unit_price || 0).toFixed(2) }}
                            </template>
                        </td>
                        <td>
                            {{ row.reference }}
                        </td>
                        <td>
                            <template v-if="row.person_name">
                                {{ row.person_name }}
                                <br />
                                <small>{{ row.person_number }}</small>
                            </template>
                        </td>
                        <td class="text-end">
                            <!-- <button @click="getStock(row)">Tesst</button> -->
                            <button
                                class="btn waves-effect waves-light btn-sm btn-info"
                                type="button"
                                @click.prevent="downloadPdfGuide(row.guide_id)"
                                v-if="row.guide_id"
                            >
                                <i class="fa fa-file-pdf"></i>
                            </button>
                        </td>
                        <!--
                            <td v-if="row.warehouse">{{row.warehouse}}</td>
                            <td v-if="row.item_warehouse_price">{{row.item_warehouse_price}}</td>
                            -->
                    </tr>
                </data-table>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/DataTableKardex.vue";

export default {
    props: ["configuration"],
    components: { DataTable },
    data() {
        return {
            showDialog: false,
            resource: "reports/kardex",
            form: {},
            item_id: null,
            warehouse_id: null,
            all_warehouses: false,
        };
    },
    created() {
        this.$eventHub.$on("emitItemID", (item_id, warehouse_id) => {
            // console.log(item_id)
            this.item_id = item_id;
            this.warehouse_id = warehouse_id;
        });
    },
    methods: {
        setAllWarehouses(value) {
            this.all_warehouses = value;
        },
        downloadPdfGuide(guide_id) {
            if (guide_id) {
                window.open(
                    `/${this.resource}/get_pdf_guide/${guide_id}`,
                    "_blank"
                );
            }
        },
    },
};
</script>

<template>
    <div class="card mb-0 pt-2 pt-md-0">
        <div class="card-header">
            <h3 class="my-0">Consulta de Compras</h3>
        </div>
        <div class="card mb-0">
                <div class="card-body">
                    <data-table :resource="resource"  
                    :configuration="configuration"
                    :applyCustomer="true" :colspanFootPurchase="9" :applyConversionToPen="applyConversionToPen">
                        <tr slot="heading">
                            <th class="">#</th>
                            <th class="">F. Emisión</th>
                            <th class="">F. Vencimiento</th>
                            <th class="">Proveedor</th>
                            <th class="">Estado</th>
                            <th class="">Número</th>
    <th v-if="configuration.purchases_control">Placa</th>
    <th v-if="configuration.purchases_control">Responsable</th>
                            <th class="">F. Pago</th>
                            <th class="text-center">Moneda</th>
                            <th>Percepcion</th>

                            <th class="">T. ISC</th>
                            <th class="" >T. Exonerado</th>

                            <th class="" >T. Inafecta</th>
                            <th class="" >T. Gratuito</th>
                            <th class="">T. Gravado</th>
                            <th class="">T. IGV</th>
                            <th class="">Total</th>
                        <tr>
                        <tr slot-scope="{ index, row }">
                            <td>{{ index }}</td>
                            <td>{{row.date_of_issue}}</td>
                            <td>{{row.date_of_due}}</td>
                            <td>{{ row.supplier_name }}<br/><small v-text="row.supplier_number"></small></td>
                            <td>{{row.state_type_description}}</td>
                            <td>{{row.number}}
                                <small v-text="row.document_type_description"></small><br/>

                            </td>
                            <td v-if="configuration.purchases_control">{{row.license}}</td>
                            <td v-if="configuration.purchases_control">{{row.responsible}}</td>
                            <td>
                                <span v-for="(pay,idx) in row.payments" :key="idx">{{pay.payment_method_type_description}}</span>
                            </td>
                            <td class="text-center">
                                {{ row.currency_type_id }}
                                
                                <template v-if="row.description_apply_conversion_to_pen">
                                    <el-tooltip class="item"
                                                :content="row.description_apply_conversion_to_pen"
                                                effect="dark"
                                                placement="top">
                                        <i class="fa fa-info-circle"></i>
                                    </el-tooltip>
                                </template>
                            </td>
                            <td class="text-end">{{ (row.total_perception && row.state_type_id != '11') ? row.total_perception : '0.00' }}</td>
                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_isc}}</td>

                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_exonerated}}</td>

                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_unaffected}}</td>
                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_free}}</td>
                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_taxed}}</td>
                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total_igv}}</td>

                            <td>{{ row.state_type_id == '11' ? '0.00' : row.total}}</td>

                        </tr>
                    </data-table>


                </div>
        </div>

    </div>
</template>

<script>

    import DataTable from '../../components/DataTableReports.vue'

    export default {
        components: {DataTable},
        props:[
            'configuration',
            'applyConversionToPen',
        ],
        data() {
            return {
                resource: 'reports/purchases',
                form: {},

            }
        },
        async created() {
            console.log(this.configuration);
        },
        methods: {


        }
    }
</script>

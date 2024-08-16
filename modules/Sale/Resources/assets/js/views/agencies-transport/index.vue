<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Listado de agencias de transporte</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Descripción</th>
                        <th class="text-end">Ubigeo</th>
                        <th class="text-end">Dirección</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(row, index) in records"
                    :key="index"
                    >
                        <td>{{ index + 1 }}</td>
                        <td>{{ row.description }}</td>
                        <td class="text-end">{{ row.ubigeo_format }}</td>
                        <td class="text-end">{{ row.address }}</td>
                        <!-- <td class="text-end">{{ row.cci }}</td> -->
                        <td class="text-end">
                            <button type="button" class="btn waves-effect waves-light btn-sm btn-info" @click.prevent="clickCreate(row.id)">Editar</button>

                            <template v-if="typeUser === 'admin'">
                              <button type="button" class="btn waves-effect waves-light btn-sm btn-danger"  @click.prevent="clickDelete(row.id)">Eliminar</button>
                            </template>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>
                </div>
            </div>
        </div>
        <agency-transport-form :showDialog.sync="showDialog"
                            :recordId="recordId"></agency-transport-form>
    </div>
</template>

<script>

    import AgencyTransportForm from './form.vue'
    import {deletable} from '../../../../../../../resources/js/mixins/deletable.js'

    export default {
        mixins: [deletable],
        props: ['typeUser'],
        components: {AgencyTransportForm},
        data() {
            return {
                showDialog: false,
                resource: 'agencies-transport',
                recordId: null,
                records: [],
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        methods: {
            getData() {
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data.data
                    })
            },
            clickCreate(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            },
            clickDelete(id) {
                this.destroy(`/${this.resource}/${id}`).then(() =>
                    this.$eventHub.$emit('reloadData')
                )
            }
        }
    }
</script>

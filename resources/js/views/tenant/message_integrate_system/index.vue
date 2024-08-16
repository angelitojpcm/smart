<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Mensajes de correo</span></li>
            </ol>
            <div class="right-wrapper pull-right"></div>
        </div>
        <div class="card mb-0" v-loading="loading">
            <div class="card-body">
                <data-table
                    :isDriver="isDriver"
                    ref="dataTable"
                    :resource="resource"
                >
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Se envia en</th>
                        <th class="text-left">Mensaje</th>

                        <th class="text-center">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.comment }}</td>
                        <td class="text-left">
                            {{ row.message }}
                        </td>

                        <td class="text-end">
                            <button
                          
                                type="button"
                                class="btn btn-sm btn-primary"
                                @click.prevent="clickCreate(row.id)"
                            >
                                Editar
                            </button>
                        </td>
                    </tr>
                </data-table>
            </div>
            <create-form
                :showDialog.sync="showDialog"
                :recordId="recordId"
                @getRecords="getRecords"
            ></create-form>
        </div>
    </div>
</template>
<style>
.state_base {
    padding: 5px;
    text-align: center;
    border-radius: 5px;
}
.state_1 {
    color: white;
    background: #ffcc00;
}
.state_2 {
    color: white;
    background: #ff9900;
}
.state_3 {
    color: white;
    background: #33cc33;
}
.state_4 {
    color: white;
    background: #0070c0;
}
.state_5 {
    color: white;
    background: #33cc33;
}
.el-dropdown {
    vertical-align: top;
}
.el-dropdown + .el-dropdown {
    margin-left: 15px;
}
.el-icon-arrow-down {
    font-size: 12px;
}
</style>
<script>
import DataTable from "../../../components/DataTable.vue";

import {  mapState } from "vuex/dist/vuex.mjs";
import CreateForm from "./form.vue";
export default {
    props: [ "configuration"],
    components: {
        DataTable,
CreateForm

    },
    computed: {
        ...mapState(["config"]),
    },
    data() {
        return {
            showDialog:false,
            loading: false,
            showDialogGenerateDispatchOrder: false,
            showModalResponsibles: false,
            showModalGenerateGuie: false,
            currentDocument: null,
            showPeriod: false,
            showModalGenerateCPE: false,
            showMigrateNv: false,
            resource: "message-integrate-system",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            saleNotesNewId: null,
            recordId: null,
            columns: {},
            isDriver: false,
            states: [],
    
        };
    },
    created() {

    },
    filters: {
        period(name) {
            let res = "";
            switch (name) {
                case "month":
                    res = "Mensual";
                    break;
                case "year":
                    res = "Anual";
                    break;
                default:
                    break;
            }

            return res;
        },
    },
    methods: {
        getRecords() {
            this.$refs.dataTable.getRecords();
        },
  
     
     
        clickCreate(id) {
            this.showDialog = true;
            this.recordId = id;
        },
    
    },
};
</script>

<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Ordenes de producción</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <!-- <a
                    href="#"
                    @click.prevent="clickCreate()"
                    class="btn btn-custom btn-sm  mt-2 mr-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
                <a
                    href="#"
                    @click.prevent="onOpenModalGenerateCPE"
                    class="btn btn-custom btn-sm  mt-2 mr-2"
                    >Generar comprobante desde múltiples Notas</a
                >
                  <a
                    href="#"
                    @click.prevent="onOpenModalGenerateGuie"
                    class="btn btn-custom btn-sm  mt-2 mr-2"
                    >Generar guía desde múltiples Notas</a
                >
                <a
                    href="#"
                    v-if="config.send_data_to_other_server === true"
                    @click.prevent="onOpenModalMigrateNv"
                    class="btn btn-custom btn-sm  mt-2 mr-2"
                    >Migrar Datos</a
                > -->
            </div>
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
                        <th class="text-center">Fecha Emisión</th>
                        <th class="text-end">Vendedor</th>
                        <th>Cliente</th>
                        <th>Orden de producción</th>
                        <th>Orden de despacho</th>
                        <th>Encargado</th>
                        <th>Estado</th>
                        <th>Guias</th>
                        <th>Documentos</th>
                        <th>Region</th>

                        <!-- <th class="text-center">Pdf</th> -->
                        <th class="text-center">Acciones</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>

                        <td class="text-end">
                            {{ row.seller_name }}
                        </td>

                        <td>
                            {{ row.customer_name }}<br /><small
                                v-text="row.customer_number"
                            ></small>
                        </td>
                        <td>
                            <el-button
                                type="primary"
                                icon="fas fa-file-pdf"
                                class="btn waves-effect waves-light btn-sm btn-info"
                                size="mini"
                                @click.prevent="clickOptions(row.id)"
                            >
                                {{ row.full_number }}
                            </el-button>
                        </td>

                        <td>
                            <template v-if="row.dispatch_order">
                                <el-button
                                    type="primary"
                                    icon="fas fa-file-pdf"
                                    class="btn waves-effect waves-light btn-sm btn-info"
                                    size="mini"
                                    @click.prevent="
                                        clickOptions(
                                            row.dispatch_order.id,
                                            'dispatch-order'
                                        )
                                    "
                                >
                                    {{ row.dispatch_order.prefix }}-{{
                                        row.dispatch_order.number
                                    }}
                                </el-button>
                            </template>
                        </td>
                        <td>
                            <template v-if="row.responsible_name">
                                <!-- un boton pequeño envuelto en un tooltip que diga "cambiar usuario" el boton que tenga solo el icono de persona -->
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    content="Cambiar responsable"
                                    placement="top-start"
                                >
                                    <button
                                        type="button"
                                        style="min-width: 41px"
                                        class="btn waves-effect waves-light btn-sm btn-primary"
                                        @click.prevent="setResponsible(row)"
                                    >
                                        <i class="fas fa-user"></i>
                                    </button>
                                </el-tooltip>

                                {{ row.responsible_name }}
                            </template>
                            <template v-else>
                                <button
                                    type="button"
                                    style="min-width: 41px"
                                    class="btn waves-effect waves-light btn-sm btn-primary"
                                    @click.prevent="setResponsible(row)"
                                >
                                    <i class="fas fa-user"></i> Asignar
                                    responsable
                                </button>
                            </template>
                        </td>
                        <td>
                            <el-dropdown trigger="click" @command="changeState">
                                <el-button :class="`state_pp_${row.state_id}`">
                                    {{ row.state
                                    }}<i
                                        class="el-icon-arrow-down el-icon--right"
                                    ></i>
                                </el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item
                                        v-for="(state, index) in states"
                                        :key="index"
                                        :disabled="state.id == row.state_id"
                                        :command="[state.id, row.id]"
                                    >
                                        {{ state.description }}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </td>

                        <td>
                            <template v-for="(dispatch, i) in row.dispatches">
                                <el-button
                                    type="primary"
                                    :key="i"
                                    :class="`state_${dispatch.state_id}_d`"
                                    @click="
                                        clickOptions(dispatch.id, 'dispatches')
                                    "
                                >
                                    {{ dispatch.number }}
                                </el-button>
                            </template>
                            <!-- guia -->
                        </td>
                        <td>
                            <template v-for="(document, i) in row.documents">
                                <el-button
                                    type="primary"
                                    :key="i"
                                    :class="`state_${document.state_id}_d`"
                                    @click="
                                        clickOptions(document.id, 'documents')
                                    "
                                >
                                    {{ document.number }}
                                </el-button>
                            </template>
                            <!-- guia -->
                        </td>
                        <!-- <td class="text-end">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-sm btn-info"
                                @click.prevent="clickDownload(row.external_id)"
                            >
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </td> -->
                        <td class="text-center">
                            {{ row.customer_region }}
                        </td>
                        <td class="text-end">
                            <div class="ms-1">
                                <button
                                    class="btn btn-light btn-sm"
                                    type="button"
                                    id="dropdownMenuButton"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <!-- <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Anular"
                                        v-if="row.state_type_id != '11'"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickVoided(row.id)"
                                    >
                                        Anular
                                    </button> -->

                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Editar"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickCreate(row.id)"
                                        v-if="row.can_edit"
                                    >
                                        Editar
                                    </button>

                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Generar comprobante"
                                        type="button"
                                        class="dropdown-item"
                                        v-if="!row.dispatch_order"
                                        @click.prevent="
                                            clickGenerateDispatchOrder(row.id)
                                        "
                                    >
                                        Generar orden de despacho
                                    </button>
                                    <!-- <el-tooltip
                                        class="item"
                                        effect="dark"
                                        content="Generar guía desde CPE"
                                        placement="top-start"
                                    >
                                        <template
                                            v-for="(
                                                document, i
                                            ) in row.documents"
                                        >
                                            <a
                                                :href="`/dispatches/create/${document.id}`"
                                                class="dropdown-item"
                                                v-if="row.changed"
                                                :key="i"
                                            >
                                                Generar guía desde
                                            </a>
                                        </template>
                                    </el-tooltip>

                                    <el-tooltip
                                        class="item"
                                        effect="dark"
                                        content="Generar guía desde Nota Venta"
                                        placement="left"
                                    >
                                        <a
                                            :href="`/dispatches/create_new/sale_note/${row.id}`"
                                            class="dropdown-item"
                                        >
                                            Generar guía
                                        </a>
                                    </el-tooltip> -->

                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Imprimir"
                                        v-if="row.state_type_id != '11'"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickOptions(row.id)"
                                    >
                                        Imprimir
                                    </button>
                                    <!-- <button
                                        @click="duplicate(row.id)"
                                        title="Duplica la nota de venta"
                                        type="button"
                                        class="dropdown-item"
                                    >
                                        Duplica la nota de venta
                                    </button>
                                    <button
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="
                                            clickKillDocument(row.id)
                                        "
                                        v-if="configuration.delete_documents"
                                    >
                                        Eliminar
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Enviar a otro servidor"
                                        v-if="
                                            row.state_type_id != '11' &&
                                            row.send_other_server === true
                                        "
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="sendToServer(row.id)"
                                    >
                                        Enviar a otro servidor
                                    </button>

                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Elimina unicamente la relación entre la nota y factura"
                                        v-if="
                                            configuration.delete_relation_note_to_invoice &&
                                            row.documents.length > 0
                                        "
                                        type="button"
                                        class="dropdown-item"
                                        @click="
                                            sendDeleteRelationInvoice(row.id)
                                        "
                                    >
                                        Eliminar factura relacionada
                                    </button> -->
                                </div>
                            </div>
                        </td>
                    </tr>
                </data-table>
            </div>
        </div>
        <!-- <el-dialog
            title="Eliminar Documento Relacionado"
            :visible="showDialogDeleteRelationInvoice"
            >
            <table>
                <tr v-for="(document, index) in dataDeleteRelation.documents" :key="index">
                    <td>
                        <el-button
                            type="button"
                            class="btn waves-effect waves-light btn-sm btn-danger"
                            @click.prevent="deleteRelationInvoice(row.id)">
                            <i class="fas fa-trash"></i>
                        </el-button>
                    </td>
                    <td>
                        {{document.number_full}}
                    </td>
                </tr>
            </table>
        </el-dialog> -->
        <period-modal
            :showDialog.sync="showPeriod"
            :document="currentDocument"
        ></period-modal>
        <sale-note-payments
            :showDialog.sync="showDialogPayments"
            :documentId="recordId"
        ></sale-note-payments>

        <sale-notes-options
            :showDialog.sync="showDialogOptions"
            :recordId="saleNotesNewId"
            :resourceDocument="documentResource"
            :showClose="true"
            :configuration="config"
        ></sale-notes-options>

        <sale-note-generate
            :show.sync="showDialogGenerate"
            :recordId="recordId"
            :showGenerate="true"
            :showClose="false"
        ></sale-note-generate>
        <responsible-modal
            @update="update"
            :showDialog.sync="showModalResponsibles"
            :document="currentDocument"
        ></responsible-modal>
        <dispatch-order
            @getRecords="getRecords"
            :recordId="recordId"
            :dialogVisible.sync="showDialogGenerateDispatchOrder"
        ></dispatch-order>
        <dispatch-finish
            :recordId="dispatchId"
            :showClose="true"
            :send-sunat="false"
            :showDialog.sync="showDialogFinish"
        ></dispatch-finish>
        <document-options
            :showDialog.sync="showDialogDocumentOptions"
            :recordId="documentId"
            :showClose="true"
            :configuration="configuration"
        ></document-options>
        <!-- <ModalGenerateCPE :show.sync="showModalGenerateCPE"></ModalGenerateCPE>
        <ModalGenerateGuie :show.sync="showModalGenerateGuie"></ModalGenerateGuie> -->
    </div>
</template>
<style>
.state_base {
    padding: 5px;
    text-align: center;
    border-radius: 5px;
}
.state_pp_1 {
    color: white;
    background: #ffcc00;
}
.state_pp_2 {
    color: white;
    background: #ff9900;
}
.state_pp_3 {
    color: white;
    background: #33cc33;
}
.state_pp_4 {
    color: white;
    background: #0070c0;
}
.state_pp_5 {
    color: white;
    background: #16a34f;
}
.state_pp_6 {
    color: white;
    background: red;
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
import PeriodModal from "../../../../../modules/Suscription/Resources/assets/js/components/PeriodModal.vue";

import DataTable from "../../../components/DataTable.vue";
import SaleNotePayments from "./partials/payments.vue";
import SaleNotesOptions from "./partials/options.vue";
import ResponsibleModal from "./partials/responsible_modal.vue";
import SaleNoteGenerate from "./partials/option_documents";
import { deletable } from "../../../mixins/deletable";
import DispatchOrder from "./partials/dispatch_order.vue";
// import ModalGenerateCPE from "./ModalGenerateCPE";
// import ModalGenerateGuie from "./ModalGenerateGuie";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import DispatchFinish from "../../tenant/dispatches/partials/finish.vue";
import DocumentOptions from "../../tenant/documents/partials/options.vue";
import Options from "./partials/options.vue";
export default {
    props: ["soapCompany", "typeUser", "configuration", "userId"],
    mixins: [deletable],
    components: {
        DispatchOrder,
        PeriodModal,
        DataTable,
        SaleNotePayments,
        SaleNotesOptions,
        SaleNoteGenerate,
        ResponsibleModal,
        DispatchFinish,
        DocumentOptions,
        Options,
        // ModalGenerateCPE,
        // UploadToOtherServer,
        // ModalGenerateGuie
    },
    computed: {
        ...mapState(["config"]),
    },
    data() {
        return {
            loading: false,
            showDialogGenerateDispatchOrder: false,
            showModalResponsibles: false,
            showModalGenerateGuie: false,
            currentDocument: null,
            showPeriod: false,
            showModalGenerateCPE: false,
            showMigrateNv: false,
            resource: "production-order",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            saleNotesNewId: null,
            recordId: null,
            columns: {},
            isDriver: false,
            states: [],
            dispatchId: null,
            showDialogFinish: false,
            documentId: null,
            showDialogDocumentOptions: false,
            documentResource: "production-order",
            // showDialogDeleteRelationInvoice: false,
            // dataDeleteRelation: {
            //     documents: {},
            //     id: ''
            // }
        };
    },
    created() {
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
        let { package_handlers } = this.configuration;
        this.isDriver = package_handlers;
        // this.getColumnsToShow();
        this.getTable();
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
        clickDocumentOptions(id) {
            this.documentId = id;
            this.showDialogDocumentOptions = true;
        },
        openDispatchFinish(id) {
            this.dispatchId = id;
            this.showDialogFinish = true;
        },
        getRecords() {
            this.$refs.dataTable.getRecords();
        },
        async generateDispatchOrder(id) {
            try {
                this.loading = true;
                const response = await this.$http.post(
                    `/dispatch-order/generate/${id}`
                );
                if (response.status == 200) {
                    this.update();

                    this.$message({
                        type: "success",
                        message: "Se generó la orden de producción",
                    });
                }
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },
        clickGenerateDispatchOrder(id) {
            this.recordId = id;
            this.showDialogGenerateDispatchOrder = true;
        },
        update() {
            this.$refs.dataTable.getRecords();
        },
        async changeState([stateId, rowId]) {
            try {
                await this.$confirm(
                    "¿Está seguro de cambiar el estado?",
                    "Advertencia",
                    {
                        confirmButtonText: "Cambiar",
                        cancelButtonText: "Cancelar",
                        type: "warning",
                    }
                );
                this.loading = true;
                const response = await this.$http(
                    `/${this.resource}/change-state/${rowId}/${stateId}`
                );
                if (response.status == 200) {
                    this.$message.success(response.data.message);
                    this.$refs.dataTable.getRecords();
                }
            } catch (e) {
                return;
            } finally {
                this.loading = false;
            }
        },
        async getTable() {
            const response = await this.$http.get(`/${this.resource}/states`);
            const { states } = response.data;
            this.states = states;
        },
        setResponsible(row) {
            this.currentDocument = row;
            this.showModalResponsibles = true;
        },
        async clickKillDocument(id) {
            try {
                const confirm = await this.$confirm(
                    "¿Está seguro de eliminar el documento y todos los registros relacionados?",
                    "Advertencia",
                    {
                        confirmButtonText: "Eliminar",
                        cancelButtonText: "Cancelar",
                        type: "warning",
                    }
                );
                if (confirm) {
                    const response = await this.$http.get(
                        `/${this.resource}/kill/${id}`
                    );
                    console.log(response);
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$refs.dataTable.getRecords();
                    } else {
                        this.$message.error(response.data.message);
                    }
                }
            } catch (e) {
                return;
            }
        },
        openPeriod(row) {
            console.log(row);
            this.currentDocument = { ...row, document_type_id: "80" };
            this.showPeriod = true;
        },
        ...mapActions(["loadConfiguration"]),
        getColumnsToShow(updated) {
            this.$http
                .post("/validate_columns", {
                    columns: this.columns,
                    report: "sale_notes_index", // Nombre del reporte.
                    updated: updated !== undefined,
                })
                .then((response) => {
                    if (updated === undefined) {
                        let currentCols = response.data.columns;
                        if (currentCols !== undefined) {
                            this.columns = currentCols;
                        }
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        duplicate(id) {
            this.$http
                .post(`${this.resource}/duplicate`, { id })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se guardaron los cambios correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch((error) => {});
            this.$eventHub.$emit("reloadData");
        },
        onOpenModalGenerateGuie() {
            this.showModalGenerateGuie = true;
        },
        onOpenModalGenerateCPE() {
            this.showModalGenerateCPE = true;
        },
        onOpenModalMigrateNv() {
            this.showMigrateNv = true;
        },
        clickDownload(external_id) {
            window.open(
                `/production-order/downloadExternal/${external_id}/ticket`,
                "_blank"
            );
        },
        clickDownloadDispatchOrden(external_id) {
            window.open(
                `/dispatch-order/downloadExternal/${external_id}/ticket`,
                "_blank"
            );
        },
        clickOptions(recordId, type = "production-order") {
            this.documentResource = type;
            this.saleNotesNewId = recordId;
            this.showDialogOptions = true;
        },
        sendToServer(recordId) {
            this.$http
                .post("/sale-notes/UpToOther", { sale_note_id: recordId })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (
                        error.response !== undefined &&
                        error.response.status !== undefined &&
                        error.response.status.errors !== undefined &&
                        error.response.status === 422
                    ) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickGenerate(recordId) {
            this.recordId = recordId;
            this.showDialogGenerate = true;
        },
        clickPayment(recordId) {
            this.recordId = recordId;
            this.showDialogPayments = true;
        },
        clickCreate(id = "") {
            location.href = `/${this.resource}/create/${id}`;
        },
        changeConcurrency(row) {
            this.$http
                .post(`/${this.resource}/enabled-concurrency`, row)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        console.log(error);
                    }
                })
                .then(() => {});
        },
        clickVoided(id) {
            this.anular(`/${this.resource}/anulate/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
        // deleteRelationInvoice(saleNote) {
        //     this.dataDeleteRelation.documents = saleNote.documents
        //     this.dataDeleteRelation.id = saleNote.id
        //     this.showDialogDeleteRelationInvoice = true
        // },
        sendDeleteRelationInvoice(id) {
            this.$http
                .post(`${this.resource}/delete-relation-invoice`, { id })
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(
                            "Se ha eliminado el comprobante relacionado correctamente."
                        );
                        this.$eventHub.$emit("reloadData");
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            this.$eventHub.$emit("reloadData");
        },
    },
};
</script>

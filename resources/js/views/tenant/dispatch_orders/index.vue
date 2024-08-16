<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Ordenes de despacho</span></li>
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
            <div class="data-table-visible-columns">
                <el-dropdown :hide-on-click="false">
                    <el-button type="primary">
                        Mostrar/Ocultar columnas<i
                            class="el-icon-arrow-down el-icon--right"
                        ></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item
                            v-for="(column, index) in columns"
                            :key="index"
                        >
                            <el-checkbox
                                @change="getColumnsToShow(1)"
                                v-model="column.visible"
                                >{{ column.title }}</el-checkbox
                            >
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
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
                        <th>Orden de despacho</th>
                        <th>Guia de remisión</th>
                        <th>Encargado</th>
                        <th>Estado</th>

                        <th class="text-center">Guia de traslado</th>

                        <th class="text-center">Estado de pago</th>
                        <th class="text-center">Confirmación de pago</th>

                        <th class="text-center">Pdf</th>
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
                        <td>{{ row.full_number }}</td>
                        <td>
                            <template v-for="(dispatch, i) in row.dispatches">
                                <el-button
                                    type="primary"
                                    :key="i"
                                    :class="`state_${dispatch.state_id}_d`"
                                    @click="openDispatchFinish(dispatch.id)"
                                >
                                    {{ dispatch.number }}
                                </el-button>
                            </template>
                            <!-- guia -->
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
                                <el-button :class="`state_${row.state_id}`">
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
                            <button
                                v-if="!row.has_agency_dispatch"
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-primary"
                                @click.prevent="setGuide(row)"
                            >
                                Guia de traslado
                            </button>
                            <button
                                v-else
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-success"
                                @click.prevent="viewGuide(row)"
                            >
                                Ver guia de traslado
                            </button>
                        </td>
                        <td>
                            <span
                                class="badge text-white"
                                :class="{
                                    'bg-success': row.total_canceled,
                                    'bg-warning': !row.total_canceled,
                                }"
                                >{{
                                    row.total_canceled ? "Pagado" : "Pendiente"
                                }}</span
                            >
                        </td>
                        <td>
                            <el-button
                                :class="`state_${row.state_payment_id}_py`"
                            >
                                {{
                                    row.state_payment_id == "01"
                                        ? "En espera"
                                        : row.state_payment_id == "02"
                                        ? "Aprobado"
                                        : "Rechazado"
                                }}
                            </el-button>
                        </td>

                        <td class="text-end">
                            <button
                                type="button"
                                class="btn waves-effect waves-light btn-sm btn-info"
                                @click.prevent="clickDownload(row.external_id)"
                            >
                                <i class="fas fa-file-pdf"></i>
                            </button>
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
                                        title="Generar guía"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="
                                            clickGenerateGuide(row.id)
                                        "
                                        v-if="row.can_create_dispatch"
                                    >
                                        Generar guia
                                    </button>
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

                                    <!-- <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Generar comprobante"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickGenerate(row.id)"
                                        v-if="
                                            !row.changed &&
                                            row.state_type_id != '11' &&
                                            soapCompany != '03'
                                        "
                                    >
                                        Generar comprobante
                                    </button>

                                    <el-tooltip
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
                                    </el-tooltip> -->

                                    <!-- <el-tooltip
                                        class="item"
                                        effect="dark"
                                        content="Generar guía desde Nota Venta"
                                        placement="left"
                                    >
                                        <a
                                            :href="`/dispatches/create_new/dispatch_order/${row.id}`"
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
        <guide-modal
            :showDialog.sync="showGuideModal"
            :currentRecord="currentRecord"
        ></guide-modal>
        <guide-modal-view
            :showDialog.sync="showGuideModalView"
            :currentRecord="currentRecord"
        ></guide-modal-view>
        <dispatch-finish
            :recordId="dispatchId"
            :showClose="true"
            :send-sunat="false"
            :showDialog.sync="showDialogFinish"
        ></dispatch-finish>
        <!-- <ModalGenerateCPE :show.sync="showModalGenerateCPE"></ModalGenerateCPE>
        <ModalGenerateGuie :show.sync="showModalGenerateGuie"></ModalGenerateGuie> -->
    </div>
</template>
<style>
.state_01_py {
    color: white;
    background: #ffcc00;
}
.state_02_py {
    color: white;
    background: #33cc33;
}
.state_03_py {
    color: white;
    background: red;
}
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
    background: #33cc33;
}
.state_6 {
    color: white;
    background: #0070c0;
}
.state_5 {
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
import GuideModal from "./partials/guide_modal.vue";
import GuideModalView from "./partials/guide_modal_view.vue";
import SaleNoteGenerate from "./partials/option_documents";
import { deletable } from "../../../mixins/deletable";
// import ModalGenerateCPE from "./ModalGenerateCPE";
// import ModalGenerateGuie from "./ModalGenerateGuie";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import DispatchFinish from "../../tenant/dispatches/partials/finish.vue";
export default {
    props: ["soapCompany", "typeUser", "configuration", "userId"],
    mixins: [deletable],
    components: {
        PeriodModal,
        DataTable,
        SaleNotePayments,
        SaleNotesOptions,
        SaleNoteGenerate,
        ResponsibleModal,
        GuideModal,
        GuideModalView,
        // ModalGenerateCPE,
        // UploadToOtherServer,
        // ModalGenerateGuie
        DispatchFinish,
    },
    computed: {
        ...mapState(["config"]),
    },
    data() {
        return {
            loading: false,
            showGuideModal: false,
            showGuideModalView: false,
            showModalResponsibles: false,
            showModalGenerateGuie: false,
            currentDocument: null,
            showPeriod: false,
            showModalGenerateCPE: false,
            showMigrateNv: false,
            resource: "dispatch-order",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            saleNotesNewId: null,
            recordId: null,
            dispatchId: null,
            showDialogFinish: false,
            currentRecord: null,
            columns: {
                observation: {
                    title: "Observación",
                    visible: false,
                },
                due_date: {
                    title: "Fecha de Vencimiento",
                    visible: false,
                },
                exchange_rate_sale: {
                    title: "Tipo de cambio",
                    visible: false,
                },
                total_free: {
                    title: "T.Gratuito",
                    visible: false,
                },
                total_exportation: {
                    title: "T.Exportación",
                    visible: false,
                },
                total_unaffected: {
                    title: "T.Inafecto",
                    visible: false,
                },
                total_exonerated: {
                    title: "T.Exonerado",
                    visible: false,
                },
                total_taxed: {
                    title: "T.Gravado",
                    visible: false,
                },
                total_igv: {
                    title: "T.IGV",
                    visible: false,
                },
                paid: {
                    title: "Estado de Pago",
                    visible: false,
                },
                type_period: {
                    title: "Tipo Periodo",
                    visible: true,
                },
                quantity_period: {
                    title: "Cantidad Periodo",
                    visible: true,
                },
                license_plate: {
                    title: "Placa",
                    visible: true,
                },
                total_paid: {
                    title: "Pagado",
                    visible: false,
                },
                total_pending_paid: {
                    title: "Por pagar",
                    visible: false,
                },
                seller_name: {
                    title: "Vendedor",
                    visible: false,
                },
                recurrence: {
                    title: "Recurrencia",
                    visible: false,
                },
                region: {
                    title: "Region",
                    visible: false,
                },
                date_payment: {
                    title: "Fecha de pago",
                    visible: false,
                },
            },
            isDriver: false,
            states: [],
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
        this.getColumnsToShow();
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
        clickGenerateGuide(id) {
            location.href = `/dispatches/create_new/dispatch_order/${id}`;
        },
            openDispatchFinish(id) {
                this.dispatchId = id;
                this.showDialogFinish = true;
            },
        viewGuide(row) {
            this.currentRecord = row;
            this.showGuideModalView = true;
        },
        setGuide(row) {
            this.currentRecord = row;
            this.showGuideModal = true;
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
                `/dispatch-order/downloadExternal/${external_id}/ticket`,
                "_blank"
            );
        },
        clickOptions(recordId) {
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

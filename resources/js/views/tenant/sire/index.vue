<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>SIRE</span></li>
            </ol>
        </div>
        <div class="card mt-3">
            <div class="card-body border-bottom d-flex">
                <el-form
                    :inline="true"
                    :model="form"
                    class="demo-form-inline mb-0"
                >
                    <el-form-item label="Año" class="label-mt-0">
                        <el-select v-model="form.year" @change="setPeriods()">
                            <el-option
                                v-for="(year, index) in period_year"
                                :key="index"
                                :label="year.title"
                                :value="year.id"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Periodo" class="label-mt-0">
                        <el-select v-model="form.period">
                            <el-option
                                v-for="(period, index) in period_month"
                                :key="index"
                                :label="
                                    period.perTributario +
                                    ' ' +
                                    period.desEstado
                                "
                                :value="period.perTributario"
                            ></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item class="label-mt-0">
                        <el-button
                            type="primary"
                            @click="sendPeriod"
                            :disabled="form.period == null"
                            >Enviar</el-button
                        >
                    </el-form-item>
                </el-form>
            </div>
            <div class="card-body border-bottom" v-if="code_ticket !== null">
                <table class="table table-unstyled mb-0">
                    <tr>
                        <th>Ticket Actual</th>
                        <th>Estado</th>
                        <th class="ps-3">Página</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td>{{ code_ticket }}</td>
                        <td>{{ states[status_ticket] }}</td>
                        <td width="10%">
                            <el-input
                                v-model="page"
                                label="Pagina"
                                size="mini"
                                type="number"
                            ></el-input>
                        </td>
                        <td class="text-right">
                            <el-button
                                v-if="documents.length > 0"
                                type="success"
                                @click="queryTicketExcel"
                            >
                                <i class="fas fa-file-excel"></i>
                                Exportar
                            </el-button>
                            <el-button
                                type="primary"
                                @click="queryTicket"
                                :disabled="code_ticket == null"
                                class="bg-primary"
                                size="mini"
                                :loading="loading_query"
                            >
                                Consultar
                            </el-button>
                        </td>
                    </tr>
                </table>
            </div>
            <div v-if="documents.length > 0" class="col-md-12">
                <div class="table-responsive m-2">
                    <table class="table">
                        <thead>
                            <th class="text-center">Servicio</th>
                            <th class="text-center">F. Emisión</th>
                            <th class="text-left">
                                <template v-if="type == 'sale'"
                                    >Cliente</template
                                >
                                <template>Proveedor</template>
                            </th>

                            <th class="text-center">Tipo Documento</th>
                            <th class="text-center">Serie</th>
                            <th class="text-center">Número</th>
                            <th class="text-end">Total</th>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(document, index) in documents"
                                :key="index"
                            >
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-center"
                                >
                                    {{ document.service }}
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-center"
                                >
                                    {{ document.date }}
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-left"
                                >
                                    {{ document.name_company }}
                                    <br />
                                    <small>{{ document.number_company }}</small>
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-center"
                                >
                                    {{ document.document_type }}
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-center"
                                >
                                    {{ document.serie }}
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-center"
                                >
                                    {{ document.number }}
                                </td>
                                <td
                                    :class="
                                        document.label == 'DANGER'
                                            ? 'text-danger'
                                            : document.label == 'STRONG'
                                            ? 'text-strong'
                                            : ''
                                    "
                                    class="text-end"
                                >
                                    {{ document.total }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td>Total Sunat ({{ totals.count_sunat }})</td>
                                <td class="text-end">
                                    {{ totals.sunat.toFixed(2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td>Total Smart ({{ totals.count_smart }})</td>
                                <td class="text-end">
                                    {{ totals.smart.toFixed(2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- <div class="row mb-5">
                        <div class="col-md-4 text-center">
                            Total notas de venta en soles S/.
                            {{ totals.total_pen }}
                        </div>
                        <div class="col-md-4 text-center">
                            Total pagado en soles S/.
                            {{ totals.total_paid_pen }}
                        </div>
                        <div class="col-md-4 text-center">
                            Total por cobrar en soles S/.
                            {{ totals.total_pending_paid_pen }}
                        </div>
                    </div> -->

                    <div>
                        <!-- <el-pagination
                            @current-change="getRequestData"
                            layout="total, prev, pager, next"
                            :total="pagination.total"
                            :current-page.sync="pagination.current_page"
                            :page-size="pagination.per_page"
                        >
                        </el-pagination> -->
                    </div>
                </div>
            </div>
            <!--       
      <div class="card-body">
        <el-table
          :data="documents"
          style="width: 100%"
          stripe
          :row-class-name="diffRows">
          <el-table-column
            prop="service"
            label="Servicio">
          </el-table-column>
          <el-table-column
            prop="date"
            label="F. Emisión">
          </el-table-column>
          <el-table-column
            prop="document_type"
            label="Tipo Documento">
          </el-table-column>
          <el-table-column
            prop="serie"
            label="Serie">
          </el-table-column>
          <el-table-column
            prop="number"
            label="Número">
          </el-table-column>
          <el-table-column
            prop="total"
            label="Total">
          </el-table-column>
        </el-table>
      </div> -->
            <div class="card-footer">
                <div class="row d-flex align-items-end">
                    <div class="col-6">
                        <el-button
                            type="success"
                            class=""
                            :disabled="documents.length <= 0"
                            @click="sendAccept"
                            :loading="loading_accept"
                        >
                            Aceptar Propuesta
                        </el-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
.text-strong {
    font-weight: bolder;
}
</style>
<script>
import queryString from "query-string";
export default {
    props: ["company"],
    components: {},
    data() {
        return {
            resource: "sire",
            type: "sale",
            period_year: [],
            period_month: [],
            form: {},
            totals: {
                sunat: 0,
                smart: 0,
                count_sunat: 0,
                count_smart: 0,
            },
            code_ticket: null,
            states: {
                "00": "No solicitado",
                "01": "Cargado (solicitado)",
                "02": "Validando Archivo (en proceso)",
                "03": "Procesado con Errores",
                "04": "Procesado sin errores (concluido)",
                "05": "En proceso",
                "06": "Terminado",
            },
            status_ticket: "01",
            period_current: null,
            page: 1,
            filename: null,
            loading_query: false,
            loading_data: false,
            documents: [],
            loading_accept: false,
        };
    },
    created() {
        this.setType();
        this.getPeriods();
        this.getData();
    },
    methods: {
        exportExcel() {},
        setType() {
            this.type = window.location.href.split("/").pop();
        },
        getPeriods() {
            this.$http
                .get(`/${this.resource}/${this.type}/tables`)
                .then((response) => {
                    if (response.data.success) {
                        this.setPeriodsData(response.data.data);
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        setPeriodsData(data) {
            this.period_year = data.map((period) => {
                return {
                    id: period.numEjercicio,
                    title: period.numEjercicio + " - " + period.desEstado,
                    periods: period.lisPeriodos,
                };
            });
        },
        setPeriods() {
            let record = this.period_year.find(
                (item) => item.id == this.form.year
            );
            this.period_month = record.periods;
        },
        sendPeriod() {
            localStorage.setItem(
                this.type + "_sire_period_" + this.company.number,
                this.form.period
            );
            this.period_current = this.form.period;
            this.$http
                .get(
                    `/${this.resource}/${this.type}/${this.form.period}/ticket`
                )
                .then((response) => {
                    if (response.data.success) {
                        this.code_ticket = response.data.data.numTicket;
                        this.status_ticket = "00";
                        localStorage.setItem(
                            this.type + "_sire_ticket_" + this.company.number,
                            this.code_ticket
                        );
                        localStorage.setItem(
                            this.type + "_sire_status_" + this.company.number,
                            "00"
                        );
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        getData() {
            let sire_ticket = localStorage.getItem(
                this.type + "_sire_ticket_" + this.company.number
            );
            this.code_ticket = sire_ticket !== null ? sire_ticket : null;
            let sire_status = localStorage.getItem(
                this.type + "_sire_status_" + this.company.number
            );
            this.status_ticket = sire_status !== null ? sire_status : "00";
            if (sire_ticket != null) {
                let sire_period = localStorage.getItem(
                    this.type + "_sire_period_" + this.company.number
                );
                this.period_current = sire_period !== null ? sire_period : null;
            }
        },
        queryTicketExcel() {
            let params = {
                period: this.period_current,
                page: this.page,
                ticket: this.code_ticket,
                type: this.type,
            };
            window.open(
                `/${this.resource}/query-excel?${queryString.stringify(
                    params
                )}`,
                "_blank"
            );
        },
        queryTicket() {
            this.documents = [];
            this.loading_query = true;
            let params = {
                period: this.period_current,
                page: this.page,
                ticket: this.code_ticket,
            };
            this.$http
                .post(`/${this.resource}/${this.type}/query`, params)
                .then((response) => {
                    this.loading_query = false;
                    if (response.data.success) {
                        this.status_ticket = response.data.data.status_code;
                        localStorage.setItem(
                            this.type + "_sire_status_" + this.company.number,
                            this.status_ticket
                        );
                        this.documents = response.data.data.documents||[];
                        this.getTotals();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    this.loading_query = false;
                    console.error(error);
                });
        },
        getTotals() {
            let sunat = this.documents.filter((d) => d.service == "Sunat");
            let count_sunat = sunat.length;
            let total_sunat = sunat.reduce(
                (a, b) => a + parseFloat(b.total.replace(",", "")),
                0
            );
            let smart = this.documents.filter((d) => d.service != "Sunat");
            let count_smart = smart.length;
            let total_smart = smart.reduce(
                (a, b) => a + parseFloat(b.total.replace(",", "")),
                0
            );
            this.totals.sunat = total_sunat;
            this.totals.smart = total_smart;
            this.totals.count_sunat = count_sunat;
            this.totals.count_smart = count_smart;
        },
        diffRows({ row, rowIndex }) {
            if (rowIndex < this.documents.length - 1) {
                const currentRow = row;
                const nextRow = this.documents[rowIndex + 1];

                if (
                    currentRow.number == nextRow.number &&
                    currentRow.serie == nextRow.serie &&
                    currentRow.total !== nextRow.total
                ) {
                    return "bg-danger";
                }
            }
            return null;
        },
        sendAccept() {
            this.loading_accept = true;
            let sire_period = localStorage.getItem(
                this.type + "_sire_period_" + this.company.number
            );
            this.$http
                .get(`/${this.resource}/${this.type}/${sire_period}/accept`)
                .then((response) => {
                    if (response.data.success) {
                        console.log(response.data);
                        this.$message.success("Propuesta enviada exitosamente");
                    }
                    this.loading_accept = false;
                })
                .catch((error) => {
                    this.$message.error("Error al enviar propuesta");
                    this.loading_accept = false;
                    console.error(error);
                });
        },
    },
};
</script>

<style scope>
.label-mt-0,
.label-mt-0 label {
    margin-top: 0px;
    margin-bottom: 0px !important;
}
.el-button--mini {
    padding: 7px 15px !important;
}
</style>

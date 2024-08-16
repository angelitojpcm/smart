<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active">
                    <span>{{ title }}</span>
                </li>
            </ol>
            <div class="right-wrapper pull-right">
                <button
                    type="button"
                    class="btn btn-success btn-sm mt-2 mr-2"
                    @click.prevent="clickExport()"
                >
                    <i class="fa fa-file-excel"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <div v-loading="loading_submit">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-xl-12">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12 pb-2">
                                    <div class="d-flex">
                                        <div style="width: 100px">
                                            Filtrar por:
                                        </div>
                                        <el-select
                                            v-model="search.column"
                                            placeholder="Select"
                                            @change="changeClearInput"
                                        >
                                            <el-option
                                                v-for="(label, key) in columns"
                                                :key="key"
                                                :value="key"
                                                :label="label"
                                            ></el-option>
                                        </el-select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                                    <template v-if="search.column == 'date'">
                                        <el-date-picker
                                            v-model="search.value"
                                            type="date"
                                            style="width: 100%"
                                            placeholder="Buscar"
                                            value-format="yyyy-MM-dd"
                                            @change="getRecords"
                                        >
                                        </el-date-picker>
                                    </template>
                                    <template v-else>
                                        <el-input
                                            placeholder="Buscar"
                                            v-model="search.value"
                                            style="width: 100%"
                                            prefix-icon="el-icon-search"
                                            @input="getRecords"
                                        >
                                        </el-input>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Talla</th>
                                            <th>Producto</th>
                                            <th>Stock</th>
                                            <th></th>
                                            <!-- <th class="text-end">Acciones</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(row, index) in records"
                                            :key="index"
                                        >
                                            <td>{{ customIndex(index) }}</td>
                                            <td>{{ row.size }}</td>
                                            <td>{{ row.item_description }}</td>
                                            <td>{{ row.stock }}</td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn waves-effect waves-light btn-sm btn-info"
                                                    @click.prevent="
                                                        clickEdit(row.id)
                                                    "
                                                >
                                                    Editar
                                                </button>
                                            </td>
                                            <!-- <td>{{ row.state }}</td>
                                            <td>{{ row.status }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn waves-effect waves-light btn-sm btn-info" @click.prevent="clickCreate(row.id)" v-if="!row.has_sale">Editar</button>
                                            </td> -->
                                        </tr>
                                    </tbody>
                                </table>
                                <div>
                                    <el-pagination
                                        @current-change="getRecords"
                                        layout="total, prev, pager, next"
                                        :total="pagination.total"
                                        :current-page.sync="
                                            pagination.current_page
                                        "
                                        :page-size="pagination.per_page"
                                    >
                                    </el-pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <el-dialog
                title="Editar Talla"
                :visible.sync="showEditDialog"
                width="30%"
                :close-on-click-modal="false"
                :close-on-press-escape="false"
                append-to-body
            >
                <div class="row">
                    <div class="col-md-12" v-if="currentRecord">
                        <el-input
                            v-model="currentRecord.size"
                            placeholder="Talla"
                        ></el-input>
                    </div>
                </div>
                <span slot="footer" class="dialog-footer">
                    <el-button @click="showEditDialog = false"
                        >Cancelar</el-button
                    >
                    <el-button type="primary" @click="sendNewSize">
                        Guardar
                    </el-button>
                </span>
            </el-dialog>
        </div>
    </div>
</template>

<script>
import ItemLotForm from "./form.vue";
import queryString from "query-string";

export default {
    components: { ItemLotForm },
    data() {
        return {
            title: null,
            showDialog: false,
            resource: "item-sizes",
            recordId: null,
            search: {
                column: null,
                value: null,
            },
            columns: [],
            records: [],
            pagination: {},
            loading_submit: false,
            showEditDialog: false,
            currentRecord: null,
        };
    },
    async mounted() {
        let column_resource = _.split(this.resource, "/");
        await this.$http
            .get(`/${_.head(column_resource)}/columns`)
            .then((response) => {
                this.columns = response.data;
                this.search.column = _.head(Object.keys(this.columns));
            });
        await this.getRecords();
    },
    created() {
        this.title = "Tallas";

        this.$eventHub.$on("reloadData", () => {
            this.getRecords();
        });
    },
    methods: {
        sendNewSize() {
            this.loading_submit = true;
            this.$http
                .put(
                    `/${this.resource}/size/${this.recordId}`,
                    this.currentRecord
                )
                .then((response) => {
                    let { success, message } = response.data;
                    if (success) {
                        this.$message.success(message);
                    } else {
                        this.$message.error(message);
                    }
                        this.getRecords();
                    this.showEditDialog = false;
                })
                .catch((error) => {
                    this.$message.error("Error al actualizar la talla");
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },
        clickEdit(recordId) {
            this.recordId = recordId;
            let currentRecord = _.find(this.records, { id: recordId });
            this.currentRecord = _.cloneDeep(currentRecord);
            this.showEditDialog = true;
        },
        customIndex(index) {
            return (
                this.pagination.per_page * (this.pagination.current_page - 1) +
                index +
                1
            );
        },
        getRecords() {
            this.loading_submit = true;

            return this.$http
                .get(`/${this.resource}/records?${this.getQueryParameters()}`)
                .then((response) => {
                    this.records = response.data.data;
                    this.pagination = response.data.meta;
                    this.pagination.per_page = parseInt(
                        response.data.meta.per_page
                    );
                })
                .catch((error) => {})
                .then(() => {
                    this.loading_submit = false;
                });
        },
        getQueryParameters() {
            return queryString.stringify({
                page: this.pagination.current_page,
                limit: this.limit,
                ...this.search,
            });
        },
        changeClearInput() {
            this.search.value = "";
            this.getRecords();
        },
        clickExport() {
            let query = queryString.stringify({
                ...this.search,
            });

            window.open(`/${this.resource}/export?${query}`, "_blank");
        },
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
    },
};
</script>

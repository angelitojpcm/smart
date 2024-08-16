<template>
    <div class="card">
        <div class="card-header">
            <h3 class="my-0">Columnas personalizadas</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Ancho</th>
                            <th class="text-end">Mostrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in records" :key="index">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <label>Nombre</label>
                                <el-input
                                    @input="
                                        updateRecord(row.id, 'name', $event)
                                    "
                                    v-model="row.name"
                                    placeholder="Nombre"
                                ></el-input>
                            </td>
                            <td>
                                <label>Ancho</label>
                                <el-input
                                    type="number"
                                    @input="
                                        updateRecord(row.id, 'width', $event)
                                    "
                                    v-model="row.width"
                                    placeholder="Ancho"
                                ></el-input>
                            </td>
                            <td>
                                <label
                                class="w-100"
                                >Mostrar</label>
                                <el-switch
                                    :disabled="disabledSwitch && !row.is_visible"
                                    v-model="row.is_visible"
                                    active-color="#13ce66"
                                    inactive-color="#ff4949"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="
                                        updateRecord(row.id, 'is_visible', $event)
                                    "
                                ></el-switch>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- <div class="row">
                <div class="col">
                    <button
                        type="button"
                        class="btn btn-custom btn-sm mt-2 mr-2"
                        @click.prevent="clickCreate()"
                    >
                        <i class="fa fa-plus-circle"></i> Nuevo
                    </button>
                </div>
            </div> -->
        </div>
    </div>
</template>

<script>
import { deletable } from "../../../mixins/deletable";

export default {
    mixins: [deletable],
    props: ["typeUser"],
    data() {
        return {
            showDialog: false,
            resource: "document-columns",
            recordId: null,
            records: [],
            timer: null,
        };
    },
    computed: {
        //disabled switch if row.is_visible is false and records has 4 visible columns
        disabledSwitch() {
            return this.records.filter((row) => row.is_visible).length >= 4;
        },

    },
    created() {
        this.$eventHub.$on("reloadData", () => {
            this.getData();
        });
        this.getData();
    },
    methods: {
        checkVisibles(id,field,value){

        },
        async updateRecord(id, field, value) {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(async () => {
                const response = await this.$http.post(`/${this.resource}`, {
                    [field]: value,
                    id,
                });
                if (response.status === 200) {
                    this.$message.success("Registro actualizado");
                }
                this.$eventHub.$emit("reloadData");
            }, 500);
        },
        getData() {
            this.$http.get(`/${this.resource}/records`).then((response) => {
                this.records = response.data;
                console.log("🚀 ~ this.$http.get ~ response:", response);
            });
        },
        clickCreate(recordId = null) {
            this.recordId = recordId;
            this.showDialog = true;
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/${id}`).then(() =>
                this.$eventHub.$emit("reloadData")
            );
        },
    },
};
</script>

<template>
    <el-dialog :visible="showDialog" @open="open" @close="close" :title="title" v-loading="loading">
        <div class="row mt-2">
            <!-- inputs para  fecha de envio, agencia de transporte,  destino, referencia, y la imnagen -->
            <div class="col-sm-6">
                <label for="date">Fecha de envio</label>
                <el-date-picker
                    v-model="form.date"
                    type="date"
                    format="dd/MM/yyyy"
                    value-format="yyyy-MM-dd"
                    placeholder="Seleccione una fecha"
                    style="width: 100%"
                ></el-date-picker>
            </div>
            <div class="col-sm-6">
                <label for="agency"
                    >Agencia de transporte
                    <a href="#" @click.prevent="showAgencyModal = true"
                        ><i class="fas fa-plus"></i>
                        Nuevo
                    </a>
                </label>
                <el-select
                    v-model="form.agency_id"
                    placeholder="Seleccione una agencia"
                >
                    <el-option
                        v-for="item in agencies"
                        :key="item.id"
                        :label="item.description"
                        :value="item.id"
                    ></el-option>
                </el-select>

            </div>
            <div class="col-sm-6">
                <label for="destination">Destino</label>
                <el-input
                    v-model="form.destination"
                    placeholder="Destino"
                ></el-input>
            </div>
            <div class="col-sm-6">
                <label for="reference">Referencia</label>
                <el-input
                    v-model="form.reference"
                    placeholder="Referencia"
                ></el-input>
            </div>
            <div class="col-sm-12">
                <label for="image">Imagen</label>
                <el-upload
                    class="upload-demo"
                    :auto-upload="false"
                    :on-change="handleChange"
                    list-type="picture"
                    action=""
                >
                    <el-button size="small" type="primary"
                        >Seleccionar</el-button
                    >
                    <div slot="tip" class="el-upload__tip">
                        <i class="el-icon-upload"></i>
                        Formatos permitidos: jpg/png
                    </div>
                </el-upload>
            </div>
            <!-- tabla de productos -->
        </div>
        <el-dialog
        append-to-body
        :visible.sync="showAgencyModal"
        title="Nueva agencia de transporte"
        >
        <div class="row mt-2">
            <div class="col-sm-12">
            <label for="name">Nombre</label>
            <el-input v-model="agency.description" placeholder="Nombre"></el-input>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="showAgencyModal = false">Cancelar</el-button>
            <el-button type="primary" @click="createAgency">Guardar</el-button>
        </span>
        </el-dialog>

        <span slot="footer" class="dialog-footer">
            <el-button @click="close">Cancelar</el-button>
            <el-button type="primary" @click="create">Guardar</el-button>
        </span>
    </el-dialog>
</template>

<script>
export default {
    props: ["showDialog", "currentRecord"],
    data() {
        return {
            showAgencyModal:false,
            fileList2: [],
            defaultFileList2: [],
            form: {},
            fullNumber: "",
            title: "",
            agencies: [],
            agency:{},
            loading:false,
        };
    },
    methods: {
        initForm() {
            this.form = {
                agency_id: null,
                date: "",
                agency: "",
                destination: "",
                reference: "",
                image: "",
            };
        },
    handleChange(file, _) {
        let type = file.raw.type;
        const isJPG = type === 'image/jpeg' || type === 'image/png' || type === 'image/jpg';
        if (!isJPG) {
            this.$message.error('Solo se permiten archivos JPG/PNG.');
            return false;
        }
        // Adjuntar la imagen al objeto form
        this.form.image = file;
        console.log("🚀 ~ file: guide_modal.vue:121 ~ beforeUpload ~ this.form:", this.form)
        return true;
    },
    validForm(){
        //validar la fecha, destino e imagen
        let { date, destination, image } = this.form;
        //validar independientemente cada campo
        if (!date) {
            this.$message.error("Debe seleccionar una fecha");
            return false;
        }
        if (!destination) {
            this.$message.error("Debe ingresar un destino");
            return false;
        }
        if (!image) {
            this.$message.error("Debe seleccionar una imagen");
            return false;
        }
        return true;  
    },
    create(){
        if(!this.validForm()){
            return;
        }
        let { date, destination, reference, image } = this.form;
        let formData = new FormData();
        formData.append("date_of_issue", date);
        formData.append("destination", destination);
        if (reference) {
            formData.append("reference", reference);
        }
        formData.append("image", image.raw);
        formData.append("dispatch_order_id", this.currentRecord.id);
        formData.append("full_number", this.fullNumber);
        formData.append("agency_id", this.form.agency_id);
        this.loading = true;
        this.$http.post("/agency/agency-dispatch", formData).then((response) => {
            let { data } = response.data;
            this.$emit("update:showDialog", false);
            this.$emit("update:currentRecord", data);
            this.initForm();
        })
        .catch((error) => {
            console.log(error);
        }).finally(() => {
            this.loading = false;
        });
        
    },

   
        createAgency() {
            this.$http.post("/agency", this.agency).then((response) => {
                console.log("🚀 ~ file: guide_modal.vue:132 ~ this.$http.post ~ response:", response)
                let { data } = response.data;
                this.agencies.push(data);
                this.form.agency_id = data.id;
                this.agency = {};
                this.showAgencyModal = false;
            })
            .catch((error) => {
                console.log(error);
            })
            ;
        },
        getAngencies() {
            this.$http("/agency/records").then((response) => {
                let data = response.data;
                console.log("🚀 ~ file: guide_modal.vue:144 ~ this.$http ~ data:", data)
                this.agencies = data;
            });
        },
        open() {
            this.getAngencies();
            let { full_number } = this.currentRecord;
            this.fullNumber = full_number;
            this.title = `Guía de traslado - ${full_number}`;
        },
        close() {
            this.$emit("update:showDialog", false);
        },
    },
};
</script>

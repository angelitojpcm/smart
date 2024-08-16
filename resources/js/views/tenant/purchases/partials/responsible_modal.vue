<template>
    <el-dialog
        :close-on-click-modal="false"
        :title="titleDialog"
        :visible="showDialog"
        :append-to-body="true"
        @close="close"
        @open="create"
        @opened="opened"
    >
        <form autocomplete="off" @submit.prevent="submit" top="2vh">
            <div class="form-body">
                <span slot="label">{{ titleTabDialog }}</span>
                <div class="row">
                    <div class="col-md-6">
                        <div
                            :class="{
                                'has-danger': errors.identity_document_type_id,
                            }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Tipo Doc. Identidad
                                <span class="text-danger">*</span></label
                            >
                            <el-select
                                v-model="form.identity_document_type_id"
                                dusk="identity_document_type_id"
                                filterable
                                popper-class="el-select-identity_document_type"
                                @change="changeIdentityDocType"
                            >
                                <el-option
                                    v-for="option in identity_document_types"
                                    :key="option.id"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <small
                                v-if="errors.identity_document_type_id"
                                class="text-danger"
                                v-text="errors.identity_document_type_id[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.number }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Número
                                <span class="text-danger">*</span></label
                            >

                            <div v-if="api_service_token != false">
                                <x-input-service
                                    v-model="form.number"
                                    :identity_document_type_id="
                                        form.identity_document_type_id
                                    "
                                    @search="searchNumber"
                                ></x-input-service>
                            </div>
                            <div v-else>
                                <el-input
                                    v-model="form.number"
                                    :maxlength="maxLength"
                                    dusk="number"
                                >
                                    <template
                                        v-if="
                                            form.identity_document_type_id ===
                                                '6' ||
                                            form.identity_document_type_id ===
                                                '1'
                                        "
                                    >
                                        <el-button
                                            slot="append"
                                            :loading="loading_search"
                                            icon="el-icon-search"
                                            type="primary"
                                            @click.prevent="searchCustomer"
                                        >
                                            <template
                                                v-if="
                                                    form.identity_document_type_id ===
                                                    '6'
                                                "
                                            >
                                                SUNAT
                                            </template>
                                            <template
                                                v-if="
                                                    form.identity_document_type_id ===
                                                    '1'
                                                "
                                            >
                                                RENIEC
                                            </template>
                                        </el-button>
                                    </template>
                                </el-input>
                            </div>

                            <small
                                v-if="errors.number"
                                class="text-danger"
                                v-text="errors.number[0]"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div
                            :class="{ 'has-danger': errors.name }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Nombre
                                <span class="text-danger">*</span></label
                            >
                            <el-input
                                ref="name"
                                v-model="form.name"
                                dusk="name"
                            ></el-input>
                            <small
                                v-if="errors.name"
                                class="text-danger"
                                v-text="errors.name[0]"
                            ></small>
                        </div>
                    </div>
                  <div
                        class="col-md-6"
                    >
                        <div
                            :class="{ 'has-danger': errors.telephone }"
                            class="form-group"
                        >
                            <label class="control-label">Teléfono</label>
                            <el-input
                                v-model="form.telephone"
                                dusk="telephone"
                            ></el-input>
                            <small
                                v-if="errors.telephone"
                                class="text-danger"
                                v-text="errors.telephone[0]"
                            ></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                 

                    <div v-if="form.state" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"
                                >Estado del Contribuyente</label
                            >
                            <template v-if="form.state == 'ACTIVO'">
                                <el-alert
                                    :closable="false"
                                    :title="`${form.state}`"
                                    show-icon
                                    type="success"
                                ></el-alert>
                            </template>
                            <template v-else>
                                <el-alert
                                    :closable="false"
                                    :title="`${form.state}`"
                                    show-icon
                                    type="error"
                                ></el-alert>
                            </template>
                        </div>
                    </div>
                    <div v-if="form.condition" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"
                                >Condición del Contribuyente</label
                            >
                            <template v-if="form.condition == 'HABIDO'">
                                <el-alert
                                    :closable="false"
                                    :title="`${form.condition}`"
                                    show-icon
                                    type="success"
                                ></el-alert>
                            </template>
                            <template v-else>
                                <el-alert
                                    :closable="false"
                                    :title="`${form.condition}`"
                                    show-icon
                                    type="error"
                                ></el-alert>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-end mt-4">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <el-button @click.prevent="close()">Cancelar</el-button>
                        <el-button
                            :loading="loading_submit"
                            native-type="submit"
                            type="primary"
                            >Guardar</el-button
                        >
                    </div>
                </div>
            </div>
            <ubigeo-form
                :departments="all_departments"
                :provinces="all_provinces"
                :districts="all_districts"
                :showDialog.sync="showDialogUbigeo"
                @locations="setLocations"
            ></ubigeo-form>
        </form>
    </el-dialog>
</template>

<script>
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import { serviceNumber } from "../../../../mixins/functions";
import UbigeoForm from "../../../../components/Ubigeo.vue";

export default {
    components: { UbigeoForm },
    mixins: [serviceNumber],
    props: [
        "isDriver",
        "showDialog",
        "type",
        "recordId",
        "external",
        "document_type_id",
        "input_person",
        "parentId",
        "api_token",
    ],
    data() {
        return {
            headers: headers_token,
            form_zone: { add: false, name: null, id: null },
            parent: null,
            loading_submit: false,
            titleDialog: null,
            titleTabDialog: null,
            typeDialog: null,
            showDialogName: false,
            resource: "purchases-responsible",
            api_service_token: false,
            errors: {},
            form: {
                optional_email: [],
            },
            temp_optional_email: [],
            temp_email: null,
            countries: [],
            zones: [],
            sellers: [],
            all_departments: [],
            all_provinces: [],
            all_districts: [],
            provinces: [],
            districts: [],
            locations: [],
            person_types: [],
            identity_document_types: [],
            discount_types: [],
            activeName: "first",
            showDialogUbigeo: false,
            last_no_document: null,
        };
    },
    async created() {
        this.loadConfiguration();

        await this.initForm();
        await this.getTables();
    },
    computed: {
        ...mapState(["config", "person", "parentPerson"]),
        maxLength: function () {
            if (this.form.identity_document_type_id === "6") {
                return 11;
            }
            if (this.form.identity_document_type_id === "1") {
                return 8;
            }
        },
    },
    methods: {
        async getTables() {
            await this.$http
                .get(`/persons/tables`)
                .then((response) => {
                    this.api_service_token = response.data.api_service_token;
                    // console.log(this.api_service_token)

                    this.countries = response.data.countries;
                    this.zones = response.data.zones;
                    this.sellers = response.data.sellers;
                    this.all_departments = response.data.departments;
                    this.all_provinces = response.data.provinces;
                    this.all_districts = response.data.districts;
                    this.identity_document_types =
                        response.data.identity_document_types;
                    this.locations = response.data.locations;
                    this.person_types = response.data.person_types;
                    this.discount_types = response.data.discount_types;
                })
                .finally(() => {
                    if (this.api_service_token === false) {
                        if (this.config.api_service_token !== undefined) {
                            this.api_service_token =
                                this.config.api_service_token;
                        }
                    }
                });
        },
        search_names() {
            this.showDialogName = true;
        },
        setLocations(locations) {
            this.locations = locations.locations;
            this.form.location_id = locations.id;
        },
        openDialogUbigeo() {
            this.showDialogUbigeo = true;
        },
        onUploadSuccess(response, file, fileList) {
            if (response.success) {
                this.form.photo_filename = response.data.filename;
                this.form.photo_temp_image = response.data.temp_image;
                this.form.photo_temp_path = response.data.temp_path;
            } else {
                this.$message.error(response.message);
            }
        },
        ...mapActions(["loadConfiguration"]),
        initForm() {
            let { package_handlers } = this.config;

            this.errors = {};
            this.form = {
                dispatch_addresses: [],
                id: null,
                type: this.type,
                credit_days: 0,
                identity_document_type_id: "1",
                number: "",
                name: null,
                trade_name: null,
                country_id: "PE",
                nationality_id: "PE",
                location_id: [],
                // department_id: null,
                // province_id: null,
                // district_id: null,
                address: null,
                telephone: null,
                condition: null,
                state: null,
                email: null,
                perception_agent: false,
                percentage_perception: 0,
                person_type_id: null,
                comment: null,
                addresses: [],
                contact: {
                    full_name: null,
                    phone: null,
                },
                optional_email: [],
                has_discount: false,
                discount_type: "01",
                discount_amount: 0,
                photo_filename: null,
                photo_temp_image: null,
                photo_temp_path: null,
            };
            this.updateEmail();
        },
        async opened() {
            if (this.external && this.input_person) {
                if (
                    this.form.number.length === 8 ||
                    this.form.number.length === 11
                ) {
                    if (this.api_service_token != false) {
                        await this.$eventHub.$emit("enableClickSearch");
                    } else {
                        this.searchCustomer();
                    }
                }
            }
        },
       
        async create() {
            // this.getTables();
            // console.log(this.input_person)
            this.parent = 0;
            if (this.parentId !== undefined) {
                this.parent = this.parentId;
            }
            /*

            'person',
            'parentPerson',
            */
            if (this.external) {
                if (this.document_type_id === "01") {
                    this.form.identity_document_type_id = "6";
                }
                if (this.document_type_id === "03") {
                    this.form.identity_document_type_id = "1";
                }

                if (this.input_person) {
                    this.form.identity_document_type_id = this.input_person
                        .identity_document_type_id
                        ? this.input_person.identity_document_type_id
                        : this.form.identity_document_type_id;
                    this.form.number = this.input_person.number
                        ? this.input_person.number
                        : "";
                }
            }
           this.titleDialog = "Registrar responsable"

            if (this.recordId) {
                this.$http
                    .get(`/${this.resource}/record/${this.recordId}`)
                    .then((response) => {
                        this.form = response.data.data;
                        if (response.data.data.contact == null) {
                            this.form.contact = {
                                full_name: null,
                                phone: null,
                            };
                        }
                        this.filterProvinces();
                        this.filterDistricts();
                    })
                    .then(() => {
                        this.updateEmail();
                    });
            }
            let { package_handlers } = this.config;
         
        },
        clickDispatchAddAddress() {
            this.form.dispatch_addresses.push({
                id: null,
                address: null,
                location_id: [],
            });
        },
        clickAddAddress() {
            /* this.form.more_address.push({
                 location_id: [],
                 address: null,
             })*/

            this.form.addresses.push({
                id: null,
                country_id: "PE",
                location_id: [],
                address: null,
                email: null,
                phone: null,
                main: false,
            });
        },
        validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        },
        updateEmail() {
            this.temp_optional_email = this.form.optional_email;
        },
        removeEmail(email) {
            if (this.form.optional_email === undefined)
                this.form.optional_email = [];
            this.form.optional_email = this.form.optional_email.filter(
                function (item) {
                    return item.email !== email.email;
                }
            );

            this.updateEmail();
        },
        checkEmail() {
            this.errors.temp_email = null;
            if (this.temp_email === null) {
                return false;
            }
            if (this.temp_email === undefined) {
                return false;
            }
            let email = this.temp_email;

            if (this.validateEmail(email)) {
                if (this.form.optional_email !== undefined) {
                    let tem = _.find(this.form.optional_email, {
                        email: email,
                    });
                    if (tem === undefined) {
                        return true;
                    } else {
                        // this.errors.temp_email = "Correo ya registrado"
                    }
                }
            } else {
                // this.errors.temp_email = "No es un correo valido"
            }
            return false;
        },
        clickAddMail() {
            if (this.form.optional_email === undefined)
                this.form.optional_email = [];
            if (this.checkEmail() === true) {
                let email = this.temp_email;
                this.form.optional_email.push({ email: email });
                this.updateEmail();
                this.temp_email = null;
            }
        },
        validateDigits() {
            const pattern_number = new RegExp("^[0-9]+$", "i");

            if (this.form.identity_document_type_id === "6") {
                if (this.form.number.length !== 11) {
                    return {
                        success: false,
                        message: `El campo número debe tener 11 dígitos.`,
                    };
                }

                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`,
                    };
                }
            }

            if (this.form.identity_document_type_id === "1") {
                if (this.form.number.length !== 8) {
                    return {
                        success: false,
                        message: `El campo número debe tener 8 dígitos.`,
                    };
                }

                if (!pattern_number.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número debe contener solo números`,
                    };
                }
            }

            if (["4", "7", "0"].includes(this.form.identity_document_type_id)) {
                const pattern = new RegExp("^[A-Z0-9\-]+$", "i");

                if (!pattern.test(this.form.number)) {
                    return {
                        success: false,
                        message: `El campo número no cumple con el formato establecido`,
                    };
                }
            }

            return {
                success: true,
            };
        },
        driverValid() {
            let { barcode, internal_code } = this.form;
            if (!barcode) {
                this.$message.error("Ingrese un vehículo");
                return false;
            }
            if (!internal_code) {
                this.$message.error("Ingrese una placa");
                return false;
            }
            return true;
        },
        async submit() {
            if (this.isDriver) {
                if (!this.driverValid()) {
                    return;
                }
                this.form.is_driver = true;
            }
            // if(this.form.address=="" || this.form.address==null){
            //     return this.$message.error("Ingrese una dirección correcta");
            // }

            let val_digits = await this.validateDigits();
            if (!val_digits.success) {
                return this.$message.error(val_digits.message);
            }

            if (!this.config.enable_discount_by_customer) {
                this.form.has_discount = false;
            }

            if (!this.form.has_discount) {
                this.form.discount_type = "01";
                this.form.discount_amount = 0;
            }

            this.loading_submit = true;
            this.form.parent_id = parseInt(this.parent);
            await this.$http
                .post(`/${this.resource}`, this.form)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        if (this.external) {
                            this.$eventHub.$emit(
                                "reloadDataPersons",
                                response.data.id
                            );
                        } else {
                            this.$eventHub.$emit("reloadData");
                        }
                        this.close();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.log(error);
                    }
                })
                .finally(() => {
                    this.loading_submit = false;
                });
        },
        selectRows(data) {
            this.form.identity_document_type_id = "1";
            this.form.number = data.numero;
            this.form.name =
                data.nombres +
                " " +
                data.apellido_paterno +
                " " +
                data.apellido_materno;
            this.form.address = data.domicilio;
            this.form.location_id = data.location_id;
            console.log("data.location_id", data.location_id);
        },
        changeIdentityDocType() {
            this.recordId == null ? this.setDataDefaultCustomer() : null;
        },
        setDataDefaultCustomer() {
            if (this.form.identity_document_type_id == "0") {
                this.form.number = "99999999";
                this.form.name = "Clientes - Varios";
            } else {
                this.form.number = "";
                this.form.name = null;
            }
        },
        close() {
            this.$eventHub.$emit("initInputPerson");
            this.$emit("update:showDialog", false);
            this.initForm();
        },
        searchCustomer() {
            this.searchServiceNumberByType();
        },
        searchNumber(data) {
            //cambios apiperu
            this.form.name = data.name;
            this.form.trade_name = data.trade_name;
            this.form.location_id = data.location_id;
            console.log("data.location_id", data);
            this.form.address =
                data.address == "" || data.address == null ? "-" : data.address;
            // this.form.department_id = data.department_id;
            // this.form.department_id = data.department_id;
            // this.form.province_id = data.province_id;
            // this.form.district_id = data.district_id;
            this.form.condition = data.condition;
            this.form.state = data.state;
            // this.filterProvinces()
            // this.filterDistricts()
            //                this.form.addresses[0].telephone = data.telefono;
        },
        clickRemoveAddress(index) {
            this.form.addresses.splice(index, 1);
        },

        saveZone() {
            this.form_zone.add = false;

            this.$http
                .post(`/zones`, this.form_zone)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.zones.push(response.data.data);
                        this.form_zone.name = null;
                    } else {
                        this.$message.error("No se guardaron los cambios");
                    }
                })
                .catch((error) => {});
        },
    },
};
</script>

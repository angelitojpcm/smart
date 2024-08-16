<template>
    <div>
        <div class="row" v-if="!loged">
            <h1>Multi Empresa</h1>
            <div class="col-md-6 col-lg-6 col-12">
                <label for="password">Contraseña de distribuidor</label>
                <input
                    type="password"
                    class="form-control"
                    v-model="password"
                    id="password"
                    placeholder="Contraseña"
                />
                <button class="btn btn-primary mt-2" @click="logIn">
                    Ingresar
                </button>
            </div>
          
        </div>
        <div v-else v-loading="loading">
            <h1>Seleccionar empresas</h1>
            <div class="row align-items-end">
                <div class="col-md-6 col-lg-6 col-12">
                    <label for="search">Buscar</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="search"
                        @input="filterCompanies"
                        id="search"
                        placeholder="Nombre de empresa"
                    />
                </div>
                  <div class="col-md-2 col-lg-2 col-12 text-left">
                <el-checkbox
                    v-model="configuration.multi_companies"
                    @change="saveConfiguration"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                >
                    Activar modo multiempresas
                </el-checkbox>
            </div>
            </div>
            <div class="row">
                <div
                    v-for="(company, index) in companies"
                    class="col-md-2 col-lg-2 col-12 card m-2"
                    :class="`bg-${
                        isSelected(company)
                            ? 'primary text-white'
                            : 'light border border-primary'
                    }`"
                    role="button"
                    :key="index"
                    @click="saveCompanies(company.id)"
                >
                    <div class="card-body">
                        <span>{{ company.name.toUpperCase() }}</span> <br>
                        <small>{{ company.number }}</small>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    // props: ["configuration"],
    data() {
        return {
            resource: "multi-companies",
            password: "",
            loged: false,
            companies: [],
            allCompanies: [],
            selectCompanies: [],
            loading: false,
            search: "",
            configuration: {},
        };
    },
    methods: {
        filterCompanies() {
            this.companies = this.allCompanies.filter((c) => {
                return c.name.toLowerCase().includes(this.search.toLowerCase());
            });
        },
        isSelected(company) {
            return this.selectCompanies.includes(company.id);
        },
        async saveConfiguration() {
            try {
                this.loading = true;
                const response = await this.$http.post(
                    `${this.resource}/save-configuration`,
                    {
                        configuration: this.configuration,
                    }
                );
                console.log(
                    "🚀 ~ file: index.vue:75 ~ saveCompanies ~ response:",
                    response
                );
            } catch (e) {
            } finally {
                this.loading = false;
            }
        },
        async logIn() {
            const response = await this.$http.post(`${this.resource}/login`, {
                password: this.password,
            });
            if (response.status == 200) {
                let { data } = response;
                console.log("🚀 ~ file: index.vue:54 ~ logIn ~ data:", data);
                let { success, companies, selectCompanies,configuration } = data;
                this.loged = success;
                this.allCompanies = companies;
                this.companies = this.allCompanies;
                this.selectCompanies = selectCompanies;
                this.configuration = configuration;
                // this.selectCompanies = companies.map((c) => c.id);
            }
        },
        async saveCompanies(company_id) {
            try {
                this.loading = true;
                if (this.selectCompanies.includes(company_id)) {
                    this.selectCompanies = this.selectCompanies.filter(
                        (c) => c != company_id
                    );
                } else {
                    this.selectCompanies.push(company_id);
                }
                const response = await this.$http.post(
                    `${this.resource}/save-companies`,
                    {
                        companies: this.selectCompanies,
                    }
                );
                console.log(
                    "🚀 ~ file: index.vue:75 ~ saveCompanies ~ response:",
                    response
                );
            } catch (e) {
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

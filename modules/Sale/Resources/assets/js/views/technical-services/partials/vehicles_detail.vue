<template>
    <div class="row">
        <el-tabs
            v-model="activePanelVehicle"
            @tab-click="handleClickVehicle"
            tab-position="left"
            :style="`height: ${height}px`"
        >
            <el-tab-pane class="mb-3" name="principal">
                <span slot="label">Principal</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Placa </label>
                        <el-input
                            type="text"
                            v-model="vehicle.plate_number"
                        ></el-input>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Nro Flota / Kilometraje </label>
                        <el-input type="text" v-model="vehicle.km"></el-input>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Color </label>
                        <el-input
                            type="text"
                            v-model="vehicle.color"
                        ></el-input>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Año </label>
                        <el-input type="text" v-model="vehicle.year"></el-input>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Chasis / VIN </label>
                        <el-input
                            type="text"
                            v-model="vehicle.chasis"
                        ></el-input>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Vencimiento SOAT </label>
                        <el-date-picker
                            v-model="vehicle.date_soat_due"
                            type="date"
                            placeholder="Seleccione una fecha"
                            value-format="yyyy-MM-dd"
                            format="yyyy-MM-dd"
                            style="width: 100%"
                        ></el-date-picker>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Vencimiento Revisión Tec </label>
                        <el-date-picker
                            v-model="vehicle.date_check_due"
                            type="date"
                            placeholder="Seleccione una fecha"
                            value-format="yyyy-MM-dd"
                            format="yyyy-MM-dd"
                            style="width: 100%"
                        ></el-date-picker>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="accesories">
                <span slot="label">Accesorios externos</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Faros delanteros </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_front_lights"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_front_lights"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Luces direccionales delanteras </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_directional_lights_front"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_directional_lights_front"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Luces direccionales posteriores </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_directional_lights_back"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado </label>
                        <el-radio-group
                            v-model="vehicle.state_directional_lights_back"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Luces de peligro </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_hazard_lights"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_hazard_lights"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Brazo plumilla limpia parabrizas</label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_wiper_washer_arm"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_wiper_washer_arm"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="">Tapa gasolina</label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_gasoil_cap"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_gasoil_cap"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Antena Radio </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_radio_antenna"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_radio_antenna"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="accesories2">
                <span slot="label">Accesorios externos 2</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Espejos laterales </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_side_mirrors"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_side_mirrors"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Manijas de prueba </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_test_handles"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_test_handles"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Alarma </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_alarm"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_alarm"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Escarpines </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_booties"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_booties"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Llanta y aro de repuesto </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_spare_tire"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_spare_tire"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Dado segruo de ruedas </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_wheel_nut"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_wheel_nut"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Copa de aro </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_wheel_cup"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_wheel_cup"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="accesories_internals">
                <span slot="label">Accesorios internos</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Ceniceros </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_ashtray"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_ashtray"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Espejo retrovisor interno </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_internal_rearview_mirror"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_internal_rearview_mirror"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Auto radio </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_car_radio"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_car_radio"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Alfombra de protección </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_protection_mat"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_protection_mat"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Pisos de jebe </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_rubber_floors"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_rubber_floors"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Posa vaso </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_cup_holder"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_cup_holder"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Llave de vehiculo </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_vehicle_key"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_vehicle_key"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                </div>
            </el-tab-pane>

            <el-tab-pane class="mb-3" name="tools">
                <span slot="label">Herramientas</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Extintor </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_extinguisher"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_extinguisher"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Gata y palanca </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_jack_lever"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_jack_lever"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Estuche de herramientas </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_toolkit"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_toolkit"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="documents">
                <span slot="label">Documentos del vehiculo</span>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Tarjeta de propiedad </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_property_card"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_property_card"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Cuaderno de bitacora </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_logbook"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_logbook"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Manual de propietario </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_owner_manual"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_owner_manual"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for=""> Porta documentos </label>
                        <el-input-number
                            class="w-100"
                            v-model="vehicle.quantity_document_holder"
                        ></el-input-number>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <label for="" class="w-100">Estado</label>
                        <el-radio-group
                            v-model="vehicle.state_document_holder"
                            size="mini"
                        >
                            <el-radio-button
                                class="w-100"
                                v-for="(state, idx) in states"
                                :key="idx"
                                :label="state.name"
                            ></el-radio-button>
                        </el-radio-group>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="auths">
                <span slot="label">Autorizaciones</span>
                <div class="row">
                    <div
                        class="col-12 d-flex flex-column justify-content-center align-items-center mt-2"
                    >
                        <label class="text-center">
                            Autorizo el ingreso de mi vehiculo a la cochera
                            interna del taller
                        </label>
                        <el-checkbox v-model="vehicle.auth_drive">
                            Acepta
                        </el-checkbox>
                    </div>
                    <el-divider></el-divider>
                    <div
                        class="col-12 d-flex flex-column justify-content-center align-items-center mt-2"
                    >
                        <label class="text-center">
                            Autorizo enviar mi vehiculo para trabajos de
                            terceros en talleres de su elección
                        </label>
                        <el-checkbox v-model="vehicle.move_on">
                            Acepta
                        </el-checkbox>
                    </div>
                    <el-divider></el-divider>
                    <div
                        class="col-12 d-flex flex-column justify-content-center align-items-center mt-2"
                    >
                        <label class="text-center">
                            Declaro que no existen elementos de valor dentro del
                            vehiculo
                        </label>
                        <el-checkbox v-model="vehicle.no_value_things">
                            Acepta
                        </el-checkbox>
                    </div>
                    <el-divider></el-divider>
                    <div
                        class="col-12 d-flex flex-column justify-content-center align-items-center"
                    >
                        <label class="text-center">
                            Acepto retirar mi vehiculo en un máximo de 3 días,
                            luego de finalizado el servicio; caso contrario
                            asumiré un costo de S/ 7.00 diarios de cochera
                            (interna y/o externa)
                        </label>
                        <el-checkbox v-model="vehicle.cost_for_days">
                            Acepta
                        </el-checkbox>
                    </div>
                </div>
            </el-tab-pane>
            <el-tab-pane class="mb-3" name="obs">
                <span slot="label">Gasolina / Observaciones</span>
                <div class="row">
                    <div class="col-12">
                        <label for=""> Gasolina </label>
                        <el-slider
                            class="p-2"
                            v-model="vehicle.gasoline_level"
                            :step="5"
                            :min="0"
                            :max="100"
                            show-stops
                        >
                        </el-slider>
                    </div>
                    <div class="col-12">
                        <label for=""> Observaciones </label>
                        <el-input
                            type="textarea"
                            rows="4"
                            v-model="vehicle.observations"
                        ></el-input>
                    </div>
                </div>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
export default {
    props: {
        vehicle: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            height: 400,
            states: [
                { id: 1, name: "Bueno" },
                { id: 2, name: "Regular" },
                { id: 3, name: "Malo" },
            ],
            activePanelVehicle: "principal",
        };
    },
    methods: {
        handleClickVehicle(tab, event) {
            this.activePanelVehicle = tab.name;
            // if (this.activePanelVehicle == "accesories") {
            //     this.height = 500;
            // } else {
            //     this.height = 200;
            // }
        },
    },
};
</script>

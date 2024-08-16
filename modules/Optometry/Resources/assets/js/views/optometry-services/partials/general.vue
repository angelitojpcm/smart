<template>
    <div class="row">
        <div class="row m-2">
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="5">PRUEBAS PRELIMINARES</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>AV s/c</th>
                        <th>AV c/c</th>
                        <th>dnp</th>
                        <th>Kx</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="10%">OD</td>
                        <td>
                            <input
                                v-model="general.od_av_sc"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_av_cc"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_dnp"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_kx"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>OI</td>
                        <td>
                            <input
                                v-model="general.oi_av_sc"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_av_cc"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_dnp"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_kx"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th></th>
                        <th>Cover test</th>
                        <th>PPC</th>
                        <th>Motilidad</th>
                        <th>Reacción pupilar</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Alterado</td>
                        <td>
                            <input
                                v-model="general.cover_test"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input v-model="general.ppc" type="checkbox" />
                        </td>
                        <td>
                            <input
                                v-model="general.motilidad"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.reaccion_pupilar"
                                type="checkbox"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <thead>
                    <tr>
                        <th colspan="6">REFRACCIÓN</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>ESF</th>
                        <th>CIL</th>
                        <th>EJE</th>
                        <th>AD</th>
                        <th>AV</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="10%">OD</td>
                        <td>
                            <input
                                v-model="general.od_esf"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_cil"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_eje"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_ad"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.od_av"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td>OI</td>
                        <td>
                            <input
                                v-model="general.oi_esf"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_cil"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_eje"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_ad"
                                type="text"
                                class="form-control"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.oi_av"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="3">PRUEBAS COMPLEMENTARIAS</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Alterado</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="20%">Test de Worth</td>
                        <td width="10%">
                            <input
                                v-model="general.test_worth"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.test_worth_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">Test de Schober</td>
                        <td width="10%">
                            <input
                                v-model="general.test_schober"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.test_schober_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">Prueba del color</td>
                        <td width="10%">
                            <input
                                v-model="general.test_color"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.test_color_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">Rejilla Amster</td>
                        <td width="10%">
                            <input v-model="general.amster" type="checkbox" />
                        </td>
                        <td>
                            <input
                                v-model="general.amster_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">T. de Medios</td>
                        <td width="10%">
                            <input
                                v-model="general.test_medias"
                                type="checkbox"
                            />
                        </td>
                        <td>
                            <input
                                v-model="general.test_medias_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">Retina</td>
                        <td width="10%">
                            <input v-model="general.retina" type="checkbox" />
                        </td>
                        <td>
                            <input
                                v-model="general.retina_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">PIO</td>
                        <td width="10%">
                            <input v-model="general.pio" type="checkbox" />
                        </td>
                        <td>
                            <input
                                v-model="general.pio_description"
                                type="text"
                                class="form-control"
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ["general"],
    data() {
        return {
            title: null,
            showDialog: false,
            showDialogOptions: false,
            resource: "optometry-services",
            recordId: null,
            showDialogPayments: false,
        };
    },
};
</script>

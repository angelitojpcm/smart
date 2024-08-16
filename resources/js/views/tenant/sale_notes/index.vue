<template>
    <div v-loading="loading">
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Notas de Venta</span></li>
            </ol>
            <div class="right-wrapper pull-right" v-if="!isCommercial">
                <a
                    href="#"
                    @click.prevent="clickCreate()"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    ><i class="fa fa-plus-circle"></i> Nuevo</a
                >
                <a
                    href="#"
                    @click.prevent="onOpenModalGenerateCPE"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    >Generar comprobante desde múltiples Notas</a
                >
                <a
                    href="#"
                    @click.prevent="onOpenModalGenerateGuie"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    >Generar guía desde múltiples Notas</a
                >
                <a
                    href="#"
                    v-if="config.send_data_to_other_server === true"
                    @click.prevent="onOpenModalMigrateNv"
                    class="btn btn-custom btn-sm mt-2 mr-2"
                    >Migrar Datos</a
                >
            </div>
        </div>
        <div class="card mb-0">
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
                    :isIntegrateSystem="isIntegrateSystem"
                >
                    <tr slot="heading">
                        <th>#</th>

                        <th class="text-center">Fecha Emisión</th>
                        <th
                            class="text-center"
                            v-if="columns.date_payment.visible"
                        >
                            Fecha de pago
                        </th>
                        <th v-if="configuration.multi_companies">Empresa</th>
                        <th>Cliente</th>
                        <th class="text-end" v-if="columns.seller_name.visible">
                            Vendedor
                        </th>
                        <th>Nota de Venta</th>
                        <th>Estado</th>
                        <th
                            class="text-end"
                            v-if="columns.exchange_rate_sale.visible"
                        >
                            T.C.
                        </th>
                        <th class="text-center">Moneda</th>
                        <th class="text-end" v-if="columns.due_date.visible">
                            F. Vencimiento
                        </th>
                        <th
                            class="text-end"
                            v-if="columns.total_exportation.visible"
                        >
                            T.Exportación
                        </th>
                        <th class="text-end" v-if="columns.total_free.visible">
                            T.Gratuito
                        </th>
                        <th
                            class="text-end"
                            v-if="columns.total_unaffected.visible"
                        >
                            T.Inafecta
                        </th>
                        <th
                            class="text-end"
                            v-if="columns.total_exonerated.visible"
                        >
                            T.Exonerado
                        </th>
                        <th class="text-end" v-if="columns.total_taxed.visible">
                            T.Gravado
                        </th>
                        <th class="text-end" v-if="columns.total_igv.visible">
                            T.Igv
                        </th>
                        <th class="text-end">Total</th>

                        <th
                            class="text-center"
                            v-if="columns.total_paid.visible"
                        >
                            Pagado
                        </th>
                        <th
                            class="text-center"
                            v-if="columns.total_pending_paid.visible"
                        >
                            Por pagar
                        </th>

                        <th class="text-center" v-if="!isIntegrateSystem">
                            Comprobantes
                        </th>
                        <th class="text-center" v-if="isIntegrateSystem">
                            Condición de Pago
                        </th>
                        <th class="text-center">Estado pago</th>
                        <th v-if="isIntegrateSystem" class="text-center">
                            Confirmación de pago
                        </th>
                        <th v-else class="text-center">Orden de compra</th>

                        <th class="text-center">Pagos</th>
                        <th class="text-center">Descarga</th>
                        <template v-if="isIntegrateSystem">
                            <th class="text-center">Encargado</th>
                            <th class="text-center">Producción</th>
                            <th class="text-center">Encargado</th>
                            <th class="text-center">Logistica</th>
                            <th class="text-center">Guia de traslado</th>
                            <th class="text-center">G. remisión</th>
                            <th class="text-center">Comprobantes</th>
                        </template>
                        <th
                            class="text-center"
                            v-if="columns.recurrence.visible"
                        >
                            Recurrencia
                        </th>
                        <th class="text-end" v-if="columns.region.visible">
                            Region
                        </th>
                        <th
                            class="text-center"
                            v-if="columns.type_period.visible"
                        >
                            Tipo Periodo
                        </th>
                        <th
                            class="text-center"
                            v-if="columns.quantity_period.visible"
                        >
                            Cantidad Periodo
                        </th>

                        <th class="text-center" v-if="columns.paid.visible">
                            Estado de Pago
                        </th>
                        <th
                            class="text-center"
                            v-if="columns.license_plate.visible"
                        >
                            Placa
                        </th>
                        <th
                            class="text-center"
                            v-if="columns.observation.visible"
                        >
                            Observación
                        </th>
                        <th class="text-end">Acciones</th>
                        <th v-if="configuration.college">Periodo</th>
                    </tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>

                        <td class="text-center">{{ row.date_of_issue }}</td>
                        <td
                            class="text-center"
                            v-if="columns.date_payment.visible"
                        >
                            {{ row.date_of_payment }}
                        </td>
                        <td v-if="configuration.multi_companies">
                            <template
                                v-if="
                                    row.alter_company &&
                                    row.alter_company.name &&
                                    row.alter_company.number
                                "
                            >
                                <strong>
                                    {{ row.alter_company.name.toUpperCase() }}
                                </strong>
                                <br />
                                <small>
                                    {{ row.alter_company.number }}
                                </small>
                            </template>
                        </td>
                        <td>
                            {{ row.customer_name }}<br /><small
                                v-text="row.customer_number"
                            ></small>
                        </td>
                        <td class="text-end" v-if="columns.seller_name.visible">
                            {{ row.seller_name }}
                        </td>
                        <td>{{ row.full_number }}</td>
                        <td>{{ row.state_type_description }}</td>
                        <td
                            class="text-center"
                            v-if="columns.exchange_rate_sale.visible"
                        >
                            {{ row.exchange_rate_sale }}
                        </td>
                        <td class="text-center">{{ row.currency_type_id }}</td>

                        <td class="text-end" v-if="columns.due_date.visible">
                            {{ row.due_date }}
                        </td>
                        <td
                            class="text-end"
                            v-if="columns.total_exportation.visible"
                        >
                            {{ row.total_exportation }}
                        </td>
                        <td class="text-end" v-if="columns.total_free.visible">
                            {{ row.total_free }}
                        </td>
                        <td
                            class="text-end"
                            v-if="columns.total_unaffected.visible"
                        >
                            {{ row.total_unaffected }}
                        </td>
                        <td
                            class="text-end"
                            v-if="columns.total_exonerated.visible"
                        >
                            {{ row.total_exonerated }}
                        </td>

                        <td class="text-end" v-if="columns.total_taxed.visible">
                            {{ row.total_taxed }}
                        </td>
                        <td class="text-end" v-if="columns.total_igv.visible">
                            {{ row.total_igv }}
                        </td>
                        <td class="text-end">{{ row.total }}</td>
                        <td class="text-center" v-if="isIntegrateSystem">
                            <span
                                v-for="(
                                    payment_method, i
                                ) in row.payments_methods"
                                :key="i"
                                class="badge bg-primary text-white me-1"
                            >
                                {{ payment_method }}
                            </span>
                        </td>
                        <td
                            class="text-center"
                            v-if="columns.total_paid.visible"
                        >
                            {{ row.total_paid }}
                        </td>
                        <td
                            class="text-center"
                            v-if="columns.total_pending_paid.visible"
                        >
                            {{ row.total_pending_paid }}
                        </td>

                        <td v-if="!isIntegrateSystem">
                            <template v-for="(document, i) in row.documents">
                                <label
                                    :key="i"
                                    v-text="document.number_full"
                                    class="d-block"
                                ></label>
                            </template>
                        </td>
                        <td class="text-center">
                            <template v-if="row.state_type_id === '11'">
                                <span class="badge text-white bg-danger">{{
                                    row.state_type_description
                                }}</span>
                            </template>
                            <template v-else>
                                <span
                                    class="badge text-white"
                                    :class="{
                                        'bg-success': row.total_canceled,
                                        'bg-warning': !row.total_canceled,
                                    }"
                                    >{{
                                        row.total_canceled
                                            ? "Pagado"
                                            : "Pendiente"
                                    }}</span
                                >
                            </template>
                        </td>

                        <td v-if="isIntegrateSystem">
                            <template v-if="isCommercial">
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
                                </el-button></template
                            >
                            <template v-else>
                                <el-dropdown
                                    trigger="click"
                                    @command="changeStatePayment"
                                >
                                    <el-button
                                        :class="`state_${row.state_payment_id}_py`"
                                    >
                                        {{
                                            row.state_payment_id == "01"
                                                ? "En espera"
                                                : row.state_payment_id == "02"
                                                ? "Aprobado"
                                                : "Rechazado"
                                        }}<i
                                            class="el-icon-arrow-down el-icon--right"
                                        ></i>
                                    </el-button>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item
                                            v-for="(state, index) in [
                                                {
                                                    id: '01',
                                                    description: 'En espera',
                                                },
                                                {
                                                    id: '02',
                                                    description: 'Aprobado',
                                                },
                                                {
                                                    id: '03',
                                                    description: 'Rechazado',
                                                },
                                            ]"
                                            :key="index"
                                            :disabled="
                                                state.id == row.state_payment_id
                                            "
                                            :command="[state.id, row.id]"
                                        >
                                            {{ state.description }}
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </template>
                        </td>
                        <td v-else>{{ row.purchase_order }}</td>

                        <td class="text-center">
                            <button
                                type="button"
                                style="min-width: 41px"
                                class="btn waves-effect waves-light btn-sm btn-primary"
                                @click.prevent="clickPayment(row.id)"
                            >
                                <i class="fas fa-money-bill-alt"></i>
                            </button>
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
                        <template v-if="isIntegrateSystem">
                            <td class="text-center">
                                {{
                                    row.production_order &&
                                    row.production_order.responsible_name
                                }}
                            </td>
                            <td class="text-center">
                                <el-button
                                    v-if="row.production_order"
                                    :class="`state_${row.production_order.state_id}_p`"
                                >
                                    <small>
                                        {{
                                            row.production_order
                                                .state_description
                                        }}
                                    </small>
                                </el-button>
                            </td>
                            <td class="text-center">
                                {{
                                    row.dispatch_order &&
                                    row.dispatch_order.responsible_name
                                }}
                            </td>
                            <td class="text-center">
                                <el-button
                                    v-if="row.dispatch_order"
                                    :class="`state_${row.dispatch_order.state_id}_d`"
                                >
                                    <small>
                                        {{
                                            row.dispatch_order.state_description
                                        }}
                                    </small>
                                </el-button>
                            </td>
                            <td class="text-center">
                                <button
                                    v-if="row.has_agency_dispatch"
                                    type="button"
                                    style="min-width: 41px"
                                    class="btn waves-effect waves-light btn-sm btn-success"
                                    @click.prevent="viewGuide(row)"
                                >
                                    Ver guia de traslado
                                </button>
                            </td>
                            <td>
                                <template
                                    v-for="(dispatch, i) in row.dispatches"
                                >
                                    <el-button
                                        type="primary"
                                        :key="i"
                                        :class="`state_${dispatch.state_id}_d`"
                                        @click="openDispatchFinish(dispatch.id)"
                                    >
                                        {{ dispatch.number }}
                                    </el-button>
                                </template>
                            </td>
                            <td>
                                <template
                                    v-for="(document, i) in row.documents"
                                >
                                    <button
                                        :key="i"
                                        v-text="document.number_full"
                                        class="d-block btn btn-success btn-sm"
                                        @click.prevent="
                                            openDocumentOptions(
                                                document.id,
                                                document.email_send_it
                                            )
                                        "
                                    ></button>
                                </template>
                            </td>
                        </template>
                        <td class="text-end" v-if="columns.recurrence.visible">
                            <template
                                v-if="
                                    row.type_period && row.quantity_period > 0
                                "
                            >
                                <el-switch
                                    :disabled="row.apply_concurrency"
                                    v-model="row.enabled_concurrency"
                                    active-text="Si"
                                    inactive-text="No"
                                    @change="changeConcurrency(row)"
                                ></el-switch>
                            </template>
                        </td>

                        <td class="text-end" v-if="columns.region.visible">
                            {{ row.customer_region }}
                        </td>

                        <td class="text-end" v-if="columns.type_period.visible">
                            {{ row.type_period | period }}
                        </td>
                        <td
                            class="text-end"
                            v-if="columns.quantity_period.visible"
                        >
                            {{ row.quantity_period }}
                        </td>

                        <td class="text-end" v-if="columns.paid.visible">
                            {{ row.paid ? "Pagado" : "Pendiente" }}
                        </td>

                        <td
                            class="text-end"
                            v-if="columns.license_plate.visible"
                        >
                            {{ row.license_plate }}
                        </td>
                        <td class="text-end" v-if="columns.observation.visible">
                            {{ row.observation }}
                        </td>

                        <td class="text-end">
                            <el-dropdown
                                trigger="click"
                                @command="manageIndexCommand"
                            >
                                <el-button type="primary">
                                    <i class="fas fa-ellipsis-v"></i>
                                </el-button>

                                <el-dropdown-menu slot="dropdown">
                                    <template v-if="isCommercial">
                                        <el-dropdown-item
                                            v-if="row.dispatches.length > 0"
                                            :command="[
                                                'openDispatchFinish',
                                                row.dispatches[0].id,
                                            ]"
                                        >
                                            Enviar guía
                                        </el-dropdown-item>

                                        <el-dropdown-item
                                            v-if="row.documents.length > 0"
                                            :command="[
                                                'openDocumentOptions',
                                                row.document_id,
                                            ]"
                                        >
                                            Enviar Documento
                                        </el-dropdown-item>
                                    </template>
                                    <el-dropdown-item
                                        v-if="
                                            row.quotation_id &&
                                            isIntegrateSystem
                                        "
                                        :command="[
                                            'clickOpenObservationQuotation',
                                            row.quotation_id,
                                        ]"
                                    >
                                        Editar observaciones cotizaciones
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="row.state_type_id != '11'"
                                        :command="['clickVoided', row.id]"
                                    >
                                        Anular
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="
                                            row.btn_generate &&
                                            row.state_type_id != '11' &&
                                            row.not_blocked
                                        "
                                        :command="['clickCreate', row.id]"
                                    >
                                        Editar
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        :command="[
                                            'clickGenerateProductionOrder',
                                            row.id,
                                        ]"
                                        v-if="
                                            isIntegrateSystem &&
                                            !row.production_order &&
                                            !isCommercial
                                        "
                                    >
                                        Generar Orden de producción
                                    </el-dropdown-item>
                                    <template v-if="isIntegrateSystem">
                                        <el-dropdown-item
                                            :command="[
                                                'clickGenerateUrl',
                                                row.id,
                                            ]"
                                            v-if="
                                                !row.changed &&
                                                row.state_type_id != '11' &&
                                                soapCompany != '03' &&
                                                !isCommercial
                                            "
                                        >
                                            Generar comprobante
                                        </el-dropdown-item>
                                    </template>
                                    <template v-else>
                                        <el-dropdown-item
                                            :command="['clickGenerate', row.id]"
                                            v-if="
                                                !row.changed &&
                                                row.state_type_id != '11' &&
                                                soapCompany != '03'
                                            "
                                        >
                                            Generar comprobante
                                        </el-dropdown-item>
                                    </template>

                                    <el-tooltip
                                        v-if="!isCommercial"
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
                                            <el-dropdown-item
                                                :command="[
                                                    'createDispatch',
                                                    document.id,
                                                ]"
                                                v-if="row.changed"
                                                :key="i"
                                            >
                                                Generar guía desde
                                            </el-dropdown-item>
                                        </template>
                                    </el-tooltip>

                                    <el-tooltip
                                        v-if="!isCommercial"
                                        class="item"
                                        effect="dark"
                                        content="Generar guía desde Nota Venta"
                                        placement="left"
                                    >
                                        <el-dropdown-item
                                            :command="[
                                                'createDispatchSaleNote',
                                                row.id,
                                            ]"
                                        >
                                            Generar guía
                                        </el-dropdown-item>
                                    </el-tooltip>

                                    <el-dropdown-item
                                        v-if="row.state_type_id != '11'"
                                        :command="['clickOptions', row.id]"
                                    >
                                        Imprimir
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        :command="['duplicate', row.id]"
                                    >
                                        Duplica la nota de venta
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        :command="['clickKillDocument', row.id]"
                                        v-if="
                                            configuration.delete_documents &&
                                            !isCommercial
                                        "
                                    >
                                        Eliminar
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="
                                            row.state_type_id != '11' &&
                                            row.send_other_server === true &&
                                            !isCommercial
                                        "
                                        :command="['sendToServer', row.id]"
                                    >
                                        Enviar a otro servidor
                                    </el-dropdown-item>

                                    <el-dropdown-item
                                        v-if="
                                            configuration.delete_relation_note_to_invoice &&
                                            row.documents.length > 0 &&
                                            !isCommercial
                                        "
                                        :command="[
                                            'sendDeleteRelationInvoice',
                                            row.id,
                                        ]"
                                    >
                                        Eliminar factura relacionada
                                    </el-dropdown-item>
                                    <el-dropdown-item
                                        v-if="isIntegrateSystem"
                                        :command="['openSendEmailModal', row]"
                                    >
                                        Enviar nota de venta
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                            <!-- <div class="ms-1">
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
                                    <template v-if="isCommercial">
                                        <button
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Enviar guia"
                                            v-if="row.dispatches.length > 0"
                                            type="button"
                                            class="dropdown-item"
                                            @click.prevent="
                                                openDispatchFinish(
                                                    row.dispatches[0].id
                                                )
                                            "
                                        >
                                            Enviar guía
                                        </button>

                                        <button
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Enviar guia"
                                            v-if="row.documents.length > 0"
                                            type="button"
                                            class="dropdown-item"
                                            @click.prevent="
                                                openDocumentOptions(
                                                    row.document_id
                                                )
                                            "
                                        >
                                            Enviar Documento
                                        </button>
                                    </template>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Anular"
                                        v-if="
                                            row.quotation_id &&
                                            isIntegrateSystem
                                        "
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="
                                            clickOpenObservationQuotation(
                                                row.quotation_id
                                            )
                                        "
                                    >
                                        Editar observaciones cotizaciones
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Anular"
                                        v-if="row.state_type_id != '11'"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickVoided(row.id)"
                                    >
                                        Anular
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Editar"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="clickCreate(row.id)"
                                        v-if="
                                            row.btn_generate &&
                                            row.state_type_id != '11' &&
                                            row.not_blocked
                                        "
                                    >
                                        Editar
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Generar orden de producción"
                                        type="button"
                                        class="dropdown-item"
                                        @click.prevent="
                                            clickGenerateProductionOrder(row.id)
                                        "
                                        v-if="
                                            isIntegrateSystem &&
                                            !row.production_order &&
                                            !isCommercial
                                        "
                                    >
                                        Generar Orden de producción
                                    </button>
                                    <template v-if="isIntegrateSystem">
                                        <button
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Generar comprobante"
                                            type="button"
                                            class="dropdown-item"
                                            @click.prevent="
                                                clickGenerateUrl(row.id)
                                            "
                                            v-if="
                                                !row.changed &&
                                                row.state_type_id != '11' &&
                                                soapCompany != '03' &&
                                                !isCommercial
                                            "
                                        >
                                            Generar comprobante
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="Generar comprobante"
                                            type="button"
                                            class="dropdown-item"
                                            @click.prevent="
                                                clickGenerate(row.id)
                                            "
                                            v-if="
                                                !row.changed &&
                                                row.state_type_id != '11' &&
                                                soapCompany != '03'
                                            "
                                        >
                                            Generar comprobante
                                        </button>
                                    </template>

                                    <el-tooltip
                                        v-if="!isCommercial"
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
                                        v-if="!isCommercial"
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
                                    </el-tooltip>

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
                                    <button
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
                                        v-if="
                                            configuration.delete_documents &&
                                            !isCommercial
                                        "
                                    >
                                        Eliminar
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Enviar a otro servidor"
                                        v-if="
                                            row.state_type_id != '11' &&
                                            row.send_other_server === true &&
                                            !isCommercial
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
                                            row.documents.length > 0 &&
                                            !isCommercial
                                        "
                                        type="button"
                                        class="dropdown-item"
                                        @click="
                                            sendDeleteRelationInvoice(row.id)
                                        "
                                    >
                                        Eliminar factura relacionada
                                    </button>
                                    <button
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        title="Elimina unicamente la relación entre la nota y factura"
                                        v-if="isIntegrateSystem"
                                        type="button"
                                        class="dropdown-item"
                                        @click="openSendEmailModal(row)"
                                    >
                                        Enviar nota de venta
                                    </button>
                                </div>
                            </div> -->
                        </td>
                        <td v-if="configuration.college">
                            <el-button type="primary" @click="openPeriod(row)">
                                Editar
                            </el-button>
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
        <sale-note-production-order
            :dialogVisible.sync="showDialogGenerateProductionOrder"
            :recordId="recordId"
            @getRecords="getRecords"
        ></sale-note-production-order>
        <sale-note-generate
            :show.sync="showDialogGenerate"
            :recordId="recordId"
            :showGenerate="true"
            :showClose="false"
        ></sale-note-generate>
        <email-modal
            :showDialog.sync="showEmailModal"
            :saleNoteId="recordId"
            :mail="mail"
            :fullNumber="fullNumber"
        ></email-modal>
        <ModalGenerateCPE :show.sync="showModalGenerateCPE"></ModalGenerateCPE>
        <ModalGenerateGuie
            :show.sync="showModalGenerateGuie"
        ></ModalGenerateGuie>
        <UploadToOtherServer
            :configuration="config"
            :showMigrate.sync="showMigrateNv"
        ></UploadToOtherServer>
        <guide-modal-view
            :showDialog.sync="showGuideModalView"
            :currentRecord="dispatch_order"
        ></guide-modal-view>

        <dispatch-finish
            :recordId="dispatchId"
            :showClose="true"
            :send-sunat="false"
            :showDialog.sync="showDialogFinish"
        ></dispatch-finish>
        <quotation-observation
            :showDialog.sync="showDialogQuotationObservation"
            :quotationId="quotationId"
        ></quotation-observation>
        <document-options
            :documentEmailSendIt="documentEmailSendIt"
            :showClose="true"
            :showDialog.sync="showDialogDocumentOptions"
            :recordId="documentId"
        ></document-options>
    </div>
</template>
<style>
.el-dropdown-link {
    cursor: pointer;
    color: #409eff;
}
.el-icon-arrow-down {
    font-size: 12px;
}
.state_1_p {
    color: white;
    background: #ffcc00;
}
.state_01_py {
    color: white;
    background: #ffcc00;
}
.state_2_p {
    color: white;
    background: #ff9900;
}
.state_3_p {
    color: white;
    background: #33cc33;
}
.state_02_py {
    color: white;
    background: #33cc33;
}
.state_4_p {
    color: white;
    background: #0070c0;
}
.state_5_p {
    color: white;
    background: #16a34f;
}
.state_6_p {
    color: white;
    background: red;
}
.state_1_d {
    color: white;
    background: #ffcc00;
}
.state_2_d {
    color: white;
    background: #ff9900;
}
.state_3_d {
    color: white;
    background: #33cc33;
}
.state_4_d {
    color: white;
    background: #33cc33;
}
.state_03_py {
    color: white;
    background: red;
}
.state_5_d {
    color: white;
    background: red;
}
.state_6_d {
    color: white;
    background: #0070c0;
}
</style>
<script>
import PeriodModal from "../../../../../modules/Suscription/Resources/assets/js/components/PeriodModal.vue";

import DataTable from "../../../components/DataTableSaleNote.vue";
import UploadToOtherServer from "./partials/upload_other_server_group.vue";
import SaleNotePayments from "./partials/payments.vue";
import SaleNotesOptions from "./partials/options.vue";
import SaleNoteProductionOrder from "./partials/order_production.vue";
import SaleNoteGenerate from "./partials/option_documents";
import EmailModal from "./partials/email_modal.vue";
import DispatchFinish from "../../tenant/dispatches/partials/finish.vue";
import { deletable } from "../../../mixins/deletable";
import ModalGenerateCPE from "./ModalGenerateCPE";
import ModalGenerateGuie from "./ModalGenerateGuie";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import GuideModalView from "./partials/guide_modal_view.vue";
import QuotationObservation from "./partials/quotation_observation.vue";
import DocumentOptions from "../../tenant/documents/partials/options.vue";
export default {
    props: [
        "soapCompany",
        "isCommercial",
        "typeUser",
        "configuration",
        "isIntegrateSystem",
    ],
    mixins: [deletable],
    components: {
        EmailModal,
        PeriodModal,
        DataTable,
        SaleNotePayments,
        SaleNotesOptions,
        SaleNoteGenerate,
        ModalGenerateCPE,
        UploadToOtherServer,
        ModalGenerateGuie,
        SaleNoteProductionOrder,
        GuideModalView,
        DispatchFinish,
        QuotationObservation,
        DocumentOptions,
    },
    computed: {
        ...mapState(["config"]),
    },
    data() {
        return {
            documentEmailSendIt: false,
            showDialogQuotationObservation: false,
            quotationId: null,
            showDialogFinish: false,
            dispatchId: null,
            showGuideModalView: false,
            fullNumber: "",
            mail: "",
            showEmailModal: false,
            showDialogGenerateProductionOrder: false,
            showModalGenerateGuie: false,
            currentDocument: null,
            showPeriod: false,
            showModalGenerateCPE: false,
            showMigrateNv: false,
            resource: "sale-notes",
            showDialogPayments: false,
            showDialogOptions: false,
            showDialogGenerate: false,
            saleNotesNewId: null,
            recordId: null,
            loading: false,
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
            // showDialogDeleteRelationInvoice: false,
            // dataDeleteRelation: {
            //     documents: {},
            //     id: ''
            // }
            dispatch_order: {},
            documentId: null,
            showDialogDocumentOptions: false,
        };
    },
    created() {
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
        let { package_handlers } = this.configuration;
        this.isDriver = package_handlers;
        this.getColumnsToShow();
        if (this.isIntegrateSystem) {
            this.columns.seller_name.visible = true;
        }
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
        manageIndexCommand([command, arg1 = null, arg2 = null]) {
            console.log(
                "🚀 ~ file: index.vue:1327 ~ manageIndexCommand ~ command:",
                command
            );
            switch (command) {
                case "openDispatchFinish":
                    this.openDispatchFinish(arg1);
                    break;
                case "openDocumentOptions":
                    this.openDocumentOptions(arg1, arg2);
                    break;
                case "clickOpenObservationQuotation":
                    this.clickOpenObservationQuotation(arg1);
                    break;
                case "clickVoided":
                    this.clickVoided(arg1);
                    break;
                case "clickCreate":
                    this.clickCreate(arg1);
                    break;
                case "clickGenerateProductionOrder":
                    this.clickGenerateProductionOrder(arg1);
                    break;
                case "clickGenerateUrl":
                    this.clickGenerateUrl(arg1);
                    break;
                case "clickGenerate":
                    this.clickGenerate(arg1);
                    break;
                case "createDispatch":
                    location.href = `/dispatches/create/${arg1}`;
                    break;
                case "createDispatchSaleNote":
                    location.href = `/dispatches/create_new/sale_note/${arg1}`;
                    break;
                case "clickOptions":
                    this.clickOptions(arg1);
                    break;
                case "clickKillDocument":
                    this.clickKillDocument(arg1);
                    break;
                case "sendToServer":
                    this.sendToServer(arg1);
                    break;
                case "sendDeleteRelationInvoice":
                    this.sendDeleteRelationInvoice(arg1);
                    break;
                case "openSendEmailModal":
                    this.openSendEmailModal(arg1);
                    break;
                case "duplicate":
                    this.duplicate(arg1);
                    break;
                default:
                    break;
            }
        },
        openDocumentOptions(id, emailSendIt = false) {
            this.documentId = id;
            this.showDialogDocumentOptions = true;
            this.documentEmailSendIt = emailSendIt;
        },
        clickOpenObservationQuotation(quotation_id) {
            this.quotationId = quotation_id;
            this.showDialogQuotationObservation = true;
        },
        openDispatchFinish(id) {
            this.dispatchId = id;
            this.showDialogFinish = true;
        },
        viewGuide(row) {
            this.dispatch_order = row.dispatch_order;
            this.showGuideModalView = true;
        },
        async changeStatePayment([stateId, rowId]) {
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
                    `/${this.resource}/change-state-payment/${rowId}/${stateId}`
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
        getRecords() {
            this.$refs.dataTable.getRecords();
        },
        openSendEmailModal(row) {
            console.log(
                "🚀 ~ file: index.vue:771 ~ openSendEmailModal ~ row:",
                row
            );
            this.showEmailModal = true;
            this.mail = row.customer_email;
            this.fullNumber = row.full_number;
            this.recordId = row.id;
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
                `/sale-notes/downloadExternal/${external_id}`,
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
        clickGenerateProductionOrder(recordId) {
            this.recordId = recordId;
            this.showDialogGenerateProductionOrder = true;
        },
        clickGenerateUrl(recordId) {
            window.open(`/documents/create/sale-notes/${recordId}`, "_blank");
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

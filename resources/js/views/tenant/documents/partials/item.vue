<template>
    <el-dialog
        :append-to-body="true"
        :close-on-click-modal="false"
        :title="titleDialog"
        :visible="showDialog"
        top="7vh"
        width="700px"
        @close="close"
        @open="create"
    >
        <Keypress key-event="keyup" @success="checkKey" />

        <form
            autocomplete="off"
            @submit.prevent="clickAddItem"
            v-loading="loading_dialog"
        >
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-xl-12 col-sm-12">
                        <div
                            id="custom-select"
                            :class="{ 'has-danger': errors.item_id }"
                            class="form-group"
                        >
                            <label class="control-label">
                                Producto/Servicio
                                <a
                                    v-if="can_add_new_product"
                                    href="#"
                                    @click.prevent="showDialogNewItem = true"
                                >
                                    [+ Nuevo]
                                </a>
                            </label>

                            <template v-if="!search_item_by_barcode">
                                <div
                                    class="el-input el-input-group el-input-group--append"
                                >
                                    <el-select
                                        id="select-width"
                                        ref="selectSearchNormal"
                                        slot="prepend"
                                        v-model="form.item_id"
                                        :disabled="recordItem != null"
                                        :loading="loading_search"
                                        :remote-method="searchRemoteItems"
                                        filterable
                                        placeholder="Buscar"
                                        remote
                                        :tabindex="'1'"
                                        @change="changeItem"
                                        @focus="focusSelectItem"
                                        @visible-change="focusTotalItem"
                                    >
                                        <el-tooltip
                                            v-for="(option, idx) in items"
                                            :key="idx"
                                            placement="left"
                                        >
                                            <div
                                                slot="content"
                                                v-html="
                                                    ItemSlotTooltipView(option)
                                                "
                                            ></div>
                                            <el-option
                                                :label="
                                                    ItemOptionDescriptionView(
                                                        option
                                                    )
                                                "
                                                :value="option.id"
                                            ></el-option>
                                        </el-tooltip>
                                    </el-select>
                                    <div class="el-input-group__append">
                                        <el-tooltip
                                            v-if="
                                                configuration.search_by_factory_code
                                            "
                                            slot="append"
                                            :disabled="recordItem != null"
                                            class="item"
                                            content="Buscar productos que compartan código de fábrica"
                                            effect="dark"
                                            placement="bottom"
                                        >
                                            <el-button
                                                @click.prevent="
                                                    clickFactoryCodeDetail()
                                                "
                                            >
                                                <i
                                                    class="fas fa-layer-group"
                                                ></i>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip
                                            v-if="
                                                configuration.select_warehouse_to_sell
                                            "
                                            slot="append"
                                            :disabled="recordItem != null"
                                            class="item"
                                            content="Ver Stock del Producto"
                                            effect="dark"
                                            placement="bottom"
                                        >
                                            <el-button
                                                :disabled="isEditItemNote"
                                                @click.prevent="
                                                    clickWarehouseDetail()
                                                "
                                            >
                                                <i class="fa fa-search"></i>
                                            </el-button>
                                        </el-tooltip>
                                        <el-tooltip
                                            slot="append"
                                            :disabled="recordItem != null"
                                            class="item"
                                            content="Historial de ventas"
                                            effect="dark"
                                            placement="bottom"
                                        >
                                            <el-button
                                                :disabled="isEditItemNote"
                                                @click.prevent="
                                                    clickHistorySales()
                                                "
                                            >
                                                <i class="fa fa-list"></i>
                                            </el-button>
                                        </el-tooltip>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <div
                                    class="el-input el-input-group el-input-group--append"
                                >
                                    <el-select
                                        id="select-width"
                                        ref="selectBarcode"
                                        slot="prepend"
                                        v-model="form.item_id"
                                        :disabled="recordItem != null"
                                        :loading="loading_search"
                                        :remote-method="searchRemoteItems"
                                        filterable
                                        placeholder="Buscar"
                                        remote
                                        value-key="id"
                                        @change="changeItem"
                                    >
                                        <el-option
                                            v-for="(option, idx) in items"
                                            :key="idx"
                                            :label="option.full_description"
                                            :value="option.id"
                                        ></el-option>
                                    </el-select>
                                    <el-tooltip
                                        slot="append"
                                        :disabled="recordItem != null"
                                        class="item"
                                        content="Ver Stock del Producto"
                                        effect="dark"
                                        placement="bottom"
                                    >
                                        <div class="el-input-group__append">
                                            <el-button
                                                :disabled="isEditItemNote"
                                                @click.prevent="
                                                    clickWarehouseDetail()
                                                "
                                            >
                                                <i class="fa fa-search"></i>
                                            </el-button>
                                        </div>
                                    </el-tooltip>
                                </div>
                            </template>

                            <div class="d-flex">
                                <div class="col-md-6">
                                    <template v-if="hasSelectedItem">
                                        <span
                                            v-if="isRestrictedForSale"
                                            class="text-danger mt-1 mb-2 d-block"
                                            >Restringido para venta</span
                                        >
                                    </template>

                                    <template v-if="!is_client">
                                        <el-checkbox
                                            v-model="search_item_by_barcode"
                                            :disabled="recordItem != null"
                                            >Buscar por código de barras
                                        </el-checkbox>
                                        <br />
                                        <template v-if="search_item_by_barcode">
                                            <el-checkbox
                                                v-model="
                                                    search_item_by_barcode_presentation
                                                "
                                                >Por presentación
                                            </el-checkbox>
                                            <br />
                                        </template>
                                    </template>
                                    <el-checkbox
                                        v-model="form.has_plastic_bag_taxes"
                                        :disabled="isEditItemNote"
                                        >Impuesto a la Bolsa Plástica
                                    </el-checkbox>
                                </div>
                                <div class="col-md-6">
                                    <el-checkbox
                                        @change="searchRemoteItems"
                                        v-model="search.order_search_stock"
                                        >Ordenar busqueda por stock
                                    </el-checkbox>
                                    <br />
                                    <el-checkbox
                                        @change="searchRemoteItems"
                                        v-model="search.order_search_price"
                                        >Ordenar busqueda por precio
                                    </el-checkbox>
                                </div>
                            </div>
                            <small
                                v-if="errors.item_id"
                                class="text-danger"
                                v-text="errors.item_id[0]"
                            ></small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="control-label">Filtrar por marca</label>
                        <el-select
                            v-model="search.filter_brand"
                            @change="searchRemoteItemsBC"
                            clearable
                            filterable
                        >
                            <el-option
                                v-for="(option, idx) in itemsBrands"
                                :key="idx"
                                :label="option.name"
                                :value="option.id"
                            ></el-option>
                        </el-select>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label"
                            >Filtrar por categoría</label
                        >
                        <el-select
                            @change="searchRemoteItemsBC"
                            v-model="search.filter_categorie"
                            clearable
                            filterable
                        >
                            <el-option
                                v-for="(option, idx) in itemsCategories"
                                :key="idx"
                                :label="option.name"
                                :value="option.id"
                            ></el-option>
                        </el-select>
                    </div>
                    <div class="col-md-4">
                        <div
                            :class="{
                                'has-danger': errors.affectation_igv_type_id,
                            }"
                            class="form-group"
                        >
                            <label class="control-label">Afectación Igv</label>
                            <el-select
                                v-model="form.affectation_igv_type_id"
                                :disabled="!change_affectation_igv_type_id"
                                filterable
                            >
                                <el-option
                                    v-for="(
                                        option, idx
                                    ) in affectation_igv_types"
                                    :key="idx"
                                    :label="option.description"
                                    :value="option.id"
                                ></el-option>
                            </el-select>
                            <el-checkbox
                                v-model="change_affectation_igv_type_id"
                                :disabled="recordItem != null"
                            >
                                Editar
                            </el-checkbox>
                            <small
                                v-if="errors.affectation_igv_type_id"
                                class="text-danger"
                                v-text="errors.affectation_igv_type_id[0]"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <!------------------------------------------------------->

                    <template v-if="form.item_unit_types.length > 0">
                        <div class="col-md-3 col-sm-4">
                            <div
                                :class="{
                                    'has-danger': errors.calculate_quantity,
                                }"
                                class="form-group"
                            >
                                <label class="control-label"> Cantidad</label>
                                <el-input
                                    :tabindex="'2'"
                                    ref="inputQuantity"
                                    v-model="form.quantity"
                                    :disabled="form.item.calculate_quantity"
                                    @blur="validateQuantity"
                                    @input.native="changeValidateQuantity"
                                >
                                    <el-button
                                        slot="prepend"
                                        :disabled="
                                            form.quantity < 0.01 ||
                                            form.item.calculate_quantity
                                        "
                                        icon="el-icon-minus"
                                        style="
                                            padding-right: 5px;
                                            padding-left: 12px;
                                        "
                                        @click="clickDecrease"
                                    ></el-button>
                                    <el-button
                                        slot="append"
                                        :disabled="form.item.calculate_quantity"
                                        icon="el-icon-plus"
                                        style="
                                            padding-right: 5px;
                                            padding-left: 12px;
                                        "
                                        @click="clickIncrease"
                                    ></el-button>
                                </el-input>
                                <small
                                    v-if="errors.quantity"
                                    class="text-danger"
                                    v-text="errors.quantity[0]"
                                ></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div
                                :class="{ 'has-danger': errors.quantity }"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Unidad de medida
                                </label>

                                <el-select
                                    @change="changePrice"
                                    v-model="form.item_unit_type_id"
                                >
                                    <el-option
                                        v-for="(
                                            row, idx
                                        ) in form.item_unit_types"
                                        :key="idx"
                                        :value="row.id"
                                        :label="row.description"
                                    >
                                    </el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div
                                :class="{
                                    'has-danger': errors.unit_price_value,
                                }"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Precio Unitario
                                    <el-tooltip
                                        v-if="itemLastPrice"
                                        class="item"
                                        :content="itemLastPrice"
                                        effect="dark"
                                        placement="top-start"
                                    >
                                        <i class="fa fa-info-circle"></i>
                                    </el-tooltip>
                                </label>
                                <template
                                    v-if="
                                        applyChangeCurrencyItem && isFromInvoice
                                    "
                                >
                                    <!-- <template v-if="form.item"> -->
                                    <el-input
                                        v-model="form.unit_price_value"
                                        :readonly="!edit_unit_price"
                                        @input="calculateQuantity"
                                    >
                                        <template
                                            v-if="
                                                form.item.currency_type_symbol
                                            "
                                        >
                                            <el-select
                                                slot="prepend"
                                                v-model="
                                                    form.item.currency_type_id
                                                "
                                                class="el-select-currency"
                                            >
                                                <el-option
                                                    v-for="(
                                                        option, idx
                                                    ) in currencyTypes"
                                                    :key="idx"
                                                    :label="option.symbol"
                                                    :value="option.id"
                                                ></el-option>
                                            </el-select>
                                        </template>
                                    </el-input>
                                    <!-- </template> -->
                                </template>
                                <template v-else>
                                    <el-input
                                        class="w-100"
                                        v-model="form.unit_price_value"
                                        :tabindex="'3'"
                                        :readonly="!edit_unit_price"
                                        @input="calculateQuantity"
                                    >
                                        <template
                                            v-if="
                                                form.item.currency_type_symbol
                                            "
                                            slot="prepend"
                                        >
                                            {{ form.item.currency_type_symbol }}
                                        </template>
                                    </el-input>
                                </template>
                                <small
                                    v-if="errors.unit_price_value"
                                    class="text-danger"
                                    v-text="errors.unit_price[0]"
                                ></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="form-group">
                                <label class="control-label">Total</label>
                                <el-input
                                    v-model="readonly_total"
                                    readonly
                                    @input="calculateTotal"
                                ></el-input>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="col-md-4 col-sm-4">
                            <div
                                :class="{ 'has-danger': errors.quantity }"
                                class="form-group"
                            >
                                <label class="control-label"> Cantidad</label>
                                <el-input
                                    :tabindex="'2'"
                                    ref="inputQuantity"
                                    v-model="form.quantity"
                                    :disabled="form.item.calculate_quantity"
                                    @blur="validateQuantity"
                                    @input.native="changeValidateQuantity"
                                >
                                    <el-button
                                        slot="prepend"
                                        :disabled="
                                            form.quantity < 0.01 ||
                                            form.item.calculate_quantity
                                        "
                                        icon="el-icon-minus"
                                        style="
                                            padding-right: 5px;
                                            padding-left: 12px;
                                        "
                                        @click="clickDecrease"
                                    ></el-button>
                                    <el-button
                                        slot="append"
                                        :disabled="form.item.calculate_quantity"
                                        icon="el-icon-plus"
                                        style="
                                            padding-right: 5px;
                                            padding-left: 12px;
                                        "
                                        @click="clickIncrease"
                                    ></el-button>
                                </el-input>
                                <small
                                    v-if="errors.quantity"
                                    class="text-danger"
                                    v-text="errors.quantity[0]"
                                ></small>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-4">
                            <div
                                :class="{
                                    'has-danger': errors.unit_price_value,
                                }"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Precio Unitario
                                    <el-tooltip
                                        v-if="itemLastPrice"
                                        class="item"
                                        :content="itemLastPrice"
                                        effect="dark"
                                        placement="top-start"
                                    >
                                        <i class="fa fa-info-circle"></i>
                                    </el-tooltip>
                                </label>

                                <template
                                    v-if="
                                        applyChangeCurrencyItem && isFromInvoice
                                    "
                                >
                                    <template v-if="form.item">
                                        <el-input
                                            v-model="form.unit_price_value"
                                            :tabindex="'3'"
                                            :readonly="!edit_unit_price"
                                            @input="calculateQuantity"
                                        >
                                            <template
                                                v-if="
                                                    form.item
                                                        .currency_type_symbol
                                                "
                                            >
                                                <el-select
                                                    slot="prepend"
                                                    v-model="
                                                        form.item
                                                            .currency_type_id
                                                    "
                                                    class="el-select-currency"
                                                >
                                                    <el-option
                                                        v-for="(
                                                            option, idx
                                                        ) in currencyTypes"
                                                        :key="idx"
                                                        :label="option.symbol"
                                                        :value="option.id"
                                                    ></el-option>
                                                </el-select>
                                            </template>
                                        </el-input>
                                    </template>
                                </template>
                                <template v-else>
                                    <el-input
                                        v-model="form.unit_price_value"
                                        :tabindex="'3'"
                                        :readonly="!edit_unit_price"
                                        @input="calculateQuantity"
                                    >
                                        <template
                                            v-if="
                                                form.item.currency_type_symbol
                                            "
                                            slot="prepend"
                                        >
                                            {{ form.item.currency_type_symbol }}
                                        </template>
                                    </el-input>
                                </template>
                                <small
                                    v-if="errors.unit_price_value"
                                    class="text-danger"
                                    v-text="errors.unit_price[0]"
                                ></small>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div
                                v-if="
                                    (form.item && form.item.meter == null) ||
                                    form.item.meter == 0
                                "
                                :class="{
                                    'has-danger': errors.unit_price_value,
                                }"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Total Importe</label
                                >
                                <el-input
                                    type="number"
                                    step="any"
                                    v-model="readonly_total"
                                    @input="calculateUnitPrice"
                                ></el-input>
                            </div>
                            <div
                                v-else
                                :class="{
                                    'has-danger': errors.unit_price_value,
                                }"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Total Importe</label
                                >
                                <el-input
                                    type="number"
                                    step="any"
                                    v-model="readonly_total_meter"
                                ></el-input>
                                <small v-text="`${form.meter} mts`"></small>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div
                                v-if="form.item && form.item.meter"
                                class="form-group"
                            >
                                <label class="control-label">
                                    Cantidad de metros</label
                                >
                                <el-input
                                    type="number"
                                    step="any"
                                    readonly
                                    v-model="readonly_meters"
                                ></el-input>
                            </div>
                        </div>
                    </template>
                    <!------------------------------------------------------->
                </div>
                <div class="row">
                    <!-- --------------------------------->
                    <div
                        v-if="showLots"
                        class="col-md-4"
                        style="padding-top: 1%"
                    >
                        <a
                            class="text-center font-weight-bold text-info"
                            href="#"
                            @click.prevent="clickLotGroup"
                            >[&#10004; Seleccionar lote]</a
                        >
                    </div>
                    <div
                        v-if="showSeries"
                        class="col-md-4"
                        style="padding-top: 1%"
                    >
                        <a
                            class="text-center font-weight-bold text-info"
                            href="#"
                            @click.prevent="clickSelectLots"
                            >[&#10004; Seleccionar series]</a
                        >
                    </div>
                    <div
                        v-if="showSizes"
                        class="col-md-4"
                        style="padding-top: 1%"
                    >
                        <a
                            class="text-center font-weight-bold text-info"
                            href="#"
                            @click.prevent="clickSelectSizes"
                            >[&#10004; Seleccionar tallas]</a
                        >
                    </div>
                    <div
                        v-show="form.item.calculate_quantity"
                        class="col-md-4 col-sm-6"
                    >
                        <div
                            :class="{ 'has-danger': errors.total_item }"
                            class="form-group"
                        >
                            <label class="control-label"
                                >Total venta producto</label
                            >
                            <el-input
                                ref="total_item"
                                v-model="total_item"
                                :min="0.01"
                                @input="calculateQuantity"
                            >
                                <template
                                    v-if="form.item.currency_type_symbol"
                                    slot="prepend"
                                >
                                    {{ form.item.currency_type_symbol }}
                                </template>
                            </el-input>
                            <small
                                v-if="errors.total_item"
                                class="text-danger"
                                v-text="errors.total_item[0]"
                            ></small>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 mt-2">
                        <el-checkbox v-model="form.update_price"
                            >Actualizar precio de venta
                        </el-checkbox>
                    </div>
                    <div
                        v-if="config.edit_name_product"
                        class="col-md-12 col-sm-12 mt-2"
                    >
                        <div class="form-group">
                            <label class="control-label"
                                >Nombre producto en PDF</label
                            >
                            <vue-ckeditor
                                v-model="form.name_product_pdf"
                                :editors="editors"
                                type="classic"
                            ></vue-ckeditor>
                        </div>
                    </div>
                    <!----------------------------------->
                </div>
                <div class="row">
                    <template v-if="canShowExtraData">
                        <tenant-item-aditional-info-selector
                            :errors="errors"
                            :form="form"
                        ></tenant-item-aditional-info-selector>
                    </template>
                    <template
                        v-if="form.item_id && configuration.discounts_acc"
                    >
                        <span class="text-primary">Descuento</span>
                        <a href="#" @click.prevent="addDiscount">[+ Agregar]</a>
                        <div
                            v-for="(discount, index) in discounts"
                            :key="index"
                        >
                            <div class="col-md-3 col-lg-3 col-12">
                                <el-input
                                    v-model="discount.percentage"
                                    type="number"
                                    @input="calculateDiscount"
                                >
                                    <el-button
                                        slot="append"
                                        icon="el-icon-delete"
                                        @click.prevent="removeDiscount(index)"
                                    ></el-button>
                                </el-input>
                            </div>
                        </div>
                    </template>
                    <template v-if="!is_client">
                        <div v-if="showDiscounts" class="col-md-12 mt-2">
                            <el-collapse v-model="activePanel">
                                <el-collapse-item
                                    name="1"
                                    title="+ Agregar Descuentos/Cargos/Atributos especiales"
                                    v-if="showSpecialData"
                                >
                                    <div v-if="discount_types.length > 0">
                                        <label class="control-label">
                                            Descuentos
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    clickAddDiscount
                                                "
                                                >[+ Agregar]</a
                                            >
                                        </label>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Descripción</th>
                                                    <th>Porcentaje</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        row, index
                                                    ) in form.discounts"
                                                    :key="index"
                                                >
                                                    <td>
                                                        <el-select
                                                            v-model="
                                                                row.discount_type_id
                                                            "
                                                            @change="
                                                                changeDiscountType(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            <el-option
                                                                v-for="(
                                                                    option, idx
                                                                ) in discount_types"
                                                                :key="idx"
                                                                :label="
                                                                    option.description
                                                                "
                                                                :value="
                                                                    option.id
                                                                "
                                                            ></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td>
                                                        <el-input
                                                            v-model="
                                                                row.description
                                                            "
                                                        ></el-input>
                                                        <br />
                                                        <el-tooltip
                                                            class="item"
                                                            content="
                                                                              Aplicar 18% de IGV al descuento global
                                                                            "
                                                            effect="dark"
                                                            placement="top"
                                                        >
                                                            <i
                                                                class="fa fa-info-circle"
                                                            ></i>
                                                        </el-tooltip>
                                                        <el-checkbox
                                                            :disabled="
                                                                !row.is_amount ||
                                                                row.amount == 0
                                                            "
                                                            v-model="
                                                                row.is_split
                                                            "
                                                            class="ml-1 mr-1"
                                                            @change="
                                                                splitBase(index)
                                                            "
                                                        ></el-checkbox>
                                                    </td>
                                                    <td>
                                                        <template
                                                            v-if="row.is_amount"
                                                        >
                                                            <el-input
                                                                v-model="
                                                                    row.amount
                                                                "
                                                            ></el-input>
                                                        </template>
                                                        <template v-else>
                                                            <el-input
                                                                v-model="
                                                                    row.percentage
                                                                "
                                                            ></el-input>
                                                        </template>
                                                        <br />
                                                        <el-checkbox
                                                            v-model="
                                                                row.is_amount
                                                            "
                                                            @change="
                                                                changeIsDiscountAmount(
                                                                    index
                                                                )
                                                            "
                                                            >Ingresar monto fijo
                                                        </el-checkbox>
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-danger"
                                                            type="button"
                                                            @click.prevent="
                                                                clickRemoveDiscount(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            x
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-if="charge_types.length > 0">
                                        <label class="control-label">
                                            Cargos
                                            <a
                                                href="#"
                                                @click.prevent="clickAddCharge"
                                                >[+ Agregar]</a
                                            >
                                        </label>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Descripción</th>
                                                    <th>Porcentaje</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        row, index
                                                    ) in form.charges"
                                                    :key="index"
                                                >
                                                    <td>
                                                        <el-select
                                                            v-model="
                                                                row.charge_type_id
                                                            "
                                                            @change="
                                                                changeChargeType(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            <el-option
                                                                v-for="(
                                                                    option, idx
                                                                ) in charge_types"
                                                                :key="idx"
                                                                :label="
                                                                    option.description
                                                                "
                                                                :value="
                                                                    option.id
                                                                "
                                                            ></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td>
                                                        <el-input
                                                            v-model="
                                                                row.description
                                                            "
                                                        ></el-input>
                                                    </td>
                                                    <td>
                                                        <el-input
                                                            v-model="
                                                                row.percentage
                                                            "
                                                        ></el-input>
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-danger"
                                                            type="button"
                                                            @click.prevent="
                                                                clickRemoveCharge(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            x
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div v-if="attribute_types.length > 0">
                                        <label class="control-label">
                                            Atributos
                                            <a
                                                href="#"
                                                @click.prevent="
                                                    clickAddAttribute
                                                "
                                                >[+ Agregar]</a
                                            >
                                        </label>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Descripción</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        row, index
                                                    ) in form.attributes"
                                                    :key="index"
                                                >
                                                    <td>
                                                        <el-select
                                                            v-model="
                                                                row.attribute_type_id
                                                            "
                                                            filterable
                                                            @change="
                                                                changeAttributeType(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            <el-option
                                                                v-for="(
                                                                    option, idx
                                                                ) in attribute_types"
                                                                :key="idx"
                                                                :label="
                                                                    option.description
                                                                "
                                                                :value="
                                                                    option.id
                                                                "
                                                            ></el-option>
                                                        </el-select>
                                                    </td>
                                                    <td>
                                                        <el-input
                                                            v-model="row.value"
                                                            @input="
                                                                inputAttribute(
                                                                    index
                                                                )
                                                            "
                                                        ></el-input>
                                                    </td>
                                                    <td>
                                                        <button
                                                            class="btn btn-danger"
                                                            type="button"
                                                            @click.prevent="
                                                                clickRemoveAttribute(
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            x
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </el-collapse-item>
                            </el-collapse>
                        </div>
                    </template>
                </div>
            </div>

            <div class="form-actions text-end pt-2 hidden-sm-down">
                <el-button @click.prevent="close()">Cerrar</el-button>
                <el-button
                    v-if="form.item_id"
                    class="add"
                    native-type="submit"
                    type="primary"
                >
                    {{ titleAction }}
                </el-button>
            </div>
            <!-- @todo: Mejorar evitando duplicar codigo -->
            <!-- Ocultar en cel -->
        </form>
        <item-form
            :external="true"
            :showDialog.sync="showDialogNewItem"
        ></item-form>
        <warehouses-detail
            :isUpdateWarehouseId="isUpdateWarehouseId"
            :showDialog.sync="showWarehousesDetail"
            :warehouses="warehousesDetail"
        >
        </warehouses-detail>
        <factory-code-detail
            :showDialog.sync="showFactoryCodeDetail"
            :item="currentItem"
        >
        </factory-code-detail>
        <history-sales-form
            :showDialog.sync="showDialogHistorySales"
            :item_id="history_item_id"
            :customer_id="this.customerId"
            :type="true"
        ></history-sales-form>
        <lots-group
            :lots-group="form.lots_group"
            :itemId="form.item_id"
            :quantity="form.quantity"
            :showDialog.sync="showDialogLots"
            @addRowLotGroup="addRowLotGroup"
        >
        </lots-group>

        <!--        <lots-group-->
        <!--            :showDialog.sync="showDialogLots"-->
        <!--            :itemId="form.item_id"-->
        <!--            :lots-group-all="lotsGroupAll"-->
        <!--            :lots_group="form.lots_group"-->
        <!--            :quantity="form.quantity"-->
        <!--            @addRowLotGroup="addRowLotGroup"-->
        <!--            :compromise-all-quantity="true">-->
        <!--        </lots-group>-->
        <select-lots-form
            :showDialog.sync="showDialogSelectLots"
            :documentItemId="documentItem"
            :itemId="form.item_id"
            :lots="lots"
            :quantity="form.quantity"
            @addRowSelectLot="addRowSelectLot"
        >
        </select-lots-form>
        <select-sizes-form
            :showDialog.sync="showDialogSelectSizes"
            :documentItemId="documentItem"
            :itemId="form.item_id"
            :sizes="sizes"
            :quantity="form.quantity"
            @addRowSelectSize="addRowSelectSize"
        >
        </select-sizes-form>
    </el-dialog>
</template>
<style>
.el-select-currency .el-input.el-input--small.el-input--suffix {
    width: 70px;
}
</style>

<script>
import ItemForm from "../../items/form.vue";
import LotsGroup from "./lots_group.vue";

import { calculateRowItem } from "../../../../helpers/functions";
import WarehousesDetail from "./select_warehouses.vue";
import FactoryCodeDetail from "./select_factory_code.vue";
import SelectLotsForm from "./lots.vue";
import SelectSizesForm from "./sizes.vue";

import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import VueCkeditor from "vue-ckeditor5";
import { mapActions, mapState } from "vuex/dist/vuex.mjs";
import {
    ItemOptionDescription,
    ItemSlotTooltip,
} from "../../../../helpers/modal_item";
import Keypress from "vue-keypress";
import HistorySalesForm from "../../../../../../modules/Pos/Resources/assets/js/views/history/sales.vue";

export default {
    name: "DocumentPartialItem",
    props: [
        "person_type_id",
        "recordItem",
        "showDialog",
        "operationTypeId",
        "currencyTypeIdActive",
        "exchangeRateSale",
        "typeUser",
        "isEditItemNote",
        "configuration",
        "documentTypeId",
        "noteCreditOrDebitTypeId",
        "displayDiscount",
        "customerId",
        "currencyTypes",
        "isFromInvoice",
        "percentageIgv",
        "isCreditNoteAndType03",
        "isUpdateDocument",
        "documentId",
    ],
    components: {
        ItemForm,
        WarehousesDetail,
        Keypress,
        LotsGroup,
        SelectLotsForm,
        SelectSizesForm,
        HistorySalesForm,
        FactoryCodeDetail,
        "vue-ckeditor": VueCkeditor.component,
    },
    data() {
        return {
            discounts: [],
            temp_qty: null,
            readonly_meters: 0,
            hasTables: false,
            showDialogSelectSizes: false,
            showDiscounts: true,
            extra_temp: undefined,
            can_add_new_product: false,
            loading_search: false,
            search: {},
            titleAction: "",
            sizes: [],
            is_client: false,
            titleDialog: "",
            resource: "documents",
            showDialogNewItem: false,
            has_list_prices: false,
            errors: {},
            form: {},
            all_items: [],
            items: [],
            operation_types: [],
            all_affectation_igv_types: [],
            aux_items: [],
            affectation_igv_types: [],
            system_isc_types: [],
            discount_types: [],
            charge_types: [],
            attribute_types: [],
            use_price: 1,
            change_affectation_igv_type_id: false,
            activePanel: 0,
            total_item: 0,
            item_unit_types: [],
            item_unit_type: {},
            showWarehousesDetail: false,
            showFactoryCodeDetail: false,
            warehousesDetail: [],
            showListStock: false,
            search_item_by_barcode: false,
            isUpdateWarehouseId: null,
            showDialogLots: false,
            showDialogSelectLots: false,
            lots: [],
            editors: {
                classic: ClassicEditor,
            },
            value1: "hello",
            readonly_total: 0,
            readonly_total_meter: 0,
            itemLastPrice: null,
            search_item_by_barcode_presentation: false,
            showDialogHistorySales: false,
            history_item_id: null,
            loading_dialog: false,
            validate_stock_add_item: false,
            itemsCategories: [],
            itemsBrands: [],
            currentItem: null,
            //item_unit_type: {}
        };
    },
    created() {
        this.loadConfiguration();
        this.$store.commit("setConfiguration", this.configuration);
        this.initForm();
        if (this.displayDiscount !== undefined) {
            if (this.displayDiscount == true) {
                this.showDiscounts = true;
            } else {
                this.showDiscounts = false;
            }
        }
    },
    async mounted() {
        await this.getTables();
        this.$eventHub.$on("reloadDataItems", (item_id) => {
            this.reloadDataItems(item_id);
        });

        this.$eventHub.$on("selectWarehouseId", (warehouse_id) => {
            this.form.warehouse_id = warehouse_id;
            let warehouse = this.warehousesDetail.find(
                (warehouse) => warehouse.warehouse_id == warehouse_id
            );
            if (warehouse) {
                this.form.unit_price_value = Number(warehouse.price).toFixed(2);
            }
        });
        this.canCreateProduct();
    },
    computed: {
        ...mapState([
            "colors",
            "CatItemUnitsPerPackage",
            "CatItemMoldProperty",
            "CatItemUnitBusiness",
            "CatItemStatus",
            "CatItemPackageMeasurement",
            "CatItemMoldCavity",
            "CatItemProductFamily",
            "CatItemSize",
            "extra_colors",
            "extra_CatItemUnitsPerPackage",
            "extra_CatItemMoldProperty",
            "extra_CatItemSize",
            "extra_CatItemUnitBusiness",
            "extra_CatItemStatus",
            "extra_CatItemPackageMeasurement",
            "extra_CatItemMoldCavity",
            "extra_CatItemProductFamily",
            "deb",
            "config",
        ]),
        canShowExtraData: function () {
            if (
                this.config &&
                this.config.show_extra_info_to_item !== undefined
            ) {
                return this.config.show_extra_info_to_item;
            }
            return false;
        },
        showLots() {
            // if (
            //     this.form.item_id &&
            //     this.form.item.lots_enabled &&
            //     this.form.lots_group.length > 0
            // )

            if (this.form.item_id && this.form.item.lots_enabled) {
                return true;
            }

            return false;
        },
        showSizes() {
            if (this.form.item_id && this.form.item.has_sizes) {
                return true;
            }
            return false;
        },
        showSeries() {
            if (this.form.item_id && this.form.item.series_enabled) {
                return true;
            }
            return false;
        },
        documentItem() {
            if (
                this.recordItem !== undefined &&
                this.recordItem !== null &&
                this.recordItem.id !== undefined &&
                this.recordItem.id !== 0
            ) {
                this.form.document_item_id = this.recordItem.id;
                return this.recordItem.id;
            }
            return this.form.document_item_id;
        },
        edit_unit_price() {
            if (this.typeUser === "admin") {
                return true;
            }
            if (this.typeUser === "seller") {
                return this.config.allow_edit_unit_price_to_seller;
            }
            return false;
        },
        applyChangeCurrencyItem() {
            if (this.configuration)
                return this.configuration.change_currency_item;

            return false;
        },
        enabledSearchFactoryCodeItems() {
            if (this.configuration)
                return this.configuration.search_factory_code_items ? 1 : 0;
            return 0;
        },
        isNoteErrorDescription() {
            if (this.isCreditNoteAndType03 !== undefined)
                return this.isCreditNoteAndType03;
            return false;
        },
        isOpenFromInvoice() {
            if (this.isFromInvoice !== undefined && this.isFromInvoice)
                return this.isFromInvoice;

            return false;
        },
        showSpecialData() {
            return (
                this.recordItem == null ||
                this.recordItem == undefined ||
                (!_.isEmpty(this.recordItem) && this.isOpenFromInvoice)
            );
        },
        applyRestrictSaleItemsCpe() {
            if (this.configuration)
                return this.configuration.restrict_sale_items_cpe;

            return false;
        },
        isRestrictedForSale() {
            return (
                this.applyRestrictSaleItemsCpe &&
                this.isOpenFromInvoice &&
                this.form.item != undefined &&
                this.form.item.restrict_sale_cpe
            );
        },
        hasSelectedItem() {
            return this.form.item_id && !_.isEmpty(this.form.item);
        },
    },
    methods: {
        setDiscounts() {
            let { unit_price_value, quantity } = this.form;
            let total = parseFloat(unit_price_value) * parseFloat(quantity);
            let result = this.discounts.reduce((a, b) => {
                return a + parseFloat(b.result);
            }, 0);

            let discount_type = this.discount_types.find((d) => d.id == "00");
            // let total = this.form.total / 1.18;
            let percentage = (result / total) * 100;
            let factor = result / total;
            this.form.discounts.push({
                discount_type_id: discount_type.id,
                discount_type,
                description: "Descuentoss",
                percentage,
                factor,
                amount: result,
                base: result / total,
                is_amount: true,
                use_input_amount: true,
                is_split: false,
            });
            console.log(
                "🚀 ~ file: item.vue:1476 ~ setDiscounts ~ this.form.discounts:",
                this.form.discounts
            );
        },
        calculateDiscount() {
            if (this.discounts == null || this.discounts.length == 0) {
                return;
            }
            let total = this.form.unit_price_value * this.form.quantity;
            let { affectation_igv_type_id, has_igv } = this.form;

            for (let i = 0; i < this.discounts.length; i++) {
                let { percentage } = this.discounts[i];
                if (percentage != 0 || percentage != null) {
                    let discount = total * (percentage / 100);

                    if (has_igv && affectation_igv_type_id == "10") {
                        discount = discount / 1.18;
                    }
                    //redondear a dos decimales

                    // discount = Math.round(discount * 100) / 100;

                    this.discounts[i].result = discount;

                    total = total - discount;
                    this.discounts[i].base = total;
                    this.discounts[i].original_price =
                        this.form.unit_price_value;
                }
            }
        },
        removeDiscount(idx) {
            this.discounts.splice(idx, 1);
        },
        addDiscount() {
            this.discounts.push({
                percentage: 0,
                result: 0,
            });
        },
        splitBase(index) {
            let row = this.form.discounts[index];
            if (row.is_split) {
                row.amount = row.amount / 1.18;
            } else {
                row.amount = row.amount * 1.18;
            }
            //round two decimals
            row.amount = Math.round(row.amount * 100) / 100;

            this.calculateTotal();
        },
        reChangeItem(bonus_items, random_key, qty = 1) {
            if (qty < 1) {
                return;
            }
            bonus_items = bonus_items.map((b) => b.item_bonus);
            let temp = [...this.items];
            this.items = bonus_items;
            this.temp_qty = qty;
            bonus_items.forEach((bonus) => {
                let { id } = bonus;
                this.form.item_id = id;
                this.changeItem();
                this.clickAddItem(random_key);
            });
            this.temp_qty = null;
            this.items = temp;
        },
        getUnitPriceByTotal() {
            let { total, quantity } = this.form;
            let unit_price = total / quantity;
            this.form.unit_price_value = unit_price.toFixed(2);
        },
        changePrice(id) {
            let itemUnitType = this.form.item_unit_types.find(
                (i) => i.id == id
            );
            if (itemUnitType) {
                this.selectedPrice(itemUnitType);
            }
        },
        ...mapActions(["loadConfiguration", "clearExtraInfoItem"]),
        hasAttributes() {
            if (
                this.form.item !== undefined &&
                this.form.item.attributes !== undefined &&
                this.form.item.attributes !== null &&
                this.form.item.attributes.length > 0
            ) {
                return true;
            }

            return false;
        },
        ItemSlotTooltipView(item) {
            return ItemSlotTooltip(item);
        },
        ItemOptionDescriptionView(item) {
            return ItemOptionDescription(item);
        },
        async getTables() {
            try {
                this.loading_dialog = true;
                const response = await this.$http.get(
                    `/${this.resource}/item/tables`
                );
                let data = response.data;
                this.all_items = data.items;
                this.itemsCategories = data.categories;
                this.itemsBrands = data.brands;
                this.operation_types = data.operation_types;
                this.all_affectation_igv_types = data.affectation_igv_types;
                this.system_isc_types = data.system_isc_types;
                this.discount_types = data.discount_types;
                this.charge_types = data.charge_types;
                this.attribute_types = data.attribute_types;
                this.is_client = data.is_client;
                this.validate_stock_add_item = data.validate_stock_add_item;

                if (this.canShowExtraData) {
                    this.$store.commit("setColors", data.colors);
                    this.$store.commit(
                        "setCatItemUnitsPerPackage",
                        data.CatItemUnitsPerPackage
                    );
                    this.$store.commit("setCatItemStatus", data.CatItemStatus);
                    this.$store.commit(
                        "setCatItemMoldCavity",
                        data.CatItemMoldCavity
                    );
                    this.$store.commit(
                        "setCatItemMoldProperty",
                        data.CatItemMoldProperty
                    );
                    this.$store.commit(
                        "setCatItemUnitBusiness",
                        data.CatItemUnitBusiness
                    );
                    this.$store.commit(
                        "setCatItemPackageMeasurement",
                        data.CatItemPackageMeasurement
                    );
                    this.$store.commit(
                        "setCatItemProductFamily",
                        data.CatItemPackageMeasurement
                    );
                    this.$store.commit("setCatItemSize", data.CatItemSize);
                }
                this.$store.commit("setConfiguration", data.configuration);
                this.filterItems();
            } catch (e) {
            } finally {
                this.loading_dialog = false;
            }
        },
        canCreateProduct() {
            if (this.typeUser === "admin") {
                this.can_add_new_product = true;
            } else if (this.typeUser === "seller") {
                if (
                    this.config !== undefined &&
                    this.config.seller_can_create_product !== undefined
                ) {
                    this.can_add_new_product =
                        this.config.seller_can_create_product;
                }
            }
            return this.can_add_new_product;
        },
        validateQuantity() {
            if (!this.form.quantity) {
                this.setMinQuantity();
            }

            if (isNaN(Number(this.form.quantity))) {
                this.setMinQuantity();
            }

            if (typeof parseFloat(this.form.quantity) !== "number") {
                this.setMinQuantity();
            }

            if (this.form.quantity <= this.getMinQuantity()) {
                this.setMinQuantity();
            }

            this.calculateTotal();
        },
        changeValidateQuantity(event) {
            this.calculateTotal();
        },
        getMinQuantity() {
            return 0.01;
        },
        setMinQuantity() {
            this.form.quantity = this.getMinQuantity();
        },
        clickDecrease() {
            this.form.quantity = parseInt(this.form.quantity - 1);

            if (this.form.quantity <= this.getMinQuantity()) {
                this.setMinQuantity();
                return;
            }

            this.calculateTotal();
        },
        clickIncrease() {
            this.form.quantity = parseInt(this.form.quantity) + 1;
            this.calculateTotal();
        },
        async searchRemoteItemsBC() {
            console.log("xdd");
                this.loading_search = true;
                const params = {
                    ...this.search,
                    search_by_barcode: this.search_item_by_barcode ? 1 : 0,
                    search_item_by_barcode_presentation: this
                        .search_item_by_barcode_presentation
                        ? 1
                        : 0,
                    search_factory_code_items:
                        this.enabledSearchFactoryCodeItems,
                };
                await this.$http
                    .get(`/${this.resource}/search-items/`, { params })
                    .then((response) => {
                        this.items = response.data.items;
                        this.loading_search = false;
                        // this.enabledSearchItemsBarcode(input);
                        // this.enabledSearchItemBySeries();
                        if (this.items.length == 0) {
                            this.filterItems();
                        }
                    });
            
        },
        async searchRemoteItems(input) {
            if (input.length > 2) {
                this.loading_search = true;
                const params = {
                    ...this.search,
                    input: input,
                    search_by_barcode: this.search_item_by_barcode ? 1 : 0,
                    search_item_by_barcode_presentation: this
                        .search_item_by_barcode_presentation
                        ? 1
                        : 0,
                    search_factory_code_items:
                        this.enabledSearchFactoryCodeItems,
                };
                await this.$http
                    .get(`/${this.resource}/search-items/`, { params })
                    .then((response) => {
                        this.items = response.data.items;
                        this.loading_search = false;
                        this.enabledSearchItemsBarcode(input);
                        this.enabledSearchItemBySeries();
                        if (this.items.length == 0) {
                            this.filterItems();
                        }
                    });
            } else {
                await this.filterItems();
            }
        },
        filterItems() {
            this.items = this.all_items;
        },
        enabledSearchItemsBarcode(input) {
            if (this.search_item_by_barcode) {
                this.$refs.selectBarcode.$data.selectedLabel = "";

                //busqueda por presentacion
                if (this.search_item_by_barcode_presentation) {
                    if (this.items.length == 1) {
                        const item_unit_type = _.find(
                            this.items[0].item_unit_types,
                            { barcode: input }
                        );

                        if (!_.isEmpty(item_unit_type)) {
                            this.form.item_id = this.items[0].id;
                            this.$refs.selectBarcode.blur();
                            this.changeItem();
                            this.selectedPrice(item_unit_type);
                        }
                    }
                }
                //busqueda comun
                else {
                    if (this.items.length == 1) {
                        this.form.item_id = this.items[0].id;
                        this.$refs.selectBarcode.blur();
                        this.changeItem();
                    }
                }
            }
        },
        async enabledSearchItemBySeries() {
            if (this.config.search_item_by_series && this.items.length == 1) {
                this.$notify({
                    title: "Serie ubicada",
                    message: "Producto añadido!",
                    type: "success",
                    duration: 1200,
                });
                this.form.item_id = this.items[0].id;
                this.$refs.selectSearchNormal.$data.selectedLabel = "";

                await this.changeItem();

                this.lots = await this.form.item.lots.map((lot) => {
                    lot.has_sale = true;
                });

                await this.clickAddItem();

                this.$refs.selectSearchNormal.$data.selectedLabel = "";
            }

            if (this.config.search_item_by_series && this.items.length == 0) {
                this.$notify({
                    title: "Serie no ubicada",
                    message: "",
                    type: "warning",
                    duration: 1200,
                });
            }
        },
        filterMethod(query) {
            let item = _.find(this.items, { internal_id: query });

            if (item) {
                this.form.item_id = item.id;
                this.changeItem();
            }
        },
        clickFactoryCodeDetail() {
            if (!this.form.item_id) {
                return this.$message.error("Seleccione un item");
            }
            let item = _.find(this.items, { id: this.form.item_id });
            this.currentItem = item;
            this.showFactoryCodeDetail = true;
        },
        clickWarehouseDetail() {
            if (!this.form.item_id) {
                return this.$message.error("Seleccione un item");
            }

            let item = _.find(this.items, { id: this.form.item_id });

            this.warehousesDetail = item.warehouses;
            this.showWarehousesDetail = true;
        },
        // filterItems(){
        //     this.items = this.items.filter(item => item.warehouses.length >0)
        // },
        initForm() {
            this.errors = {};

            this.form = {
                // category_id: [1],
                // edit: false,
                update_price: false,
                update_unit_types: false,
                item_id: null,
                item: {},
                affectation_igv_type_id: null,
                affectation_igv_type: {},
                has_isc: false,
                system_isc_type_id: null,
                percentage_isc: 0,
                suggested_price: 0,
                quantity: 1,
                unit_price: 0,
                unit_price_value: 0,
                input_unit_price: 0,
                input_unit_price_value: 0,
                charges: [],
                discounts: [],
                attributes: [],
                has_igv: null,
                is_set: false,
                item_unit_types: [],
                has_plastic_bag_taxes: false,
                series_enabled: false,
                warehouse_id: null,
                lots_group: [],
                IdLoteSelected: null,
                document_item_id: null,
                name_product_pdf: "",
            };

            this.activePanel = 0;
            this.total_item = 0;
            this.item_unit_type = {};
            this.lots = [];
            this.sizes = [];
            this.has_list_prices = false;
        },
        // initializeFields() {
        //     this.form.affectation_igv_type_id = this.affectation_igv_types[0].id
        // },

        waitForTrueCondition() {
            return new Promise((resolve) => {
                const checkCondition = () => {
                    if (this.loading_dialog == false) {
                        resolve();
                    } else {
                        setTimeout(checkCondition, 100); // Verifica de nuevo después de 100 milisegundos
                    }
                };
                checkCondition();
            });
        },
        async create() {
            // if (this.operation_types.length == 0) {
            //     await this.getTables();
            // }
            //no avanzar hasta que loading_dialog sea false
            await this.waitForTrueCondition();
            //
            this.discounts = [];
            this.extra_temp = undefined;

            this.titleDialog = this.recordItem
                ? " Editar Producto o Servicio"
                : " Agregar Producto o Servicio";
            this.titleAction = this.recordItem ? " Editar" : " Agregar";
            let operation_type = await _.find(this.operation_types, {
                id: this.operationTypeId,
            });
            this.affectation_igv_types = await _.filter(
                this.all_affectation_igv_types,
                { exportation: operation_type.exportation }
            );

            //

            if (this.recordItem) {
                if (
                    this.recordItem.item !== undefined &&
                    this.recordItem.item.extra !== undefined
                ) {
                    this.extra_temp = this.recordItem.item.extra;
                }

                await this.reloadDataItems(this.recordItem.item_id);
                this.form.item_id = await this.recordItem.item_id;
                await this.changeItem();
                this.form.quantity = this.recordItem.quantity;
                this.form.unit_price_value =
                    this.recordItem.input_unit_price_value ||
                    this.recordItem.unit_price;
                this.form.has_plastic_bag_taxes =
                    this.recordItem.total_plastic_bag_taxes > 0 ? true : false;
                this.form.warehouse_id = this.recordItem.warehouse_id;
                this.isUpdateWarehouseId = this.recordItem.warehouse_id;
                if (this.isOpenFromInvoice) {
                    this.form.attributes = this.recordItem.attributes;
                    this.form.discounts = this.recordItem.discounts;
                    this.form.charges = this.recordItem.charges;
                }
                this.discounts = this.recordItem.discounts_acc || [];
                if (this.discounts.length > 0) {
                    this.form.discounts = [];
                }

                if (this.isEditItemNote) {
                    this.form.item.currency_type_id = this.currencyTypeIdActive;
                    this.form.item.currency_type_symbol =
                        this.currencyTypeIdActive == "PEN" ? "S/" : "$";

                    if (
                        this.documentTypeId == "07" &&
                        this.noteCreditOrDebitTypeId == "07"
                    ) {
                        this.form.document_item_id = this.recordItem.id
                            ? this.recordItem.id
                            : this.recordItem.document_item_id;
                        this.form.item.lots = this.recordItem.item.lots;
                        await this.regularizeLots();
                        this.lots = this.form.item.lots;
                    }
                } else {
                    this.form.item.lots = this.recordItem.item.lots;
                    this.form.item.sizes_selected =
                        this.recordItem.sizes_selected;
                    this.sizes = this.recordItem.sizes_selected || [];
                    this.lots = this.form.item.lots;
                }

                this.setPresentationEditItem();

                if (this.recordItem.item.name_product_pdf) {
                    this.form.name_product_pdf =
                        this.recordItem.item.name_product_pdf;
                }
                // if(this.recordItem.name_product_pdf){
                //     this.form.name_product_pdf = this.recordItem.name_product_pdf
                // }
                if (this.recordItem.item.change_free_affectation_igv) {
                    this.form.affectation_igv_type_id = "15";
                    this.form.item.change_free_affectation_igv = true;
                } else {
                    if (this.recordItem.item.original_affectation_igv_type_id) {
                        this.form.affectation_igv_type_id =
                            this.recordItem.item.original_affectation_igv_type_id;
                    }
                }
                this.calculateQuantity();
                if (this.recordItem.item.exchanged_for_points)
                    this.form.item.exchanged_for_points =
                        this.recordItem.item.exchanged_for_points;
            } else {
                this.isUpdateWarehouseId = null;
            }
            //     if(this.affectation_igv_types.length == 1){
            //     this.form.affectation_igv_type_id = this.affectation_igv_types[0].id;
            // }
            this.filterItems();
            this.$refs.selectSearchNormal.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        setPresentationEditItem() {
            if (!_.isEmpty(this.recordItem.item.presentation)) {
                this.selectedPrice(this.recordItem.item.presentation);
                this.getSelectedClass(this.recordItem.item.presentation);
            }
        },
        async regularizeLots() {
            if (this.form.document_item_id && this.form.item.lots.length > 0) {
                await this.$http
                    .get(
                        `/${this.resource}/regularize-lots/${this.form.document_item_id}`
                    )
                    .then((response) => {
                        let all_lots = this.form.item.lots;
                        let available_lots = response.data;

                        all_lots.forEach((lot, index) => {
                            let exist_lot = _.find(available_lots, (it) => {
                                return it.id == lot.id;
                            });

                            if (!exist_lot) {
                                this.form.item.lots.splice(index, 1);
                            }
                        });
                    })
                    .catch((error) => {})
                    .then(() => {});
            }
        },
        clickAddDiscount() {
            this.form.discounts.push({
                discount_type_id: null,
                discount_type: null,
                description: null,
                percentage: 0,
                factor: 0,
                amount: 0,
                base: 0,
                is_amount: false,
                use_input_amount: true,
                is_split: false,
            });
        },
        clickRemoveDiscount(index) {
            this.form.discounts.splice(index, 1);
        },
        changeDiscountType(index) {
            let discount_type_id = this.form.discounts[index].discount_type_id;
            this.form.discounts[index].discount_type = _.find(
                this.discount_types,
                { id: discount_type_id }
            );
        },
        changeIsDiscountAmount(index) {
            this.form.discounts[index].amount = 0;
            this.form.discounts[index].percentage = 0;
        },
        clickAddCharge() {
            this.form.charges.push({
                charge_type_id: null,
                charge_type: null,
                description: null,
                percentage: 0,
                factor: 0,
                amount: 0,
                base: 0,
            });
        },
        clickRemoveCharge(index) {
            this.form.charges.splice(index, 1);
        },
        changeChargeType(index) {
            let charge_type_id = this.form.charges[index].charge_type_id;
            this.form.charges[index].charge_type = _.find(this.charge_types, {
                id: charge_type_id,
            });
        },
        clickAddAttribute() {
            this.form.attributes.push({
                attribute_type_id: null,
                description: null,
                value: null,
                start_date: null,
                end_date: null,
                duration: null,
            });
        },
        clickRemoveAttribute(index) {
            this.form.attributes.splice(index, 1);
        },
        changeAttributeType(index) {
            let attribute_type_id =
                this.form.attributes[index].attribute_type_id;
            let attribute_type = _.find(this.attribute_types, {
                id: attribute_type_id,
            });
            this.form.attributes[index].description =
                attribute_type.description;
            this.inputAttribute(index);
        },
        inputAttribute(index) {
            let value = this.form.attributes[index].value;
            let hotelAttributes = ["4003", "4004"];

            this.form.attributes[index].start_date = hotelAttributes.includes(
                this.form.attributes[index].attribute_type_id
            )
                ? value
                : null;
        },
        close() {
            this.initForm();
            this.$emit("update:showDialog", false);
        },
        async changeItem() {
            this.clearExtraInfoItem();

            this.form.item = _.find(this.items, { id: this.form.item_id });
            console.log("🚀 ~ changeItem ~ this.form.item:", JSON.stringify(this.form.item))

            if (this.person_type_id) {
                let { item_customer_prices } = this.form.item;
                let exist = item_customer_prices.find(
                    (i) => i.person_type_id == this.person_type_id
                );
                if (exist) {
                    this.form.item.sale_unit_price = exist.price;
                }
            }

            this.form.item = this.setExtraFieldOfitem(this.form.item);
            this.form.item_unit_types = _.find(this.items, {
                id: this.form.item_id,
            }).item_unit_types;

            if (
                this.form.item_unit_types &&
                this.form.item_unit_types.length != 0
            ) {
                let { item_unit_types } = this.form;
                let exist = item_unit_types.find((i) => i.factor_default);
                if (exist) {
                    this.form.item_unit_type_id = exist.id;
                }
            }
            this.form.unit_price_value = this.form.item.sale_unit_price;
            this.form.meter = this.form.item.meter;
            this.lots = this.form.item.lots;
            this.form.has_igv = this.form.item.has_igv;
            this.form.has_plastic_bag_taxes =
                this.form.item.has_plastic_bag_taxes;
            this.form.affectation_igv_type_id =
                this.form.item.sale_affectation_igv_type_id;
            let affectation_igv_type = _.find(this.affectation_igv_types, {
                id: this.form.item.sale_affectation_igv_type_id,
            });
            if (affectation_igv_type == undefined) {
                this.form.affectation_igv_type_id =
                    this.affectation_igv_types[0].id;
            }

            this.form.quantity = this.temp_qty || 1;
            this.cleanTotalItem();
            this.showListStock = true;

            //asignar variables isc
            this.form.has_isc = this.form.item.has_isc;
            this.form.percentage_isc = this.form.item.percentage_isc;
            this.form.system_isc_type_id = this.form.item.system_isc_type_id;

            if (this.hasAttributes()) {
                const contex = this;
                this.form.item.attributes.forEach((row) => {
                    contex.form.attributes.push({
                        attribute_type_id: row.attribute_type_id,
                        description: row.description,
                        value: row.value,
                        start_date: row.start_date,
                        end_date: row.end_date,
                        duration: row.duration,
                    });
                });
            }

            this.form.lots_group = this.form.item.lots_group;
            this.setExtraElements(this.form.item);
            if (
                this.form.item_unit_types &&
                this.form.item_unit_types.length != 0
            ) {
                this.changePrice(this.form.item_unit_type_id);
            } else {
                await this.getLastPriceItem();
            }
            if (
                this.form.item.name_product_pdf &&
                this.config.item_name_pdf_description
            ) {
                this.form.name_product_pdf = this.form.item.name_product_pdf;
            }
        },
        focusTotalItem(change) {
            if (!change && this.form.item.calculate_quantity) {
                this.$refs.total_item.$el
                    .getElementsByTagName("input")[0]
                    .focus();
                this.total_item = this.form.unit_price_value;
            }
        },
        calculateQuantity() {
            if (this.form.item.calculate_quantity) {
                this.form.quantity = _.round(
                    this.total_item / this.form.unit_price_value,
                    4
                );
            }
            this.calculateTotal();
        },
        calculateTotal() {
            this.readonly_total = _.round(
                this.form.quantity * this.form.unit_price_value,
                4
            );
            if (
                this.form.meter &&
                this.form.meter > 0
                // &&
                // this.form.item_unit_type_id == null
            ) {
            

                this.readonly_meters = _.round(
                    this.form.quantity * this.form.meter,
                    4
                );
                this.readonly_total_meter = _.round(
                    this.form.quantity *
                        this.form.unit_price_value *
                        this.form.meter,
                    4
                );
            }
            this.calculateDiscount();
        },
        calculateUnitPrice() {
            this.form.unit_price_value = _.round(
                this.readonly_total / this.form.quantity,
                4
            );
        },
        cleanTotalItem() {
            this.total_item = null;
        },
        async validateCurrentStock() {
            this.loading_dialog = true;

            const data = {
                item_id: this.form.item_id,
                quantity: this.form.quantity,
                warehouse_id: this.form.warehouse_id,
                presentation: this.item_unit_type,
            };

            const response = await this.$http.post(
                `/validate-current-item-stock`,
                data
            );

            this.loading_dialog = false;

            return response.data;
        },
        applyValidateStock() {
            return (
                this.validate_stock_add_item &&
                this.isFromInvoice !== undefined &&
                this.isFromInvoice &&
                this.isUpdateDocument !== undefined &&
                !this.isUpdateDocument
            );
        },
        async clickAddItem(key) {
            let { affectation_igv_type_id: aff_id, unit_price_value: un_pr } =
                this.form;
            aff_id = Number(aff_id);
            un_pr = Number(un_pr);
            if (aff_id != 15 && un_pr <= 0) {
                return this.$message.error(
                    "El total debe ser mayor a 0 o elija tipo de afectación 'gravado bonificaciones'."
                );
            }
            if (this.isRestrictedForSale)
                return this.$message.error(
                    "No puede agregar el producto, está restringido para venta."
                );

            if (this.applyValidateStock()) {
                const validate_current_stock =
                    await this.validateCurrentStock();
                if (!validate_current_stock.success)
                    return this.$message.error(validate_current_stock.message);
            }

            if (this.isNoteErrorDescription) {
                if (parseFloat(this.form.unit_price_value) < 0)
                    return this.$message.error(
                        "El Precio Unitario debe ser mayor o igual 0"
                    );
            } else {
                if (parseFloat(this.form.unit_price_value) <= 0)
                    return this.$message.error(
                        "El Precio Unitario debe ser mayor a 0"
                    );
            }

            // if(this.form.quantity < this.getMinQuantity()){
            //     return this.$message.error(`La cantidad no puede ser inferior a ${this.getMinQuantity()}`);
            // }
            this.validateQuantity();
            if (this.discounts.length != 0) {
                this.setDiscounts();
            }

            if (this.form.item.lots_enabled) {
                if (!this.form.IdLoteSelected)
                    return this.$message.error("Debe seleccionar lote.");
            }
            let extra = this.form.item.extra;

            if (this.validateTotalItem().total_item) return;

            let affectation_igv_type_id = this.form.affectation_igv_type_id;
            // let unit_price = (this.form.has_igv) ? this.form.unit_price_value : this.form.unit_price_value * 1.18;
            let unit_price = this.form.unit_price_value;
            if (this.form.has_igv === false) {
                if (
                    affectation_igv_type_id === "20" ||
                    affectation_igv_type_id === "21" ||
                    affectation_igv_type_id === "40"
                ) {
                    // do nothing
                    // exonerado de igv
                } else {
                    unit_price =
                        this.form.unit_price_value * (1 + this.percentageIgv);
                }
            }

            //validar precio compra y venta
            if (this.configuration) {
                if (this.configuration.validate_purchase_sale_unit_price) {
                    let val_purchase_unit_price = parseFloat(
                        this.form.item.purchase_unit_price
                    );

                    if (val_purchase_unit_price > parseFloat(unit_price)) {
                        return this.$message.error(
                            `El precio de venta no puede ser menor al precio de compra (P. Compra: ${val_purchase_unit_price})`
                        );
                    }
                }
            }

            this.form.input_unit_price_value = this.form.unit_price_value;

            this.form.unit_price = unit_price;
            this.form.item.unit_price = unit_price;
            // this.form.item.meter =
            this.form.item.presentation = this.item_unit_type;

            this.form.affectation_igv_type = _.find(
                this.affectation_igv_types,
                { id: affectation_igv_type_id }
            );

            let IdLoteSelected = this.form.IdLoteSelected;
            let document_item_id = this.form.document_item_id;
            this.row = calculateRowItem(
                this.form,
                this.currencyTypeIdActive,
                this.exchangeRateSale,
                this.percentageIgv
            );
            this.row.update_price = this.form.update_price;
            this.row.meter = this.form.item.meter;
            this.row.item.name_product_pdf = this.row.name_product_pdf || "";
            if (this.recordItem) {
                this.row.indexi = this.recordItem.indexi;
            }

            let select_lots = await _.filter(this.row.item.lots, {
                has_sale: true,
            });
            let un_select_lots = await _.filter(this.row.item.lots, {
                has_sale: false,
            });

            if (this.form.item.series_enabled) {
                if (select_lots.length != this.form.quantity)
                    return this.$message.error(
                        "La cantidad de series seleccionadas son diferentes a la cantidad a vender"
                    );
            }
            let sizes_quantity = this.sizes.reduce((acc, size) => {
                return acc + size.qty;
            }, 0);
            if (this.form.item.has_sizes) {
                if (sizes_quantity != this.form.quantity)
                    return this.$message.error(
                        "La cantidad de tallas  son diferentes a la cantidad a vender"
                    );
            }
            let { has_bonus_item, bonus_items } = this.form.item;
            this.row.sizes_selected = this.sizes;
            let general_random_key = null;
            if (has_bonus_item) {
                let random_key = Math.random().toString(36).substring(2);
                this.row.random_key = random_key;
                general_random_key = random_key;
            }
            // this.row.depend_key = this.form.item.depend_key;
            //si key no es nulo y es string
            if (key && typeof key === "string") {
                this.row.depend_key = key;
            }

            // this.row.edit = false;
            this.initForm();
            this.row.item.extra = extra;
            //this.initializeFields()

            if (this.recordItem) {
                this.row.indexi = this.recordItem.indexi;
            }

            this.row.IdLoteSelected = IdLoteSelected;
            this.row.document_item_id = document_item_id;

            this.showMessageDetraction();
            if (this.discounts.length > 0) {
                this.row.discounts_acc = this.discounts;
            }
            this.$emit("add", this.row);
            this.discounts = [];
            if (this.search_item_by_barcode) {
                this.cleanItems();
            }
            if (has_bonus_item) {
                this.reChangeItem(
                    bonus_items,
                    general_random_key,
                    this.row.quantity
                );
            }
            if (this.recordItem) {
                this.close();
            } else {
                this.setFocusSelectItem();
            }
        },
        showMessageDetraction() {
            let total =
                this.currencyTypeIdActive === "PEN"
                    ? this.row.total
                    : this.row.total * this.exchangeRateSale;
            let total_restriction = 700;

            if (
                total >= total_restriction &&
                this.row.item.subject_to_detraction
            )
                this.$message.warning(
                    "El producto/servicio vendido está sujeto a detracción"
                );
        },
        cleanItems() {
            this.items = [];
            this.$refs.selectBarcode.$el
                .getElementsByTagName("input")[0]
                .focus();
            // console.log("add cart barcode")
        },
        validateTotalItem() {
            this.errors = {};

            if (this.form.item.calculate_quantity) {
                if (this.total_item < 0.01)
                    this.$set(this.errors, "total_item", [
                        "total venta item debe ser mayor a 0.01",
                    ]);
            }

            return this.errors;
        },
        async reloadDataItems(item_id) {
            if (!item_id) {
                await this.$http
                    .get(`/${this.resource}/table/items`)
                    .then((response) => {
                        this.items = response.data;
                        this.form.item_id = item_id;
                        // if(item_id) this.changeItem()
                        // this.filterItems()
                    });
            } else {
                await this.$http
                    .get(`/${this.resource}/search/item/${item_id}`)
                    .then((response) => {
                        this.items = response.data.items;
                        this.form.item_id = item_id;
                        this.changeItem();
                    });
            }
        },
        changePresentation() {
            let price = 0;

            this.item_unit_type = _.find(this.form.item.item_unit_types, {
                id: this.form.item_unit_type_id,
            });

            switch (this.item_unit_type.price_default) {
                case 1:
                    price = this.item_unit_type.price1;
                    break;
                case 2:
                    price = this.item_unit_type.price2;
                    break;
                case 3:
                    price = this.item_unit_type.price3;
                    break;
            }

            this.form.unit_price_value = price;
            this.form.item.unit_type_id = this.item_unit_type.unit_type_id;
        },
        getSelectedClass(row) {
            if (this.isSelectedPrice(row)) return "btn-success";

            return "btn-secondary";
        },
        isSelectedPrice(item_unit_type) {
            if (!_.isEmpty(this.item_unit_type)) {
                return this.item_unit_type.id === item_unit_type.id;
            }
            return false;
        },
        selectedPrice(row) {
            if (this.isSelectedPrice(row)) {
                this.form.item_unit_type_id = null;
                this.item_unit_type = {};
                this.form.unit_price = this.form.item.sale_unit_price;
                this.form.unit_price_value = this.form.item.sale_unit_price;
                this.form.item.unit_type_id =
                    this.form.item.original_unit_type_id;
            } else {
                let valor = 0;
                switch (row.price_default) {
                    case 1:
                        valor = row.price1;
                        break;
                    case 2:
                        valor = row.price2;
                        break;
                    case 3:
                        valor = row.price3;
                        break;
                }
                this.form.item_unit_type_id = row.id;
                this.item_unit_type = row;
                this.form.unit_price = valor;
                this.form.unit_price_value = valor;
                this.form.item.unit_type_id = row.unit_type_id;
            }

            this.calculateQuantity();
        },
        addRowLotGroup(id) {
            this.form.IdLoteSelected = id;
        },
        clickLotGroup() {
            this.showDialogLots = true;
        },
        async clickSelectLots() {
            this.showDialogSelectLots = true;
        },
        clickSelectSizes() {
            this.showDialogSelectSizes = true;
        },
        addRowSelectSize(sizes) {
            this.sizes = sizes;
        },
        addRowSelectLot(lots) {
            this.lots = lots;
        },
        focusSelectItem() {
            this.$refs.selectSearchNormal.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        setFocusSelectItem() {
            this.$refs.selectSearchNormal.$el
                .getElementsByTagName("input")[0]
                .focus();
        },
        setExtraFieldOfitem(item) {
            if (this.canShowExtraData) {
                if (item.extra === undefined) item.extra = {};
                if (item.extra.colors === undefined) item.extra.colors = null;
                if (item.extra.CatItemUnitsPerPackage === undefined)
                    item.extra.CatItemUnitsPerPackage = null;
                if (item.extra.CatItemMoldProperty === undefined)
                    item.extra.CatItemMoldProperty = null;
                if (item.extra.CatItemUnitBusiness === undefined)
                    item.extra.CatItemUnitBusiness = null;
                if (item.extra.CatItemStatus === undefined)
                    item.extra.CatItemStatus = null;
                if (item.extra.CatItemPackageMeasurement === undefined)
                    item.extra.CatItemPackageMeasurement = null;
                if (item.extra.CatItemMoldCavity === undefined)
                    item.extra.CatItemMoldCavity = null;
                if (item.extra.CatItemProductFamily === undefined)
                    item.extra.CatItemProductFamily = null;
                if (item.extra.CatItemSize === undefined)
                    item.extra.CatItemSize = null;

                if (this.extra_temp !== undefined) {
                    item.extra = this.extra_temp;
                }
            }
            return item;
        },
        setExtraElements(item) {
            this.clearExtraInfoItem();
            if (this.canShowExtraData) {
                let temp = [];
                this.colors.find((obj) => {
                    for (var i = 0, iLen = item.colors.length; i < iLen; i++) {
                        if (item.colors[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraColors", temp);
                temp = [];
                this.CatItemUnitsPerPackage.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemUnitsPerPackage.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemUnitsPerPackage[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemUnitsPerPackage", temp);
                temp = [];
                this.CatItemMoldProperty.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemMoldProperty.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemMoldProperty[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemMoldProperty", temp);
                temp = [];
                this.CatItemUnitBusiness.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemUnitBusiness.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemUnitBusiness[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemUnitBusiness", temp);
                temp = [];
                this.CatItemStatus.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemStatus.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemStatus[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemStatus", temp);
                temp = [];
                this.CatItemPackageMeasurement.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemPackageMeasurement.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemPackageMeasurement[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemPackageMeasurement", temp);
                temp = [];
                this.CatItemMoldCavity.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemMoldCavity.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemMoldCavity[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemMoldCavity", temp);
                temp = [];
                this.CatItemProductFamily.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemProductFamily.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemProductFamily[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemProductFamily", temp);
                temp = [];
                this.CatItemSize.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemSize.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemSize[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemSize", temp);
                temp = [];
                this.CatItemMoldProperty.find((obj) => {
                    for (
                        var i = 0, iLen = item.CatItemMoldProperty.length;
                        i < iLen;
                        i++
                    ) {
                        if (item.CatItemMoldProperty[i] === obj.id) {
                            temp.push(obj);
                        }
                    }
                });
                this.$store.commit("setExtraCatItemMoldProperty", temp);
                if (this.extra_temp !== undefined) {
                    this.form.item.extra = this.extra_temp;
                }
            }
        },
        checkKey(e) {
            let code = e.event.code;
            if (code === "Escape") {
                this.close();
            }
        },
        async getLastPriceItem() {
            this.itemLastPrice = null;
            let show_last_price_sale = _.has(
                this.configuration,
                "show_last_price_sale"
            )
                ? this.configuration.show_last_price_sale
                : false;
            if (show_last_price_sale) {
                if (this.customerId && this.form.item_id) {
                    const params = {
                        type_document: "CPE",
                        customer_id: this.customerId,
                        item_id: this.form.item_id,
                    };
                    await this.$http
                        .get(`/items/last-sale`, { params })
                        .then((response) => {
                            if (response.data.unit_price) {
                                this.itemLastPrice = `Último precio de venta: ${response.data.unit_price}`;
                            }
                        });
                }
            }
        },
        clickHistorySales() {
            if (!this.form.item_id) {
                return this.$message.error("Seleccione un item");
            }

            let item = _.find(this.items, { id: this.form.item_id });
            this.history_item_id = item.id;
            this.showDialogHistorySales = true;
            // console.log(item)
        },
    },
};
</script>

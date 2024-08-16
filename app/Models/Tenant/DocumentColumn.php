<?php

namespace App\Models\Tenant;

use App\Services\ItemLotsGroupService;
use Hyn\Tenancy\Abstracts\TenantModel;

class DocumentColumn extends TenantModel
{
    protected $table = 'document_columns';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'value',
        'width',
        'order',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function getValudDocumentItem($document_item, $value)
    {

        $configuration = Configuration::select(['decimal_quantity', 'decimal_quantity_unit_price_pdf', 'change_decimal_quantity_unit_price_pdf'])->first();
        // if ($configuration->change_decimal_quantity_unit_price_pdf) {
        //     if (
        //         $configuration->decimal_quantity_unit_price_pdf > 6 &&
        //         $configuration->decimal_quantity_unit_price_pdf <= 8
        //     ) {
        //         $width_column = 13;
        //     } elseif ($configuration->decimal_quantity_unit_price_pdf > 8) {
        //         $width_column = 15;
        //     } else {
        //         $width_column = 12;
        //     }
        // }
        $item = Item::find($document_item->item_id);
        $digemid = $item->getCatDigemid();
        switch ($value) {
            case  'second_name':
                return $item->second_name;
            case  'description':
                return $item->name;
            case  'category':
                return optional($item->category)->name;
            case  'model':
                return $item->model;
            case  'lot':
                $itemLotGroup = new ItemLotsGroupService;
                return $itemLotGroup->getLote($document_item->item->IdLoteSelected);
            case  'serie':
                if (isset($document_item->item->lots)) {
                    foreach ($document_item->item->lots as $index => $lot) {
                        if (isset($lot->has_sale) && $lot->has_sale) {
                        }
                        return $lot->series;
                        if ($index != count($document_item->item->lots) - 1) {

                            return "-";
                        }
                    }
                }
            case  'date_of_due':
                return $item->date_of_due;
            case  'barcode':
                return $item->barcode;
            case  'internal_code':
                return $document_item->item->internal_code;
            case  'factory_code':
                return $item->factory_code;
            case  'cod_digemid':
                return $document_item->item->cod_digemid;
            case  'nom_prod':
                if (!empty($digemid)) {
                    return $digemid->getNomProd();
                }
            case  'num_reg_san':
                if (!empty($digemid)) {
                    return $digemid->getNumRegSan();
                }
            case  'nom_titular':
                if (!empty($digemid)) {
                    return $digemid->getNomTitular();
                }
            case  'info_link':
                return $item->info_link;
            case  'discount':
                if ($document_item->discounts) {
                    $total_discount_line = 0;
                    foreach ($document_item->discounts as $disto) {
                        $amount = $disto->amount;
                        if ($disto->is_split) {
                            $amount = $amount * 1.18;
                        }
                        $total_discount_line = $total_discount_line + $amount;
                    }
                    return number_format($total_discount_line, 2);
                } else {
                    return 0;
                }
            case  'unit_value':
                return $document_item->unit_value;
            case  'total_value':
                return $document_item->total_value;
            case  'unit_price':
                if ($configuration->change_decimal_quantity_unit_price_pdf) {
                    return    $document_item->generalApplyNumberFormat($document_item->unit_price, $configuration->decimal_quantity_unit_price_pdf);
                } else {
                    return number_format($document_item->unit_price, 2);
                }
            case  'total_price':
                return $document_item->total;
        }
    }
}

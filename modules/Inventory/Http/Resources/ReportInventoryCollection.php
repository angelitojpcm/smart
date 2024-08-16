<?php

namespace Modules\Inventory\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportInventoryCollection extends ResourceCollection
{

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($row, $key) {

            $item = $row->item;
            return [
                'meter' => $item->meter,
                'laboratory' => optional($item->cat_digemid)->nom_titular,
                'num_reg_san' => optional($item->cat_digemid)->num_reg_san,
                'kardex_quantity' => (float) $row->kardex_quantity ?? 0,
                'lots_group' => $item->lots_group->transform(function ($row, $key) {
                    return [
                        'id' => $row->id,
                        'code' => $row->code,
                        'quantity' => $row->quantity,
                        'date_of_due' => $row->date_of_due,
                    ];
                }),
                'barcode' => $item->barcode,
                'internal_id' => $item->internal_id,
                'name' => $item->description,
                'description' => $item->name,
                'item_category_name' => optional($item->category)->name,
                'stock_min' => $item->stock_min,
                'stock' => $row->stock,
                'sale_unit_price' => $item->sale_unit_price,
                'purchase_unit_price' => $item->purchase_unit_price,
                'profit' => number_format($item->sale_unit_price - $item->purchase_unit_price, 2, '.', ''),
                'model' => $item->model,
                'brand_name' => $item->brand->name,
                'date_of_due' => optional($item->date_of_due)->format('d/m/Y'),
                'warehouse_name' => $row->warehouse->description
            ];
        });
    }
}

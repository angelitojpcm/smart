<?php

namespace App\Models\Tenant;


class PersonFoodDealer extends ModelTenant
{
    public $timestamps = false;
    protected $table = 'person_food_dealer';
    protected $fillable = [
        'item_id',
        'person_id',
        'order_note_id',
        'date_of_issue',
    ];

    protected $casts = [
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function order_note()
    {
        return $this->belongsTo(OrderNote::class);
    }
    // public function getCollectionData()
    // {
    //     //crea un string random de 10 caracteres
    //     $data = $this->toArray();
    //     $data['item'] = $this->item;
    //     $data['item_bonus'] = $this->bonus_item->getDataToItemModal();
    //     $data['item_bonus']["bonus"] = true;
    //     $data['item_bonus']["sale_affectation_igv_type_id"] = "15";
    //     $data['item_bonus']["sale_affectation_igv_type"] = AffectationIgvType::find("15");
    //     //borrar la key 'bonus_items" del array 'bonus'
    //     unset($data['item_bonus']["bonus"]['bonus_items']);

    //     return $data;
    // }
}

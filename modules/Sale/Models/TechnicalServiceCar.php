<?php

namespace Modules\Sale\Models;

use App\Models\Tenant\ModelTenant;
use Hyn\Tenancy\Traits\UsesTenantConnection;


class TechnicalServiceCar extends ModelTenant
{
    use UsesTenantConnection;

    protected $fillable = [
        'technical_service_id',
        'plate_number',
        'km',
        'color',
        'year',
        'chasis',
        'date_soat_due',
        'date_check_due',
        'quantity_front_lights',
        'state_front_lights',
        'quantity_directional_lights_front',
        'state_directional_lights_front',
        'quantity_directional_lights_back',
        'state_directional_lights_back',
        'quantity_hazard_lights',
        'state_hazard_lights',
        'quantity_wiper_washer_arm',
        'state_wiper_washer_arm',
        'quantity_radio_antenna',
        'state_radio_antenna',
        'quantity_side_mirrors',
        'state_side_mirrors',
        'quantity_test_handles',
        'state_test_handles',
        'quantity_alarm',
        'state_alarm',
        'quantity_booties',
        'state_booties',
        'quantity_spare_tire',
        'state_spare_tire',
        'quantity_wheel_nut',
        'state_wheel_nut',
        'quantity_wheel_cup',
        'state_wheel_cup',
        'quantity_ashtray',
        'state_ashtray',
        'quantity_internal_rearview_mirror',
        'state_internal_rearview_mirror',
        'quantity_car_radio',
        'state_car_radio',
        'quantity_protection_mat',
        'state_protection_mat',
        'quantity_rubber_floors',
        'state_rubber_floors',
        'quantity_cup_holder',
        'state_cup_holder',
        'quantity_vehicle_key',
        'state_vehicle_key',
        'quantity_extinguisher',
        'state_extinguisher',
        'quantity_jack_lever',
        'state_jack_lever',
        'quantity_toolkit',
        'state_toolkit',
        'quantity_property_card',
        'state_property_card',
        'quantity_logbook',
        'state_logbook',
        'quantity_owner_manual',
        'state_owner_manual',
        'quantity_document_holder',
        'state_document_holder',
        'auth_drive',
        'move_on',
        'no_value_things',
        'cost_for_days',
        'gasoline_level',
        'observations',
    ];

    protected $casts = [
        "auth_drive" => "boolean",
        "move_on" => "boolean",
        "no_value_things" => "boolean",
        "cost_for_days" => "boolean",
    ];

    public function technical_service()
    {
        return $this->belongsTo(TechnicalService::class);
    }
}

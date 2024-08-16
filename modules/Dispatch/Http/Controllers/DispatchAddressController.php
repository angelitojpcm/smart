<?php

namespace Modules\Dispatch\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Person;
use Exception;
use Modules\ApiPeruDev\Data\ServiceData;
use Modules\Dispatch\Http\Requests\DispatchAddressRequest;
use Modules\Dispatch\Models\DispatchAddress;

class DispatchAddressController extends Controller
{
    public function tables()
    {
        $locations = func_get_locations();

        return [
            'locations' => $locations
        ];
    }

    public function store(DispatchAddressRequest $request)
    {
        $id = $request->input('id');
        $record = DispatchAddress::query()->firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'id' => $record->id
        ];
    }

    public function destroy($id)
    {
        DispatchAddress::query()
            ->find($id)
            ->update([
                'is_active' => false,
            ]);

        return [
            'success' => true,
            'message' => 'Dirección eliminada con éxito'
        ];
    }
    function transformAddress($person){
        $address = $person->address;
        $department_id = $person->department_id;
        $province_id = $person->province_id;
        $district_id = $person->district_id;
        $location_id = null;
        if($department_id && $province_id && $district_id){
            $location_id = [$department_id, $province_id, $district_id];
        }

        return [
            'location_id' => $location_id,
            'address' => $address,
        ];
    }

    public function getOptions($person_id)
    {   
        $person = Person::find($person_id);
        $address = $this->transformAddress($person);
        // if($address['location_id'] == null){
        //     throw new Exception("El cliente no tiene el ubigeo en su dirección", 1);
        // }
        $addresses = DispatchAddress::query()
            ->where('person_id', $person_id)
            ->get()
            ->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'location_id' => $row->location_id,
                    'address' => $row->address
                ];
            });
            //insertar address en primer lugar
        //buscar en $addresses si existe address 
        if($address['location_id']!=null && $address['address']!=null){
            $address_exist = $addresses->where('address', $address['address'])
            ->where('location_id', $address['location_id'])
            ->first();
    
            if(!$address_exist){
                $dispatch_address = 
                DispatchAddress::updateOrCreate([
                    'person_id' => $person_id,
                    'address' => $address['address'],
                    'location_id' => $address['location_id'],
                ]);
                $addresses->prepend($dispatch_address);
            }
        }
        //crear address en DispatchAddress



        
        return $addresses;
    }

    public function searchAddress($person_id)
    {
        $person = Person::query()->find($person_id);
        if ($person->identity_document_type_id === '1') {
            $type = 'dni';
        } elseif ($person->identity_document_type_id === '6') {
            $type = 'ruc';
        } else {
            return [
                'success' => false,
                'message' => 'No se encontró dirección'
            ];
        }
        return (new ServiceData())->service($type, $person->number);
    }
}

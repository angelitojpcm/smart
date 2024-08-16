<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DocumentColumn;
use Illuminate\Http\Request;

class DocumentColumnController extends Controller
{


    public function index(){

        return view('tenant.document_columns.index');
    }
    public function records()
    {
        $records = DocumentColumn::all();
        return $records;
    }

    
    public function record($id)
    {
        $record = DocumentColumn::findOrFail($id);
        return $record;
    }


    public function store(Request $request)
    {

        $id = $request->input('id');
        $agency = DocumentColumn::firstOrNew(['id' => $id]);
        $agency->fill(request()->all());
        $agency->save();

        return [
            'success' => true,
            'data' => $agency,
            'message' => ($id) ? 'Agencia editada con éxito' : 'Agencia registrada con éxito'
        ];
    }
}

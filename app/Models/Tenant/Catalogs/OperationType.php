<?php

namespace App\Models\Tenant\Catalogs;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class OperationType extends ModelCatalog
{
    use UsesTenantConnection;

    protected $table = "cat_operation_types";
    public $incrementing = false;

    protected $casts = [
        'exportation' => 'integer',
        'free' => 'integer',
    ];
}
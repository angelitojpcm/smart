<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Catalogs\CurrencyType;

class SaleNoteFee extends ModelTenant
{
    public $timestamps = false;
    protected $table = 'sale_note_fee';

    protected $fillable = [
        'sale_note_id',
        'date',
        'currency_type_id',
        'amount',
    ];

    protected $casts = [
        // 'date' => 'date',
        'amount' => 'float',
    ];

    


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale_note()
    {
        return $this->belongsTo(SaleNote::class, 'sale_note_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency_type()
    {
        return $this->belongsTo(CurrencyType::class, 'currency_type_id');
    }

}

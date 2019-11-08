<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_details';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'prod_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prod_id', 'purchase_date', 'purchase_price', 'km', 'tax', 'seller_name', 'seller_phone', 'seller_bank_name', 'seller_bank_account', 'seller_idcard', 'unit_in_tkp', 'plat_no'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    // protected $guarded = [];
}

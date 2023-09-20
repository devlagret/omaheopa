<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'purchase_payment_item'; 
    protected $primaryKey   = 'payment_item_id';
    
    protected $guarded = [
        'payment_item_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

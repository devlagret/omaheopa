<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentGiro extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'purchase_payment_giro'; 
    protected $primaryKey   = 'payment_giro_id';
    
    protected $guarded = [
        'payment_giro_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

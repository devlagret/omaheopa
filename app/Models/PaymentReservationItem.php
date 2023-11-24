<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Model;

class PaymentReservationItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'sales_payment_reservation_item'; 
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
    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDeletedScope);
    // }
}

<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentTransfer extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'purchase_payment_transfer'; 
    protected $primaryKey   = 'payment_transfer_id';
    
    protected $guarded = [
        'payment_transfer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

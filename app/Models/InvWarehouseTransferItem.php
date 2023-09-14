<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvWarehouseTransferItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'invt_warehouse_transfer_item'; 
    protected $primaryKey   = 'warehouse_transfer_item_id';
    
    protected $guarded = [
        'warehouse_transfer_item_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

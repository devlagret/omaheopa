<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvWarehouseTransferType extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'invt_warehouse_transfer_type'; 
    protected $primaryKey   = 'warehouse_transfer_type_id';
    
    protected $guarded = [
        'warehouse_transfer_type_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

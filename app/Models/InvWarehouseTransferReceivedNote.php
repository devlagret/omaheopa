<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvWarehouseTransferReceivedNote extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'inv_warehouse_transfer_received_note'; 
    protected $primaryKey   = 'warehouse_transfer_received_note_id';
    
    protected $guarded = [
        'warehouse_transfer_received_note_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvWarehouseLocation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'invt_warehouse_location'; 
    protected $primaryKey   = 'warehouse_location_id';
    
    protected $guarded = [
        'warehouse_location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

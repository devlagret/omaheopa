<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvGoodsReceivedNote extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'invt_goods_received_note'; 
    protected $primaryKey   = 'goods_received_note_id';
    
    protected $guarded = [
        'goods_received_note_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRoomPrice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_room_price';
    protected $primaryKey = 'room_price_id';
    public function room(){
        return $this->belongsTo(CoreRoom::class,'room_id','room_id');
    }
    public function type(){
        return $this->belongsTo(CorePriceType::class,'price_type_id','price_type_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}

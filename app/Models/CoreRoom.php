<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreRoom extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'core_room';
    protected $primaryKey = 'room_id';
    public function building(){
        return $this->belongsTo(CoreBuilding::class,'building_id','building_id');
    }
    public function roomType(){
        return $this->belongsTo(CoreRoomType::class,'room_type_id','room_type_id');
    }
    public function price(){
        return $this->hasMany(SalesRoomPrice::class,'room_id','room_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}

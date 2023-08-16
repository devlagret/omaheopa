<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreRoomType extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'core_room_type';
    protected $primaryKey = 'room_type_id';
    public function room(){
        $this->hasMany(CoreRoomType::class,'room_type_id','room_type_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}

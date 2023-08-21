<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreBuilding extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'core_building';
    protected $primaryKey = 'building_id';
    public function rooms(){
        return $this->hasMany(CoreRoomType::class,'building_id','building_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}

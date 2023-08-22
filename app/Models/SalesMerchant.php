<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesMerchant extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_merchant';
    protected $primaryKey = 'merchant_id';
    public function categoy(){
        return $this->hasMany(CoreBuilding::class,'building_id','building_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}

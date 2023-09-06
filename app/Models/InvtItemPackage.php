<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvtItemPackage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table        = 'invt_item_package';
    protected $primaryKey   = 'invt_item_package_id';
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
    public function detail(){
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function unit(){
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    public function items(){
        return $this->belongsTo(InvtItem::class,'package_item_id','package_item_id');

    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

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
    protected $primaryKey   = 'item_package_id';
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
    public function item() {
        return $this->hasMany(InvtItemPackageItem::class,'item_packge_id','item_packge_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

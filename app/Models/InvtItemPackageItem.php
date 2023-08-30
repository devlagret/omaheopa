<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvtItemPackageItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table        = 'invt_item_package_item';
    protected $primaryKey   = 'invt_item_package_item_id';
    public function package() {
        return $this->belongsTo(InvtItemPackage::class,'item_package_id','item_package_id');
    }
    public function invtItem() {
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

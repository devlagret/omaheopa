<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItem extends Model
{
    protected $table = 'invt_item';
    protected $primaryKey = 'item_id';
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class,'merchant_id');
    }
    public function category() {
        return $this->belongsTo(InvtItemCategory::class,'item_category_id', 'item_category_id')->withDefault();;
    }
    public function packets() {
        return $this->hasMany(InvtItemPackage::class,'item_id','item_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

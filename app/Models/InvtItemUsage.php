<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvtItemUsage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'invt_item_usage';
    protected $primaryKey = 'invt_item_usage_id';
    public function item(){
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function merchant(){
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
    public function unit(){
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

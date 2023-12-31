<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockAdjustmentItem extends Model
{
    // use HasFactory;
    protected $table        = 'invt_stock_adjustment_item';
    protected $primaryKey   = 'stock_adjustment_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    public function item() {
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}
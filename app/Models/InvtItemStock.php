<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemStock extends Model
{
    // use HasFactory;
    protected $table        = 'invt_item_stock';
    protected $primaryKey   = 'item_stock_id';
    public function item() {
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    public function category() {
        return $this->belongsTo(InvtItemCategory::class,'item_category_id','item_category_id');
    }
    public function warehouse() {
        return $this->belongsTo(InvtWarehouse::class,'warehouse_id','warehouse_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
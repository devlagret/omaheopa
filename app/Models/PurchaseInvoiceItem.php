<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    // use HasFactory;
    protected $table = 'purchase_invoice_item';
    protected $primaryKey = 'purchase_invoice_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
    public function item() {
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
    public function warehouse() {
        return $this->belongsTo(InvtWarehouse::class,'warehouse_id','warehouse_id');
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    public function category() {
        return $this->belongsTo(InvtItemCategory::class,'item_category_id','item_category_id');
    }
    public function invoice() {
        return $this->belongsTo(PurchaseInvoice::class,'purchase_invoice_id','purchase_invoice_id');
    }
    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDeletedScope);
    // }
}

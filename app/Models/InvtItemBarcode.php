<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvtItemBarcode extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'invt_item_barcode';
    protected $primaryKey = 'item_barcode_id';
    public function item() {
        return $this->belongsTo(InvtItem::class,"item_id","item_id");
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
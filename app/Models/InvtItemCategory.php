<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemCategory extends Model
{
    protected $table        = 'invt_item_category';
    protected $primaryKey   = 'item_category_id';
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class, "merchant_id");
    }
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}

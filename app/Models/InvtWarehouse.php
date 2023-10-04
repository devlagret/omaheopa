<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtWarehouse extends Model
{
    protected $table        = 'invt_warehouse';
    protected $primaryKey   = 'warehouse_id';
    public function merchant() {
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

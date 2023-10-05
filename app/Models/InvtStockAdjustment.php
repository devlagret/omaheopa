<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockAdjustment extends Model
{
    // use HasFactory;
    protected $table        = 'invt_stock_adjustment';
    protected $primaryKey   = 'stock_adjustment_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

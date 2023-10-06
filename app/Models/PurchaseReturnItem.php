<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $table        = 'purchase_return_item';
    protected $primaryKey   = 'purchase_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDeletedScope);
    // }
}

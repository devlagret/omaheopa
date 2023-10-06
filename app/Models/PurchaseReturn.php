<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table        = 'purchase_return';
    protected $primaryKey   = 'purchase_return_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDeletedScope);
    // }
}

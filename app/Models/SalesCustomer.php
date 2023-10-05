<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCustomer extends Model
{
    // use HasFactory;
    protected $table        = 'sales_customer';
    protected $primaryKey   = 'customer_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

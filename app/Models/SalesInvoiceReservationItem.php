<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceReservationItem extends Model
{
    // use HasFactory;
    protected $table  = 'sales_invoice_reservation_item';
    protected $primaryKey = 'sales_invoice_reservation_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDeletedScope);
    // }
}

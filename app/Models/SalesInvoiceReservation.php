<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceReservation extends Model
{
    protected $table        = 'sales_invoice_reservation';
    protected $primaryKey   = 'sales_invoice_reservation_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
    protected static function booted()
    {
        // static::addGlobalScope(new NotDeletedScope);
    }
}

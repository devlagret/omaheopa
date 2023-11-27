<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    protected $table        = 'sales_invoice';
    protected $primaryKey   = 'sales_invoice_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
    public function items() {
        return $this->hasMany(SalesInvoiceItem::class,'sales_invoice_id','sales_invoice_id');
    }
    public function user() {
        return $this->belongsTo(User::class,'created_id','user_id');
    }
    protected static function booted()
    {
        // static::addGlobalScope(new NotDeletedScope);
    }
}

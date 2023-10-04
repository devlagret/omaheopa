<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_order';
    protected $primaryKey = 'sales_order_id';
    public function rooms() {
        return $this->hasMany(SalesOrderRoom::class,'sales_order_id','sales_order_id');
    }
    public function facilities() {
        return $this->hasMany(SalesOrderFacility::class,'sales_order_id','sales_order_id');
    }
    public function menus() {
        return $this->hasMany(SalesOrderMenu::class,'sales_order_id','sales_order_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

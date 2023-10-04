<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderRescedule extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_order_rescedule';
    protected $primaryKey = 'rescedule_id';
    public function order() {
        return $this->hasMany(SalesOrder::class,'sales_order_id','sales_order_id');
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

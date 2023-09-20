<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderRoom extends Model
{
    use HasFactory;
    protected $table = 'sales_order_room';
    protected $primaryKey = 'sales_order_room_id';
    public function detail() {
        return $this->belongsTo(CoreRoom::class,'room_id','room_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

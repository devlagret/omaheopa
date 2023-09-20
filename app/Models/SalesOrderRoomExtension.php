<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrderRoomExtension extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_order_room_extension';
    protected $primaryKey = 'extension_id';
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderFacility extends Model
{
    use HasFactory;
    protected $table = 'sales_order_facility';
    protected $primaryKey = 'sales_room_facility_id';
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

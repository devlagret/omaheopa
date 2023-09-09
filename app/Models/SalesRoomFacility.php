<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRoomFacility extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_room_facility';
    protected $primaryKey = 'room_facility_id';
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}

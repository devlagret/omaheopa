<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRoomMenu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'sales_room_menu';
    protected $primaryKey = 'room_menu_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}


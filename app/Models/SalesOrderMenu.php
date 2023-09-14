<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderMenu extends Model
{
    use HasFactory;
    protected $table = 'sales_order_menu';
    protected $primaryKey = 'sales_order_menu_id';
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemPackageItem extends Model
{
    use HasFactory;
    protected $table        = 'invt_item_package_item';
    protected $primaryKey   = 'invt_item_package_item_id';
    protected $guarded = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];
}

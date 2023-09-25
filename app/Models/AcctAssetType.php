<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctAssetType extends Model
{
    // use HasFactory;
    protected $table        = 'acct_asset_type';
    protected $primaryKey   = 'asset_type_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}

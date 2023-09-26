<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctAssetReportItem extends Model
{
    // use HasFactory;
    protected $table        = 'acct_asset_depreciation_item';
    protected $primaryKey   = 'asset_depreciation_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}

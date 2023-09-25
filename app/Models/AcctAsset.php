<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctAsset extends Model
{
    // use HasFactory;
    protected $table        = 'acct_asset';
    protected $primaryKey   = 'asset_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}

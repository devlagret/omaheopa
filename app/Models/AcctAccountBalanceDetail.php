<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctAccountBalanceDetail extends Model
{
    // use HasFactory;
    protected $table        = 'acct_account_balance_detail';
    protected $primaryKey   = 'account_balance_detail_id';
    protected $guarded = [
        'last_update'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

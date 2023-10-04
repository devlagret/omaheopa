<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    // use HasFactory;
    protected $table        = 'acct_journal_voucher';
    protected $primaryKey   = 'journal_voucher_id';
    public function items() {
        return $this->hasMany(JournalVoucherItem::class,'journal_voucher_id','journal_voucher_id');
    }
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}

<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemMutation extends Model
{
    protected $table        = 'invt_item_mutation';
    protected $primaryKey   = 'item_mutation_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

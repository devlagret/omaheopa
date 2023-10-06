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
    public function item() {
        return $this->belongsTo(InvtItem::class,'item_id','item_id');
    }
    public function category() {
        return $this->belongsTo(InvtItemCategory::class,'item_category_id','item_category_id');
    }
    public function unit() {
        return $this->belongsTo(InvtItemUnit::class,'item_unit_id','item_unit_id');
    }
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

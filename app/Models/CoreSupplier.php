<?php

namespace App\Models;

use App\Scopes\NotDeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreSupplier extends Model
{
    use SoftDeletes;
    protected $table = 'core_supplier';
    protected $primaryKey = 'supplier_id';
    protected $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new NotDeletedScope);
    }
}

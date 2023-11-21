<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLoginLog extends Model
{
    // use HasFactory;
    protected $table        = 'system_log_user';
    protected $primaryKey   = 'user_log_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
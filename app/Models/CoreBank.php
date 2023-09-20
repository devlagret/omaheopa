<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreBank extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_bank'; 
    protected $primaryKey   = 'bank_id';
    
    protected $guarded = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreExpedition extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_expedition'; 
    protected $primaryKey   = 'expedition_id';
    
    protected $guarded = [
        'expedition_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}

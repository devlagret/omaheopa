<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemMenuMapping extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'system_menu_mapping'; 
    protected $primaryKey   = 'menu_mapping_id';
    
    protected $guarded = [
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function menu(){
        return  $this->belongsTo(SystemMenu::class,'id_menu','id_menu');
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'system_user'; 
    protected $primaryKey   = 'user_id';
    
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'password',
        'user_group_id',
        'division_id',
        'user_token',
        'company_id',
        'merchant_id',
        'phone_number',
        'full_name',
        'data_state',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function group() {
        return $this->hasOne(SystemUserGroup::class,'user_group_id','user_group_id');
    }
    public function merchant(){
        return $this->belongsTo(SalesMerchant::class,'merchant_id','merchant_id');
    }
}

<?php
namespace App\Helpers;

use App\Models\InvtItemStock;
use Illuminate\Support\Facades\Auth;

class StockHelper{
    protected $data;
    public static function get(){
        
    }
    public static function add(int $quantity){

    }
    public static function sub(int $quantity){

    }
    public static function find(int $item_id, int $unit_id,int $warehouse_id){
       self::$data = InvtItemStock::where('company_id',Auth::user()->company_id)
        ->where('data_state',0)
        ->where('item_unit_id',$unit_id)
        ->where('warehouse_id',$warehouse_id)
        ->where('item_id',$item_id)->first();
        $qty = self::$data->last_balance;
    }
}
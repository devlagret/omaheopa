<?php
namespace App\Helpers;

use App\Models\InvtItem;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use Illuminate\Support\Facades\Auth;

class StockHelper{
    public $qty;
    protected static $data;
    protected static $item;
    public function get(){
        return self::$data;
    }
    /**
     * Add stock
     *
     * @param integer $quantity (Will converted to absolute value)
     * @param integer|string|null $unit
     * @return bool
     */
    public function add(int $quantity = 1,int|string $unit=null){
        $data = self::$data;
        $data->last_balance = ($data->last_balance + (abs($quantity)* is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit)));
        return $data->save();
    }
    /**
     * Substac stock
     *
     * @param integer $quantity (Will converted to absolute value)
     * @param integer|string|null $unit
     * @return bool
     */
    public function sub(int $quantity = 1,int|string $unit=null){
        $data = self::$data;
        $data->last_balance = ($data->last_balance - (abs($quantity)* is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit)));
        return $data->save();
    }
    /**
     * Get item stock by item code
     *
     * @param integer $item_id
     * @param integer|string|null $unit_id if null get the first unit (unit id|unit code)
     * @return StockHelper
     */
    public static function find(int $item_id, int|string $unit=null){
        $item = InvtItem::find($item_id);
        self::$item = $item;
        self::$data = InvtItemStock::where('company_id',Auth::user()->company_id)
        ->where('item_id',$item_id)->orderByDesc('item_stock_id')->first();
        $qty = self::$data->last_balance;
        $sh = new StockHelper();
        $sh->setdata($unit);
        return $sh;
    }
    protected function getDefaultQty(int|String $unit){
        $unit_id = $this->getUnitId($unit);
        for($i = 1;$i<=3;$i++){
            if(self::$item['item_unit_id'.$i]==$unit_id){
            return self::$item['item_default_quantity'.$i];
            }
        }
    }
    protected function getUnitId(int|String $unit){
        if(is_int($unit)||(int)$unit){
            return (int)$unit;
        }elseif(is_string($unit)){
            return InvtItemUnit::get()->pluck('item_unit_id','item_unit_code')[$unit];
        }
    }
    private function setdata($unit = null){
        is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit);
        $this->qty = (self::$data->last_balance/$unit);
    }
}
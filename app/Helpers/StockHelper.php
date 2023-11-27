<?php
namespace App\Helpers;

use App\Models\InvtItem;
use App\Models\InvtItemUnit;
use App\Models\InvtItemStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockHelper{
    public $qty;
    protected static $data;
    protected static $warehouse;
    protected static $makeStock;
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
    public function add(int $quantity = 1,$unit=null){
        $data = self::$data;
        if(count($data->get())==0&&self::$makeStock){
           return self::make(self::$item->item_id,self::$warehouse,(0+abs($quantity)));
        }
        return $data->update([
            'last_balance' => DB::raw('last_balance +'.(abs($quantity)* (is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit)))),
            'updated_id'       => Auth::id()]);
    }
    /**
     * Substract stock
     *
     * @param integer $quantity (Will converted to absolute value)
     * @param integer|string|null $unit
     * @return bool
     */
    public function sub(int $quantity = 1,$unit=null){
        $data = self::$data;
        if(count($data->get())==0&&self::$makeStock){
            return self::make(self::$item->item_id,self::$warehouse,(0-abs($quantity)));
        }
        return $data->update([
            'last_balance' => DB::raw('last_balance -'.(abs($quantity) * (is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit)))),
            'updated_id'       => Auth::id()]);
    }
    /**
     * Update stock
     *
     * @param integer $quantity (Will converted to absolute value)
     * @param integer|string|null $unit
     * @return bool
     */
    public function update(int $quantity = 1,$unit=null){
        $data = self::$data;
        if(count($data->get())==0&&self::$makeStock){
            return self::make(self::$item->item_id,self::$warehouse,(0+$quantity));
        }
        return $data->update([
            'last_balance' => DB::raw('last_balance +'.($quantity * (is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit)))),
            'updated_id'       => Auth::id()]);
    }
    /**
     * Find item stock by item code and warehouse
     * if the warehouse is empty all warehouse will be updated (and retrived)
     * @param integer $warehouse_id
     * @param integer $item_id
     * @param integer|string|null $unit_id if null get the first unit (unit id|unit code)
     * @param integer $makeStock make stock data if data with warehouse provided not exist
     * @return StockHelper
     */
    public static function find(int $item_id, $unit=null,int $warehouse_id=null,$makeStock=1){
        self::$makeStock=$makeStock;
        self::$warehouse=$warehouse_id;
        $item = InvtItem::find($item_id);
        self::$item = $item;
        $stock = InvtItemStock::where('company_id',Auth::user()->company_id)
                    ->where('item_id',$item_id)->orderByDesc('item_stock_id');
        if(!empty($warehouse_id)){
            $stock->where('warehouse_id',$warehouse_id);
        }
        
        self::$data=$stock;
        $sh = new StockHelper();
        $sh->setdata($unit);
        return $sh;
    }
    /**
     * Make Stock Item
     *
     * @param integer $item_id
     * @param integer $warehouse_id
     * @return void|bool
     */
    public static function make(int $item_id,int $warehouse_id,int $intialqty=0) {
        $item = self::$item;
        if(empty($item)){
        $item = InvtItem::find($item_id);
        }
        return  InvtItemStock::create([
            'company_id'        => Auth::user()->company_id,
            'warehouse_id'      => $warehouse_id,
            'item_id'           => $item_id,
            'item_unit_id'      => $item['item_unit_id1'],
            'item_category_id'  => $item['item_category_id'],
            'last_balance'      => $intialqty,
            'updated_id'        => Auth::id(),
            'created_id'        => Auth::id(),
        ]);
    }
    protected function getDefaultQty($unit){
        $unit_id = $this->getUnitId($unit);
        for($i = 1;$i<=3;$i++){
            if(self::$item['item_unit_id'.$i]==$unit_id){
            return self::$item['item_default_quantity'.$i];
            }
        }
    }
    protected function getUnitId($unit){
        if(is_int($unit)||(int)$unit){
            return (int)$unit;
        }elseif(is_string($unit)){
            return InvtItemUnit::get()->pluck('item_unit_id','item_unit_code')[$unit];
        }
    }
    private function setdata($unit = null){
        is_null($unit)?$unit = self::$item['item_default_quantity1']:$unit = $this->getDefaultQty($unit);
    }
}
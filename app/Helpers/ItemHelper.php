<?php

namespace App\Helpers;

use App\Models\InvtItem;
use App\Helpers\AppHelper;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\InvtItemCategory;

class ItemHelper extends AppHelper
{
    protected static $general=0;
    /**
     * Get item category
     *
     * @param int|mixed $lastCategoryId
     * @param Request $request (merchant_id)
     * @param integer $general
     * @return Response
     */
    public static function getCategory($lastCategoryId, Request $request, $general = 0)
    {
        $data = '';
        try {
            $category = InvtItemCategory::select('item_category_id', 'item_category_name');
            if ($general) {
                $category->whereNull('merchant_id');
            } else {
                $category->where('merchant_id', $request->merchant_id);
            }
            $category = $category->get();
            $lastCategoryId ?? $lastCategoryId = $category->first()->item_category_id;
            foreach ($category as $val) {
                $data .= "<option value='$val[item_category_id]' " . ($lastCategoryId == $val['item_category_id'] ? 'selected' : '') . ">$val[item_category_name]</option>\n";
            }
            if ($category->count() == 0) {
                $data = "<option>Wahana / Merchant Tidak Memiliki Kategori</option>\n";
            }
            return response($data);
        } catch (\Exception $e) {
            report($e);
            return response($e);
        }
    }
    /**
     * Get Item Select List
     * set $withMerchant to 0 to get general item
     *
     * @param int|mixed $lastItmId
     * @param Request $request (item_category_id,merchant_id)
     * @param integer $withMerchant
     * @return Response
     */
    public static function getItem($lastItmId, Request $request, $withMerchant = 1)
    {
        $data = '';
        try {
            $item = InvtItem::select('item_id', 'item_name')
                ->where('item_category_id', $request->item_category_id);
            if(self::$general){
                $item->where('item_status', 1);
            }else{
                $item->where('item_status', 0);
            }
            if ($withMerchant) {
                $item->where('merchant_id', $request->merchant_id);
            }
            $item= $item->get();
            $lastItmId ?? $lastItmId = $item->first()->item_id;
            foreach ($item as $val) {
                $data .= "<option value='$val[item_id]' " . ($lastItmId == $val['item_id'] ? 'selected' : '') . ">$val[item_name]</option>\n";
            }
            if ($item->count() == 0) {
                $data = "<option>Wahana / Merchant Tidak Memiliki Barang</option>\n";
            }
            return response($data);
        } catch (\Exception $e) {
            report(strval($e));
            return response($data);
        }
    }
    /**
     * Get item unit
     *
     * @param int|mixed $lastItemunit
     * @param Request $request (item_id)
     * @return Response
     */
    public static function getItemUnit($lastItemunit, Request $request)
    {
        $data = '';
        try {
            $item = InvtItem::find($request->item_id);
            $unit = InvtItemUnit::get();
            $lastItemunit ?? $lastItemunit = 1;
            for ($a = 1; $a <= 4; $a++) {
                if ($item['item_unit_id' . $a] != null&&$item['item_default_quantity' . $a] != null) {
                    $data .= "<option value='" . $item['item_unit_id' . $a] . "' " . ($lastItemunit == $item['item_unit_id' . $a] ? 'selected' : '') . ">" . $unit->where('item_unit_id', $item['item_unit_id' . $a])->pluck('item_unit_name')[0] . "</option>\n";
                }
            }
            return response($data);
        } catch (\Exception $e) {
            error_log(strval($e));
            return response($data);
        }
    }

    /**
     * Set the return item to general item
     *
     * @return  self
     */
    public static function general($general=1)
    {
        self::$general = $general;
        return new self;
    }
}

<?php
namespace App\Helpers;
use App\Models\InvtItem;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use App\Models\InvtItemCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ItemHelper extends AppHelper
{
    public static function getCategory($lastCategoryId,Request $request,$general=0)
    {
        $data = '';
        try{
        $category = InvtItemCategory::select('item_category_id', 'item_category_name');
        if($general){
        $category->whereNull('merchant_id');
        }else{
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
        }catch(\Exception $e){
        report($e);
        return response($e);
        }
    }
    /**
     * Get Item Select List
     * set $withMerchant to 0 to get general item
     *
     * @param int|mixed $lastItmId
     * @param Request $request
     * @param integer $withMerchant
     * @return Response
     */
    public function getItem($lastItmId,Request $request,$withMerchant=1){
        $data = '';
    try{
        $item = InvtItem::select('item_id', 'item_name')
            ->where('item_category_id', $request->item_category_id);
        if($withMerchant){
            $item->where('merchant_id');
        }
        $item->get();
        $lastItmId ?? $lastItmId = 1;
        foreach ( $item as $val) {
            $data .= "<option value='$val[item_id]' " . ($lastItmId == $val['item_id'] ? 'selected' : '') . ">$val[item_name]</option>\n";
        }
        if ($item->count() == 0) {
            $data = "<option>Wahana / Merchant Tidak Memiliki Barang</option>\n";
        }
        return response($data);
    }catch(\Exception $e){
        report(strval($e));
        return response($data);
    }
}
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InvtItemPackgeController extends Controller
{
    public function processAddItem(Request $request) {
        $data ='';
        $no = $request->no + 1;
        $qty = $request->qty;
       $item = InvtItem::with('category','merchant')->find($request->item_id);
       $unit = InvtItemUnit::find($request->item_unit)->item_unit_name;
       error_log($request->item_unit);
       error_log(strval($unit));
       if($item){
        $data = "<tr class='pkg-itm'><td style='text-align:center'>".$no."</td>
                        <td>".$item->category->item_category_name."</td>
                        <td>".$item->item_code."</td>
                        <td>".$item->merchant->merchant_name."</td>
                        <td>".$item->item_name."</td>
                        <td>
                        <div class='row'>
                            <input
                                oninput='function_change_quantity(".$request->item_id.",".$request->item_id.", this.value)'
                                type='number' name='item_package_".$request->item_id."_".$request->item_unit."_quantity'
                                id='item_package_".$request->item_id."_".$request->item_unit."_quantity'
                                style='width: 100%; text-align: center; height: 30px; font-weight: bold; font-size: 15px'
                                class='form-control col input-bb'
                                value='".$qty."' autocomplete='off'>
                                <div class='col-auto'>".$unit."</col>
                            </div>
                        </td>
                        <td class='text-center'>
                        <a type='button' class='btn btn-outline-danger btn-sm' href='". url('/item/delete-item/'.$request->item_id)."'>Hapus</a>
                        </td><tr>
                    ";
            $ses = Session::get('paket');
            $arr=[$request->item_id=>[$qty,$request->item_unit]];
            Session::push('paket',$arr);

        }
        return $data;
    }
    public function processDeleteItem($item_id) {
        $item = collect(Session::get('paket'));
        Session::put('paket',$item->forget($item_id)->toArray());
        return 1;
    }
    public function changeItemQty($item_id,$unit_id, $value) {
         Session::put('paket',[$item_id=> $value]);
    }
    public function processAdd(Request $request){
        dump($request->all());
        $paket = collect(Session::get('paket'));
        $data = InvtItem::wherein('item_id',$paket->keys())->get() ;
        dump($data);
        dump($paket->keys());
        return 0;
    }
    public function clearItem() {
        Session::forget('paket');
        return response(1);
    }
}

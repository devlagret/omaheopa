<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackage;
use App\Models\InvtItemPackageItem;
use App\Models\InvtItemUnit;
use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InvtItemPackgeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function processAddItem(Request $request) {
        $data ='';
        $no = $request->no + 1;
        $qty = $request->qty;
       $item = InvtItem::with('category','merchant')->find($request->item_id);
       $unit = InvtItemUnit::find($request->item_unit)->item_unit_name;
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
                                class='form-control col input-bb' min='1'
                                value='".$qty."' autocomplete='off'>
                                <div class='col-auto'>".$unit."</col>
                            </div>
                        </td>
                        <td class='text-center'>
                        <button type='button' class='btn btn-outline-danger btn-sm'  onclick='deleteIsiPaket('".$request->item_id."','".$request->item_unit."','". url('package/delete-item/')."')'>Hapus</button>
                        </td><tr>
                    ";
            $arr=[$request->item_id=>[$qty,$request->item_unit]];
            Session::push('paket',$arr);

        }
        return $data;
    }
    public function processDeleteItem($item_id,$item_unit) {
        $pktitem = collect(Session::get('paket'));
        $item = collect(Session::get('paket'))->toArray();
        Session::forget('paket');
        for($i=0;$i<=$pktitem->count()-1;$i++){
            if($pktitem[$i][array_keys($pktitem[$i])[0]][1]!=$item_unit||array_keys($pktitem[$i])[0]!=$item_id){
               Session::push('paket',$item[$i]);
            }
        }
        return 1;
    }
    public function changeItemQty($item_id,$unit_id, $value) {
        $pktitem = collect(Session::get('paket'));
        $item = collect(Session::get('paket'))->toArray();
        for($i=0;$i<=$pktitem->count()-1;$i++){
            if(array_keys($pktitem[$i])[0]==$item_id){
                if($pktitem[$i][$item_id][1]==$unit_id){
                    $item[$i][$item_id][0]=$value;
                }
            }
        }
        // Session::forget('paket');
        Session::put('paket',$item);
        return 1;
    }
    public function clearItem() {
        Session::forget('paket');
        return response(1);
    }
}

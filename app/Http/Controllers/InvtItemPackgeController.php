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
                        <a type='button' class='btn btn-outline-danger btn-sm' href='". url('/item/delete-item/'.$request->item_id)."'>Hapus</a>
                        </td><tr>
                    ";
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
    public function processAdd(Request $request){
        $fields = $request->validate([
            'package_name'  => 'required',
            'package_price'  => 'required',
        ],[
            'package_name.required'=>'Nama Paket Harus Diisi',
            'package_price.required'=>'Harga Paket Harus Diisi',
        ]);
        $paket = collect(Session::get('paket'));
        try{
            DB::beginTransaction();
            $package = InvtItemPackage::create([
                'package_name' => $fields['package_name'],
                'merchant_id' => $request->package_merchant_id,
                'package_barcode' => $request->package_barcode,
                'package_code' => $request->package_code,
                'package_unit_price' => $fields['package_price'],
                'package_remark' => $request->package_remark,
                'company_id' => Auth::user()->company_id,
                'created_id' => Auth::id(),
            ]);
            $packageId = InvtItemPackage::where('data_state','0')->orderByDesc('item_package_id')->first();
            foreach($paket as $val){
                InvtItemPackageItem::create([
                    'item_packge_id' => $packageId->item_package_id,
                    'item_id' => array_keys($val)[0],
                    'item_quantity' => $val[array_keys($val)[0]][0],
                    'item_unit_id' => $val[array_keys($val)[0]][1],
                ]);
            }
            DB::commit();
            $msg    = "Tambah Paket Berhasil";
            return redirect('/item')->with('msg', $msg);
        }catch(\Exception $e){
            error_log(strval($e));
            DB::rollBack();
            $msg  = "Tambah Paket Gagal";
            return redirect('/item')->with('msg', $msg);
        }
    }
    public function edit($item_package_id){
        $ubahpaket=1;
        $items = Session::get('items');
        $counts = collect();
        $pktitem = collect(Session::get('paket'));
        // return empty(Session::get('paket'));
        $invtpaket = InvtItemPackage::with('item')->find($item_package_id);
        dump($invtpaket);exit;
        $unit = InvtItemUnit::get(['item_unit_id','item_unit_name']);
        foreach($pktitem as $key => $val){
            if(! $counts->contains(collect($val)->keys()[0])){
                $counts->push(collect($val)->keys()[0]);
            }
          }
        $paket = InvtItem::with('category','merchant')->wherein('item_id',$counts)->get();
        $itemunits    = InvtItemUnit::where('data_state', '=', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_unit_name', 'item_unit_id');
        $category    = InvtItemCategory::where('data_state', '=', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $merchant   = SalesMerchant::where('data_state', 0)
            ->get()
            ->pluck('merchant_name', 'merchant_id');
        $data  = InvtItem::where('item_id', 0)->first();
        $base_kemasan=0;
        for($n=1;$n<=4;$n++){
            $data['item_unit_id'.$n] != null ? $base_kemasan++ : '';
        }
        return view('content.InvtItem.FormEditInvtItem', compact('data','pktitem','counts','itemunits', 'category', 'items', 'merchant', 'base_kemasan','ubahpaket'));
    }
    public function processEdit(Request $request){
        $fields = $request->validate([
            'package_name'  => 'required',
            'package_price'  => 'required',
        ],[
            'package_name.required'=>'Nama Paket Harus Diisi',
            'package_price.required'=>'Harga Paket Harus Diisi',
        ]);
        $paket = collect(Session::get('paket'));
        try{
            DB::beginTransaction();
            $package = InvtItemPackage::find($request->item_package_id);
                $package->package_name = $fields['package_name'];
                $package->merchant_id = $request->package_merchant_id;
                $package->package_barcode = $request->package_barcode;
                $package->package_code = $request->package_code;
                $package->package_unit_price = $fields['package_price'];
                $package->package_remark = $request->package_remark;
                $package->company_id = Auth::user()->company_id;
                $package->updated_id = Auth::id();

            foreach($paket as $val){
                InvtItemPackageItem::where('item_packge_id',$request->item_package_id)->delete();
                InvtItemPackageItem::create([
                    'item_packge_id' => $package->item_package_id,
                    'item_id' => array_keys($val)[0],
                    'item_quantity' => $val[array_keys($val)[0]][0],
                    'item_unit_id' => $val[array_keys($val)[0]][1],
                ]);
            }
            DB::commit();
            $msg    = "Edit Paket Berhasil";
            return redirect('/item')->with('msg', $msg);
        }catch(\Exception $e){
            error_log(strval($e));
            DB::rollBack();
            $msg  = "Edit Paket Gagal";
            return redirect('/item')->with('msg', $msg);
        }
    }
    public function clearItem() {
        Session::forget('paket');
        return response(1);
    }
    public function delete($item_package_id){
        $package=InvtItemPackage::find($item_package_id);
        $package->data_state = '1';
        $package->deleted_id = Auth::id();
        if($package->save()){if($package->delete()){
           return redirect()->route('item')->with(['type'=>'success','msg'=>'Hapus Paket Berhasil']);
        };}
        return redirect()->route('item')->with(['type'=>'danger','msg'=>'Hapus Paket Gagal']);
    }
}

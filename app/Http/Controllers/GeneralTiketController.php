<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackage;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\SalesMerchant;
use App\Models\SystemMenu;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GeneralTiketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('items');
        Session::forget('paket');

        $data = InvtItem::with('merchant')->where('item_status',1)->where('company_id', Auth::user()->company_id);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
                $data->where('merchant_id',Auth::user()->merchant_id);
        }
            $data =$data->get();
        return view('content.GeneralTiket.ListGeneralTiket', compact('data'));
    }

    public function addItem()
    {
        Session::put('token',Str::uuid());
        $canAddCategory =0;
        $counts = collect();
        $items = Session::get('items');
        $pktitem = collect(Session::get('paket'));
        $unit = InvtItemUnit::get(['item_unit_id','item_unit_name']);
        foreach($pktitem as $key => $val){
        if(! $counts->contains(collect($val)->keys()[0])){
            $counts->push(collect($val)->keys()[0]);
        }
        }
        // dump($itemnya[1]);
        $paket = InvtItem::with('category','merchant')->wherein('item_id',$counts)->get();
        // exit;
        $itemunits  = InvtItemUnit::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name', 'item_unit_id');
        $category   = InvtItemCategory::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $allmerchant   = SalesMerchant::get()->pluck('merchant_name', 'merchant_id');
        $invtitm   = InvtItem::where('data_state', 0)
            ->get()
            ->pluck('item_name', 'item_id');
        $canAddCategory=!empty(User::with('group.maping.menu')->find(Auth::id())->group->maping->where('id_menu',SystemMenu::where('id','item-category')->first()->id_menu));
        return view('content.GeneralTiket.FormAddGeneralTiket', compact('category','pktitem','allmerchant', 'itemunits', 'items', 'merchant','invtitm','canAddCategory','paket','counts','unit'));
    }

    public function addItemElements(Request $request)
    {
        $items = Session::get('items');
        if (!$items || $items == '') {
            $items['item_code']  = '';
            $items['item_name']  = '';
            $items['item_barcode']  = '';
            $items['item_remark']  = '';
            $items['item_quantity']  = '';
            $items['item_price']  = '';
            $items['package_item_id']  = 1;
            $items['item_cost']  = '';
            $items['item_category_id']  = '';
            $items['kemasan']  = 1;
            $items['merchant_id']  = '';
            $items['max_kemasan']  = 4;
        }
        $items[$request->name] = $request->value;
        Session::put('items', $items);
    }

    public function processAddTiket(Request $request)
    {
        if(empty(Session::get('token'))){return redirect('/general-ticket')->with('msg', "Tambah Barang Berhasil");}
        // dump($request->all());
        // dump(Session::get('paket'));exit;
            $fields = $request->validate([
                'item_code'         => 'required',
                'item_name'         => 'required',
                // 'item_unit_id1'     => 'required',
            ]);
            // $warehouse = InvtWarehouse::where('data_state',0)
            // ->where('company_id',Auth::user()->company_id)
            // ->where('merchant_id',$request->merchant_id)
            // ->get();
            // if(!$warehouse->count()){
            //     return redirect('/item/add-item')->with('msg','Merchant Tidak Memiliki Warehouse, Harap Tambah Warehouse.');
            // }
            
        
            DB::beginTransaction();
            try {
                $data = InvtItem::create([
                    'item_code'             => $fields['item_code'],
                    'item_name'             => $fields['item_name'],
                    'item_remark'           => $request->item_remark,
                    'item_status'           => 1,
                    // * Kemasan
                    'item_unit_id1'         => 1,
                    'item_default_quantity1'=> $request->item_default_quantity1,
                    'item_unit_price1'      => $request->item_unit_price1,
                    'item_unit_cost1'       => $request->item_unit_cost1,
                    'item_unit_id2'         => 1,
                    'item_default_quantity2'=> $request->item_default_quantity2,
                    'item_unit_price2'      => $request->item_unit_price2,
                    'item_unit_cost2'       => $request->item_unit_cost2,
                    'item_unit_id3'         => 1,
                    'item_default_quantity3'=> $request->item_default_quantity3,
                    'item_unit_price3'      => $request->item_unit_price3,
                    'item_unit_cost3'       => $request->item_unit_cost3,
                    'item_unit_id4'         => 1,
                    'item_default_quantity4'=> $request->item_default_quantity4,
                    'item_unit_price4'      => $request->item_unit_price4,
                    'item_unit_cost4'       => $request->item_unit_cost4,
                    // *
                    'company_id'            => Auth::user()->company_id,
                    'created_id'            => Auth::id(),
                ]);
                // echo json_encode($data);exit;
                $item = InvtItem::orderBy('created_at', 'DESC')->where('item_status',1)->where('company_id',Auth::user()->company_id)->first();
                // echo json_encode($item);exit;

                InvtItemStock::create([
                    'company_id'        => $item['company_id'],
                    'warehouse_id'      => 1,
                    'item_id'           => $item['item_id'],
                    'item_unit_id'      => 1,
                    'item_category_id'  => null,
                    'last_balance'      => 0,
                    'updated_id'        => Auth::id(),
                    'created_id'        => Auth::id(),
                ]);
            $itm = "";
            if(!empty(Session::get('paket'))){
                $itm = "Paket";
                $paket = collect(Session::get('paket'));
                foreach($paket as $val){
                    InvtItemPackage::create([
                        'item_id' => $item['item_id'],
                        'package_item_id' => array_keys($val)[0],
                        'item_quantity' => $val[array_keys($val)[0]][0],
                        'item_unit_id' => $val[array_keys($val)[0]][1],
                    ]);
                }
            }
                DB::commit();
                Session::forget('token');
                $msg    = "Tambah ".$itm." Berhasil";
                return redirect('/general-ticket')->with('msg', $msg);
            } catch (\Exception $e) {
                report($e);
                // dd($e);
                Session::forget('token');
                $msg  = "Tambah  Gagal";
                return redirect('/general-ticket')->with('msg', $msg);
            }

    }

    public function editTiket($item_id)
    {
        $paket='';
        $pktitem='';
        $counts = collect();
        $items = Session::get('items');
        $msg = '';
        $invtpaket = InvtItemPackage::where('item_id',$item_id)->get();
        $pkg = InvtItemPackage::where('package_item_id',$item_id)->get()->count();
        if($pkg){
            $msg ='Ada paket yang menggunakan item ini';
        }
        if($invtpaket->count()){
            //* mengecek apakah (session) paket kosong
            if(empty(Session::get('paket'))){
            //* mengisi data paket dari db jika session kosong
            foreach ($invtpaket as $itm){
            $arr=[$itm->package_item_id=>[$itm->item_quantity,$itm->item_unit_id]];
            Session::push('paket',$arr);
            }}
            //* set package item data for view
            $pktitem = collect(Session::get('paket'));
            //* set unit item data for view
            $unit = InvtItemUnit::get(['item_unit_id','item_unit_name']);
            //* get all package item id (using sesion so that the last input saved)
            foreach($pktitem as $key => $val){
                if(! $counts->contains(collect($val)->keys()[0])){
                    $counts->push(collect($val)->keys()[0]);
                }
              }
              $paket = InvtItem::with('category','merchant')->wherein('item_id',$counts)->get();
        }
              $unit = InvtItemUnit::get(['item_unit_id','item_unit_name']);

        $itemunits    = InvtItemUnit::where('data_state', '=', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_unit_name', 'item_unit_id');
        $category    = InvtItemCategory::where('data_state', '=', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $data  = InvtItem::where('item_id', $item_id)->first();
        $base_kemasan=0;
        for($n=1;$n<=4;$n++){
            $data['item_unit_id'.$n] != null ? $base_kemasan++ : '';
        }
        return view('content.GeneralTiket.FormEditGeneralTiket', compact('data','unit', 'itemunits', 'paket','pktitem','category', 'items', 'merchant', 'base_kemasan','counts','msg','pkg'));
    }
    public function processEditTiket(Request $request)
    {
        dump($request->all());
        $itm="Barang";
        $warehouse = InvtWarehouse::where('data_state',0)
            ->where('company_id',Auth::user()->company_id)
            ->where('merchant_id',$request->merchant_id)
            ->get();
        if(!$warehouse->count()){
            return redirect('/item/add-item')->with('msg','Merchant Tidak Memiliki Warehouse, Harap Tambah Warehouse.');
        }
        $fields = $request->validate([
            'item_category_id'  => 'required|integer',
            'item_code'         => 'required',
            'item_name'         => 'required',
            'item_id'         => 'required',
        ],['item_category_id.integer'=>'Wahana / Merchant Tidak Memiliki Kategori']);
        $paket= InvtItemPackage::where('item_id',$fields['item_id']);
        try{
        DB::beginTransaction();
        $table       = InvtItem::findOrFail($fields['item_id']);
        $packageitem = InvtItemPackage::with('unit')->where('package_item_id',$fields['item_id']);
        for($l=1;$l<=4;$l++){
            if($table['item_unit_id'.$l] != $request['item_unit_id'.$l]){
                if($table['item_unit_id'.$l]!=null && $request['item_unit_id'.$l]==null){
                    if($packageitem->where('item_unit_id',$table['item_unit_id'.$l])->get()->count()){
                    return redirect()->back()->withErrors('Ada Paket yang Menggunankan Item "'.$table->item_name.'" Dengan Satuan "'.$packageitem->where('item_unit_id',$table['item_unit_id'.$l])->first()->unit->item_unit_name.'". Harap Tidak Menghapus Satuan Tersebut.' );
                }
                }
                /*if($table['item_unit_id'.$l]!=null&&$request->used_in_package){
                    $itmpackage = InvtItemPackage::where('item_id',$fields['item_id'])->where('item_unit_id',$table['item_unit_id'.$l]);
                    $itmpackage->update(['item_unit_id'=> $request['item_unit_id'.$l]]);
                }*/
            }
        }
        foreach ($warehouse as $key => $val) {
            InvtItemStock::updateOrCreate(['company_id'=>Auth::user()->company_id,
                'item_id'=>$table['item_id'],
                'item_category_id'=>$table['item_category_id'],
                'warehouse_id'=>$val['warehouse_id'],
                'item_unit_id'=>$table['item_unit_id1']
            ],['item_unit_id'=>$request->item_unit_id1]);
        }
        $table->item_category_id        = $fields['item_category_id'];
        $table->item_code               = $fields['item_code'];
        $table->item_name               = $fields['item_name'];
        $table->merchant_id             = $request->merchant_id;
        $table->item_remark             = $request->item_remark;
        // * Kemasan
        $table->item_unit_id1           = $request->item_unit_id1;
        $table->item_default_quantity1  = $request->item_default_quantity1;
        $table->item_unit_price1        = $request->item_unit_price1;
        $table->item_unit_cost1         = $request->item_unit_cost1;
        $table->item_unit_id2           = $request->item_unit_id2;
        $table->item_default_quantity2  = $request->item_default_quantity2;
        $table->item_unit_price2        = $request->item_unit_price2;
        $table->item_unit_cost2         = $request->item_unit_cost2;
        $table->item_unit_id3           = $request->item_unit_id3;
        $table->item_default_quantity3  = $request->item_default_quantity3;
        $table->item_unit_price3        = $request->item_unit_price3;
        $table->item_unit_cost3         = $request->item_unit_cost3;
        $table->item_unit_id4           = $request->item_unit_id4;
        $table->item_default_quantity4  = $request->item_default_quantity4;
        $table->item_unit_price4        = $request->item_unit_price4;
        $table->item_unit_cost4         = $request->item_unit_cost4;
        $table->updated_id              = Auth::id();

        $paketarr = collect(Session::get('paket'));
        if($paket->count()&&empty(Session::get('paket'))){
            $itm = "Paket";
            $paket->delete();
        }else{
            $itm = "Paket";
            $paket->delete();
            foreach($paketarr as $vala){
                InvtItemPackage::create([
                    'item_id' => $fields['item_id'],
                    'package_item_id' => array_keys($vala)[0],
                    'item_quantity' => $vala[array_keys($vala)[0]][0],
                    'item_unit_id' => $vala[array_keys($vala)[0]][1],
                ]);
            }
        }

        if ($table->save()) {
            DB::commit();
            $msg = "Ubah ".$itm." Berhasil";
            return redirect('/general-ticket')->with('msg', $msg);
        } else {
            $msg = "Ubah ".$itm." Gagal.";
            return redirect('/general-ticket')->with('msg', $msg);
        }}catch(\Exception $e){
            report($e);
            DB::rollBack();
            $msg = "Ubah ".$itm." Gagal";
            return redirect('/general-ticket')->with('msg', $msg);
        }
    }

    public function deleteTiket($item_id)
    {
        $table             = InvtItem::findOrFail($item_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = "Hapus Tiket Berhasil";
            return redirect('/general-ticket')->with('msg', $msg);
        } else {
            $msg = "Hapus Tiket Gagal";
            return redirect('/general-ticket')->with('msg', $msg);
        }
    }

    public function addResetItem()
    {
        Session::forget('items');
        return redirect('/item/add-item');
    }
    public function getCategory(Request $request)
    {
        $data = '';
        $items = Session::get('items');
        $category = InvtItemCategory::select('item_category_id', 'item_category_name')
            ->where('item_category_type',1)
            ->where('data_state', 0)
            ->get();
        $items['item_category_id'] ?? $items['item_category_id'] = 1;
        $ctg = $items['item_category_id'];
        if($request->from_paket){
            $items['package_item_category'] ?? $items['package_item_category'] = 1;
            $ctg = $items['package_item_category'];
        }
        foreach ($category as $val) {
            $data .= "<option value='$val[item_category_id]' " . ($ctg == $val['item_category_id'] ? 'selected' : '') . ">$val[item_category_name]</option>\n";
        }
        if ($category->count() == 0) {
            $data = "<option>Wahana / Merchant Tidak Memiliki Kategori</option>\n";
        }
        return response($data);
    }
    public function addKemasan()
    {
        $items = Session::get('items');
        if (!$items || $items == '') {
            $items['item_code']  = '';
            $items['item_name']  = '';
            $items['item_barcode']  = '';
            $items['item_remark']  = '';
            $items['item_quantity']  = '';
            $items['item_price']  = '';
            $items['item_cost']  = '';
            $items['item_category_id']  = '';
            $items['kemasan']  = 1;
            $items['max_kemasan']  = 4;
            $items['package_item_id']  = 1;
        }
        $items['kemasan'] = $items['kemasan'] + 1;
        Session::put('items', $items);
    }
    public function removeKemasan()
    {
        $items = Session::get('items');
        if (!$items || $items == '') {
            $items['item_code']  = '';
            $items['item_name']  = '';
            $items['item_barcode']  = '';
            $items['item_remark']  = '';
            $items['item_quantity']  = '';
            $items['item_price']  = '';
            $items['item_cost']  = '';
            $items['item_category_id']  = '';
            $items['kemasan']  = 1;
            $items['max_kemasan']  = 4;
            $items['package_item_id']  = 1;
        }
        $items['kemasan'] = $items['kemasan'] - 1;
        Session::put('items', $items);
    }
    public function getTiketItem(Request $request){
        $data = '';
        $items = Session::get('items');
        try{
        $item = InvtItem::select('item_id', 'item_name','item_status')
            // ->where('merchant_id', $request->merchant_id)
            // ->where('item_category_id', $request->item_category_id)
            ->where('item_id', $request->item_id)
            ->where('item_status',1)
            ->where('data_state', 0)
            ->get();
        $items['package_item_id'] ?? $items['package_item_id'] = 1;
        foreach ( $item as $val) {
            $data .= "<option value='$val[item_id]' " . ($items['package_item_id'] == $val['item_id'] ? 'selected' : '') . ">$val[item_name]</option>\n";
        }
        if ($item->count() == 0) {
            $data = "<option>Wahana / Merchant Tidak Memiliki Barang</option>\n";
        }
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        return response($data);

    }
    }
    public function getItemUnit(Request $request){
        $data = '';
        $items = Session::get('items');
        try{
        $item = InvtItem::find($request->item_id);
        $unit = InvtItemUnit::get();
        $items['package_item_unit'] ?? $items['package_item_unit'] = 1;
        for ( $a = 1 ; $a <= 4; $a++) {
            if( $item['item_unit_id'.$a] != null){
                
            $data .= "<option value='".$item['item_unit_id'.$a]."' " . ($items['package_item_unit'] == $item['item_unit_id'.$a] ? 'selected' : '') .">".$unit->where('item_unit_id',$item['item_unit_id'.$a])->pluck('item_unit_name')[0]."</option>\n";
            }
        }
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        return response('error');

    }
    }
    public function checkDeleteItem($item_id) {

        $pkg = InvtItemPackage::where('item_id',$item_id)->get()->count();
        if($pkg){
           return response(1);
        }
        return response(0);
    }
    public function getItemCost(Request $request) {
        $itm = InvtItem::where('item_id',$request->item_id)->first();
        for ( $a = 1 ; $a <= 4; $a++) {
            if( !empty($itm['item_unit_id'.$a]) && $itm['item_unit_id'.$a]==$request->item_unit){
                return  ['cost'=>$itm['item_unit_cost'.$a],'price'=>$itm['item_unit_price'.$a]];
            }
        }
    }
}

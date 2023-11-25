<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreReservation;
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

class GeneralReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('items');
        Session::forget('paket');

        $data = CoreReservation::where('data_state',0)
        ->get();
        return view('content.GeneralReservation.ListGeneralReservation', compact('data'));
    }

    public function addReservation()
    {
        
        return view('content.GeneralReservation.FormAddGeneralReservation');
    }

    public function processAddReservation(Request $request)
    {
        // dump($request->all());
            $fields = $request->validate([
                'reservation_name'    => 'required',
                'reservation_price'         => 'required',
            ]);
            DB::beginTransaction();
            try {
                $data = CoreReservation::create([
                    'reservation_name'      => $fields['reservation_name'],
                    'reservation_price'     => $fields['reservation_price'],
                    'reservation_remark'     => $request->reservation_remark,
                    'item_status'           => 1,
                    'company_id'            => Auth::user()->company_id,
                    'created_id'            => Auth::id(),
                ]);
                DB::commit();
                $msg    = "Tambah Master Reservasi Berhasil";
                return redirect('/general-reservation')->with('msg', $msg);
            } catch (\Exception $e) {
                report($e);
                $msg  = "Tambah  Master Reservasi Gagal";
                return redirect('/general-reservation')->with('msg', $msg);
            }

    }

    public function editReservation($reservation_id)
    {
        $reservation= CoreReservation::where('reservation_id',$reservation_id)->first();
        return view('content.GeneralReservation.FormEditGeneralReservation',compact('reservation'));
    }
    public function processEditReservation(Request $request)
    {
         // dump($request->all());
         $fields = $request->validate([
            'reservation_name'    => 'required',
            'reservation_price'         => 'required',
        ]);
        DB::beginTransaction();
        try 
        {
           CoreReservation::where('reservation_id', $request->reservation_id)
                ->update([
                'reservation_name' => $request->reservation_name,
                'reservation_price' => $request->reservation_price,
                'reservation_remark' => $request->reservation_remark,
                ]
              );      
            DB::commit();
            $msg    = "Edit Master Reservasi Berhasil";
            return redirect('/general-reservation')->with('msg', $msg);
        } catch (\Exception $e) {
            report($e);
            $msg  = "Edit  Master Reservasi Gagal";
            return redirect('/general-reservation')->with('msg', $msg);
        }
    }

    public function deleteReservation($reservation_id)
    {
        $table             = CoreReservation::findOrFail($reservation_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = "Hapus master Reservasi Berhasil";
            return redirect('/general-reservation')->with('msg', $msg);
        } else {
            $msg = "Hapus master Reservasi Gagal";
            return redirect('/general-reservation')->with('msg', $msg);
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
    public function getReservationCost(Request $request) {
        $itm = CoreReservation::where('reservation_id',$request->reservation_id)->first();
                return $itm['reservation_price'];
    }
}

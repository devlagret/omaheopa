<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemUnit;
use App\Models\SalesMerchant;
use App\Models\SystemMenu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class InvtItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('items');
        $data = InvtItem::with('merchant')->join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
            ->where('invt_item.data_state', '=', 0)
            ->where('invt_item.company_id', Auth::user()->company_id)
            ->get();
        return view('content.InvtItem.ListInvtItem', compact('data'));
    }

    public function addItem()
    {
        $canAddCategory =0;
        $items      = Session::get('items');
        $itemunits  = InvtItemUnit::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_unit_name', 'item_unit_id');
        $category   = InvtItemCategory::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $merchant   = SalesMerchant::where('data_state', 0)
            ->get()
            ->pluck('merchant_name', 'merchant_id');
        $invtitm   = InvtItem::where('data_state', 0)
            ->get()
            ->pluck('item_name', 'item_id');
         $canAddCategory=!empty(User::with('group.maping.menu')->find(Auth::id())->group->maping->where('id_menu',SystemMenu::where('id','item-category')->first()->id_menu));
        return view('content.InvtItem.FormAddInvtItem', compact('category', 'itemunits', 'items', 'merchant','invtitm','canAddCategory'));
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

    public function processAddItem(Request $request)
    {
        $fields = $request->validate([
            'item_category_id'  => 'required',
            'item_code'         => 'required',
            'item_name'         => 'required',
        ]);
        DB::beginTransaction();
        try {
            $data = InvtItem::create([
                'item_category_id'      => $fields['item_category_id'],
                'item_code'             => $fields['item_code'],
                'item_name'             => $fields['item_name'],
                'item_barcode'          => $request->item_barcode,
                'merchant_id'           => $request->merchant_id,
                'item_remark'           => $request->item_remark,
                // * Kemasan
                'item_unit_id1'         => $request->item_unit_id1,
                'item_default_quantity1'=> $request->item_default_quantity1,
                'item_unit_price1'      => $request->item_unit_price1,
                'item_unit_cost1'       => $request->item_unit_cost1,
                'item_unit_id2'         => $request->item_unit_id2,
                'item_default_quantity2'=> $request->item_default_quantity2,
                'item_unit_price2'      => $request->item_unit_price2,
                'item_unit_cost2'       => $request->item_unit_cost2,
                'item_unit_id3'         => $request->item_unit_id3,
                'item_default_quantity3'=> $request->item_default_quantity3,
                'item_unit_price3'      => $request->item_unit_price3,
                'item_unit_cost3'       => $request->item_unit_cost3,
                'item_unit_id4'         => $request->item_unit_id4,
                'item_default_quantity4'=> $request->item_default_quantity4,
                'item_unit_price4'      => $request->item_unit_price4,
                'item_unit_cost4'       => $request->item_unit_cost4,
                // *
                'company_id'            => Auth::user()->company_id,
                'created_id'            => Auth::id(),
            ]);
            DB::commit();
            $msg    = "Tambah Barang Berhasil";
            return redirect('/item')->with('msg', $msg);
        } catch (\Exception $e) {
            error_log(strval($e));
            $msg  = "Tambah Barang Gagal";
            return redirect('/item')->with('msg', $msg);
        }
    }

    public function editItem($item_id)
    {
        $items = Session::get('items');
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
        $data  = InvtItem::where('item_id', $item_id)->first();
        $base_kemasan=0;
        for($n=1;$n<=4;$n++){
            $data['item_unit_id'.$n] != null ? $base_kemasan++ : '';
        }
        return view('content.InvtItem.FormEditInvtItem', compact('data', 'itemunits', 'category', 'items', 'merchant', 'base_kemasan'));
    }

    public function processEditItem(Request $request)
    {

        $fields = $request->validate([
            'item_category_id'  => 'required',
            'item_code'         => 'required',
            'item_name'         => 'required',
            'item_id'         => 'required',
        ]);

        $table                          = InvtItem::findOrFail($fields['item_id']);
        $table->item_category_id        = $fields['item_category_id'];
        $table->item_code               = $fields['item_code'];
        $table->item_name               = $fields['item_name'];
        $table->item_barcode            = $request->item_barcode;
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

        if ($table->save()) {
            $msg = "Ubah Barang Berhasil";
            return redirect('/item')->with('msg', $msg);
        } else {
            $msg = "Ubah Barang Gagal";
            return redirect('/item')->with('msg', $msg);
        }
    }

    public function deleteItem($item_id)
    {
        $table             = InvtItem::findOrFail($item_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = "Hapus Barang Berhasil";
            return redirect('/item')->with('msg', $msg);
        } else {
            $msg = "Hapus Barang Gagal";
            return redirect('/item')->with('msg', $msg);
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
            ->where('merchant_id', $request->merchant_id)
            ->where('data_state', 0)
            ->get();
        foreach ($category as $val) {
            $data .= "<option value='$val[item_category_id]' " . ($items['item_category_id'] == $val['item_category_id'] ? 'selected' : '') . ">$val[item_category_name]</option>\n";
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
    public function getMerchantItem(Request $request){
        $data = '';
        $items = Session::get('items');
        try{
        $item = InvtItem::select('item_id', 'item_name')
            ->where('merchant_id', $request->merchant_id)
            ->where('data_state', 0)
            ->get();
        $items['package_item_id'] ?? $items['package_item_id'] = 1;
        foreach ( $item as $val) {
            $data .= "<option value='$val[item_id]' " . ($items['package_item_id'] == $val['item_id'] ? 'selected' : '') . ">$val[item_name]</option>\n";
        }
        if ($item->count() == 0) {
            $data = "<option>Wahana / Merchant Tidak Memiliki Barang</option>\n";
        }
        error_log(strval($item));
        error_log(strval("merchant id :".$request->merchant_id));
        error_log(strval("merchant id :".$request->merchant_id));
        return response($data);
    }catch(\Exception $e){
        error_log($e);
        return response($data);

    }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemUnit;
use App\Models\SalesMerchant;
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
        return view('content.InvtItem.FormAddInvtItem', compact('category', 'itemunits', 'items', 'merchant'));
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
                'merchant_id'          => $request->merchant_id,
                'item_remark'           => $request->item_remark,
                'company_id'            => Auth::user()->company_id,
                'created_id'            => Auth::id(),
            ]);
            $itm = InvtItem::where('data_state', '0')->orderByDesc('item_id')->get()->pluck('item_id');
            // * iterate kemasan
            foreach ($request->kemasan as $item) {
                $item['item_id'] = $itm[0];
                $item['company_id'] = Auth::user()->company_id;
                InvtItemPackge::create($item);
            }
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
        $package = InvtItemPackge::where('data_state', '=', 0)
            ->where('item_id', $item_id)->get();
        $data  = InvtItem::where('item_id', $item_id)->first();
        $base_kemasan = $package->count();
        return view('content.InvtItem.FormEditInvtItem', compact('data', 'itemunits', 'category', 'items', 'package', 'merchant', 'base_kemasan'));
    }

    public function processEditItem(Request $request)
    {
        dump($request->all());exit;
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
        $table->updated_id              = Auth::id();

        // * iterate kemasan
        foreach ($request->kemasan as $item) {
            $item['item_id'] = $fields['item_id'];
            $item['updated_id'] = Auth::id();
            for($c=1;$c<=$request->base_kemasan;$c++){
               $pkg = InvtItemPackge::findOrFail($request->item_packge_id);
               $pkg->item_unit_id = $request->item_unit_id;
               $pkg->item_default_quantity = $request->item_default_quantity;
               $pkg->item_unit_price = $request->item_unit_price;
               $pkg->item_unit_cost = $request->item_unit_cost;
            }
        }
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
        error_log("category id : " . $items['item_category_id']);
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
        }
        $items['kemasan'] = $items['kemasan'] - 1;
        error_log($items['kemasan']);
        Session::put('items', $items);
    }
}

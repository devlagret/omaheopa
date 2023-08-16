<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $data = InvtItem::join('invt_item_category', 'invt_item_category.item_category_id', '=', 'invt_item.item_category_id')
        ->where('invt_item.data_state','=',0)
        ->where('invt_item.company_id', Auth::user()->company_id)
        ->get();
        return view('content.InvtItem.ListInvtItem', compact('data'));
    }

    public function addItem()
    {
        $items      = Session::get('items');
        $itemunits  = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $category   = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name', 'item_category_id');
        return view('content.InvtItem.FormAddInvtItem', compact('category','itemunits','items'));
    }

    public function addItemElements(Request $request)
    {
        $items = Session::get('items');
        if(!$items || $items == ''){
            $items['item_code']  = '';
            $items['item_name']  = '';
            $items['item_barcode']  = '';
            $items['item_remark']  = '';
            $items['item_quantity']  = '';
            $items['item_price']  = '';
            $items['item_cost']  = '';
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
            'item_barcode'      => '',
            'item_remark'       => '',
            'item_unit_id'      => 'required',
            'item_quantity'     => 'required',
            'item_price'        => 'required',
            'item_cost'         => 'required'
        ]);

        $data = InvtItem::create([
            'item_category_id'      => $fields['item_category_id'],
            'item_code'             => $fields['item_code'],
            'item_name'             => $fields['item_name'],
            'item_barcode'          => $fields['item_barcode'],
            'item_remark'           => $fields['item_remark'],
            'item_unit_id'          => $fields['item_unit_id'],
            'item_default_quantity' => $fields['item_quantity'],
            'item_unit_price'       => $fields['item_price'],
            'item_unit_cost'        => $fields['item_cost'],
            'company_id'            => Auth::user()->company_id,
            'updated_id'            => Auth::id(),
            'created_id'            => Auth::id(),
        ]);

        if($data->save()){
            $msg    = "Tambah Barang Berhasil";
            return redirect('/item/add-item')->with('msg', $msg);
        } else {
            $msg    = "Tambah Barang Gagal";
            return redirect('/item/add-item')->with('msg', $msg);
        }
    }

    public function editItem($item_id)
    {
        $itemunits    = InvtItemUnit::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $category    = InvtItemCategory::where('data_state','=',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $items  = InvtItem::where('item_id', $item_id)->first();
        return view('content.InvtItem.FormEditInvtItem', compact('items', 'itemunits', 'category'));
    }

    public function processEditItem(Request $request)
    {
        $fields = $request->validate([
            'item_id'           => '',
            'item_category_id'  => 'required',
            'item_status'       => 'required',
            'item_code'         => 'required',
            'item_name'         => 'required',
            'item_barcode'      => '',
            'item_remark'       => '',
            'item_unit_id'      => 'required',
            'item_quantity'     => 'required',
            'item_price'        => 'required',
            'item_cost'         => 'required'
        ]);

        $table                          = InvtItem::findOrFail($fields['item_id']);
        $table->item_category_id        = $fields['item_category_id'];
        $table->item_status             = $fields['item_status'];
        $table->item_code               = $fields['item_code'];
        $table->item_name               = $fields['item_name'];
        $table->item_barcode            = $fields['item_barcode'];
        $table->item_remark             = $fields['item_remark'];
        $table->item_unit_id            = $fields['item_unit_id'];
        $table->item_default_quantity   = $fields['item_quantity'];
        $table->item_unit_price         = $fields['item_price'];
        $table->item_unit_cost          = $fields['item_cost'];
        $table->updated_id              = Auth::id();

        if($table->save()){
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

        if($table->save()){
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackageItem;
use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class InvtItemCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('datacategory');
        $data = InvtItemCategory::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->with('merchant');

        //filter prepend
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $merchant = $merchant->prepend('Tampil Semua',0);
        // dump($merchant);

        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $data->where('merchant_id',Auth::user()->merchant_id);
        }
        $data =$data->get();
        return view('content.InvtItemCategory.ListInvtItemCategory', compact('data','merchant'));
    }

    public function addItemCategory($merchant_id = null)
    {
        $datacategory = Session::get('datacategory');
        $url = 'item-category';
        $merchant = SalesMerchant::where('data_state','0');
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant_id = Auth::user()->merchant_id;
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name','merchant_id');
        if($merchant_id != null){
            $datacategory['item_category_code']  = '';
            $datacategory['item_category_name']    = '';
            $datacategory['item_category_remark']  = '';
            $datacategory['merchant_id']  = $merchant_id;
            $datacategory['from_item']  = 1;
            $url = 'add-item';
        }
        return view('content.InvtItemCategory.FormAddInvtItemCategory',compact('datacategory','merchant','url'));
    }

    public function elementsAddItemCategory(Request $request)
    {
        $datacategory = Session::get('datacategory');
        if(!$datacategory || $datacategory == ''){
            $datacategory['item_category_code']    = '';
            $datacategory['item_category_name']    = '';
            $datacategory['item_category_remark']  = '';
            $datacategory['merchant_id']  = '';
            $datacategory['from_item']  = 0;
        }
        $datacategory[$request->name] = $request->value;
        Session::put('datacategory', $datacategory);
    }

    public function addReset()
    {
        Session::forget('datacategory');
        return redirect()->back();
    }

    public function processAddItemCategory(Request $request)
    {
        $fields = $request->validate([
            'item_category_code'     => 'required',
            'item_category_name'     => 'required',
            'merchant_id'     => 'required',
        ]);

        $data = InvtItemCategory::create([
            'item_category_code'        => $fields['item_category_code'],
            'item_category_name'        => $fields['item_category_name'],
            'merchant_id'               => $fields['merchant_id'],
            'item_category_remark'      => $request->item_category_remark,
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
        ]);
        if($data->save()){
            if($request->from_item){
                return redirect()->route('add-item')->with(['msg'=>'Tambah Kategori Berhasil','merchant_id'=>$fields['merchant_id']]);
            }
            $msg = 'Tambah Kategori Berhasil';
            return redirect('/item-category/')->with('msg',$msg);
        } else {
            $msg = 'Tambah Kategori Gagal';
            return redirect('/item-category/')->with('msg',$msg);
        }
    }

    public function editItemCategory($item_category_id)
    {
         //* check if item is in package
         $msg = '';
         $pkg = InvtItem::with('package')->where('item_category_id',$item_category_id)->get()->count();
         if($pkg){
             $msg ='Ada item yang menggunakan kategori ini';
         }
        $datacategory = Session::get('datacategory');
        $data = InvtItemCategory::where('item_category_id',$item_category_id)->first();
        $merchant = SalesMerchant::get()->pluck('merchant_name','merchant_id');
        return view('content.InvtItemCategory.FormEditInvtItemCategory', compact('data','datacategory','merchant','msg','pkg'));
    }

    public function processEditItemCategory(Request $request)
    {
        $fields = $request->validate([
            'category_id'       => '',
            'category_code'     => 'required',
            'category_name'     => 'required',
            'merchant_id'     => 'required',

        ]);

        $table                          = InvtItemCategory::findOrFail($fields['category_id']);
        $table->item_category_code      = $fields['category_code'];
        $table->item_category_name      = $fields['category_name'];
        $table->merchant_id             = $fields['merchant_id'];
        $table->item_category_remark    = $request->category_remark;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Ubah Kategori Barang Berhasil";
            return redirect('/item-category')->with('msg', $msg);
        } else {
            $msg = "Ubah Kategori Barang Gagal";
            return redirect('/item-category')->with('msg', $msg);
        }
    }

    public function deleteItemCategory($item_category_id)
    {
        $table              = InvtItemCategory::findOrFail($item_category_id);
        $table->data_state  = 1;
        $table->updated_id  = Auth::id();

        if($table->save()){
            $msg = "Hapus Kategori Barang Berhasil";
            return redirect('/item-category')->with('msg', $msg);
        } else {
            $msg = "Hapus Kategori Barang Gagal";
            return redirect('/item-category')->with('msg', $msg);
        }
    }
    
    public function checkDeleteItemCategory($item_category_id) {
        $pkg = InvtItem::where('data_state','0')->where('item_category_id',$item_category_id)->get()->count();
        if($pkg){
           return response(1);
        }
        return response(0);
    }
}

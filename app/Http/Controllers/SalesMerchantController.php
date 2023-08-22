<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SalesMerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('merchant-data');
        $merchant = SalesMerchant::get();
        return view('content.SalesMerchant.ListSalesMerchant')->with(['merchant'=>$merchant]);
    }
    public function add() {
        $sessiondata = Session::get('merchant-data');
        return view('content.SalesMerchant.FormAddSalesMerchant',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('merchant-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['merchant_type_name']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('merchant-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        $fields = $request->validate(['merchant_name'     => 'required',]);
        if(SalesMerchant::create(['merchant_name'=>$fields['merchant_name'],'created_id'=>Auth::id()])){
           return redirect()->route('sales-merchant.index')->with(['type'=>'success','msg'=>'Tambah Wahana Berhasil']);
        }
        return redirect()->route('sales-merchant.index')->with(['type'=>'danger','msg'=>'Tambah Wahana Gagal']);

    }
    public function edit($merchant_id) {
        $sessiondata = Session::get('merchant-data');
        $merchant = SalesMerchant::find($merchant_id);
        return view('content.SalesMerchant.FormEditSalesMerchant',compact('sessiondata','merchant'));
    }
    public function processEdit(Request $request){
        $fields = $request->validate(['merchant_name'=> 'required',]);
        $merchanttype = SalesMerchant::find($request->merchant_id);
        $merchanttype->merchant_name = $fields['merchant_name'];
        $merchanttype->updated_id = Auth::id();
        if($merchanttype->save()){
           return redirect()->route('sales-merchant.index')->with(['type'=>'success','msg'=>'Edit Wahana Berhasil']);
        }
        return redirect()->route('sales-merchant.index')->with(['type'=>'danger','msg'=>'Edit Wahana Gagal']);
    }
    public function delete($merchant_id) {
        $merchant=SalesMerchant::find($merchant_id);
        $merchant->data_state = '1';
        $merchant->deleted_id = Auth::id();
        if($merchant->save()){if($merchant->delete()){
           return redirect()->route('sales-merchant.index')->with(['type'=>'success','msg'=>'Hapus Wahana Berhasil']);
        };}
        return redirect()->route('sales-merchant.index')->with(['type'=>'danger','msg'=>'Hapus Wahana Gagal']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CorePriceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CorePriceTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('price-type-data');
        $pricetype = CorePriceType::get();
        return view('content.CorePriceType.ListPriceType')->with(['pricetype'=>$pricetype]);
    }
    public function add() {
        $sessiondata = Session::get('price-type-data');
        return view('content.CorePriceType.FormAddPriceType',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('price-type-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['price_type']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('price-type-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        if(CorePriceType::create(['price_type_name'=>$request->price_type_name,'created_id'=>Auth::id()])){
           return redirect()->route('price-type.index')->with(['type'=>'success','msg'=>'Tambah Tipe Harga Berhasil']);
        }
        return redirect()->route('price-type.index')->with(['type'=>'danger','msg'=>'Tambah Tipe Harga Gagal']);

    }
    public function edit($price_type_id) {
        $sessiondata = Session::get('price-type-data');
        $pricetype = CorePriceType::find($price_type_id);
        return view('content.CorePriceType.FormEditPriceType',compact('sessiondata','pricetype'));
    }
    public function processEdit(Request $request){
        $pricetype = CorePriceType::find($request->price_type_id);
        $pricetype->price_type_name = $request->price_type_name;
        $pricetype->updated_id = Auth::id();
        if($pricetype->save()){
           return redirect()->route('price-type.index')->with(['type'=>'success','msg'=>'Edit Tipe Harga Berhasil']);
        }
        return redirect()->route('price-type.index')->with(['type'=>'danger','msg'=>'Edit Tipe Harga Gagal']);
    }
    public function delete($price_type_id) {
        $pricetype=CorePriceType::find($price_type_id);
        $pricetype->data_state = '1';
        $pricetype->deleted_id = Auth::id();
        if($pricetype->save()){if($pricetype->delete()){
           return redirect()->route('price-type.index')->with(['type'=>'success','msg'=>'Hapus Tipe Harga Berhasil']);
        };}
        return redirect()->route('price-type.index')->with(['type'=>'danger','msg'=>'Hapus Tipe Harga Gagal']);
    }
}

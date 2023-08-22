<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('supplier-data');
        $supplier = CoreSupplier::get();
        return view('content.CoreSupplier.ListCoreSupplier')->with(['supplier'=>$supplier]);
    }
    public function add() {
        $sessiondata = Session::get('supplier-data');
        return view('content.CoreSupplier.FormAddCoreSupplier',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('supplier-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['supplier_name'] = '';
            $sessiondata['supplier_mobile_phone1'] = '';
            $sessiondata['supplier_address'] = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('supplier-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        $fields = $request->validate(['supplier_name' => 'required',]);
        if(CoreSupplier::create([
            'supplier_name'=>$fields['supplier_name'],
            'supplier_address'=>$request->supplier_address,
            'supplier_mobile_phone1'=>$request->supplier_mobile_phone1,
            'created_id'=>Auth::id()])){
           return redirect()->route('supplier.index')->with(['type'=>'success','msg'=>'Tambah Supplier Berhasil']);
        }
        return redirect()->route('supplier.index')->with(['type'=>'danger','msg'=>'Tambah Supplier Gagal']);

    }
    public function edit($supplier_id) {
        $sessiondata = Session::get('supplier-data');
        $supplier = CoreSupplier::find($supplier_id);
        return view('content.CoreSupplier.FormEditCoreSupplier',compact('sessiondata','supplier'));
    }
    public function processEdit(Request $request){
        $fields = $request->validate(['supplier_name' => 'required',]);
        $supplier = CoreSupplier::find($request->supplier_id);
        $supplier->supplier_name = $fields['supplier_name'];
        $supplier->supplier_address = $request->supplier_address;
        $supplier->supplier_mobile_phone1 = $request->supplier_mobile_phone1;
        $supplier->updated_id = Auth::id();
        if($supplier->save()){
           return redirect()->route('supplier.index')->with(['type'=>'success','msg'=>'Edit Supplier Berhasil']);
        }
        return redirect()->route('supplier.index')->with(['type'=>'danger','msg'=>'Edit Supplier Gagal']);
    }
    public function delete($supplier_id) {
        $supplier=CoreSupplier::find($supplier_id);
        $supplier->data_state = '1';
        $supplier->deleted_id = Auth::id();
        if($supplier->save()){if($supplier->delete()){
           return redirect()->route('supplier.index')->with(['type'=>'success','msg'=>'Hapus Supplier Berhasil']);
        };}
        return redirect()->route('supplier.index')->with(['type'=>'danger','msg'=>'Hapus Supplier Gagal']);
    }
}

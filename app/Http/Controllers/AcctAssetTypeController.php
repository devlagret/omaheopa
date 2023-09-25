<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AcctAssetTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {

        $asettype = AcctAssetType::where('data_state',0)
        ->get();

        return view('content.AcctAsetType.ListAcctAssetType',compact('asettype'));
    }
    public function add() {
        $sessiondata = Session::get('supplier-data');
        return view('content.AcctAsetType.FormAddAcctAssetType',compact('sessiondata'));
    }

    public function processAdd(Request $request) {
        $fields = $request->validate(['asset_type_code' => 'required',]);
        if(AcctAssetType::create([
            'asset_type_code'=>$fields['asset_type_code'],
            'asset_type_name'=>$request->asset_type_name,
            'asset_type_description'=>$request->asset_type_description,
            'created_id'=>Auth::id()])){
           return redirect()->route('aset-type.index')->with(['type'=>'success','msg'=>'Tambah Aset Berhasil']);
        }
        return redirect()->route('aset-type.index')->with(['type'=>'danger','msg'=>'Tambah Aset Gagal']);

    }
    public function edit($asset_type_id) {
        $sessiondata = Session::get('supplier-data');
        $supplier = AcctAssetType::find($asset_type_id);
        return view('content.AcctAsetType.FormEditAcctAssetType',compact('sessiondata','supplier'));
    }
    public function processEdit(Request $request){
        $asettype = AcctAssetType::find($request->asset_type_id);
        $asettype->asset_type_code = $request->asset_type_code;
        $asettype->asset_type_name = $request->asset_type_name;
        $asettype->asset_type_description = $request->asset_type_description;
        $asettype->updated_id = Auth::id();
        if($asettype->save()){
           return redirect()->route('aset-type.index')->with(['type'=>'success','msg'=>'Edit Aset Berhasil']);
        }
        return redirect()->route('aset-type.index')->with(['type'=>'danger','msg'=>'Edit Aset Gagal']);
    }
    public function delete($asset_type_id) {
        $asettype=AcctAssetType::find($asset_type_id);
        $asettype->data_state = '1';
        $asettype->deleted_id = Auth::id();
        if($asettype->save()){
           return redirect()->route('aset-type.index')->with(['type'=>'success','msg'=>'Hapus Aset Berhasil']);
        };
        return redirect()->route('aset-type.index')->with(['type'=>'danger','msg'=>'Hapus Aset Gagal']);
    }


}
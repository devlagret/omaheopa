<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtItemBarcodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($item_id) {
        $unit_id =collect();
        $item = InvtItem::findOrFail($item_id);
        for ( $a = 1 ; $a <= 4; $a++) {
            if( $item['item_unit_id'.$a] != null){
                $unit_id->push($item['item_unit_id'.$a]);
            }
        }
        $unit = InvtItemUnit::where("data_state","0")->whereIn("item_unit_id",$unit_id->flatten())->get()->pluck('item_unit_name', 'item_unit_id');
        $barcode = InvtItemBarcode::with('unit')->where("data_state",0)->where("item_id",$item_id)->get();
        return view('content.InvtItemBarcode.ListInvtItemBarcode', compact('barcode','item','unit'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('room-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['room_name']   = '';
            $sessiondata['room_type_id']   = '';
            $sessiondata['building_id']   = '';
            $sessiondata['room_facility']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('room-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        if(InvtItemBarcode::create([
            'item_unit_id'=>$request->item_unit_id,
            'item_barcode'=>$request->item_barcode,
            'item_id'=>$request->item_id,
            'company_id'=>Auth::user()->company_id,
            'created_id'=>Auth::id(),
            ])){
           return redirect()->route('item-barcode.index',$request->item_unit_id)->with(['type'=>'success','msg'=>'Tambah Barcode Berhasil']);
        }
        return redirect()->route('item-barcode.index',$request->item_unit_id)->with(['type'=>'danger','msg'=>'Tambah Barcode Gagal']);

    }
    public function delete($item_id,$item_barcode_id) {
        $room=InvtItemBarcode::find($item_barcode_id);
        $room->data_state = '1';
        $room->deleted_id = Auth::id();
        if($room->save()){if($room->delete()){
           return redirect()->route('item-barcode.index',$item_id)->with(['type'=>'success','msg'=>'Hapus Barcode Berhasil']);
        };}
        return redirect()->route('item-barcode.index',$item_id)->with(['type'=>'danger','msg'=>'Hapus Barcode Gagal']);
    }
}

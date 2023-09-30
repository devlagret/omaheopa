<?php

namespace App\Http\Controllers;

use App\Models\InvtItemUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HotelItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('h-item-data');
        $usage = InvtItemUsage::get();
        return view('content.HotelItem.ListUsage')->with(['usage'=>$usage]);
    }
    public function add() {
        $sessiondata = Session::get('h-item-data');
        return view('content.HotelItem.FormAddUsage.blade',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('h-item-data');
        $sessiondata[$request->name] = $request->value;
        Session::put('building-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        try {
            DB::beginTransaction();
            InvtItemUsage::create([
                'item_id'=>$request->item_id,
                'usage_type'=>1,
                'merchant_id'=>Auth::user()->merchant_id??1,
                'usage_remark'=>$request->usage_remark,
                'item_unit_id'=>$request->item_unit_id,
                'quantity'=>$request->quantity,
                'created_id'=>Auth::id()]);
            DB::commit();
           return redirect()->route('hi.index')->with(['type'=>'success','msg'=>'Tambah Penggunaan Barang Berhasil']);
        }catch(\Exception $e){
            DB::rollBack();
            report($e);
            return redirect()->route('hi.index')->with(['type'=>'danger','msg'=>'Tambah Penggunaan Barang Gagal']);
        }

    }
    public function edit($building_id) {
        $sessiondata = Session::get('room-data');
        $building = InvtItemUsage::find($building_id);
        return view('content.CoreBuilding.FormEditBuilding',compact('sessiondata','building'));
    }
    public function processEdit(Request $request){
        $building = InvtItemUsage::find($request->building_id);
        $building->building_name = $request->building_name;
        $building->updated_id = Auth::id();
        if($building->save()){
           return redirect()->route('building.index')->with(['type'=>'success','msg'=>'Edit Bangunan Berhasil']);
        }
        return redirect()->route('building.index')->with(['type'=>'danger','msg'=>'Edit Bangunan Gagal']);
    }
    public function delete($building_id) {
        $building=InvtItemUsage::find($building_id);
        $building->data_state = '1';
        $building->deleted_id = Auth::id();
        if($building->save()){if($building->delete()){
           return redirect()->route('building.index')->with(['type'=>'success','msg'=>'Hapus Bangunan Berhasil']);
        };}
        return redirect()->route('building.index')->with(['type'=>'danger','msg'=>'Hapus Bangunan Gagal']);
    }
}

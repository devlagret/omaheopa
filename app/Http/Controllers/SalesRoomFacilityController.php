<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesRoomFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SalesRoomFacilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('sales-room-facility-data');
        $facility = SalesRoomFacility::get();
        return view('content.SalesRoomFacility.ListSalesRoomFacility')->with(['facility'=>$facility]);
    }
    public function add() {
        $sessiondata = Session::get('sales-room-facility-data');
        return view('content.SalesRoomFacility.FormAddSalesRoomFacility',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('sales-room-facility-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['facility_name'] = '';
            $sessiondata['facility_price'] = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('sales-room-facility-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        
        if(SalesRoomFacility::create([
            'facility_name'=>$request->facility_name,
            'facility_remark'=>$request->facility_remark,
            'facility_price'=>$request->facility_price,
            'created_id'=>Auth::id()])){
           return redirect()->route('sales-room-facility.index')->with(['type'=>'success','msg'=>'Tambah Menu Kamar Berhasil']);
        }
        return redirect()->route('sales-room-facility.index')->with(['type'=>'danger','msg'=>'Tambah Menu Kamar Gagal']);

    }
    public function edit($room_facility_id) {
        $sessiondata = Session::get('sales-room-facility-data');
        $facility = SalesRoomFacility::find($room_facility_id);
        return view('content.SalesRoomFacility.FormEditSalesRoomFacility',compact('sessiondata','facility'));
    }
    public function processEdit(Request $request){
        $facility = SalesRoomFacility::find($request->room_facility_id);
        $facility->facility_name = $request->facility_name;
        $facility->facility_remark = $request->facility_remark;
        $facility->facility_price = $request->facility_price;
        $facility->updated_id = Auth::id();
        if($facility->save()){
           return redirect()->route('sales-room-facility.index')->with(['type'=>'success','msg'=>'Edit Menu Kamar Berhasil']);
        }
        return redirect()->route('sales-room-facility.index')->with(['type'=>'danger','msg'=>'Edit Menu Kamar Gagal']);
    }
    public function delete($room_facility_id) {
        $facility=SalesRoomFacility::find($room_facility_id);
        $facility->data_state = '1';
        $facility->deleted_id = Auth::id();
        if($facility->save()){if($facility->delete()){
           return redirect()->route('sales-room-facility.index')->with(['type'=>'success','msg'=>'Hapus Menu Kamar Berhasil']);
        };}
        return redirect()->route('sales-room-facility.index')->with(['type'=>'danger','msg'=>'Hapus Menu Kamar Gagal']);
    }
}

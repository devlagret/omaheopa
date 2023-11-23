<?php

namespace App\Http\Controllers;

use App\Models\CoreRoom;
use Illuminate\Support\Str;
use App\Models\CoreBuilding;
use App\Models\CoreRoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreRoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        if(Auth::user()->merchant_id !=1&&Auth::id()!=1){
            return redirect()->back();
           }
           Session::forget('room-token');
           Session::forget('room-data');
        $room = CoreRoom::with(['roomType','building'])->get();
        return view('content.CoreRoom.ListRoom')->with(['room'=>$room]);
    }
    public function add() {
        if(empty(Session::get('room-token'))){
            Session::put('room-token',Str::uuid());
         }
        $sessiondata = Session::get('room-data');
        $roomtype = CoreRoomType::get()->pluck('room_type_name','room_type_id');
        $building = CoreBuilding::get()->pluck('building_name','building_id');
        return view('content.CoreRoom.FormAddRoom',compact('sessiondata','roomtype','building'));
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
        $token = Session::get('room-token');
        if(empty($token)){
           return redirect()->route('room.index')->with(['type'=>'success','msg'=>'Tambah Kamar Berhasil*']);
        }
        $request->validate([
            'room_name'=>'required',
            'room_type_id'=>'required',
            'building_id'=>'required'
        ],[
            'room_name.required'=>'Nama Kamar Harus Diisi',
            'room_type_id.required'=>'Tipe Kamar Harus Diisi',
            'building_id.required'=>'Bangunan Harus Diisi'
        ]);
        try {
        DB::beginTransaction();
        CoreRoom::create([
            'room_name'=>$request->room_name,
            'room_type_id'=>$request->room_type_id,
            'building_id'=>$request->building_id,
            'room_facility'=>$request->room_facility,
            'room_token'=>$token,
            'created_id'=>Auth::id(),
        ]);
        DB::commit();
        Session::forget('room-token');
        return redirect()->route('room.index')->with(['type'=>'success','msg'=>'Tambah Kamar Berhasil']);
        } catch (\Exception $e) {
        DB::rollBack();
        report($e);
        Session::forget('room-token');
        return redirect()->route('room.index')->with(['type'=>'danger','msg'=>'Tambah Kamar Gagal']);
        }
    }
    public function edit($room_id) {
        $sessiondata = Session::get('room-data');
        $roomtype = CoreRoomType::get()->pluck('room_type_name','room_type_id');
        $building = CoreBuilding::get()->pluck('building_name','building_id');
        $room = CoreRoom::find($room_id);
        return view('content.CoreRoom.FormEditRoom',compact('sessiondata','room','roomtype','building'));
    }
    public function processEdit(Request $request){
        $room = CoreRoom::find($request->room_id);
        $room->room_name = $request->room_name;
        $room->room_type_id = $request->room_type_id;
        $room->building_id = $request->building_id;
        $room->room_facility = $request->room_facility;
        $room->updated_id = Auth::id();
        if($room->save()){
           return redirect()->route('room.index')->with(['type'=>'success','msg'=>'Edit Kamar Berhasil']);
        }
        return redirect()->route('room.index')->with(['type'=>'danger','msg'=>'Edit Kamar Gagal']);
    }
    public function delete($room_id) {
        $room=CoreRoom::find($room_id);
        $room->data_state = '1';
        $room->deleted_id = Auth::id();
        if($room->save()){if($room->delete()){
           return redirect()->route('room.index')->with(['type'=>'success','msg'=>'Hapus Kamar Berhasil']);
        };}
        return redirect()->route('room.index')->with(['type'=>'danger','msg'=>'Hapus Kamar Gagal']);
    }
}

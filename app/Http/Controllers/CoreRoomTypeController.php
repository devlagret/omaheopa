<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreRoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreRoomTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('room-data');
        $roomtype = CoreRoomType::get();
        return view('content.CoreRoomType.ListRoomType')->with(['room'=>$roomtype]);
    }
    public function add() {
        $sessiondata = Session::get('room-data');
        return view('content.CoreRoomType.FormAddRoomType',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('room-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['room_type_name']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('room-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        if(CoreRoomType::create(['room_type_name'=>$request->room_type_name,'created_id'=>Auth::id()])){
           return redirect()->route('room-type.index')->with(['type'=>'success','msg'=>'Tambah Tipe Kamar Berhasil']);
        }
        return redirect()->route('room-type.index')->with(['type'=>'danger','msg'=>'Tambah Tipe Kamar Gagal']);

    }
    public function edit($room_type_id) {
        $sessiondata = Session::get('room-data');
        $roomtype = CoreRoomType::find($room_type_id);
        return view('content.CoreRoomType.FormEditRoomType',compact('sessiondata','roomtype'));
    }
    public function processEdit(Request $request){
        $roomtype = CoreRoomType::find($request->room_type_id);
        $roomtype->room_type_name = $request->room_type_name;
        $roomtype->updated_id = Auth::id();
        if($roomtype->save()){
           return redirect()->route('room-type.index')->with(['type'=>'success','msg'=>'Edit Tipe Kamar Berhasil']);
        }
        return redirect()->route('room-type.index')->with(['type'=>'danger','msg'=>'Edit Tipe Kamar Gagal']);
    }
    public function delete($room_type_id) {
        $roomtype=CoreRoomType::find($room_type_id);
        $roomtype->data_state = '1';
        $roomtype->deleted_id = Auth::id();
        if($roomtype->save()){if($roomtype->delete()){
           return redirect()->route('room-type.index')->with(['type'=>'success','msg'=>'Hapus Tipe Kamar Berhasil']);
        };}
        return redirect()->route('room-type.index')->with(['type'=>'danger','msg'=>'Hapus Tipe Kamar Gagal']);
    }
}

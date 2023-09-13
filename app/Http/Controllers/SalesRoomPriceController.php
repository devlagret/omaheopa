<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CorePriceType;
use App\Models\CoreRoom;
use App\Models\SalesRoomPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SalesRoomPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('room-price-data');
        $roomprice = SalesRoomPrice::get();
        return view('content.SalesRoomPrice.ListSalesRoomPrice')->with(['roomprice'=>$roomprice]);
    }
    public function add() {
        $sessiondata = Session::get('room-price-data');
        $building= CoreBuilding::get()->pluck('building_name','building_id');
        $pricetype = CorePriceType::get()->pluck('price_type_name','price_type_id');
        return view('content.SalesRoomPrice.FormAddSalesRoomPrice',compact('sessiondata','building','pricetype'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('room-price-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['room_id'] = '';
            $sessiondata['price_type_id'] = '';
            $sessiondata['room_price_price'] = '';
            $sessiondata['room_price_start_date'] = '';
            $sessiondata['room_price_end_date'] = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('room-price-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        $request->validate([
            'room_id' => 'integer',
        ],['room_id.integer'=>'Bangunan Tidak Memiliki Kamar']);
        dump($request->all());return 1;
        if(SalesRoomPrice::create([
            'room_id'=>$request->room_id,
            'price_type_id'=>$request->price_type_id,
            'room_price_price'=>$request->room_price_price,
            'room_price_start_date'=>$request->room_price_start_date,
            'room_price_end_date'=>$request->room_price_end_date,
            'created_id'=>Auth::id()])){
           return redirect()->route('room-price.index')->with(['type'=>'success','msg'=>'Tambah Harga Kamar Berhasil']);
        }
        return redirect()->route('room-price.index')->with(['type'=>'danger','msg'=>'Tambah Harga Kamar Gagal']);

    }
    public function edit($room_price_id) {
        $sessiondata = Session::get('room-price-data');
        $roomprice = SalesRoomPrice::find($room_price_id);
        $room= CoreRoom::get()->pluck('room_name','room_id');
        $pricetype = CorePriceType::get()->pluck('price_type_name','price_type_id');
        return view('content.SalesRoomPrice.FormEditSalesRoomPrice',compact('sessiondata','roomprice','room','pricetype'));
    }
    public function processEdit(Request $request){
        $roomprice = SalesRoomPrice::find($request->room_price_id);
        $roomprice->room_id = $request->room_id;
        $roomprice->price_type_id = $request->price_type_id;
        $roomprice->room_price_price = $request->room_price_price;
        $roomprice->room_price_start_date = $request->room_price_start_date;
        $roomprice->room_price_end_date = $request->room_price_end_date;
        $roomprice->updated_id = Auth::id();
        if($roomprice->save()){
           return redirect()->route('room-price.index')->with(['type'=>'success','msg'=>'Edit Harga Kamar Berhasil']);
        }
        return redirect()->route('room-price.index')->with(['type'=>'danger','msg'=>'Edit Harga Kamar Gagal']);
    }
    public function delete($room_price_id) {
        $roomprice=SalesRoomPrice::find($room_price_id);
        $roomprice->data_state = '1';
        $roomprice->deleted_id = Auth::id();
        if($roomprice->save()){if($roomprice->delete()){
           return redirect()->route('room-price.index')->with(['type'=>'success','msg'=>'Hapus Harga Kamar Berhasil']);
        };}
        return redirect()->route('room-price.index')->with(['type'=>'danger','msg'=>'Hapus Harga Kamar Gagal']);
    }
    public function getType(Request $request) {
        $data = '';
        $sessiondata = Session::get('room-price-data');
        try{
        $building = CoreBuilding::with('rooms:building_id,room_type_id','rooms.roomType')->find($request->building_id);
        $sessiondata['room_type_id'] ?? $sessiondata['room_type_id'] = 1;
        if ($building->rooms->count() == 0) {
            $data = "<option>Bangunan Tidak Memiliki Kamar</option>\n";
        }
        foreach ( $building->rooms as $val) {
            $data .= "<option value='".$val->roomType->room_type_id."' " . ($sessiondata['room_type_id'] == $val->roomType->room_type_id ? 'selected' : '') .">".$val->roomType->room_type_name."</option>\n";
        }
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        return response($data);

    }
    }
    public function getRoom(Request $request) {
        $data = '';
        $sessiondata = Session::get('room-price-data');
        try{
        $room = CoreRoom::where('room_type_id',$request->room_type_id)
        ->where('building_id',$request->building_id)->get();
        $sessiondata['room_id'] ?? $sessiondata['room_id'] = 1;
        if ($room->count() == 0) {
            $data = "<option>Bangunan Tidak Memiliki Kamar</option>\n";
        }
        foreach ( $room as $val) {
            $data .= "<option value='".$val->room_id."' " . ($sessiondata['room_id'] == $val->room_id ? 'selected' : '') .">".$val->room_name."</option>\n";
        }
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        return response($data);

    }
    }
}

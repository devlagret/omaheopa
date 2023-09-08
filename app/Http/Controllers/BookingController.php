<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CoreRoom;
use App\Models\SalesOrder;
use App\Models\SalesRoomMenu;
use App\Models\SalesRoomPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $filter = Session::get('filter');
        Session::forget('booking-data');
        Session::forget('booked-room-data');
        Session::forget('booked-room-data-qty');
        Session::forget('booked-room-menu');
        Session::forget('booked-room-menu-qty');
        $booking = SalesOrder::with(['BookingType','building'])->get();
        return view('content.Booking.ListBooking')->with(['booking'=>$booking,'start_date'=>$filter['start_date']??null,'end_date'=>$filter['end_date']??null]);
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter',$data);
        return redirect()->route('booking.index');
    }
    public function add() {
        Session::put('booking-token',Str::uuid());
        $sessiondata = Session::get('booking-data');
        $roomData = collect(Session::get('booked-room-data'));
        $booked = Session::get('booked-room-data-qty');
        $menuData = collect(Session::get('booked-menu-data'));
        $menuqty = Session::get('booked-menu-data-qty');
        $building = CoreBuilding::get()->pluck('building_name','building_id');
        $menu = SalesRoomMenu::get();
        $menutype = [
            1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner'
        ];
        $room = CoreRoom::with('building','roomType','price.type')->whereIn('room_id',$roomData->flatten())->get();
        return view('content.Booking.FormAddBooking',compact('sessiondata','menutype','booked','room','building'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('booking-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['building_name']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('booking-data', $sessiondata);
    }
    public function getType(Request $request) {
        $data = '';
        $sessiondata = Session::get('booking-data');
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
        $sessiondata = Session::get('booking-data');
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
    public function addRoom(Request $request) {
        $data = '';$dropdown = '';$i=1;
        $no = $request->no + 1;
        $room = CoreRoom::with('building','roomType','price.type')->find($request->room_id);
        foreach ($room->price as $val){
            $dropdown .= "<option value='". $val->room_price_id."' " . ($i == 1 ? 'selected' : '') .">".$val->type->price_type_name."</option>\n";
            $i++;
        }
        $data = "
        <tr class='booked-room room-".$request->room_id."'>
        <td>".$no."</td>
        <td>".$room->room_name."</td>
        <td>".$room->roomType->room_type_name."</td>
        <td>".$room->building->building_name."</td>
        <td>
        <div class='row'>
        <div class='col-5'>
        <input
            oninput='changeHowManyPerson(".$request->room_id.", this.value)'
            type='number' name='room_qty_".$request->room_id."'
            id='room_qty_".$request->room_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'></div>
            <div class='col-auto'>Orang</div>

        </div>
        </td>
        <td width='15%'> <select class='selection-search-clear required select-form' required placeholder='Pilih Harga' name='room_price_id_".$request->room_id."' id='room_price_id_".$request->room_id."'
        onchange='changePrice(  this.value)' required>
        ".$dropdown."
        </select>
        </td>
        <td width='10%'>
        <input type='text' class='form-control input-bb readonly room_price_price_view' name='room_price_view_".$val->room_id."' id='room_price_view_".$val->room_id."' value='".number_format($room->price->first()->room_price_price,2,',','.')."' readonly/>
        <input type='hidden' class='form-control input-bb readonly room_price_price_view' name='room_price_".$val->room_id."' id='room_price_".$val->room_id."' value='".$room->price->first()->room_price_price."' readonly/>
        </td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteItem(".$room->room_id.")'>Hapus</button></td>
        </tr>
        ";
        $qty=collect(Session::get('booked-room-data-qty'));
        $qty->put($request->room_id,1);
        Session::put('booked-room-data-qty',$qty->toArray());
        Session::push('booked-room-data',$request->room_id);
        return response($data);
    }
    public function addPersonBooked(Request $request ) {
        $qty=collect(Session::get('booked-room-data-qty'));
        $qty->put($request->id,$request->qty);
        Session::put('booked-room-data-qty',$qty->toArray());
        return 1;
    }
    public function clearBooked() {
        Session::forget('booked-room-data');
        return 1;
    }
    public function deleteBookedRoom($room_id){
        $data=collect(Session::get('booked-room-data'));
        foreach($data as $key => $val){
            if($val = $room_id){
                $data->forget($room_id);
            }
        }
        Session::put('booked-room-data',$data->toArray());
        $qty=collect(Session::get('booked-room-data-qty'));
        $qty->forget($room_id);
        Session::put('booked-room-data-qty',$qty->toArray());
        return 1;
    }
    public function getRoomPrice(Request $request) {
        $price = SalesRoomPrice::find($request->room_price_id);
        return response($price->room_price_price);
    }
    public function getRoomMenus(Request $request) {
        $data = '';
        $menu = SalesRoomMenu::where('room_menu_type',$request->room_menu_type)->get(['room_menu_id','room_menu_name']);
        if(!$menu->count()){
            $data = "<option>Tidak Ada Menu </option>";
        }
        foreach($menu as $val){
            $data .= "<option value='".$val->room_menu_id."'>".$val->room_menu_name."</option>";
        }
        return response($data);
    }
    public function addMenuItem(Request $request) {
        $data = '';
        $menu = SalesRoomMenu::find($request->room_menu_id);
        $menutype = [
            1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner'
        ];
        $no = $request->no + 1;
        $data = "
        <tr class='menu-item'>
        <td>".$no."</td>
        <td>".$menu->room_menu_type."</td>
        <td>".$menu->room_menu_name."</td>
        <td>
        <input
            oninput='changeHowManyPerson(".$request->_id.", this.value)'
            type='number' name='_qty_".$request->_id."'
            id='_qty_".$request->_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'>
        </td>
        <td> Rp ".number_format($menu->room_menu_price,2)." -</td>
        <td width='10%'>
        Rp <div class='sbs-menu-item-view d-inline' id='sbs-menu-item-view'>".number_format($menu->room_menu_price,2)."
        </td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteItem(".$menu->room_menu_id.")'>Hapus</button></td>
        </tr>
        ";
        $qty=collect(Session::get('booked-room-menu-qty'));
        $qty->put($request->room_id,1);
        Session::put('booked-room-menu-qty',$qty->toArray());
        Session::push('booked-room-menu',$request->room_id);
        return response($data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CoreRoom;
use App\Models\SalesOrder;
use App\Models\SalesRoomMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
        $sessiondata = Session::get('booking-data');
        $roomData = collect(Session::get('booked-room-data'));
        $booked = Session::get('booked-room-data-qty');
        $building = CoreBuilding::get()->pluck('building_name','building_id');
        $menu = SalesRoomMenu::get();
        $room = CoreRoom::with('price.type')->whereIn('room_id',$roomData->flatten())->get();
        return view('content.Booking.FormAddBooking',compact('sessiondata','booked','room','building'));
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
        $data = '';
        $no = $request->no + 1;
        $room = CoreRoom::with('building','roomType')->find($request->room_id);
        $data = "
        <tr class='booked-room'>
        <td>".$no."</td>
        <td>".$room->room_name."</td>
        <td>".$room->roomType->room_type_name."</td>
        <td>".$room->building->building_name."</td>
        <td>
        <div class='row'>
        <input
            oninput='changeHowManyPerson(".$request->room_id.", this.value)'
            type='number' name='room_qty_".$request->room_id."'
            id='room_qty_".$request->room_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'>
            <div class='col-auto'>Orang</div>
        </div>
        </td>
        <td>".$no."</td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteItem(".$room->room_id.")'>Hapus</button></td>
        </tr>
        ";
        Session::push('booked-room-data',$request->room_id);
        return response($data);
    }
    public function addPersonBooked(Request $request ) {
        $data[$request->id] = $request->qty;
        Session::push('booked-room-data-qty',$data);
        return 1;
    }
}

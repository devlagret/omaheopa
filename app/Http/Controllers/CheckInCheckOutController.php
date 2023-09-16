<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CoreRoom;
use App\Models\SalesOrder;
use App\Models\SalesRoomFacility;
use App\Models\SalesRoomMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckInCheckOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $filter = Session::get('filter-cc');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.CheckInCheckOut.ListCheckInCheckOut')->with(['booking'=>$booking,'start_date'=>$filter['start_date'],'end_date'=>$filter['end_date']]);
    }
    public function add() {
        $ci = 1;
        Session::put('booking-token',Str::uuid());
        $sessiondata = Session::get('checkin-data');
        $roomData = collect(Session::get('booked-room-data'));
        $booked = Session::get('booked-room-data-qty');
        $menuData = collect(Session::get('booked-room-menu'));
        $price=collect(Session::get('booked-room-price'));
        $menuqty = Session::get('booked-room-menu-qty');
        $facilityData = collect(Session::get('booked-room-facility'));
        $facilityqty = Session::get('booked-room-facility-qty');
        $building = CoreBuilding::get()->pluck('building_name','building_id');
        $facility = SalesRoomFacility::get()->pluck('facility_name','room_facility_id');
        $menu = SalesRoomMenu::get();
        $start_date = $sessiondata['start_date'] ?? Carbon::now()->format('Y-m-d');
        $end_date = $sessiondata['end_date'] ?? Carbon::now()->add(1,'day')->format('Y-m-d');
        $menutype = AppHelper::menuType();
        $room = CoreRoom::with(['building','roomType','price'=>function ($query) use($start_date,$end_date){
            $query->where('room_price_start_date', '<=', $start_date)
                  ->where('room_price_end_date', '>=', $end_date)
                  ->orWhereNull('room_price_start_date')
                  ->orWhereNull('room_price_end_date')
                  ->orderByDesc('price_type_id');
        }])->whereIn('room_id',$roomData->flatten())->get();
        $facilityitm = SalesRoomFacility::whereIn('room_facility_id',$facilityData->flatten())->get();
        $menuItm = SalesRoomMenu::whereIn('room_menu_id',$menuData->flatten())->get();
        return view('content.Booking.FormAddBooking',compact('sessiondata','price','menutype','facility','booked','room','building','menuqty','facilityqty','facilityitm','menuItm','ci'));
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-cc',$data);
        return redirect()->route('cc.index');
    }
    public function elementsAdd(Request $request) {
        $sessiondata = Session::get('checkin-data');
        $sessiondata[$request->name] = $request->value;
        Session::put('checkin-data', $sessiondata);
    }
}

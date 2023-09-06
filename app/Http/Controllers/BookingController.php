<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $room = CoreRoom::get()->pluck('room_type_name','room_type_id');
        $menu = SalesRoomMenu::get();
        return view('content.Booking.FormAddBooking',compact('sessiondata','room'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
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
        Session::forget('booking-data');
        $booking = SalesOrder::with(['BookingType','building'])->get();
        return view('content.Booking.ListBooking')->with(['booking'=>$booking]);
    }
}

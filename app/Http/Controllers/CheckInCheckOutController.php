<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckInCheckOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $booking = SalesOrder::with('rooms')->get();
        return view('content.CheckInCheckOut.ListCheckInCheckOut',compact('booking'));
    }
}

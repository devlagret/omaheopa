<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DownPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $filter = Session::get('filter-dp');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status',1)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.ListDownPayment',compact('booking'));
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-dp',$data);
        return redirect()->route('dp.index');
    }
    public function add() {
        $filter = Session::get('filter-dp');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.AddDownPayment',compact('booking'));
    }
}

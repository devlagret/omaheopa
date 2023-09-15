<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DownPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $status = AppHelper::status();
        $filter = Session::get('filter-dp');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.ListDownPayment')->with(['booking'=>$booking,'status'=>$status,'start_date'=>$filter['start_date'],'end_date'=>$filter['end_date']]);
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-dp',$data);
        return redirect()->route('dp.index');
    }
    public function filterAdd(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-dp-add',$data);
        return redirect()->route('dp.add');
    }
    public function processAdd($sales_order_id,$source){
        $token = Session::get('dp-token-si');
        if(empty(Session::get('dp-token-si'))){
            if($source == 'booking'){
                return redirect()->route('booking.index')->with('msg','Tambah Booking Berhasil  -.');
            }
            elseif($source == 'direct'){
                return redirect()->route('cc.index')->with('msg','Tambah Check-In Berhasil-');
            }
            return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Berhasil-');
        }
        $order = SalesOrder::find($sales_order_id);
        try{
        DB::beginTransaction();


        // * Update sales order
        $order->sales_order_status = 1;
        $order->save();
        DB::rollBack();
    }catch(\Exception $e){
            DB::rollBack();
            if($source == 'booking'){
                return redirect()->route('booking.index')->with('msg','Tambah Booking Gagal  -.');
            }
            elseif($source == 'direct'){
                return redirect()->route('cc.index')->with('msg','Tambah Check-In Gagal');
            }
            return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Gagal');
        }
        dump($order);
        return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Berhasil');
    }
    public function add() {
        $filter = Session::get('filter-dp-add');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.AddDownPayment')->with(['booking'=>$booking,'start_date'=>$filter['start_date'],'end_date'=>$filter['end_date']]);
    }
}

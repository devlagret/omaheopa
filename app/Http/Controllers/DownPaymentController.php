<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

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
    public function processAdd($sales_order_id,$source=null){
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
            SalesInvoice::create([
                'total_amount' => $order->sales_order_price,
                'sales_invoice_token' => $token,
                'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                'created_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'merchant_id' =>  empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
            ]);
        $si = SalesInvoice::where('sales_invoice_token',$token)->first();
        // * Update sales order
        $order->sales_invoice_id = $si->sales_invoice_id;
        if($source == 'booking'){
            $order->sales_order_status = 1;
            $order->save();
            DB::rollBack();
            return redirect()->route('booking.index')->with('msg','Tambah Booking Berhasil  -.');
        }
        $order->sales_order_status = 1;
        $order->save();
        DB::commit();
        return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Berhasil');
    }catch(\Exception $e){
            DB::rollBack();
            report($e);
            if($source == 'booking'){
                return redirect()->route('booking.index')->with('msg','Tambah Booking Gagal  -.');
            }
            return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Gagal');
        }
    }
    public function add() {
        Session::put('dp-token-si',Str::uuid());
        $filter = Session::get('filter-dp-add');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.AddDownPayment')->with(['booking'=>$booking,'start_date'=>$filter['start_date'],'end_date'=>$filter['end_date']]);
    }
}

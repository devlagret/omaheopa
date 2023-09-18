<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CoreRoom;
use App\Models\PreferenceCompany;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesRoomFacility;
use App\Models\SalesRoomMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckInCheckOutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::put('cc-token',Str::uuid());
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
        $roomData = collect(Session::get('checkin-room-data'));
        $booked = Session::get('checkin-room-data-qty');
        $price=collect(Session::get('checkin-room-price'));
        //menu
        $menuData = collect(Session::get('checkin-room-menu'));
        $menuqty = Session::get('checkin-room-menu-qty');
        //facility
        $facilityData = collect(Session::get('checkin-room-facility'));
        $facilityqty = Session::get('checkin-room-facility-qty');
        //
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
    public function extend($sales_order_id) {
        $data = SalesOrder::with(['rooms','facilities','menus'])->find($sales_order_id);
        $room = CoreRoom::with(['price','roomType','building'])->whereIn('room_id',$data->rooms->pluck('room_id'))->get();
        $facility = SalesRoomFacility::whereIn('room_facility_id',$data->facilities->pluck('room_facility_id'))->get();
        $menu = SalesRoomMenu::whereIn('room_menu_id',$data->menus->pluck('room_menu_id'))->get();
        $menutype = AppHelper::menuType();
        return  view('content.CheckInCheckOut.ExtendCheckIn',compact('data','room','facility','menu','menutype'));
    }
    public function processExtend(){

    }
    public function check(Request $request){
        $pref = PreferenceCompany::find(Auth::user()->company_id,['checkin_time','checkout_time']);
        $now = Carbon::now()->format('Y-m-d');
        $order = SalesOrder::find($request->sales_order_id);
        $sales = SalesInvoice::find($order->sales_invoice_id);
        return response()->json(['status'=>Carbon::now()->format('H:i:s')>$pref->checkout_time,'late'=>$now>Carbon::parse($order->checkout_date),'diff'=>Carbon::parse($order->checkout_date)->diffInDays($now),'needtopay'=>$order->sales_order_price-$order->down_payment]);
    }
    public function processCheckOut(Request $request) {
        $token = Session::get('cc-token');
        if(empty(Session::get('cc-token'))){
            return redirect()->back()->with('msg',"Check-Out Berhasil -");
        }
        $field = $request->validate(['payed_amount'=>'required','sales_order_id'=>'required'],['payed_amount.required'=>'Uang Yang dibayar Harus Dimasukan','sales_order_id.required'=>'Error']);
        $order = SalesOrder::find($request->sales_order_id);
        $invoice = SalesInvoice::find($order->sales_invoice_id);
        try{
            DB::beginTransaction();
            $order->sales_order_status= 3;
            $order->save();
            if($request->use_penalty){
                $invoice->penalty_amount = $request->pinalty;
            }
            $invoice->paid_amount = $field['paid_amount'];
            $invoice->change_amount = $request->change_amount;
            $invoice->update_id = Auth::id();

            Session::forget('cc-token');
            DB::commit();
            return redirect()->back(200)->with('msg','Check-Out Berhasil');
        }catch(\Exception $e){
            DB::rollBack();
            return dump($e);
            // report($e);
            // return redirect()->back(200)->with('msg','Check-Out Gagal');
        }
     
    }
    public function getPenalty(Request $request) {
        $total = 0;
        $order = SalesOrder::with('rooms')->find($request->sales_order_id);
        foreach($order->rooms as $val) {
            $total += $val->room_price;
        }
        return $total;
    }
}

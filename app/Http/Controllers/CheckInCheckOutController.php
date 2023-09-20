<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CorePriceType;
use App\Models\CoreRoom;
use App\Models\PreferenceCompany;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderFacility;
use App\Models\SalesOrderMenu;
use App\Models\SalesOrderRoom;
use App\Models\SalesOrderRoomExtension;
use App\Models\SalesRoomFacility;
use App\Models\SalesRoomMenu;
use App\Models\SalesRoomPrice;
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
        $this->resetSession();
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
        dump($sessiondata);
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
        error_log($request->name);
    }
    public function extend($sales_order_id) {
        Session::put('extend-token');
        $data = SalesOrder::with(['rooms','facilities','menus'])->find($sales_order_id);
        $room = CoreRoom::with(['price','roomType','building'])->whereIn('room_id',$data->rooms->pluck('room_id'))->get();
        $facility = SalesRoomFacility::whereIn('room_facility_id',$data->facilities->pluck('room_facility_id'))->get();
        $menu = SalesRoomMenu::whereIn('room_menu_id',$data->menus->pluck('room_menu_id'))->get();
        $menutype = AppHelper::menuType();
        return  view('content.CheckInCheckOut.ExtendCheckIn',compact('data','room','facility','menu','menutype'));
    }
    public function checkExtend(Request $request) {
        $i=0;
        $data = SalesOrder::with('rooms')->find($request->sales_order_id);
        $dataroom = SalesOrderRoom::where('sales_order_id',$request->sales_order_id)
        ->get()->pluck('room_id');
        $so = SalesOrder::with('rooms')->where('sales_order_id','!=',$request->sales_order_id)->where('checkin_date','<',$request->checkout_date)
        ->where('checkin_date','>',$request->checkin_date)
        // ->where('checkout_date','>',$request->checkout_date)
        ->get();
        foreach ($data->rooms as $d){
        }
        foreach($so as $val){
            foreach($val->rooms->whereIn('room_id',$dataroom) as $row){
                $i++;
            }
        }
        return response($i);
    }
    public function processExtend(Request $request){
        $data = SalesOrder::with('rooms')->find($request->sales_order_id);
        $dataroom = SalesOrderRoom::where('sales_order_id',$request->sales_order_id)
        ->get()->pluck('room_id');
        dump($request->all());
        if(Session::get('extend-token')){
            report('['.Carbon::now().'] : Doubleclik at checkin extend');
            return redirect()->route('cc.index')->with('msg','Perpanjangan Berhasil');
        }
        $so = SalesOrder::with('rooms')->where('sales_order_id','!=',$request->sales_order_id)->where('checkin_date','<',$request->checkout_date)
        ->where('checkin_date','>',$request->checkin_date)
        // ->where('checkout_date','>',$request->checkout_date)
        ->get();
        dump($dataroom);
        foreach ($data->rooms as $d){
        }
        foreach($so as $val){
            foreach($val->rooms->whereIn('room_id',$dataroom) as $row){
                dump($row);
            }
        }
        dump($so);
        return response('',202);
        try{
            DB::beginTransaction();
            SalesOrderRoomExtension::create([ 
                'checkout_date'=>$request->checkout_date_old,
                'checkout_date_new'=>$request->checkout_date,
                'sales_order_id'=>$request->sales_order_id,
                'created_id'=>Auth::id(),
            ]);
            $data->checkout_date;
            $data->save();
            DB::commit();
            return redirect()->route('index')->with('msg','Data Berhasil Diinput');
        }catch(\Exception $e){
            DB::rollBack();
            report($e);
            dump($e);return 0;
            return redirect()->route('index')->with('msg','Data Gagal Diinput');
        }
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
    public function processAdd(Request $request) {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $roomData = collect(Session::get('checkin-room-data'));
        $booked = Session::get('checkin-room-data-qty');
        $price = Session::get('checkin-room-price');
        $menuData = collect(Session::get('checkin-room-menu'));
        $menuqty = Session::get('checkin-room-menu-qty');
        $facilityData = collect(Session::get('checkin-room-facility'));
        $facilityqty = Session::get('checkin-room-facility-qty');
        $token = Session::get('booking-token');
        $pricetype = CorePriceType::get();
        $prices = SalesRoomPrice::get();
        $room = CoreRoom::with(['price'=>function ($query) use($start_date,$end_date){
            $query->where('room_price_start_date', '<=', $start_date)
                  ->where('room_price_end_date', '>=', $end_date)
                  ->orWhereNull('room_price_start_date')
                  ->orWhereNull('room_price_end_date')
                  ->orderByDesc('price_type_id');
        }])->get();
        if(!$roomData->count()){
            return redirect()->back()->with(['msg'=>'Harap Tambahkan Kamar Yang Dibooking','type'=>'warning','tab-index' =>2]);
        }
        if(empty(Session::get('booking-token'))){
            return redirect()->route('cc.index')->with('msg','Tambah Booking Kamar Berhasil -');
        }
        dump($request->all());
        $field = $request->validate([
            'atas_nama' => 'required',
        ],['atas_nama.required' => 'Nama Pemesan Diperlukan']);
        $check = collect();
        $checkfac = collect();
        $checkmenu = collect();
        foreach($roomData as $roomval){
            if(empty($price[$roomval])){
                $priced = $room->find($roomval)->price->first();
            }else{
                $priced = $prices->find($price[$roomval]);
            }
            $check->push([
                'sales_order_id'=> 5,
                'room_id'       => $roomval,
                'people'        => $booked[$roomval],
                'room_price'    => $priced->room_price_price,
                'price_type_id_old' => $priced->price_type_id,
                'room_price_id' => $priced->room_price_id,
                'price_type_name_old' => $priced->type->price_type_name,
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
        foreach($facilityData as $facval){
            $checkfac->push([
                'sales_order_id'=> 5,
                'room_facility_id'=> $facval,
                'quantity'      => $facilityqty[$facval],
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
        foreach($menuData as $menuval){
            $checkmenu->push([
                'sales_order_id'=> 5,
                'room_menu_id'  => $menuval,
                'quantity'      => $menuqty[$menuval],
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
        dump($check);
        dump($checkfac);
        dump($checkmenu);
        // return 0;
        try{
            DB::beginTransaction();
            SalesInvoice::create([
                'total_amount' => $request->total_amount,
                'sales_invoice_token' => $token,
                'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                'created_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'merchant_id' => empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
            ]);
            $si = SalesInvoice::where('sales_invoice_token',$token)->first();
            SalesOrder::create([
                'checkin_date' =>$start_date,
                'checkout_date' =>$end_date,
                'sales_order_type' =>1,
                'sales_order_status' =>2,
                'sales_invoice_id' => $si->sales_invoice_id,
                'sales_order_price' =>$request->total_amount,
                'discount' =>$request->discount_percentage_total,
                'order_date' => Carbon::now()->format('Y-m-d'),
                'sales_order_name' => $field['atas_nama'],
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
                'sales_order_token' => $token->toString(),
            ]);
            $order = SalesOrder::where('sales_order_token',$token->toString())->first();
        foreach($roomData as $roomval){
            if(empty($price[$roomval])){
                $priced = $room->find($roomval)->price->first();
            }else{
                $priced = $prices->find($price[$roomval]);
            }
            SalesOrderRoom::create([
                'sales_order_id'=> $order->sales_order_id,
                'room_id'       => $roomval,
                'people'        => $booked[$roomval],
                'room_price'    => $priced->room_price_price,
                'price_type_id_old' => $priced->price_type_id,
                'room_price_id' => $priced->room_price_id,
                'price_type_name_old' => $priced->type->price_type_name,
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
        foreach($facilityData as $facval){
            SalesOrderFacility::create([
                'sales_order_id'=> $order->sales_order_id,
                'room_facility_id'=> $facval,
                'quantity'      => $facilityqty[$facval],
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
        foreach($menuData as $menuval){
            SalesOrderMenu::create([
                'sales_order_id'=> $order->sales_order_id,
                'room_menu_id'  => $menuval,
                'quantity'      => $menuqty[$menuval],
                'created_id'    => Auth::id(),
                'company_id'    => Auth::user()->company_id,
            ]);
        }
            DB::commit();
            Session::forget('booking-token');
            $this->resetSession();
            return redirect()->route('cc.index')->with(['msg'=>'Tambah Check-In Kamar Berhasil','type'=>'success']);
        }catch(\Exception $e){
            $this->resetSession();
            Session::forget('booking-token');
            DB::rollBack();
            report($e);
            return dump($e);
            return redirect()->route('cc.add')->with(['msg'=>'Tambah Check-In Kamar Gagal','type'=>'danger']);
        }
    }
    public function resetSession() {
        Session::forget([
            'checkin-data',
            'checkin-room-data','checkin-room-price',
            'checkin-room-data-qty','checkin-room-menu',
            'checkin-room-menu-qty','checkin-room-facility',
            'checkin-room-facility-qty','check-in',
        ]);
        return 1;
    }
    public function delete($sales_order_id) {
        $order=SalesOrder::find($sales_order_id);
        $si = SalesInvoice::find($order->sales_invoice_id);
        $si->data_state = 1;
        $order->data_state = '1';
        $order->deleted_id = Auth::id();
        if($order->save()&&$si->save()){if($order->delete()){
           return redirect()->route('cc.index')->with(['type'=>'success','msg'=>'Pembatalan Check-in Berhasil']);
        };}
        return redirect()->route('cc.index')->with(['type'=>'danger','msg'=>'Pembatalan Check-in Gagal']);
    }
}

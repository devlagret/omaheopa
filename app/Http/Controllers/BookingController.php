<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CorePriceType;
use App\Models\CoreRoom;
use App\Models\SalesOrder;
use App\Models\SalesOrderFacility;
use App\Models\SalesOrderMenu;
use App\Models\SalesOrderRoom;
use App\Models\SalesRoomFacility;
use App\Models\SalesRoomMenu;
use App\Models\SalesRoomPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        $filter = Session::get('filter-booking');
        Session::forget([
            'booking-data','booking-token',
            'booked-room-data','booked-room-price',
            'booked-room-data-qty','booked-room-menu',
            'booked-room-menu-qty','booked-room-facility',
            'booked-room-facility-qty']);
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))->get();
        return view('content.Booking.ListBooking')->with(['booking'=>$booking,'start_date'=>$filter['start_date']??null,'end_date'=>$filter['end_date']??null]);
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-booking',$data);
        return redirect()->route('booking.index');
    }
    public function add() {
        Session::forget('check-in');
        Session::put('booking-token',Str::uuid());
        $sessiondata = Session::get('booking-data');
        $roomData = collect(Session::get('checkin-room-data'));
        $booked = Session::get('checkin-room-data-qty');
        $menuData = collect(Session::get('checkin-room-menu'));
        $price=collect(Session::get('checkin-room-price'));
        $menuqty = Session::get('checkin-room-menu-qty');
        $facilityData = collect(Session::get('checkin-room-facility'));
        $facilityqty = Session::get('checkin-room-facility-qty');
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
        return view('content.Booking.FormAddBooking',compact('sessiondata','price','menutype','facility','booked','room','building','menuqty','facilityqty','facilityitm','menuItm'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('booking-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['atas_nama']   = '';
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
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',3)
        ->where('checkin_date','>=',$request->start_date)
        ->where('checkin_date','<=',$request->end_date)
        ->where('checkout_date','>',$request->start_date)
        ->get()->pluck('rooms');
        $building = CoreBuilding::with('rooms:building_id,room_type_id','rooms.roomType')->find($request->building_id);
        $room = CoreRoom::where('room_type_id',$request->room_type_id)
        ->where('building_id',$request->building_id);
        if($booking->count()){
            $room = $room->whereNotIn('room_id',$booking->collapse()->pluck('room_id'));
        }
        $room = $room->get();
        $sessiondata['room_id'] ?? $sessiondata['room_id'] = 1;
        if(!$room->count()&&$booking->count()&&$building->rooms->count()){
            $data = "<option>Semua Kamar Sudah Dipesan</option>\n";
        }elseif ($room->count() == 0) {
            $data = "<option>Bangunan Tidak Memiliki Kamar</option>\n";
        }else{
        foreach ( $room as $val) {
            $data .= "<option value='".$val->room_id."' " . ($sessiondata['room_id'] == $val->room_id ? 'selected' : '') .">".$val->room_name."</option>\n";
        }}
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        $data="Error";
        return response($data);

    }
    }
    public function addRoom(Request $request) {
        $data = '';$dropdown = '';$i=1;
        $no = $request->no + 1;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $room = CoreRoom::with(['building','roomType','price'=>function ($query) use($start_date,$end_date){
            $query->where('room_price_start_date', '<=', $start_date)
                  ->where('room_price_end_date', '>=', $end_date)
                  ->orWhereNull('room_price_start_date')
                  ->orWhereNull('room_price_end_date')
                  ->orderByDesc('price_type_id');
        }])->find($request->room_id);
        ;
        if($room->count()){
        foreach ($room->price as $val){
            $dropdown .= "<option value='". $val->room_price_id."' " . ($i == 1 ? 'selected' : '') .">".$val->type->price_type_name."</option>\n";
            $i++;
        }
        }else{
            $dropdown ="<option selected>Kamar Tidak Memiliki Harga.<option>\n";
        }
        $data = "
        <tr class='booked-room room-".$request->room_id."' id='booked-room-".$request->room_id."'>
        <td>".$no."
        <input type='hidden' class='room-id' name='room_id[]' value='".$request->room_id."'/> </td>
        </td>
        <td>".$room->room_name."</td>
        <td>".$room->roomType->room_type_name."</td>
        <td>".$room->building->building_name."</td>
        <td>
        <div class='row'>
        <div class='col-5'>
        <input
            oninput='changeHowManyPerson(".$request->room_id.", this.value)'
            type='number' name='room_qty_".$request->room_id."'
            id='room_qty_".$request->room_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'></div>
            <div class='col-auto'>Orang</div>

        </div>
        </td>
        <td width='15%'> <select class='selection-search-clear required room-price-select select-form' required placeholder='Pilih Harga' name='room_price_id[]' id='room_price_id_".$request->room_id."'
        onchange='changePrice(".$request->room_id."  ,this.value)' >
        ".$dropdown."
        </select>
        </td>
        <td width='10%'>
        <input type='text' class='form-control input-bb readonly room_price_price_view' name='room_price_view_".$val->room_id."' id='room_price_view_".$val->room_id."' value='".number_format($room->price->first()->room_price_price,2,',','.')."' readonly/>
        <input type='hidden' class='form-control input-bb readonly room_price_price' name='room_price_".$val->room_id."' id='room_price_".$val->room_id."' data-id='".$val->room_id."' value='".$room->price->first()->room_price_price."' readonly/>
        <input type='hidden' class='sbs-room-booked' id='sbs-rbook-input-".$val->room_id."' value='".($room->price->first()->room_price_price * $request->days_booked)."'/>
        </td>
        <td id='sbs-room-booked-".$val->room_id."'>".number_format(($room->price->first()->room_price_price * $request->days_booked),2,',','.')."</td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteBooked(".$room->room_id.")'>Hapus</button></td>
        </tr>
        ";
        if($request->ci){
            $qty=collect(Session::get('checkin-room-data-qty'));
            $qty->put($request->room_id,1);
            Session::put('checkin-room-data-qty',$qty->toArray());
            Session::push('checkin-room-data',$request->room_id);
        }else{
            $qty=collect(Session::get('booked-room-data-qty'));
            $qty->put($request->room_id,1);
            Session::put('booked-room-data-qty',$qty->toArray());
            Session::push('booked-room-data',$request->room_id);
        }
        return response($data);
    }
    public function addPersonBooked(Request $request ) {
        if($request->ci){
            $qty=collect(Session::get('checkin-room-data-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('checkin-room-data-qty',$qty->toArray());
        }else{
            $qty=collect(Session::get('booked-room-data-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('booked-room-data-qty',$qty->toArray());
        }
        return 1;
    }
    public function clearBooked() { 
        Session::forget('checkin-room-data');
        Session::forget('checkin-room-data-qty');
        Session::forget('booked-room-data');
        Session::forget('booked-room-data-qty');
        return 1;
    }
    public function clearFacility() {
        Session::forget('checkin-room-facility');
        Session::forget('checkin-room-facility-qty');
        Session::forget('booked-room-facility');
        Session::forget('booked-room-facility-qty');
        return 1;
    }
    public function clearMenu() {
        Session::forget('checkin-room-menu');
        Session::forget('checkin-room-menu-qty');
        Session::forget('booked-room-menu');
        Session::forget('booked-room-menu-qty');
        return 1;
    }
    public function deleteBookedRoom($room_id){
        $data=collect(Session::get('booked-room-data'));
        foreach($data as $key => $val){
            if($val == $room_id){
                $data->forget($key);
            }
        }
        Session::put('booked-room-data',$data->toArray());
        $qty=collect(Session::get('booked-room-data-qty'));
        $qty->forget($room_id);
        Session::put('booked-room-data-qty',$qty->toArray());
        return 1;
    }
    public function deleteFacility($room_facility_id){
        $data=collect(Session::get('booked-room-facility'));
        foreach($data as $key => $val){
            if($val == $room_facility_id){
                $data->forget($key);
            }
        }
        Session::put('booked-room-facility',$data->toArray());
        $qty=collect(Session::get('booked-room-facility-qty'));
        $qty->forget($room_facility_id);
        Session::put('booked-room-facility-qty',$qty->toArray());
        return 1;
    }
    public function deleteMenu($room_menu_id){
        $data=collect(Session::get('booked-room-menu'));
        foreach($data as $key => $val){
            if($val == $room_menu_id){
                $data->forget($key);
            }
        }
        Session::put('booked-room-menu',$data->toArray());
        $qty=collect(Session::get('booked-room-menu-qty'));
        $qty->forget($room_menu_id);
        Session::put('booked-room-data-qty',$qty->toArray());
        return 1;
    }
    public function getRoomPrice(Request $request) {
        if(empty($request->room_price_id)){
            return 0;
        }
        $price = SalesRoomPrice::find($request->room_price_id);
        if($request->room_id!=null){
        $pr=collect(Session::get('booked-room-price'));
        $pr->put($request->room_id,$request->room_price_id);
        Session::put('booked-room-price',$pr->toArray());
        }
        return response($price->room_price_price);
    }
    public function getRoomPriceList(Request $request) {
        $i=1;$data ='';
        $sessiondata = Session::get('booking-data');
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $room = CoreRoom::with(['building','roomType','price'=>function ($query) use($start_date,$end_date){
            $query->where('room_price_start_date', '<=', $start_date)
                  ->where('room_price_end_date', '>=', $end_date)
                  ->orWhereNull('room_price_start_date')
                  ->orWhereNull('room_price_end_date')
                  ->orderByDesc('price_type_id');
        }])->find($request->room_id);
        ;
        $selectedId = $sessiondata['room_price_id_'.$request->room_id]??1;
        if($room->count()){
        foreach ($room->price as $val){
            $data .= "<option value='". $val->room_price_id."' " . ($val->room_price_id == $selectedId ? 'selected' : '') .">".$val->type->price_type_name."</option>\n";
            $i++;
        }}else{
            $data = "<option selected>Kamar Tidak Memiliki Harga.<option>\n";
        }
        return response($data);
    }
    public function addFacility(Request $request) {
        $data = '';
        $no = $request->no + 1;
        $facility = SalesRoomFacility::find($request->room_facility_id);
        $data = "
        <tr class='room-facility facility-".$request->room_facility_id."' id='facility-".$request->room_facility_id."'>
        <td>".$no."
        <input type='hidden' id='room_facility_id[]' value='".$request->room_facility_id."'/> </td>
        </td>
        <td>".$facility->facility_name."</td>
        <td>".$facility->facility_remark."</td>
        <td>
        <input type='text' class='form-control input-bb readonly facility_price_view' name='room_price_view_".$facility->room_facility_id."' id='room_price_view_".$facility->room_facility_id."' value='".number_format($facility->facility_price,2,',','.')."' readonly/>
        <input type='hidden' class='form-control input-bb readonly facility_price_price' data-id='".$request->room_facility_id."' name='room_price_".$facility->room_facility_id."' id='room_price_".$facility->room_facility_id."' value='".$facility->facility_price."' readonly/>
        </td>
        <td>
        <input
            oninput='changeFacilityQty(".$request->room_facility_id.", this.value)'
            type='number' name='facility_qty_".$request->room_facility_id."'
            id='facility_qty_".$request->room_facility_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'/>
        </td>
        <td align='right' id='sbs-facility-itm-".$request->room_facility_id."'
        ".number_format($facility->facility_price,2)."
        </td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteFacilityItm(".$facility->room_facility_id.")'>Hapus</button></td>
        </tr>
        ";
        if($request->ci){
            $qty=collect(Session::get('checkin-room-facility-qty'));
            $qty->put($request->room_facility_id,1);
            Session::put('checkin-room-facility-qty',$qty->toArray());
            Session::push('checkin-room-facility',$request->room_facility_id);
        }else{
            $qty=collect(Session::get('booked-room-facility-qty'));
            $qty->put($request->room_facility_id,1);
            Session::put('booked-room-facility-qty',$qty->toArray());
            Session::push('booked-room-facility',$request->room_facility_id);
        }
        return response($data);
    }
    public function changeFacilityQty(Request $request) {
        if($request->ci){
            $qty=collect(Session::get('checkin-room-facility-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('checkin-room-facility-qty',$qty->toArray());
        }else{
            $qty=collect(Session::get('booked-room-facility-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('booked-room-facility-qty',$qty->toArray());
        }
        return $qty;
    }
    public function getRoomMenus(Request $request) {
        $data = '';
        $menu = SalesRoomMenu::where('room_menu_type',$request->room_menu_type)->get(['room_menu_id','room_menu_name']);
        if(!$menu->count()){
            $data = "<option>Tidak Ada Menu </option>";
        }
        foreach($menu as $val){
            $data .= "<option value='".$val->room_menu_id."'>".$val->room_menu_name."</option>";
        }
        return response($data);
    }
    public function addMenuItem(Request $request) {
        $data = '';
        $menu = SalesRoomMenu::find($request->room_menu_id);
        $menutype = [
            1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner'
        ];
        $no = $request->no + 1;
        $data = "
        <tr class='menu-item menu-item-".$request->room_menu_id."' id='menu-item-".$request->room_menu_id."'>
        <td>".$no."
        <input type='hidden' id='room_menu_id[]' value='".$request->room_menu_id."' />
        </td>
        <td>".$menutype[$menu->room_menu_type]."</td>
        <td>".$menu->room_menu_name."</td>
        <td>
        <input type='text' class='form-control input-bb readonly menu_price_view' name='menu_price_view_".$menu->room_menu_id."' id='menu_price_view_".$menu->room_menu_id."' value='".number_format($menu->room_menu_price,2,',','.')."' readonly/>
        <input type='hidden' class='form-control input-bb readonly menu_price_price' data-id='".$request->room_menu_id."' name='menu_price_".$menu->room_menu_id."' id='menu_price_".$menu->room_menu_id."' value='".$menu->room_menu_price."' readonly/>
        </td>
        <td>
        <input
            oninput='changeMenuQty(".$request->room_menu_id.", this.value)'
            type='number' name='menu_qty_".$request->room_menu_id."'
            id='menu_qty_".$request->room_menu_id."'
            style='text-align: center; height: 30px; font-weight: bold; font-size: 15px'
            class='form-control col input-bb' min='1'
            value='1' autocomplete='off'>
        </td>
        <td align='right' id='sbs-menu-itm-".$request->room_menu_id."'>
       ".number_format($menu->room_menu_price,2)."
       </td>
        <td class='text-center'><button type='button' class='btn btn-outline-danger btn-sm' onclick='deleteMenuItm(".$menu->room_menu_id.")'>Hapus</button></td>
        </tr>
        ";
        if($request->ci){
            $qty=collect(Session::get('checkin-room-menu-qty'));
            $qty->put($request->room_menu_id,1);
            Session::put('checkin-room-menu-qty',$qty->toArray());
            Session::push('checkin-room-menu',$request->room_menu_id);
        }else{
            $qty=collect(Session::get('booked-room-menu-qty'));
            $qty->put($request->room_menu_id,1);
            Session::put('booked-room-menu-qty',$qty->toArray());
            Session::push('booked-room-menu',$request->room_menu_id);
        }
        return response($data);
    }
    public function changeMenuQty(Request $request) {
        if($request->ci){
            $qty=collect(Session::get('checkin-room-menu-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('checkin-room-menu-qty',$qty->toArray());
        }else{
            $qty=collect(Session::get('booked-room-menu-qty'));
            $qty->put($request->id,$request->qty);
            Session::put('booked-room-menu-qty',$qty->toArray());
        }
        return $qty;
    }
    public function processAdd(Request $request) {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $roomData = collect(Session::get('booked-room-data'));
        $booked = Session::get('booked-room-data-qty');
        $price = Session::get('booked-room-price');
        $menuData = collect(Session::get('booked-room-menu'));
        $menuqty = Session::get('booked-room-menu-qty');
        $facilityData = collect(Session::get('booked-room-facility'));
        $facilityqty = Session::get('booked-room-facility-qty');
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
            return redirect()->route('booking.add')->with(['msg'=>'Harap Tambahkan Kamar Yang Dibooking','type'=>'warning','tab-index' =>2]);
        }
        if(empty(Session::get('booking-token'))){
            return redirect()->route('booking.index')->with('msg','Tambah Booking Kamar Berhasil -');
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
        return 0;
        try{
            DB::beginTransaction();
            SalesOrder::create([
                'checkin_date' =>$start_date,
                'checkout_date' =>$end_date,
                'sales_order_price' =>$request->total_amount,
                'discount' =>$request->discount_percentage_total,
                'down_payment' =>$field['down_payment'],
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
            return redirect()->route('booking.index')->with(['msg'=>'Tambah Booking Kamar Berhasil','type'=>'success']);
        }catch(\Exception $e){
            $this->resetSession();
            Session::forget('booking-token');
            DB::rollBack();
            report($e);
            dump($e);
            return redirect()->route('booking.add')->with(['msg'=>'Tambah Booking Kamar Gagal','type'=>'danger']);
        }
    }
    public function resetSession() {
        Session::forget([
            'booking-data','booking-token',
            'booked-room-data','booked-room-price',
            'booked-room-data-qty','booked-room-menu',
            'booked-room-menu-qty','booked-room-facility',
            'booked-room-facility-qty','check-in',
            'checkin-data',
            'checkin-room-data','checkin-room-price',
            'checkin-room-data-qty','checkin-room-menu',
            'checkin-room-menu-qty','checkin-room-facility',
            'checkin-room-facility-qty','check-in',
        ]);
        return 1;
    }
    public function detail($sales_order_id){
        $data = SalesOrder::with(['rooms','facilities','menus'])->find($sales_order_id);
        $room = CoreRoom::with(['price','roomType','building'])->whereIn('room_id',$data->rooms->pluck('room_id'))->get();
        $facility = SalesRoomFacility::whereIn('room_facility_id',$data->facilities->pluck('room_facility_id'))->get();
        $menu = SalesRoomMenu::whereIn('room_menu_id',$data->menus->pluck('room_menu_id'))->get();
        $menutype = AppHelper::menuType();
        return  view('content.Booking.DetailBooking',compact('data','room','facility','menu','menutype'));
    }
    public function checkRoom(Request $request) {
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',3)
        ->where('checkin_date','>=',$request->start_date)
        ->where('checkin_date','<=',$request->end_date)
        ->Where('checkout_date','>',$request->start_date)
        ->Where('sales_order_status',1)
        ->get()->pluck('rooms');
        return response($booking->collapse()->pluck('room_id'));
    }
}

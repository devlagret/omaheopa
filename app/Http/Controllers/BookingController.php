<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\JournalHelper;
use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use App\Models\CorePriceType;
use App\Models\CoreRoom;
use App\Models\CoreRoomType;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use App\Models\SalesOrderFacility;
use App\Models\SalesOrderMenu;
use App\Models\SalesOrderRescedule;
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
// * Note : ci = check-in
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
        $this->resetUpdateSession();
        $booking = SalesOrder::with('rooms','invoice')->where('data_state',0)
        ->where('sales_order_type','!=',1)
        ->where('sales_order_type','!=',2)
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
        Session::put('booking-token',Str::uuid());
        $sessiondata = Session::get('booking-data');
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
        $ordertype= AppHelper::orderType();
        $facilityitm = SalesRoomFacility::whereIn('room_facility_id',$facilityData->flatten())->get();
        $menuItm = SalesRoomMenu::whereIn('room_menu_id',$menuData->flatten())->get();
        return view('content.Booking.FormAddBooking',compact('sessiondata','price','menutype','facility','ordertype','booked','room','building','menuqty','facilityqty','facilityitm','menuItm'));
    }
    public function elementsAdd(Request $request){
        if($request->ci==2){
            $sessiondata = Session::get('edit-booking-data');
        }else{
            $sessiondata = Session::get('booking-data');
        }
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['atas_nama']   = '';
        }
        $sessiondata[$request->name] = $request->value;
        if($request->ci==2){
            Session::put('edit-booking-data', $sessiondata);
        }else{
            Session::put('booking-data', $sessiondata);
        }
    }
    public function getType(Request $request) {
        $data = '';
        $sessiondata = Session::get('booking-data');
        try{
        $building = CoreRoom::where('building_id',$request->building_id)->groupBy('room_type_id')->get('room_type_id')->pluck('room_type_id');
        $type = CoreRoomType::whereIn('room_type_id',$building)->get();
        $sessiondata['room_type_id'] ?? $sessiondata['room_type_id'] = 1;
        if ($building->count() == 0) {
            $data = "<option>Bangunan Tidak Memiliki Kamar</option>\n";
        }
        foreach ( $type as $val) {
            $data .= "<option value='".$val->room_type_id."' " . ($sessiondata['room_type_id'] == $val->room_type_id ? 'selected' : '') .">".$val->room_type_name."</option>\n";
        }
        return response($data);
    }catch(\Exception $e){
        error_log(strval($e));
        return response('err');

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
        ->where('checkout_date','>',$request->start_date);
        if(isset($request->sales_order_id)){
            $booking->where('sales_order_id','!=',$request->sales_order_id);
        }
        $booking = $booking->get()->pluck('rooms');
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
        $qty =$this->getQtyBySession('data',$request->ci);
        $qty->put($request->room_id,1);
        $this->putQtyToSession('data',$request->ci,$qty->toArray());
        $this->pushDataToSession('data',$request->ci,$request->room_id);
        return response($data);
    }
    public function addPersonBooked(Request $request ) {
            $qty=collect($this->getQtyBySession('data',$request->ci));
            $qty->put($request->id,$request->qty);
            $this->putQtyToSession('data',$request->ci,$qty->toArray());
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
    private function getDataSession($type, $ci) {
        switch ($ci) {
            case 1:
                return Session::get('checkin-room-'.$type);
            case 2:
                return Session::get('edit-booked-room-'.$type);
            default:
                return Session::get('booked-room-'.$type);
        }
    }
    private function putDataToSession($type,$ci,$data) {
        switch ($ci) {
            case 1:
                return Session::put('checkin-room-'.$type,$data);
            case 2:
                return Session::put('edit-booked-room-'.$type,$data);
            default:
                return Session::put('booked-room-'.$type,$data);
        }
    }
    private function pushDataToSession($type,$ci,$data) {
        switch ($ci) {
            case 1:
                return Session::push('checkin-room-'.$type,$data);
            case 2:
                return Session::push('edit-booked-room-'.$type,$data);
            default:
                return Session::push('booked-room-'.$type,$data);
        }
    }
    private function getQtyBySession($type,$ci) {
        switch ($ci) {
            case 1:
                return collect(Session::get('checkin-room-'.$type.'-qty'));
            case 2:
                return collect(Session::get('edit-booked-room-'.$type.'-qty'));
            default:
                return collect(Session::get('booked-room-'.$type.'-qty'));
        }
    }
    private function putQtyToSession($type,$ci,$data) {
        switch ($ci) {
            case 1:
                return Session::put('checkin-room-'.$type.'-qty',$data);
            case 2:
                return Session::put('edit-booked-room-'.$type.'-qty',$data);
            default:
                return Session::put('booked-room-'.$type.'-qty',$data);
        }
    }
    public function deleteBookedRoom($room_id,$ci){
        $data=collect($this->getDataSession('data',$ci));
        foreach($data as $key => $val){
            if($val == $room_id){
                $data->forget($key);
            }
        }
        $this->putDataToSession('data',$ci,$data->toArray());
        $qty=collect($this->getQtyBySession('data',$ci));
        $qty->forget($room_id);
        $this->putQtyToSession('data',$ci,$qty->toArray());
        return 1;
    }
    public function deleteFacility($room_facility_id,$ci){
        $data=collect($this->getDataSession('facility',$ci));
        foreach($data as $key => $val){
            if($val == $room_facility_id){
                $data->forget($key);
            }
        }
        $this->putDataToSession('facility',$ci,$data->toArray());
        $qty=collect($this->getQtyBySession('facility',$ci));
        $qty->forget($room_facility_id);
        $this->putQtyToSession('facility',$ci,$qty->toArray());
        return 1;
    }
    public function deleteMenu($room_menu_id,$ci){
        $data=collect($this->getDataSession('menu',$ci));
        foreach($data as $key => $val){
            if($val == $room_menu_id){
                $data->forget($key);
            }
        }
        $this->putDataToSession('menu',$ci,$data->toArray());
        $qty=collect($this->getQtyBySession('menu',$ci));
        $qty->forget($room_menu_id);
        $this->putQtyToSession('menu',$ci,$qty->toArray());
        return 1;
    }
    public function getRoomPrice(Request $request) {
        if(empty($request->room_price_id)){
            return 0;
        }
        $price = SalesRoomPrice::find($request->room_price_id);
        if($request->ci==1&&$request->room_id!=null){
            $pr=collect(Session::get('checkin-room-price'));
            $pr->put($request->room_id,$request->room_price_id);
            Session::put('checkin-room-price',$pr->toArray());
        }elseif($request->ci==2&&$request->room_id!=null){
            $pr=collect(Session::get('edit-booked-room-price'));
            $pr->put($request->room_id,$request->room_price_id);
            Session::put('edit-booked-room-price',$pr->toArray());
        }elseif($request->room_id!=null){
            $pr=collect(Session::get('booked-room-price'));
            $pr->put($request->room_id,$request->room_price_id);
            Session::put('booked-room-price',$pr->toArray());
        }
        return response($price->room_price_price);
    }
    public function getRoomPriceList(Request $request) {
        $i=1;$data ='';
        if($request->ci==1){
            $sessiondata = Session::get('checkin-data');
        }elseif($request->ci==2){
            $sessiondata = Session::get('edit-booking-data');
        }
        else{
            $sessiondata = Session::get('booking-data');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $room = CoreRoom::with(['building','roomType','price'=>function ($query) use($start_date,$end_date){
            $query->where('room_price_start_date', '<=', $start_date)
                  ->where('room_price_end_date', '>=', $end_date)
                  ->orWhereNull('room_price_start_date')
                  ->orWhereNull('room_price_end_date')
                  ->orderByDesc('price_type_id');
        }])->find($request->room_id);
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
        $qty =$this->getQtyBySession('facility',$request->ci);
        $qty->put($request->room_facility_id,1);
        $this->putQtyToSession('facility',$request->ci,$qty->toArray());
        $this->pushDataToSession('facility',$request->ci,$request->room_facility_id);
        return response($data);
    }
    public function changeFacilityQty(Request $request) {
        $qty=collect($this->getQtyBySession('facility',$request->ci));
        $qty->put($request->id,$request->qty);
        $this->putQtyToSession('facility',$request->ci,$qty->toArray());
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
        $menutype = AppHelper::menuType();
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
        $qty =$this->getQtyBySession('menu',$request->ci);
        $qty->put($request->room_menu_id,1);
        $this->putQtyToSession('menu',$request->ci,$qty->toArray());
        $this->pushDataToSession('menu',$request->ci,$request->room_menu_id);
        return response($data);
    }
    public function changeMenuQty(Request $request) {
        $qty =$this->getQtyBySession('menu',$request->ci);
        $qty->put($request->id,$request->qty);
        $this->putQtyToSession('menu',$request->ci,$qty->toArray());
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
        // dump($request->all());
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
        // dump($check);
        // dump($checkfac);
        // dump($checkmenu);
        // // return 0;
        try{
            DB::beginTransaction();
            $down_payment=null;$sales_invoice_id=null;$sales_order_status=0;
            if($request->sales_order_type==0){
                // * booking normal
                $down_payment = $request->down_payment;
            }elseif($request->sales_order_type==3){
                // * booking tanpa dp
                $sales_order_status = 2;
                SalesInvoice::create([
                    'total_amount' => $request->total_amount,
                    'sales_invoice_token' => $token,
                    'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                    'created_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'merchant_id' => empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
                ]);
                $si = SalesInvoice::where('sales_invoice_token',$token)->first();
                $sales_invoice_id = $si->sales_invoice_id;
            }elseif($request->sales_order_type==4){
                // * booking full book
                $sales_order_status = 2;
                SalesInvoice::create([
                    'total_amount' => $request->total_amount,
                    'sales_invoice_token' => $token,
                    'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                    'created_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'merchant_id' => empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
                ]);
                // * buat jurnal
                JournalHelper::token($token)->make('Booking Full Book',['hotel_account','hotel_cash_account'],$request->total_amount);
                $si = SalesInvoice::where('sales_invoice_token',$token)->first();
                $sales_invoice_id = $si->sales_invoice_id;
            }
            SalesOrder::create([
                'checkin_date' =>$start_date,
                'checkout_date' =>$end_date,
                'sales_invoice_id' =>$sales_invoice_id,
                'sales_order_status' =>$sales_order_status,
                'sales_order_price' =>$request->total_amount,
                'discount' =>$request->discount_percentage_total,
                'down_payment' =>$down_payment,
                'sales_order_type' => $request->sales_order_type,
                'order_date' => Carbon::now()->format('Y-m-d'),
                'sales_order_name' => $field['atas_nama'],
                'phone_number' => $request->phone_number,
                'address' => $request->address,
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
            return redirect()->route('booking.index')->with(['msg'=>'Tambah Booking Kamar Gagal','type'=>'danger']);
        }
    }
    private function resetSession() {
        Session::forget([
            'booking-data','booking-token',
            'booked-room-data','booked-room-price',
            'booked-room-data-qty','booked-room-menu',
            'booked-room-menu-qty','booked-room-facility',
            'booked-room-facility-qty',
        ]);
        return 1;
    }
    private function resetUpdateSession() {
        Session::forget([
            'edit-booking-data',
            'edit-booked-room-data','edit-booked-room-price',
            'edit-booked-room-data-qty','edit-booked-room-menu',
            'edit-booked-room-menu-qty','edit-booked-room-facility',
            'edit-booked-room-facility-qty',
        ]);
        return 1;
    }
    public function detail($sales_order_id){
        $data = SalesOrder::with(['rooms','facilities','menus','invoice'])->find($sales_order_id);
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
    public function rescedule($sales_order_id) {
        $sessiondata = Session::get('booking-data');
        $rsc =1;
        $data = SalesOrder::with(['rooms','facilities','menus'])->find($sales_order_id);
        $room = CoreRoom::with(['price','roomType','building'])->whereIn('room_id',$data->rooms->pluck('room_id'))->get();
        $facility = SalesRoomFacility::whereIn('room_facility_id',$data->facilities->pluck('room_facility_id'))->get();
        $menu = SalesRoomMenu::whereIn('room_menu_id',$data->menus->pluck('room_menu_id'))->get();
        $menutype = AppHelper::menuType();
        return  view('content.Booking.DetailBooking',compact('data','room','facility','menu','menutype','sessiondata','rsc'));
    }
    public function processRescedule(Request $request) {
        dump($request->all());
        $so = SalesOrder::with('rooms')->find($request->sales_order_id);
        dump($so);
        foreach ($so->rooms as $val){
            dump($val);
        }
        // return 1;
        try{
            DB::beginTransaction();
            SalesOrderRescedule::create([
                'sales_order_id' => $so->sales_order_id,
                'checkin_date' => $request->checkin_date,
                'checkin_date_old' => $request->checkin_date_old,
                'checkout_date' => $request->checkout_date,
                'checkout_date_old' => $request->checkout_date_old,
                'created_id' => Auth::id(),
            ]);
            $so->checkin_date       = $request->checkin_date;
            $so->checkout_date      = $request->checkout_date;
            $so->down_payment       = $request->down_payment;
            $so->sales_order_price  = $request->total_amount;
            $so->save();
            DB::rollBack();
            return redirect()->route('booking.index')->with('msg','Rescedule Booking Berhasil');
        }catch(\Exception $e){
            DB::rollBack();
            report($e);
            dump($e);
        }

    }
    public function edit($sales_order_id) {
        $ci = 2;
        $data =SalesOrder::find($sales_order_id);
        if(empty(Session::get('edit-booked-room-data'))&&empty(Session::get('edit-booked-room-menu'))&&empty(Session::get('edit-booked-room-facility'))){
            $this->setEditSession($sales_order_id);
        }
        // return $this->resetUpdateSession();
        Session::put('eb-token',Str::uuid());
        $sessiondata = Session::get('edit-booking-data');
        $roomData = collect(Session::get('edit-booked-room-data'));
        $booked = Session::get('edit-booked-room-data-qty');
        $menuData = collect(Session::get('edit-booked-room-menu'));
        $price=collect(Session::get('edit-booked-room-price'));
        $menuqty = Session::get('edit-booked-room-menu-qty');
        $facilityData = collect(Session::get('edit-booked-room-facility'));
        $facilityqty = Session::get('edit-booked-room-facility-qty');
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
        $ordertype= AppHelper::orderType();
        $facilityitm = SalesRoomFacility::whereIn('room_facility_id',$facilityData->flatten())->get();
        $menuItm = SalesRoomMenu::whereIn('room_menu_id',$menuData->flatten())->get();
        return view('content.Booking.FormEditBooking',compact('data','sessiondata','price','menutype','facility','ordertype','booked','room','building','menuqty','facilityqty','facilityitm','menuItm','ci'));
    }
    private function setEditSession($sales_order_id): void {
        $so =SalesOrder::with(['rooms','facilities','menus'])->find($sales_order_id);
        $sessiondata = Session::get('edit-booking-data');
        $sessiondata['tab-index']   = 2;
        foreach($so->rooms as $rv){
            $sessiondata['room_price_id_'.$rv->room_id] = $rv->room_price_id;
            $qtyR=collect(Session::get('edit-booked-room-data-qty'));
            $qtyR->put($rv->room_id,$rv->people);
            Session::put('edit-booked-room-data-qty',$qtyR->toArray());
            Session::push('edit-booked-room-data',$rv->room_id);
        }
        foreach($so->facilities as $rf){
            $qtyF=collect(Session::get('edit-booked-room-facility-qty'));
            $qtyF->put($rf->room_facility_id,$rf->quantity);
            Session::put('edit-booked-room-facility-qty',$qtyF->toArray());
            Session::push('edit-booked-room-facility',$rf->room_facility_id);
        }
        foreach($so->menus as $rm){
            $qtyM=collect(Session::get('edit-booked-room-menu-qty'));
            $qtyM->put($rm->room_menu_id,$rm->quantity);
            Session::put('edit-booked-room-menu-qty',$qtyM->toArray());
            Session::push('edit-booked-room-menu',$rm->room_menu_id);
        }
        Session::put('edit-booking-data', $sessiondata);
    }
    public function processEdit(Request $request) {
        if (empty(Session::get('eb-token'))){
            return redirect()->route('booking.index')->with('msg','Edit Bookibg Berhasil -');
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $roomData = collect(Session::get('edit-booked-room-data'));
        $booked = Session::get('edit-booked-room-data-qty');
        $price = Session::get('edit-booked-room-price');
        $menuData = collect(Session::get('edit-booked-room-menu'));
        $menuqty = Session::get('edit-booked-room-menu-qty');
        $facilityData = collect(Session::get('edit-booked-room-facility'));
        $facilityqty = Session::get('edit-booked-room-facility-qty');
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
        // dump($check);
        // dump($checkfac);
        // dump($checkmenu);
        // return 0;
        try{
            DB::beginTransaction();
            $down_payment=null;$sales_invoice_id=null;$sales_order_status=0;
            if($request->sales_order_type==0){
                // * booking normal
                $down_payment = $request->down_payment;
            }elseif($request->sales_order_type==3){
                // * booking tanpa dp
                $sales_order_status = 2;
                SalesInvoice::create([
                    'total_amount' => $request->total_amount,
                    'sales_invoice_token' => $token,
                    'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                    'created_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'merchant_id' => empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
                ]);
                $si = SalesInvoice::where('sales_invoice_token',$token)->first();
                $sales_invoice_id = $si->sales_invoice_id;
            }elseif($request->sales_order_type==4){
                // * booking full book
                $sales_order_status = 2;
                SalesInvoice::create([
                    'total_amount' => $request->total_amount,
                    'sales_invoice_token' => $token,
                    'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                    'created_id' => Auth::id(),
                    'company_id' => Auth::user()->company_id,
                    'merchant_id' => empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
                ]);
                // * buat jurnal
                JournalHelper::token($token)->make('Booking Full Book',['hotel_account','hotel_cash_account'],$request->total_amount);
                //
                $si = SalesInvoice::where('sales_invoice_token',$token)->first();
                $sales_invoice_id = $si->sales_invoice_id;
            }
          $order = SalesOrder::find($request->sales_order_id);
            // SalesOrder::create([
            //     'checkin_date' =>$start_date,
            //     'checkout_date' =>$end_date,
            //     'sales_invoice_id' =>$sales_invoice_id,
            //     'sales_order_status' =>$sales_order_status,
            //     'sales_order_price' =>$request->total_amount,
            //     'discount' =>$request->discount_percentage_total,
            //     'down_payment' =>$down_payment,
            //     'sales_order_type' => $request->sales_order_type,
            //     'order_date' => Carbon::now()->format('Y-m-d'),
            //     'sales_order_name' => $field['atas_nama'],
            //     'created_id'    => Auth::id(),
            //     'company_id'    => Auth::user()->company_id,
            //     'sales_order_token' => $token->toString(),
            // ]);
            $order->sales_order_price = $request->sales_order_price;
            $order->sales_invoice_id = $sales_invoice_id;
            $order->discount = $request->discount;
            $order->down_payment = $down_payment;
            $order->sales_order_status = $sales_order_status;
            $order->sales_order_type = $request->sales_order_type;
            $order->sales_order_name =  $field['atas_nama'];
            $order->phone_number = $request->phone_number;
            $order->address      = $request->address;
            $order->save();
            SalesOrderRoom::where('sales_order_id',$order->sales_order_id)->delete();
            SalesOrderFacility::where('sales_order_id',$order->sales_order_id)->delete();
            SalesOrderMenu::where('sales_order_id',$order->sales_order_id)->delete();
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
            Session::forget('eb-token');
            $this->resetSession();
            return redirect()->route('booking.index')->with(['msg'=>'Edit Booking Kamar Berhasil','type'=>'success']);
        }catch(\Exception $e){
            $this->resetSession();
            Session::forget('eb-token');
            DB::rollBack();
            report($e);
            dump($e);
            return redirect()->route('booking.add')->with(['msg'=>'Edit Booking Kamar Gagal','type'=>'danger']);
        }
    }
    public function checkConflic(Request $request) {
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',3)
        ->where('checkin_date','>=',$request->start_date)
        ->where('checkin_date','<=',$request->end_date)
        ->Where('checkout_date','>',$request->start_date)
        ->Where('sales_order_status',1)
        ->get();
        return response(['status'=>0,'data'=>$booking]);
    }
    public function delete($sales_order_id) {
        $so =SalesOrder::find($sales_order_id);
        dump($sales_order_id);
        return dump($so);
    }

}

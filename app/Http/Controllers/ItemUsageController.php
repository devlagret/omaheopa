<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\InvtItemUnit;
use App\Models\InvtItemUsage;
use App\Models\SalesMerchant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ItemUsageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('h-item-data');
        $filter = Session::get('lst-usage-filter');
        $usage = InvtItemUsage::with('item','unit')
        ->where('date','>=',$filter['start_date']??date('Y-m-d'))
        ->where('date','<=',$filter['end_date']??date('Y-m-d'))
        ->get();
        return view('content.ItemUsage.ListUsage')->with(['usage'=>$usage]);
    }
    public function add() {
        $sessiondata = Session::get('h-item-data');
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        return view('content.ItemUsage.FormAddUsage',compact('sessiondata','merchant'));
    }
    public function filter(Request $request) {
        $data = collect(Session::get('lst-usage-filter'));
        $data->put('start_date',$request->start_date);
        $data->put('end_date',$request->end_date);
        Session::put('lst-usage-filter',$data->toArray());
        return redirect()->route('hi.index');
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('h-item-data');
        $sessiondata[$request->name] = $request->value;
        Session::put('building-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        // dump(StockHelper::find($request->item_id));
        // return dump($request->all());
        try {
            DB::beginTransaction();
            InvtItemUsage::create([
                'item_id'=>$request->item_id,
                'usage_type'=>1,
                'date'=>Carbon::now()->format('Y-m-d'),
                'merchant_id'=>Auth::user()->merchant_id??$request->merchant_id,
                'usage_remark'=>$request->usage_remark,
                'item_unit_id'=>$request->item_unit_id,
                'quantity'=>$request->quantity,
                'created_id'=>Auth::id()]);
            StockHelper::find($request->item_id)->sub($request->quantity,$request->item_unit_id);
            DB::commit();
           return redirect()->route('hi.index')->with(['type'=>'success','msg'=>'Tambah Penggunaan Barang Berhasil']);
        }catch(\Exception $e){
            DB::rollBack();
            dd($e);
            report($e);
            return redirect()->route('hi.index')->with(['type'=>'danger','msg'=>'Tambah Penggunaan Barang Gagal']);
        }

    }
    public function edit($invt_item_usage_id) {
        $sessiondata = Session::get('h-item-data');
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $data = InvtItemUsage::with('item')->find($invt_item_usage_id);
        return view('content.ItemUsage.FormEditUsage',compact('sessiondata','data','merchant'));
    }
    public function processEdit(Request $request){
        try {
        DB::beginTransaction();
            $usage = InvtItemUsage::find($request->invt_item_usage_id);
            if($usage->quantity!=$request->quantity||$usage->item_unit_id!=$request->item_unit_id||$usage->item_id!=$request->item_id){
            StockHelper::find($usage->item_id)->add($usage->quantity,$usage->item_unit_id);
            StockHelper::find($request->item_id)->sub($request->quantity,$request->item_unit_id);
            }
            $usage->item_id=$request->item_id;
            $usage->merchant_id=Auth::user()->merchant_id??$request->merchant_id;
            $usage->usage_remark=$request->usage_remark;
            $usage->item_unit_id=$request->item_unit_id;
            $usage->quantity=$request->quantity;
            $usage->save();
            DB::commit();
            return redirect()->route('hi.index')->with(['type'=>'success','msg'=>'Edit Penggunaan Berhasil']);
        }catch(\Exception $e){
            DB::rollBack();
            report($e);
            return redirect()->route('hi.index')->with(['type'=>'danger','msg'=>'Edit Penggunaan Gagal']);
        }
    }
    public function delete($invt_item_usage_id) {
        $item=InvtItemUsage::find($invt_item_usage_id);
        $item->data_state = '1';
        $item->deleted_id = Auth::id();
        if($item->save()){if($item->delete()){
           return redirect()->route('hi.index')->with(['type'=>'success','msg'=>'Hapus Penggunaan Berhasil']);
        };}
        return redirect()->route('hi.index')->with(['type'=>'danger','msg'=>'Hapus Penggunaan Gagal']);
    }
}

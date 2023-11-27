<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtStockAdjustment;
use App\Models\InvtStockAdjustmentItem;
use App\Models\InvtWarehouse;
use App\Models\SalesMerchant;
use App\Models\PurchaseInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtStockAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {
        Session::forget('category_id');
        Session::forget('item_id');
        Session::forget('unit_id');
        Session::forget('warehouse_id');
        Session::forget('date');
        Session::forget('data_item');
        Session::forget('datases');
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data  = InvtStockAdjustment::with('warehouse','item.item')
        ->where('stock_adjustment_date', '>=', $start_date)
        ->where('stock_adjustment_date', '<=', $end_date)
        ->where('company_id', Auth::user()->company_id)
        ->get(); 
        return view('content.InvtStockAdjustment.ListInvtStockAdjustment',compact('data','start_date','end_date'));
    }

    public function addStockAdjustment()
    {   
        if(!$unit_id = Session::get('unit_id')){
            $unit_id = '';
        } else {
             $unit_id = Session::get('unit_id');
        }

        // if(!$date = Session::get('date')){
        //     $date = date('Y-m-d');
        // } else {
        //      $date = Session::get('date');
        // }
        if(!$warehouse_id = Session::get('warehouse_id')){
            $warehouse_id = '';
        } else {
             $warehouse_id = Session::get('warehouse_id');
        }
        
        $data_item = Session::get('data_item');
        if(!$date = Session::get('date')){
            $date = date('Y-m-d');
        } else {
            $date = Session::get('date');
        }
        $merchant = SalesMerchant::where("data_state",0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');

        $categorys  = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouse  = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units      = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items      = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');
        $datasess   = Session::get('datases');
        $data = InvtItemStock::with(['item','unit','category','warehouse'])->where('data_state',0)
        ->where('item_id', $data_item['item_id']??'')
        ->where('item_category_id', $data_item['item_category']??'')
        ->where('item_unit_id', $data_item['item_unit']??'')
        ->where('warehouse_id',$data_item['warehouse_id']??'')->get();
        return view('content.InvtStockAdjustment.FormAddInvtStockAdjustment', compact('warehouse_id','merchant','categorys', 'units', 'items', 'datasess', 'data', 'date','warehouse','unit_id'));
    }

    public function addElementsStockAdjustment(Request $request)
    {
        $datasess = Session::get('datases');
        if(!$datasess || $datasess == ''){
            $datasess['item_category_id']        = '';
            $datasess['item_id']                 = '';
            $datasess['item_unit_id']            = '';
            $datasess['warehouse_id']            = '';
            $datasess['stock_adjustment_date']   = '';
        }

        $datasess[$request->name] = $request->value;
        $datasess = Session::put('datases',$datasess);
        return 1;
    }

    public function filterAddStockAdjustment(Request $request)
    {
        $request->validate(['item_unit_id'=>'required|integer','item_id'=>'required|integer'],[
            'item_unit_id.required'=>'Harap Pilih item unit!',
            'item_id.required'=>'Harap Pilih item!',
            'item_unit_id.integer'=>'Item tidak memiliki unit',
            'item_id.integer'=>'Wahana/Merchant tidak memiliki wahana',
        ]);
        $datas = [
            "item_category"         =>$request->item_category,
            "item_id"               =>$request->item_id,
            "warehouse_id"          =>$request->warehouse_id,
            "stock_adjustment_date" =>$request->stock_adjustment_date,
            "item_unit"             => InvtItem::findOrFail($request->item_id)->item_unit_id1,
        ];
        // dd($datas);exit;
        $date        = $request->stock_adjustment_date;
        $warehouse_id        = $request->warehouse_id;
        Session::put('data_item', $datas);
        Session::put('date',$date);
        Session::put('warehouse_id',$warehouse_id);

        $data = InvtItemStock::with(['item','unit','category','warehouse'])->where('data_state',0)
        ->where('item_id', $datas['item_id']??'')
        ->where('item_category_id', $datas['item_category']??'')
        ->where('warehouse_id',$datas['warehouse_id']??'')->first();
        if($data==null){
            return redirect()->route('add-stock-adjustment')->with('msg',"Barang yang Dicari Tidak Memiliki Stok");
        }
        return redirect()->route('add-stock-adjustment');
    }

    public function filterListStockAdjustment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/stock-adjustment');
    }

    public function getItemName($item_id)
    {
        $data   = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getWarehouseName($warehouse_id)
    {
        $data   = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();

        return $data['warehouse_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data   = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function getItemStock($item_id, $item_unit_id, $item_category_id, $warehouse_id)
    {
        $data = InvtItemStock::where('item_id',$item_id)
        ->where('warehouse_id', $warehouse_id)
        ->where('item_category_id',$item_category_id)
        ->where('item_unit_id', $item_unit_id)
        ->first();
        return $data['last_balance'];
    }

    public function processAddStockAdjustment(Request $request)
    {

        $data_header = array(
            'stock_adjustment_date' => Session::get('date'),
            'warehouse_id'          => Session::get('warehouse_id'),
            'company_id'            => Auth::user()->company_id,
            'created_id'            => Auth::id(),
            'updated_id'            => Auth::id()
        );

        if(InvtStockAdjustment::create($data_header)){
            $stock_adjustment_id   = InvtStockAdjustment::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();
            $dataArray = array(
                'stock_adjustment_id'           => $stock_adjustment_id['stock_adjustment_id'],
                'item_id'                       => $request['item_id'],
                'item_category_id'              => $request['item_category_id'],
                'item_unit_id'                  => $request['item_unit_id'],
                'last_balance_data'             => $request['last_balance_data'],
                'last_balance_physical'         => $request['last_balance_physical'],
                'last_balance_adjustment'       => $request['last_balance_adjustment'],
                'stock_adjustment_item_remark'  => $request['stock_adjustment_item_remark'],
                'company_id'                    => Auth::user()->company_id,
                'created_id'                    => Auth::id(),
                'updated_id'                    => Auth::id(),
            );
            InvtStockAdjustmentItem::create($dataArray); 
            $stock_item = InvtItemStock::where('item_id',$dataArray['item_id'])
            ->where('item_category_id',$dataArray['item_category_id'])
            ->where('warehouse_id', $data_header['warehouse_id'])
            ->where('item_unit_id', $dataArray['item_unit_id'])
            ->first();
            if(isset($stock_item)){
                $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                $table->last_balance = $dataArray['last_balance_adjustment'];
                $table->updated_id = Auth::id();
                $table->save();

            }
        } else {
            $msg = 'Tambah Stock Gagal';
            return redirect('/stock-adjustment/add')->with('msg',$msg);
        }
        Session::forget('data_item');
        Session::forget('warehouse_id');
        Session::forget('date');
        Session::forget('datases');

        $msg = 'Tambah Stock Berhasil';
        return redirect('/stock-adjustment/add')->with('msg',$msg);
    }

    public function addReset(){
        Session::forget('category_id');
        Session::forget('item_id');
        Session::forget('unit_id');
        Session::forget('warehouse_id');
        Session::forget('date');
        Session::forget('datases');

        return redirect('/stock-adjustment/add');
    }

    public function listReset()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/stock-adjustment');
    }

    public function detailStockAdjustment($stock_adjustment_id)
    {
        $categorys  = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $warehouse  = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        $units      = InvtItemUnit::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $items      = InvtItem::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');

        $data = InvtStockAdjustment::with('items.item','items.unit','warehouse')->find($stock_adjustment_id);
        return view('content.InvtStockAdjustment.DetailInvtStockAdjustment',compact('categorys','warehouse','units','items','data'));
    }
}


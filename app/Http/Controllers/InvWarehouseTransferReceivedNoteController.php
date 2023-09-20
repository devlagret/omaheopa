<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InvWarehouse;
use App\Models\CoreSupplier;
use App\Models\CoreExpedition;
use App\Models\InvItemCategory;
use App\Models\InvItem;
use App\Models\InvItemUnit;
use App\Models\InvItemType;
use App\Models\InvItemStock;
use App\Models\InvGoodsReceivedNote;
use App\Models\InvGoodsReceivedNoteItem;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\InvWarehouseTransferReceivedNote;
use App\Models\InvWarehouseTransferReceivedNoteItem;
use App\Models\InvWarehouseTransfer;
use App\Models\InvWarehouseTransferItem;
use App\Models\InvWarehouseTransferType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class InvWarehouseTransferReceivedNoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }

        $warehousetransferreceivednote = InvWarehouseTransferReceivedNote::select('inv_warehouse_transfer_received_note.*', 'invt_warehouse_transfer.*')
        ->where('inv_warehouse_transfer_received_note.data_state','=',0)
        ->join('invt_warehouse_transfer', 'invt_warehouse_transfer.warehouse_transfer_id', 'inv_warehouse_transfer_received_note.warehouse_transfer_id')
        ->where('inv_warehouse_transfer_received_note.created_at', '>=', $start_date)
        ->where('inv_warehouse_transfer_received_note.created_at', '<=', $end_date)
        ->get();

        return view('content/InvWarehouseTransferReceivedNote/ListInvWarehouseTransferReceivedNote',compact('warehousetransferreceivednote', 'start_date', 'end_date'));
    }

    public function filterInvWarehouseTransferReceivedNote(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/warehouse-transfer-received-note');
    }

    public function resetFilterInvWarehouseTransferReceivedNote(){
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/warehouse-transfer-received-note');
    }

    public function searchWarehouseTransfer()
    {
        $warehousetransfer = InvWarehouseTransfer::where('data_state', 0)
        ->where('warehouse_transfer_status', 0)
        ->get();

        return view('content/InvWarehouseTransferReceivedNote/SearchWarehouseTransfer', compact('warehousetransfer'));
    }

    public function addInvWarehouseTransferReceivedNote($warehouse_transfer_id)
    {
        $warehousetransfer = InvWarehouseTransfer::where('data_state', 0)
        ->where('warehouse_transfer_id', $warehouse_transfer_id)
        ->first();
        
        $warehousetransferitem = InvWarehouseTransferItem::where('data_state', 0)
        ->where('warehouse_transfer_id', $warehouse_transfer_id)
        ->get();

        return view('content/InvWarehouseTransferReceivedNote/FormAddInvWarehouseTransferReceivedNote',compact('warehousetransfer', 'warehousetransferitem'));
    }

    public function detailInvWarehouseTransferReceivedNote($warehouse_transfer_received_note_id)
    {
        $invwarehousetransferreceivednote = InvWarehouseTransferReceivedNote::where('data_state', 0)
        ->where('warehouse_transfer_received_note_id', $warehouse_transfer_received_note_id)
        ->first();
        
        $invwarehousetransferreceivednoteitem = InvWarehouseTransferReceivedNoteItem::select('warehouse_transfer_received_note_item.*', 'warehouse_transfer_item.quantity')
        ->where('warehouse_transfer_received_note_item.data_state', 0)
        ->join('warehouse_transfer_item', 'warehouse_transfer_item.warehouse_transfer_item_id', 'warehouse_transfer_received_note_item.warehouse_transfer_item_id')
        ->where('warehouse_transfer_received_note_id', $warehouse_transfer_received_note_id)
        ->get();

        return view('content/InvWarehouseTransferReceivedNote/FormDetailInvWarehouseTransferReceivedNote',compact('invwarehousetransferreceivednote', 'invwarehousetransferreceivednoteitem', 'warehouse_transfer_received_note_id'));
    }

    public function voidInvWarehouseTransferReceivedNote($warehouse_transfer_received_note_id)
    {
        $invwarehousetransferreceivednote = InvWarehouseTransferReceivedNote::where('data_state', 0)
        ->where('warehouse_transfer_received_note_id', $warehouse_transfer_received_note_id)
        ->first();
        
        $invwarehousetransferreceivednoteitem = InvWarehouseTransferReceivedNoteItem::select('warehouse_transfer_received_note_item.*', 'warehouse_transfer_item.quantity')
        ->where('warehouse_transfer_received_note_item.data_state', 0)
        ->join('warehouse_transfer_item', 'warehouse_transfer_item.warehouse_transfer_item_id', 'warehouse_transfer_received_note_item.warehouse_transfer_item_id')
        ->where('warehouse_transfer_received_note_id', $warehouse_transfer_received_note_id)
        ->get();

        return view('content/InvWarehouseTransferReceivedNote/FormVoidInvWarehouseTransferReceivedNote',compact('invwarehousetransferreceivednote', 'invwarehousetransferreceivednoteitem', 'warehouse_transfer_received_note_id'));
    }

    // public function processVoidInvWarehouseTransferReceivedNote($warehouse_transfer_received_note_id)
    // {
    //     $warehousetransferreceivednote = InvWarehouseTransferReceivedNote::findOrFail($warehouse_transfer_received_note_id);
    //     $warehousetransferreceivednote->data_state = 1;
    //     $warehousetransferreceivednote->save();

    //     $warehousetransfer = InvWarehouseTransfer::where('warehouse_transfer_id', $warehousetransferreceivednote['warehouse_transfer_id'])
    //     ->first();
    //     $warehousetransfer->warehouse_transfer_status = 0;
    //     $warehousetransfer->save();

    //     $warehousetransferreceivednoteitem = InvWarehouseTransferReceivedNoteItem::where('data_state', 0)
    //     ->where('warehouse_transfer_received_note_id', $warehouse_transfer_received_note_id)
    //     ->get();

    //     foreach($warehousetransferreceivednoteitem as $item){
    //         $receivednoteitem = InvWarehouseTransferReceivedNoteItem::findOrFail($item['warehouse_transfer_received_note_item_id']);
    //         $receivednoteitem->data_state = 1;
    //         $receivednoteitem->save();

    //         $itemstock = InvItemStock::where('item_stock_id', $item['item_stock_id'])
    //         ->first();
    //         $itemstock->data_state = 1;
    //         $itemstock->save();
    //     }

    //     $msg = 'Hapus Penerimaan Transfer Gudang Berhasil';
    //     return redirect('/warehouse-transfer-received-note')->with('msg',$msg);
    // }

    // public function detailPurchaseOrder($purchase_order_id)
    // {
    //     $purchaseorder= PurchaseOrder::where('data_state',0)
    //     ->where('purchase_order_id', $purchase_order_id)
    //     ->first();

    //     $purchaseorderitem= PurchaseOrderItem::where('data_state',0)
    //     ->where('purchase_order_id', $purchase_order_id)
    //     ->get();

    //     return view('content/PurchaseOrder/FormDetailPurchaseOrder',compact('purchaseorderitem', 'purchaseorder'));
    // }

    public function getItemCategoryName($item_category_id){
        $itemcategory = InvtItemCategory::where('data_state', 0)
        ->where('item_category_id', $item_category_id)
        ->first();

        return $itemcategory['item_category_name'];
    }

    // public function getItemTypeName($item_type_id){
    //     $itemtype = InvItemType::where('data_state', 0)
    //     ->where('item_type_id', $item_type_id)
    //     ->first();

    //     return $itemtype['item_type_name'];
    // }

    public function getItemUnitName($item_unit_id){
        $itemunit = InvtItemUnit::where('data_state', 0)
        ->where('item_unit_id', $item_unit_id)
        ->first();

        return $itemunit['item_unit_name'];
    }

    // public function getItemStockBatchNumber($item_stock_id){
    //     $itemstock = InvItemStock::where('data_state', 0)
    //     ->where('item_stock_id', $item_stock_id)
    //     ->first();

    //     return $itemstock['item_batch_number'];
    // }

    public function getItemName($item_id){
        $invitem = InvtItem::select('invt_item.item_id', DB::raw('CONCAT(invt_item_category.item_category_name, " ", invt_item.item_name) AS item_name'))
        ->join('invt_item_category', 'invt_item_category.item_category_id', 'invt_item.item_category_id')
        ->where('invt_item.item_id', $item_id)
        ->where('invt_item.data_state','=',0)
        ->first();

        return $invitem['item_name'];
    }

    // public function getItemNameItemId0($item_type_id, $item_category_id){
    //     $invitemcategory    = InvtItemCategory::findOrFail($item_category_id);
    //     $invitemtype        = InvtItemType::findOrFail($item_type_id);

    //     return $invitemcategory['item_category_name'].' '.$invitemtype['item_type_name'].' No Grade';
    // }

    public function getCoreSupplierName($supplier_id){
        $supplier = CoreSupplier::where('data_state', 0)
        ->where('supplier_id', $supplier_id)
        ->first();

        return $supplier['supplier_name'];
    }

    public function getCoreExpeditionName($expedition_id){
        $expedition = CoreExpedition::where('data_state', 0)
        ->where('expedition_id', $expedition_id)
        ->first();

        return $expedition['expedition_name'] ?? '';
    }

    public function getInvWarehouseName($warehouse_id){
        $warehouse = InvtWarehouse::where('data_state', 0)
        ->where('warehouse_id', $warehouse_id)
        ->first();

        return $warehouse['warehouse_name'] ?? '';
    }

    public function getInvItemCategoryName($item_category_id){
        $itemcategory = InvtItemCategory::where('data_state', 0)
        ->where('item_category_id', $item_category_id)
        ->first();

        return $itemcategory['item_category_name'];
    }

    // public function getInvItemTypeName($item_type_id){
    //     $itemtype = InvItemType::where('data_state', 0)
    //     ->where('item_type_id', $item_type_id)
    //     ->first();

    //     return $itemtype['item_type_name'];
    // }

    public function getInvItemUnitName($item_unit_id){
        $itemunit = InvtItemUnit::where('data_state', 0)
        ->where('item_unit_id', $item_unit_id)
        ->first();

        return $itemunit['item_unit_name'];
    }

    public function getInvWarehouseTransferTypeName($warehouse_transfer_type_id){
        $value = InvWarehouseTransferType::where('data_state', 0)
        ->where('warehouse_transfer_type_id', $warehouse_transfer_type_id)
        ->first();

        return $value['warehouse_transfer_type_name']?? '';
    }

    // public function getPurchaseOrderNo($purchase_order_id){
    //     $purchaseorder = PurchaseOrder::where('data_state', 0)
    //     ->where('purchase_order_id', $purchase_order_id)
    //     ->first();

    //     return $purchaseorder['purchase_order_no'];
    // }

    // public function getPurchaseOrderDate($purchase_order_id){
    //     $purchaseorder = PurchaseOrder::where('data_state', 0)
    //     ->where('purchase_order_id', $purchase_order_id)
    //     ->first();

    //     return $purchaseorder['purchase_order_date'];
    // }

    public function processAddInvWarehouseTransferReceivedNote(Request $request){


        // dd($request->all());


        $fields = $request->validate([
            'warehouse_transfer_id'                     => 'required',
            'warehouse_transfer_received_note_date'     => 'required',
            'warehouse_transfer_to'                     => 'required',
            'warehouse_transfer_from'                   => 'required',
        ]);


        $invwarehousetransferreceivednote = array (
            'warehouse_transfer_id'                     => $fields['warehouse_transfer_id'],
            'warehouse_transfer_received_note_date'     => $fields['warehouse_transfer_received_note_date'],
            'warehouse_transfer_received_note_remark'   => $request->warehouse_transfer_received_note_remark,
            'warehouse_transfer_to'                     => $fields['warehouse_transfer_to'],
            'warehouse_transfer_from'                   => $fields['warehouse_transfer_from'],
            'created_id' 				                => Auth::id(),
        );

        if(InvWarehouseTransferReceivedNote::create($invwarehousetransferreceivednote)){
            $warehousetransferreceivednote = InvWarehouseTransferReceivedNote::select('warehouse_transfer_received_note_id')
            ->orderBy('created_at','DESC')
            ->first();

            $warehousetransfer = InvWarehouseTransfer::where('warehouse_transfer_id', $fields['warehouse_transfer_id'])
            ->first();
            $warehousetransfer->warehouse_transfer_status = 1;
            $warehousetransfer->save();

            $total_no = $request->total_no;
            $total_received_item = 0;
			for($i = 1; $i <= $total_no; $i++){
                $temprequest = $request->all();

                $invitemstock = InvtItemStock::where('item_stock_id', $temprequest['item_stock_id_'.$i])
                ->first();

                $invitemstock = array(
                    'goods_received_note_id'        => $invitemstock['goods_received_note_id'],
                    'goods_received_note_item_id'   => $invitemstock['goods_received_note_item_id'],
                    'item_stock_date'               => $invitemstock['item_stock_date'],
                    'warehouse_id'                  => $fields['warehouse_transfer_to'],
                    'last_balance'                    => $temprequest['quantity_received_'.$i],
                    'item_category_id'              => $temprequest['item_category_id_'.$i],
                    'item_id'                       => $temprequest['item_id_'.$i],
                    'item_unit_id'                  => $temprequest['item_unit_id_'.$i],
                    'created_id'                    => Auth::id(),
                );
                InvtItemStock::create($invitemstock);

                $itemstock = InvtItemStock::select('item_stock_id')
                ->orderBy('created_at', 'DESC')
                ->first();

                $warehousetransferreceivednoteitem = array (
                    'warehouse_transfer_item_id'            => $temprequest['warehouse_transfer_item_id_'.$i],
                    'warehouse_transfer_received_note_id'   => $warehousetransferreceivednote['warehouse_transfer_received_note_id'],
                    'quantity'						        => $temprequest['quantity_received_'.$i],
                    'item_id'				                => $temprequest['item_id_'.$i],
                    'item_category_id'						=> $temprequest['item_category_id_'.$i],
                    'item_unit_id'							=> $temprequest['item_unit_id_'.$i],
                    'item_stock_id'							=> $itemstock['item_stock_id'],
                    'created_id'                            => Auth::id(),
                );
                InvWarehouseTransferReceivedNoteItem::create($warehousetransferreceivednoteitem);
			}

            $msg = 'Tambah Penerimaan Transfer Gudang Berhasil';
            return redirect('/warehouse-transfer-received-note')->with('msg',$msg);
        }else{
            $msg = 'Tambah Penerimaan Transfer Gudang Gagal';
            return redirect('/warehouse-transfer-received-note')->with('msg',$msg);
        }
    }
    

}

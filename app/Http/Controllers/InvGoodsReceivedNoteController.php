<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\AcctAccount;
use App\Models\AcctJournalVoucher;
use App\Models\AcctJournalVoucherItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderItemTemporary;
use App\Models\InvWarehouse;
use App\Models\CoreSupplier;
use App\Models\InvItemCategory;
use App\Models\InvItemUnit;
use App\Models\InvItemType;
use App\Models\InvItemStock;
use App\Models\InvGoodsReceivedNote;
use App\Models\InvGoodsReceivedNoteItem;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\SalesMerchant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvGoodsReceivedNoteController extends Controller
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

        $goodsreceivednote = InvGoodsReceivedNote::where('data_state','=',0)
        ->where('created_at', '>=', $start_date)
        ->where('created_at', '<=', $end_date)
        ->get();

        $preference_company = PreferenceCompany::select('account_inventory_trade_id')->first();

        return view('content/InvGoodsReceivedNote/ListInvGoodsReceivedNote',compact('preference_company', 'goodsreceivednote', 'start_date', 'end_date'));
    }

    public function filterInvGoodsReceivedNote(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/goods-received-note');
    }

    public function resetFilterInvGoodsReceivedNote(){
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/goods-received-note');
    }

    public function searchPurchaseOrder()
    {
        Session::forget('purchaseorderitem');

        $purchaseorder = PurchaseInvoice::select('purchase_invoice.*','purchase_invoice_item.*')
        ->where('purchase_invoice_item.data_state','=',0)
        ->join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','purchase_invoice_item.purchase_invoice_id')
        ->where('purchase_invoice_item.quantity', '>', 0)
        ->get();
        // dd($purchaseorder);

        return view('content/InvGoodsReceivedNote/SearchPurchaseOrder', compact('purchaseorder'));
    }



    public function addInvGoodsReceivedNote($purchase_invoice_id)
    {
        $purchaseInvoice = PurchaseInvoice::where('purchase_invoice.data_state', 0)
        ->join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','purchase_invoice_item.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_id', $purchase_invoice_id)
        ->where('purchase_invoice_item.quantity', '>', 0)
        ->first();
        
        $purchaseInvoiceitem = PurchaseInvoice::select('*')
        ->join('purchase_invoice_item','purchase_invoice.purchase_invoice_id','purchase_invoice_item.purchase_invoice_id')
        ->where('purchase_invoice.data_state', 0)
        ->where('purchase_invoice.purchase_invoice_id', $purchase_invoice_id)
        ->where('purchase_invoice_item.quantity', '>', 0)
        ->get()->toArray();

        $purchaseInvoiceitem_temporary = Session::get('purchaseorderitem');

        if($purchaseInvoiceitem_temporary == null){
            $merge_data = $purchaseInvoiceitem;
        }else{
            $merge_data = array_merge($purchaseInvoiceitem, $purchaseInvoiceitem_temporary);
            $key_type = array_column($merge_data, 'item_type_id'); 
            $key_qty= array_column($merge_data, 'quantity'); 
            array_multisort($key_type, SORT_ASC, $merge_data, SORT_DESC, $merge_data);
        }
        // dd($merge_data);
        
        $add_type_purchaseInvoiceitem = PurchaseInvoiceItem::select('*')
        ->join('invt_item', 'invt_item.item_id', '=', 'purchase_invoice_item.item_id')
        ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'purchase_invoice_item.item_unit_id')
        ->where('purchase_invoice_item.data_state', 0)
        ->where('purchase_invoice_item.purchase_invoice_id', $purchase_invoice_id)
        
        ->pluck('invt_item.item_name', 'purchase_invoice_item.item_id');

        $add_unit_purchaseInvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_item.data_state', 0)
        ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'purchase_invoice_item.item_unit_id')
        ->where('purchase_invoice_item.purchase_invoice_id', $purchase_invoice_id)
        ->pluck('item_unit_name', 'purchase_invoice_item.item_unit_id');

        $null_add_purchaseInvoiceitem = Session::get('purchase_invoice_item_id');
        $null_add_unit_purchaseInvoiceitem = Session::get('item_unit_id');

        return view('content/InvGoodsReceivedNote/FormAddInvGoodsReceivedNote',compact('merge_data', 'purchaseInvoiceitem_temporary', 'purchaseInvoice', 'purchaseInvoiceitem', 'add_type_purchaseInvoiceitem', 'null_add_purchaseInvoiceitem', 'add_unit_purchaseInvoiceitem', 'null_add_unit_purchaseInvoiceitem'));
    }

    public function processAddInvGoodsReceivedNote(Request $request){


        // dd($request->all());


        $purchaseorderitem_temporary = Session::get('purchaseorderitem');

        $fields = $request->validate([
            'purchase_order_id'         => 'required',
            'goods_received_note_date'  => 'required',
            'supplier_id'               => 'required',
            'warehouse_id'              => 'required',
        ]);
        
        $fileNameToStore = '';

        if($request->hasFile('receipt_image')){

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('receipt_image')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('receipt_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('receipt_image')->storeAs('public/receipt',$fileNameToStore);

        }

        $invgoodsreceivednote = array (
            'goods_received_note_date'              => $fields['goods_received_note_date'],
            'purchase_order_id'                     => $fields['purchase_order_id'],
            'supplier_id'                           => $fields['supplier_id'],
            'warehouse_id'                          => $fields['warehouse_id'],
            'goods_received_note_remark'            => $request->goods_received_note_remark,
            'faktur_no'                             => $request->faktur_no,
            'subtotal_item'                         => $request->quantity_received_total,
            'receipt_image'                         => $fileNameToStore,
            'created_id' 				            => Auth::id(),
        );
        //dd($invgoodsreceivednote);
        if(InvGoodsReceivedNote::create($invgoodsreceivednote)){
            $goodsreceivednote = InvGoodsReceivedNote::select('goods_received_note_id', 'goods_received_note_no')
            ->where('created_id', Auth::id())
            ->orderBy('created_at','DESC')
            ->first();
            
            $temprequest = $request->all();
            // dd($temprequest);

//----------------------------------------------------------Journal Voucher-------------------------------------------------------------------//
            
//             $preferencecompany 			= PreferenceCompany::first();
        
//             $transaction_module_code 	= "GRN";
    
//             $transactionmodule 		    = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
//             ->first();
    
//             $transaction_module_id 		= $transactionmodule['transaction_module_id'];

//             $journal_voucher_period 	= date("Ym", strtotime($invgoodsreceivednote['goods_received_note_date']));

//             $data_journal = array(
//                 'branch_id'						=> 1,
//                 'journal_voucher_period' 		=> $journal_voucher_period,
//                 'journal_voucher_date'			=> $invgoodsreceivednote['goods_received_note_date'],
//                 'journal_voucher_title'			=> 'Penerimaan Barang '.$goodsreceivednote['goods_received_note_no'],
//                 'journal_voucher_no'			=> $goodsreceivednote['goods_received_note_no'],
//                 'journal_voucher_description'	=> $invgoodsreceivednote['goods_received_note_remark'],
//                 'transaction_module_id'			=> $transaction_module_id,
//                 'transaction_module_code'		=> $transaction_module_code,
//                 'transaction_journal_id' 		=> $goodsreceivednote['goods_received_note_id'],
//                 'transaction_journal_no' 		=> $goodsreceivednote['goods_received_note_no'],
//                 'created_id' 					=> Auth::id(),
//             );
            
//             AcctJournalVoucher::create($data_journal);
// //---------------------------------------------------------End Journal Voucher----------------------------------------------------------------//

//             $total_no = $request->total_no;
//             $total_received_item = $temprequest['quantity_received_total'];
//             // dd($total_no);
            
// 			for($i = 1; $i <= $total_no; $i++){
//                 $invgoodsreceivednoteitem = array (
//                     'goods_received_note_id'                => $goodsreceivednote['goods_received_note_id'],
//                     'purchase_order_id'						=> $temprequest['purchase_order_id_'.$i],
//                     'purchase_order_item_id'				=> $temprequest['purchase_order_item_id_'.$i],
//                     'item_category_id'						=> $temprequest['item_category_id_'.$i],
//                     'item_type_id'						    => $temprequest['item_type_id_'.$i],
//                     'item_unit_id'							=> $temprequest['item_unit_id_'.$i],
//                     'quantity'					            => $temprequest['quantity_received_'.$i],
//                     'quantity_ordered'					    => $temprequest['quantity_received_'.$i],
//                     'quantity_received'					    => $temprequest['quantity_received_'.$i],
//                     'item_batch_number'                     => $temprequest['item_batch_number_'.$i],
//                     'item_expired_date'                     => $temprequest['item_expired_date_'.$i],
//                     'created_id'                            => Auth::id(),
//                 );

//                 // dd($invgoodsreceivednoteitem);
//                 InvGoodsReceivedNoteItem::create($invgoodsreceivednoteitem);

//                 //update purchase order item
//                 $purchaseorderitem = PurchaseOrderItem::findOrFail($invgoodsreceivednoteitem['purchase_order_item_id']);
//                 $purchaseorderitem->quantity_outstanding = $purchaseorderitem['quantity_outstanding'] - $invgoodsreceivednoteitem['quantity'];
//                 $purchaseorderitem->quantity_received    = $purchaseorderitem['quantity_received'] + $invgoodsreceivednoteitem['quantity'];
//                 $purchaseorderitem->save();

//                 // $total_received_item = $total_received_item + $purchaseorderitem['quantity_received'] + $invgoodsreceivednoteitem['quantity'];

//                 $goodsreceivednoteitem = InvGoodsReceivedNoteItem::select('inv_goods_received_note_item.goods_received_note_item_id')
//                 ->where('inv_goods_received_note_item.quantity', $invgoodsreceivednoteitem['quantity'])
//                 ->where('inv_goods_received_note_item.item_type_id', $invgoodsreceivednoteitem['item_type_id'])
//                 ->where('inv_goods_received_note_item.created_id', Auth::id())
//                 ->orderBy('inv_goods_received_note_item.created_at', 'DESC')
//                 ->first();

//                 $item_type = InvItemType::where('data_state', 0)
//                 ->where('item_type_id', $invgoodsreceivednoteitem['item_type_id'])
//                 ->first();

//                 // dd($item_type);

//                 $item_unit_id_default = $item_type['item_unit_1'];

//                 if($invgoodsreceivednoteitem['item_unit_id'] == $item_type['item_unit_1']){
//                     $quantity_unit = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_quantity_default_1'];
//                     $default_quantity = $item_type['item_quantity_default_1'];
//                     $item_weight = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_weight_1'];
//                     $item_weight_default = $item_type['item_weight_1'];
//                     // dd($quantity_unit, $default_quantity, $item_weight, $item_weight_default);
//                 }
//                 if($invgoodsreceivednoteitem['item_unit_id'] == $item_type['item_unit_2']){
//                     $quantity_unit = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_quantity_default_2'];
//                     $default_quantity = $item_type['item_quantity_default_2'];
//                     $item_weight = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_weight_2'];
//                     $item_weight_default = $item_type['item_weight_2'];

//                     // dd($quantity_unit, $default_quantity, $item_weight, $item_weight_default);
                    
//                 }
//                 if($invgoodsreceivednoteitem['item_unit_id'] == $item_type['item_unit_3']){
//                     $quantity_unit = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_quantity_default_3'];
//                     $default_quantity = $item_type['item_quantity_default_3'];
//                     $item_weight = $invgoodsreceivednoteitem['quantity_received'] * $item_type['item_weight_3'];
//                     $item_weight_default = $item_type['item_weight_3'];

//                 // dd($quantity_unit, $default_quantity, $item_weight, $item_weight_default);
//                 }

//                 $invitemstock = array(
//                     'goods_received_note_id'        => $goodsreceivednote['goods_received_note_id'],
//                     'goods_received_note_item_id'   => $goodsreceivednoteitem['goods_received_note_item_id'],
//                     'item_stock_date'               => $invgoodsreceivednote['goods_received_note_date'],
//                     'item_batch_number'             => $invgoodsreceivednoteitem['item_batch_number'],
//                     'item_stock_expired_date'       => $invgoodsreceivednoteitem['item_expired_date'],
//                     'warehouse_id'                  => $fields['warehouse_id'],
//                     'item_total'                    => $temprequest['quantity_received_'.$i],
//                     'item_unit_id_default' 		    => $item_unit_id_default,
//                     'quantity_unit' 		        => $quantity_unit,
//                     'item_default_quantity_unit'    => $default_quantity,
//                     // 'item_weight_unit' 		        => $item_weight,
//                     // 'item_weight_default' 		    => $item_weight_default,
//                     'purchase_order_item_id'        => $temprequest['purchase_order_item_id_'.$i],
//                     'item_category_id'              => $temprequest['item_category_id_'.$i],
//                     'item_type_id'                  => $temprequest['item_type_id_'.$i],
//                     'item_unit_id'                  => $temprequest['item_unit_id_'.$i],
//                     'created_id'                    => Auth::id(),
//                 );

//                 // dd($invitemstock);

//                 $data_item_stock = InvItemStock::where('item_type_id', $invitemstock['item_type_id'])
//                 ->where('item_batch_number', $invitemstock['item_batch_number'])->first();
//                 // dd($item);
                
//                 if($data_item_stock == null){
//                     InvItemStock::create($invitemstock);
//                 }else{
//                     $itemstockupdate = InvItemStock::findOrFail($data_item_stock['item_stock_id']);
//                     $itemstockupdate->item_total += $invitemstock['item_total'];
//                     $itemstockupdate->quantity_unit += $invitemstock['quantity_unit'];
//                     // $itemstockupdate->item_weight_unit += $invitemstock['item_weight_unit'];
//                     // $itemstockupdate->item_stock_date = $invitemstock['item_stock_date'];
//                     $itemstockupdate->save();
//                 }

//                 // dd($invitemstock);

// //----------------------------------------------------------Journal Voucher Item-------------------------------------------------------------------//


//                 $purchaseorderitem          = PurchaseOrderItem::where('purchase_order_item_id', $temprequest['purchase_order_item_id_'.$i])
//                 ->first();

//                 $purchaseorder              = PurchaseOrder::findOrFail($invgoodsreceivednote['purchase_order_id']);
//                 $total_amount               = $temprequest['quantity_received_'.$i] * $purchaseorderitem['item_unit_cost'] -  $purchaseorderitem['discount_amount'] ;

//                 $journalvoucher = AcctJournalVoucher::where('created_id', Auth::id())
//                 ->orderBy('journal_voucher_id', 'DESC')
//                 ->first();

                
//                 $journal_voucher_id 	= $journalvoucher['journal_voucher_id'];


//                 //------account_id Persediaan Barang Dagang------//
//                 $preference_company = PreferenceCompany::first();
                
//                 $account = AcctAccount::where('account_id', $preference_company['account_inventory_trade_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $account_id_default_status 		= $account['account_default_status'];

                
//                 $data_debit1 = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $account['account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($total_amount),
//                     'journal_voucher_debit_amount'	=> ABS($total_amount),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 1,
//                 );
                
//                 // dd($data_debit1);
                
//                 AcctJournalVoucherItem::create($data_debit1);
//             }
                
//                 //------account_id PPN Masukan------//
//                 $account = AcctAccount::where('account_id', $preference_company['account_vat_in_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $ppn_in_amount = $purchaseorder['ppn_in_amount'];
                
//                 $account_id_default_status 		= $account['account_default_status'];


                
//                 $data_debit2 = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $account['account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($ppn_in_amount),
//                     'journal_voucher_debit_amount'	=> ABS($ppn_in_amount),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 1,
//                 );
                
//                 // dd($data_debit2);

//                 AcctJournalVoucherItem::create($data_debit2);

                
//                 $account 		= AcctAccount::where('account_id', $preferencecompany['account_payable_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $subtotal_after_ppn_in = $purchaseorder['subtotal_after_ppn_in'];

//             // dd($account);

//                 $account_id_default_status 		= $account['account_default_status'];

//                 $data_credit = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $preferencecompany['account_payable_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($subtotal_after_ppn_in),
//                     'journal_voucher_credit_amount'	=> ABS($subtotal_after_ppn_in),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 0,
//                 );
//                 // dd($data_credit);


//                 AcctJournalVoucherItem::create($data_credit);


// //--------------------------------------------------------End Journal Voucher-----------------------------------------------------------------//

			

//             // $purchaseorder = PurchaseOrder::findOrFail($invgoodsreceivednote['purchase_order_id']);
//             // $purchaseorder->total_received_item = $purchaseorder['total_received_item'] + $total_received_item;
//             // if($purchaseorder['total_item'] == $purchaseorder->total_received_item ){
//             //     $purchaseorder->purchase_order_status = 2;
//             // }else{
//             //     $purchaseorder->purchase_order_status = 1;
//             // }
//             // $purchaseorder->save();

            $msg = 'Tambah Penerimaan Barang Berhasil';
            return redirect('/goods-received-note')->with('msg',$msg);
        }else{
            $msg = 'Tambah Penerimaan Barang Gagal';
            return redirect('/goods-received-note')->with('msg',$msg);
        }

    }














    //get func
    public function getItemName($item_id){
        $item = InvtItem::where('data_state', 0)
        ->where('item_id', $item_id)
        ->first();

        if($item == null){
            return "-";
        }

        return $item['item_name'];
    }  

    public function getMerchantName($merchant_id)
    {
        $data = SalesMerchant::where('merchant_id', $merchant_id)->first();

        return $data['merchant_name'];
    }


    public function getItemCategoryName($item_category_id){
        $itemcategory = InvtItemCategory::where('data_state', 0)
        ->where('item_category_id', $item_category_id)
        ->first();

        if($itemcategory == null){
            return "-";
        }

        return $itemcategory['item_category_name'];
    }   



    public function getCoreSupplierName($supplier_id){
        $supplier = CoreSupplier::where('data_state', 0)
        ->where('supplier_id', $supplier_id)
        ->first();

        if($supplier == null){
            return "-";
        }

        return $supplier['supplier_name'];
    }


    public function getInvWarehouseName($warehouse_id){
        $warehouse = InvtWarehouse::where('data_state', 0)
        ->where('warehouse_id', $warehouse_id)
        ->first();

        if($warehouse == null){
            return "-";
        }

        return $warehouse['warehouse_name'];
    }

    public function getInvItemCategoryName($item_category_id){
        $itemcategory = InvtItemCategory::where('data_state', 0)
        ->where('item_category_id', $item_category_id)
        ->first();

        if($itemcategory == null){
            return "-";
        }
        return $itemcategory['item_category_name'];
    }

    // public function getInvItemTypeName($item_type_id){
    //     $itemtype = InvtItem::where('data_state', 0)
    //     ->where('item_type_id', $item_type_id)
    //     ->first();
    //     if($itemtype == null){
    //         return "-";
    //     }
    //     return $itemtype['item_type_name'];
    // }

    public function getInvItemUnitName($item_unit_id){
        $itemunit = InvtItemUnit::where('data_state', 0)
        ->where('item_unit_id', $item_unit_id)
        ->first();

        if($itemunit == null){
            return "-";
        }

        return $itemunit['item_unit_name'];
    }
}

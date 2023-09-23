<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\InvtWarehouse;
use App\Models\CoreSupplier;
use App\Models\AcctAccount;
use App\Models\AcctJournalVoucher;
use App\Models\AcctJournalVoucherItem;
use App\Models\InvtItem;
use App\Models\InvtItemUnit;
use App\Models\CoreGrade;
use App\Models\CorePackage;
use App\Models\InvtItemCategory;
use App\Models\InvtItemType;
use App\Models\InvtItemStock;
use App\Models\InvtItemStockPackage;
use App\Models\InvGoodsReceivedNote;
use App\Models\InvGoodsReceivedNoteItem;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use stdClass;

class GradingController extends Controller
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
        Session::forget('gradingelements');
        Session::forget('dataarrayinvitemstock');
        Session::forget('dataarrayinvitemstockpackage');
    
        $invitemstock = InvtItemStock::where('data_state','=',0)
        ->where('item_id','!=',0)
        ->get();

        return view('content/Grading/ListGrading',compact('invitemstock'));
    }

    public function search()
    {
        Session::forget('gradingelements');
        Session::forget('dataarrayinvitemstock');
        Session::forget('dataarrayinvitemstockpackage');

        $invitemstock = InvtItemStock::where('data_state','=',0)
        ->where('item_id','=',0)
        ->where('item_category_id', '!=', 3)
        ->get();

        return view('content/Grading/SearchNoGradeItemStock',compact('invitemstock'));
    }

    // public function addGrading($item_stock_id)
    // {
    //     $invitemstock = InvItemStock::where('data_state','=',0)
    //     ->where('item_stock_id', $item_stock_id)
    //     ->first();
        
    //     $dataarray	        = Session::get('dataarrayinvitemstock');
    //     $dataarraypackage	= Session::get('dataarrayinvitemstockpackage');
    //     $gradingelements    = Session::get('gradingelements');

    //     if($gradingelements == null){
    //         $data = array(
    //             'item_id' => '',
    //             'item_quantity' => '',
    //             'item_stock_unit_id' => '',
    //         );
    //         Session::put('gradingelements', $data);
    //     }

    //     // dd($gradingelements);


    //     $invitem = InvItemStock::select('inv_item.item_id', DB::raw('CONCAT(inv_item_category.item_category_name, " ", inv_item_type.item_type_name, " ", core_grade.grade_name) AS item_name'))
    //     ->join('inv_item', 'inv_item.item_type_id', 'inv_item_stock.item_type_id')
    //     ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
    //     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //     ->join('core_grade', 'core_grade.grade_id', 'inv_item.grade_id')
    //     ->where('item_stock_id', $item_stock_id)
    //     ->where('inv_item_stock.data_state','=',0)
    //     ->pluck('item_name','inv_item.item_id');

    //     $package = InvItemStock::select(DB::raw("inv_item_stock.item_stock_id, CONCAT(inv_item_stock.item_batch_number, ' - ',inv_item_category.item_category_name, ' ', inv_item_type.item_type_name, ' : ', inv_item_stock.item_stock_date) AS item_batch_number"))
    //     ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
    //     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //     ->where('inv_item_stock.data_state', 0)
    //     ->where('inv_item_stock.item_category_id', 3)
    //     ->pluck('item_batch_number', 'item_stock_id');

    //     $invitemcategory = InvItemCategory::where('data_state', 0)
    //     ->pluck('item_category_name', 'item_category_id');

    //     $invitemunit = InvItemUnit::where('data_state', 0)
    //     ->pluck('item_unit_name', 'item_unit_id');

    //     $invgrade = CoreGrade::where('data_state', 0)
    //     ->pluck('grade_name', 'grade_id');

    //     $acctaccountcode    = AcctAccount::select('account_id', DB::raw('CONCAT(account_code, " - ", account_name) AS full_name'))
    //     ->where('acct_account.data_state','=','0')
    //     ->where('parent_account_status', 0)
    //     ->get()
    //     ->pluck('full_name','account_id');

    //     return view('content/Grading/FormAddGrading',compact('invitemstock', 'invitem', 'dataarray', 'dataarraypackage', 'item_stock_id', 'package', 'invitemcategory', 'invgrade', 'invitemunit', 'acctaccountcode', 'gradingelements'));
    // }

//     public function editGrading($item_stock_id)
//     {
//         $invitemstock = InvItemStock::where('data_state','=',0)
//         ->where('item_stock_id', $item_stock_id)
//         ->first();
        
//         $dataarray	        = Session::get('dataarrayinvitemstock');
//         $dataarraypackage	= Session::get('dataarrayinvitemstockpackage');
//         $gradingelements    = Session::get('gradingelements');
        
//         if($gradingelements == null){
//             $data = array(
//                 'item_id' => '',
//                 'item_quantity' => '',
//                 'item_stock_unit_id' => '',
//             );
//             Session::put('gradingelements', $data);
//         }

//         $invitem = InvItemStock::select('inv_item.item_id', DB::raw('CONCAT(inv_item_category.item_category_name, " ", inv_item_type.item_type_name, " ", core_grade.grade_name) AS item_name'))
//         ->join('inv_item', 'inv_item.item_type_id', 'inv_item_stock.item_type_id')
//         ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
//         ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
//         ->join('core_grade', 'core_grade.grade_id', 'inv_item.grade_id')
//         ->where('item_stock_id', $item_stock_id)
//         ->where('inv_item_stock.data_state','=',0)
//         ->pluck('item_name','inv_item.item_id');

//         $package = InvItemStock::select(DB::raw("inv_item_stock.item_stock_id, CONCAT(inv_item_stock.item_batch_number, ' - ',inv_item_category.item_category_name, ' ', inv_item_type.item_type_name, ' : ', inv_item_stock.item_stock_date) AS item_batch_number"))
//         ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
//         ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
//         ->where('inv_item_stock.data_state', 0)
//         ->where('inv_item_stock.item_category_id', 3)
//         ->pluck('item_batch_number', 'item_stock_id');

//         $invitemcategory = InvItemCategory::where('data_state', 0)
//         ->pluck('item_category_name', 'item_category_id');

//         $invitemunit = InvItemUnit::where('data_state', 0)
//         ->pluck('item_unit_name', 'item_unit_id');

//         $invgrade = CoreGrade::where('data_state', 0)
//         ->pluck('grade_name', 'grade_id');

//         $acctaccountcode    = AcctAccount::select('account_id', DB::raw('CONCAT(account_code, " - ", account_name) AS full_name'))
//         ->where('acct_account.data_state','=','0')
//         ->where('parent_account_status', 0)
//         ->get()
//         ->pluck('full_name','account_id');

//         return view('content/Grading/FormEditGrading',compact('invitemstock', 'invitem', 'dataarray', 'dataarraypackage', 'gradingelements', 'item_stock_id', 'package', 'invitemcategory', 'invitemunit', 'invgrade', 'acctaccountcode'));
//     }


//     public function addArrayInvItemStock(Request $request)
//     {
//         $dataarrayinvitemstockpackage = Session::get('dataarrayinvitemstockpackage');

//         $dataarrayinvitemstock = array(
//             'item_id'				=> $request->item_id,
//             'item_stock_unit_id'	=> $request->item_stock_unit_id,
//             'item_quantity'			=> $request->item_quantity,
//             'package'               => $dataarrayinvitemstockpackage,
//         );

//         $lastdataarrayinvitemstock = Session::get('dataarrayinvitemstock');
//         if($lastdataarrayinvitemstock !== null){
//             array_push($lastdataarrayinvitemstock, $dataarrayinvitemstock);
//             Session::put('dataarrayinvitemstock', $lastdataarrayinvitemstock);
//         }else{
//             $lastdataarrayinvitemstock = [];
//             array_push($lastdataarrayinvitemstock, $dataarrayinvitemstock);
//             Session::push('dataarrayinvitemstock', $dataarrayinvitemstock);
//         }

//         Session::forget('gradingelements');
//         Session::forget('dataarrayinvitemstockpackage');
        
//     }


//     public function addArrayInvItemStockPackage(Request $request)
//     {
//         $dataarrayinvitemstockpackage = array(
//             'item_package_unit_id'	=> $request->item_package_unit_id,
//             'item_package_quantity'	=> $request->item_package_quantity,
//             'package_id'			=> $request->package_id,
//         );

//         $lastdataarrayinvitemstockpackage = Session::get('dataarrayinvitemstockpackage');
//         if($lastdataarrayinvitemstockpackage !== null){
//             array_push($lastdataarrayinvitemstockpackage, $dataarrayinvitemstockpackage);
//             Session::put('dataarrayinvitemstockpackage', $lastdataarrayinvitemstockpackage);
//         }else{
//             $lastdataarrayinvitemstockpackage = [];
//             array_push($lastdataarrayinvitemstockpackage, $dataarrayinvitemstockpackage);
//             Session::push('dataarrayinvitemstockpackage', $dataarrayinvitemstockpackage);
//         }
        
//     }


//     public function deleteArrayInvItemStock($record_id, $item_stock_id)
//     {
//         $arrayBaru			= array();
//         $dataArrayHeader	= Session::get('dataarrayinvitemstock');
        
//         foreach($dataArrayHeader as $key=>$val){
//             if($key != $record_id){
//                 $arrayBaru[$key] = $val;
//             }
//         }

//         Session::forget('dataarrayinvitemstock');
//         Session::put('dataarrayinvitemstock', $arrayBaru);

//         return redirect('/grading/add/'.$item_stock_id);
//     }


//     public function deleteArrayInvItemStockPackage($record_id, $item_stock_id)
//     {
//         $arrayBaru			= array();
//         $dataArrayHeader	= Session::get('dataarrayinvitemstockpackage');
        
//         foreach($dataArrayHeader as $key=>$val){
//             if($key != $record_id){
//                 $arrayBaru[$key] = $val;
//             }
//         }

//         Session::forget('dataarrayinvitemstockpackage');
//         Session::put('dataarrayinvitemstockpackage', $arrayBaru);

//         return redirect('/grading/add/'.$item_stock_id);
//     }


//     public function resetArrayInvItemStock($item_stock_id)
//     {
//         Session::forget('dataarrayinvitemstock');

//         return redirect('/grading/add/'.$item_stock_id);
//     }

//     public function processAddGrading(Request $request)
//     {
//         $fields = $request->validate([
//             'item_stock_id'         => 'required',
//         ]);

//         $dataarrayinvitemstock = Session::get('dataarrayinvitemstock');
//         foreach($dataarrayinvitemstock as $item){
//             $invitemstockmaterial = InvItemStock::findOrFail($fields['item_stock_id']);

//             $invitemstockmaterial_account = InvItemStock::select('inv_item_type.*')
//             ->where('item_stock_id', $fields['item_stock_id'])
//             ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
//             ->first();

//             $itemunitfirst = InvItemUnit::where('data_state', 0)
//             ->where('item_unit_id', $invitemstockmaterial['item_unit_id'])
//             ->first();

//             $itemunitsecond = InvItemUnit::where('data_state', 0)
//             ->where('item_unit_id', $item['item_stock_unit_id'])
//             ->first();

//             $total_add = ($item['item_quantity'] / $itemunitfirst['item_unit_default_quantity'] * $itemunitsecond['item_unit_default_quantity']);

//             $purchaseorderitemmaterial = InvItemStock::select('purchase_order_item.item_unit_cost')
//             ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//             ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//             ->where('item_stock_id', $fields['item_stock_id'])
//             ->first();

//             $hpp_add = $purchaseorderitemmaterial['item_unit_cost'] * $total_add;

//             $item_total = $invitemstockmaterial['item_total'] - $total_add;

//             $invitemstockmaterial->item_total = $item_total;
//             if($item_total<=0){
//                 $invitemstockmaterial->data_state = 1;
//             }

//             if($invitemstockmaterial->save()){

//                 $hpp_package = 0;

//                 foreach($item['package'] as $package){
//                     $invitemstock = InvItemStock::findOrFail($package['package_id']);

//                     $itemunitfirst = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $invitemstock['item_unit_id'])
//                     ->first();

//                     $itemunitsecond = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $package['item_package_unit_id'])
//                     ->first();

//                     $total_package = ($package['item_package_quantity'] / $itemunitfirst['item_unit_default_quantity'] * $itemunitsecond['item_unit_default_quantity']);

//                     $item_total = $invitemstock['item_total'] - $total_package;

//                     $invitemstock->item_total = $item_total;
//                     if($item_total<=0){
//                         $invitemstock->data_state = 1;
//                     }
//                     if($invitemstock->save()){
//                         $purchaseorderitempackage = InvItemStock::select('purchase_order_item.item_unit_cost')
//                         ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                         ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                         ->where('item_stock_id', $package['package_id'])
//                         ->first();

//                         if($itemunitsecond['item_unit_default_quantity'] == 1){
//                             $temp_hpp_package = $purchaseorderitempackage['item_unit_cost'];
//                         }else{
//                             $temp_hpp_package = $purchaseorderitempackage['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'];
//                         }

//                         $hpp_package += $temp_hpp_package;
//                     }else{
//                         $msg = 'Tambah Grade Barang Gagal';
//                         return redirect('/grading')->with('msg',$msg);
//                     }
//                 }
//             }else{
//                 $msg = 'Tambah Grade Barang Gagal';
//                 return redirect('/grading')->with('msg',$msg);
//             }

//             $invitemstock_old = InvItemStock::findOrFail($fields['item_stock_id']);

//             $itemstock = array(
//                 'item_batch_number'             => $invitemstock_old['item_batch_number'],
//                 'goods_received_note_id'        => $invitemstock_old['goods_received_note_id'],
//                 'goods_received_note_item_id'   => $invitemstock_old['goods_received_note_item_id'],
//                 'item_stock_date'               => $request->item_stock_date,
//                 'item_stock_expired_date'       => $request->item_stock_expired_date,
//                 'warehouse_id'                  => $request->warehouse_id,
//                 'item_category_id'              => $request->item_category_id,
//                 'item_type_id'                  => $request->item_type_id,
//                 'item_id'                       => $item['item_id'],
//                 'item_unit_id'                  => $item['item_stock_unit_id'],
//                 'item_total'                    => $item['item_quantity'],
//                 'created_id'                    => Auth::id(),
//             );
            
//             $invitemstock = InvItemStock::select('inv_item_stock.*', 'inv_item_unit.item_unit_default_quantity')
//             ->where('item_id', $itemstock['item_id'])
//             ->join('inv_item_unit', 'inv_item_unit.item_unit_id', 'inv_item_stock.item_unit_id')
//             ->where('inv_item_stock.data_state', 0)
//             ->get();

//             if(InvItemStock::create($itemstock)){
//                 $invitemstocklast = InvItemStock::where('created_id', Auth::id())
//                 ->orderBy('created_at', 'DESC')
//                 ->first();

//                 $invitemstocklast_account = InvItemStock::select('inv_item.*')
//                 ->join('inv_item', 'inv_item.item_id', 'inv_item_stock.item_id')
//                 ->where('inv_item_stock.created_id', Auth::id())    
//                 ->orderBy('inv_item_stock.created_at', 'DESC')
//                 ->first();

//                 $invitem_production = InvItemStock::select('inv_item.*', 'inv_item_stock.item_stock_id')
//                 ->join('inv_item', 'inv_item.item_id', 'inv_item_stock.item_id')
//                 ->where('inv_item_stock.created_id', Auth::id())
//                 ->orderBy('inv_item_stock.item_stock_id', 'DESC')
//                 ->first();
    
//                 $total_start = 0;

//                 foreach($invitemstock as $val){
//                     if($val['item_unit_default_quantity'] != 1){
//                         $temp_total = $val['item_total'] * $val['item_unit_default_quantity'];
//                     }else{
//                         $temp_total = $val['item_total'];
//                     }
    
//                     $total_start += $temp_total;
//                 }
    
//                 $hpp_start = $invitem_production['hpp_amount'];

//                 $invitemunit = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $item['item_stock_unit_id'])
//                 ->first();

//                 if($invitemunit['item_unit_default_quantity'] == 1){
//                     $total_add = $item['item_quantity'];
//                 }else{
//                     $total_add = $item['item_quantity'] * $invitemunit['item_unit_default_quantity'];
//                 }
                
//                 $purchaseorderitemproduction = InvItemStock::select('purchase_order_item.item_unit_cost')
//                 ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                 ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                 ->where('item_stock_id', $invitem_production['item_stock_id'])
//                 ->first();

//                 $hpp_add = $hpp_package + $purchaseorderitemproduction['item_unit_cost'];
                
//                 $invitemproduction = InvItem::where('item_id', $invitem_production['item_id'])
//                 ->first();
                
//                 $invitemproduction->hpp_amount = ( ($total_start * $hpp_start) + ($total_add * $hpp_add) ) / ($total_start + $total_add);
//                 $invitemproduction->save();


// //----------------------------------------------------------Journal Voucher-------------------------------------------------------------------//

//             // if($purchaseorder['purchase_order_type_id'] == 2){
                
//                 $preferencecompany 			= PreferenceCompany::first();

//                 $purchaseorderitemmaterial  = InvItemStock::select('purchase_order_item.item_unit_cost', 'purchase_order_item.item_unit_id')
//                 ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                 ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                 ->where('inv_item_stock.item_stock_id', $invitemstocklast['item_stock_id'])
//                 ->first();
                
//                 $itemunitfirst  = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $purchaseorderitemmaterial['item_unit_id'])
//                 ->first();

//                 $itemunitsecond = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $item['item_stock_unit_id'])
//                 ->first();

//                 if($itemunitfirst['item_unit_default_quantity'] != 0){
//                     $total_amount_material      = $item['item_quantity'] * ($purchaseorderitemmaterial['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'] / $itemunitfirst['item_unit_default_quantity']);
//                 }else{
//                     $total_amount_material      = 0;   
//                 }
            
//                 $transaction_module_code 	= "GR";
        
//                 $transactionmodule 		    = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
//                 ->first();
        
//                 $transaction_module_id 		= $transactionmodule['transaction_module_id'];

//                 $journal_voucher_period 	= date("Ym", strtotime($invitemstocklast['created_at']));

//                 $data_journal = array(
//                     'branch_id'						=> 1,
//                     'journal_voucher_period' 		=> $journal_voucher_period,
//                     'journal_voucher_date'			=> $invitemstocklast['created_at'],
//                     'journal_voucher_title'			=> 'Grading '.$invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'journal_voucher_no'			=> $invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'journal_voucher_description'	=> "Grading",
//                     'transaction_module_id'			=> $transaction_module_id,
//                     'transaction_module_code'		=> $transaction_module_code,
//                     'transaction_journal_id' 		=> $invitemstocklast['item_stock_id'],
//                     'transaction_journal_no' 		=> $invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'created_id' 					=> Auth::id(),
//                 );
                
//                 AcctJournalVoucher::create($data_journal);

//                 $journalvoucher             = AcctJournalVoucher::where('created_id', Auth::id())
//                 ->orderBy('journal_voucher_id', 'DESC')
//                 ->first();

//                 $journal_voucher_id 	    = $journalvoucher['journal_voucher_id'];

//                 $account 		            = AcctAccount::where('account_id', $invitemstockmaterial_account['inv_account_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $account_id_default_status 	= $account['account_default_status'];

//                 $data_credit = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $invitemstockmaterial_account['inv_account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($total_amount_material),
//                     'journal_voucher_credit_amount'	=> ABS($total_amount_material),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 0,
//                 );

//                 AcctJournalVoucherItem::create($data_credit);
                
//                 $total_amount_package = 0;
//                 foreach($item['package'] as $package){
//                     $itemstockpackage = array(
//                         'item_stock_id'        => $invitemstocklast['item_stock_id'],
//                         'package_stock_id'     => $package['package_id'],
//                         'quantity'             => $package['item_package_quantity'],
//                         'package_unit_id'      => $package['item_package_unit_id'],
//                         'created_id'           => Auth::id(),
//                     );

//                     InvItemStockPackage::create($itemstockpackage);
                    
//                     $purchaseorderitempackage              = InvItemStock::select('purchase_order_item.item_unit_cost', 'purchase_order_item.item_unit_id')
//                     ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                     ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                     ->where('inv_item_stock.item_stock_id', $package['package_id'])
//                     ->first();
                    
//                     $itemunitfirst  = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $purchaseorderitempackage['item_unit_id'])
//                     ->first();

//                     $itemunitsecond = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $package['item_package_unit_id'])
//                     ->first();

//                     if($itemunitfirst['item_unit_default_quantity'] != 0){
//                         $amount_package      = $package['item_package_quantity'] * ($purchaseorderitempackage['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'] / $itemunitfirst['item_unit_default_quantity']);
//                     }else{
//                         $amount_package      = 0;
//                     }
                    
//                     $invitemstockpackage = InvItemStock::where('item_stock_id', $package['package_id'])
//                     ->first();

//                     $invitemstockpackage_account = InvItemStock::select('inv_item_type.*')
//                     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
//                     ->where('item_stock_id', $package['package_id'])
//                     ->first();

//                     $account 		     = AcctAccount::where('account_id', $invitemstockpackage_account['inv_account_id'])
//                     ->where('data_state', 0)
//                     ->first();

//                     $account_id_default_status 		= $account['account_default_status'];

//                     $data_credit = array (
//                         'journal_voucher_id'			=> $journal_voucher_id,
//                         'account_id'					=> $invitemstockpackage_account['inv_account_id'],
//                         'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                         'journal_voucher_amount'		=> ABS($amount_package),
//                         'journal_voucher_credit_amount'	=> ABS($amount_package),
//                         'account_id_default_status'		=> $account_id_default_status,
//                         'account_id_status'				=> 0,
//                     );

//                     AcctJournalVoucherItem::create($data_credit);
//                     $total_amount_package += $amount_package;
//                 }

//                 $total_amount = $total_amount_material + $total_amount_package;
                
//                 $account 	  = AcctAccount::where('account_id', $invitemstocklast_account['inv_account_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $account_id_default_status 		= $account['account_default_status'];

//                 $data_debit = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $invitemstocklast_account['inv_account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($total_amount),
//                     'journal_voucher_debit_amount'	=> ABS($total_amount),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 1,
//                 );

//                 AcctJournalVoucherItem::create($data_debit);
//             // }
// //--------------------------------------------------------End Journal Voucher-----------------------------------------------------------------//


//             }else{
//                 $msg = 'Tambah Grade Barang Gagal';
//                 return redirect('/grading')->with('msg',$msg);
//             }
            
//         }

//         $msg = 'Tambah Grade Barang Berhasil';
//         return redirect('/grading')->with('msg',$msg);
//     }

//     public function processEditGrading(Request $request)
//     {
//         $fields = $request->validate([
//             'item_stock_id'         => 'required',
//         ]);

//         $dataarrayinvitemstock = Session::get('dataarrayinvitemstock');
//         foreach($dataarrayinvitemstock as $item){
//             $invitemstockmaterial = InvItemStock::findOrFail($fields['item_stock_id']);
            
//             $invitemstockmaterial_account = InvItemStock::select('inv_item.*')
//             ->join('inv_item', 'inv_item.item_id', 'inv_item_stock.item_id')
//             ->where('item_stock_id', $fields['item_stock_id'])
//             ->first();

//             $invitem_material = InvItemStock::select('inv_item.*')
//             ->where('inv_item_stock.item_stock_id', $fields['item_stock_id'])
//             ->join('inv_item', 'inv_item.item_id', 'inv_item_stock.item_id')
//             ->first();
            
//             $invitemstock = InvItemStock::select('inv_item_stock.*', 'inv_item_unit.item_unit_default_quantity')
//             ->where('item_id', $invitem_material['item_id'])
//             ->join('inv_item_unit', 'inv_item_unit.item_unit_id', 'inv_item_stock.item_unit_id')
//             ->where('inv_item_stock.data_state', 0)
//             ->get();

//             $total_start = 0;

//             foreach($invitemstock as $val){
//                 if($val['item_unit_default_quantity'] != 1){
//                     $temp_total = $val['item_total'] * $val['item_unit_default_quantity'];
//                 }else{
//                     $temp_total = $val['item_total'];
//                 }

//                 $total_start += $temp_total;
//             }

//             $hpp_start = $invitem_material['hpp_amount'];

//             $itemunitfirst = InvItemUnit::where('data_state', 0)
//             ->where('item_unit_id', $invitemstockmaterial['item_unit_id'])
//             ->first();

//             $itemunitsecond = InvItemUnit::where('data_state', 0)
//             ->where('item_unit_id', $item['item_stock_unit_id'])
//             ->first();

//             $total_add = ($item['item_quantity'] / $itemunitfirst['item_unit_default_quantity'] * $itemunitsecond['item_unit_default_quantity']);

//             $purchaseorderitemmaterial = InvItemStock::select('purchase_order_item.item_unit_cost')
//             ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//             ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//             ->where('item_stock_id', $fields['item_stock_id'])
//             ->first();

//             $hpp_add = $purchaseorderitemmaterial['item_unit_cost'] * $total_add;

//             $item_total = $invitemstockmaterial['item_total'] - $total_add;

//             $invitemstockmaterial->item_total = $item_total;
//             if($item_total<=0){
//                 $invitemstockmaterial->data_state = 1;
//             }

//             if($invitemstockmaterial->save()){

//                 $hpp_package = 0;

//                 foreach($item['package'] as $package){
//                     $invitemstock = InvItemStock::findOrFail($package['package_id']);

//                     $itemunitfirst = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $invitemstock['item_unit_id'])
//                     ->first();

//                     $itemunitsecond = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $package['item_package_unit_id'])
//                     ->first();

//                     $total_package = ($package['item_package_quantity'] / $itemunitfirst['item_unit_default_quantity'] * $itemunitsecond['item_unit_default_quantity']);

//                     $item_total = $invitemstock['item_total'] - $total_package;

//                     $invitemstock->item_total = $item_total;
//                     if($item_total<=0){
//                         $invitemstock->data_state = 1;
//                     }
//                     if($invitemstock->save()){
//                         $purchaseorderitempackage = InvItemStock::select('purchase_order_item.item_unit_cost')
//                         ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                         ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                         ->where('item_stock_id', $package['package_id'])
//                         ->first();

//                         if($itemunitsecond['item_unit_default_quantity'] == 1){
//                             $temp_hpp_package = $purchaseorderitempackage['item_unit_cost'];
//                         }else{
//                             $temp_hpp_package = $purchaseorderitempackage['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'];
//                         }

//                         $hpp_package += $temp_hpp_package;
//                     }else{
//                         $msg = 'Edit Grade Barang Gagal';
//                         return redirect('/grading')->with('msg',$msg);
//                     }
//                 }
//             }else{
//                 $msg = 'Edit Grade Barang Gagal';
//                 return redirect('/grading')->with('msg',$msg);
//             }

//             $invitemstock_old = InvItemStock::findOrFail($fields['item_stock_id']);

//             $itemstock = array(
//                 'item_batch_number'             => $invitemstock_old['item_batch_number'],
//                 'goods_received_note_id'        => $invitemstock_old['goods_received_note_id'],
//                 'goods_received_note_item_id'   => $invitemstock_old['goods_received_note_item_id'],
//                 'item_stock_date'               => $request->item_stock_date,
//                 'item_stock_expired_date'       => $request->item_stock_expired_date,
//                 'warehouse_id'                  => $request->warehouse_id,
//                 'item_category_id'              => $request->item_category_id,
//                 'item_type_id'                  => $request->item_type_id,
//                 'item_id'                       => $item['item_id'],
//                 'item_unit_id'                  => $item['item_stock_unit_id'],
//                 'item_total'                    => $item['item_quantity'],
//                 'created_id'                    => Auth::id(),
//             );

//             $invitemstock = InvItemStock::select('inv_item_stock.*', 'inv_item_unit.item_unit_default_quantity')
//             ->where('item_id', $itemstock['item_id'])
//             ->join('inv_item_unit', 'inv_item_unit.item_unit_id', 'inv_item_stock.item_unit_id')
//             ->where('inv_item_stock.data_state', 0)
//             ->get();

//             if(InvItemStock::create($itemstock)){
//                 $invitemstocklast = InvItemStock::where('created_id', Auth::id())
//                 ->orderBy('created_at', 'DESC')
//                 ->first();

//                 $invitemstocklast_account = InvItemStock::select('inv_item.*')
//                 ->join('inv_item', 'inv_item_stock.item_id', 'inv_item.item_id')
//                 ->where('inv_item_stock.created_id', Auth::id())
//                 ->orderBy('inv_item_stock.created_at', 'DESC')
//                 ->first();

//                 $invitem_production = InvItemStock::select('inv_item.*', 'inv_item_stock.item_stock_id')
//                 ->where('inv_item_stock.created_id', Auth::id())
//                 ->orderBy('inv_item_stock.created_at', 'DESC')
//                 ->join('inv_item', 'inv_item.item_id', 'inv_item_stock.item_id')
//                 ->first();
            
    
//                 $total_start = 0;

//                 foreach($invitemstock as $val){
//                     if($val['item_unit_default_quantity'] != 1){
//                         $temp_total = $val['item_total'] * $val['item_unit_default_quantity'];
//                     }else{
//                         $temp_total = $val['item_total'];
//                     }
    
//                     $total_start += $temp_total;
//                 }
    
//                 $hpp_start = $invitem_production['hpp_amount'];

//                 $invitemunit = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $item['item_stock_unit_id'])
//                 ->first();

//                 if($invitemunit['item_unit_default_quantity'] == 1){
//                     $total_add = $item['item_quantity'];
//                 }else{
//                     $total_add = $item['item_quantity'] * $invitemunit['item_unit_default_quantity'];
//                 }
                
//                 $purchaseorderitemproduction = InvItemStock::select('purchase_order_item.item_unit_cost')
//                 ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                 ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                 ->where('item_stock_id', $invitem_production['item_stock_id'])
//                 ->first();

//                 $hpp_add = $hpp_package + $purchaseorderitemproduction['item_unit_cost'];
                
//                 $invitemproduction = InvItem::where('item_id', $invitem_production['item_id'])
//                 ->first();

//                 $invitemproduction->hpp_amount = ( ($total_start * $hpp_start) + ($total_add * $hpp_add) ) / ($total_start + $total_add);
//                 $invitemproduction->save();


// //---------------------------------------------------------Journal Voucher-------------------------------------------------------------------//
                
//                 $preferencecompany 			= PreferenceCompany::first();

//                 $purchaseorderitemmaterial  = InvItemStock::select('purchase_order_item.item_unit_cost', 'purchase_order_item.item_unit_id')
//                 ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                 ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                 ->where('inv_item_stock.item_stock_id', $invitemstocklast['item_stock_id'])
//                 ->first();
                
//                 $itemunitfirst = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $purchaseorderitemmaterial['item_unit_id'])
//                 ->first();

//                 $itemunitsecond = InvItemUnit::where('data_state', 0)
//                 ->where('item_unit_id', $item['item_stock_unit_id'])
//                 ->first();

//                 $total_amount_material               = $item['item_quantity'] * ($purchaseorderitemmaterial['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'] / $itemunitfirst['item_unit_default_quantity']);
            
//                 $transaction_module_code 	= "GR";
        
//                 $transactionmodule 		    = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
//                 ->first();
        
//                 $transaction_module_id 		= $transactionmodule['transaction_module_id'];

//                 $journal_voucher_period 	= date("Ym", strtotime($invitemstocklast['created_at']));

//                 $data_journal = array(
//                     'branch_id'						=> 1,
//                     'journal_voucher_period' 		=> $journal_voucher_period,
//                     'journal_voucher_date'			=> $invitemstocklast['created_at'],
//                     'journal_voucher_title'			=> 'Grading '.$invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'journal_voucher_no'			=> $invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'journal_voucher_description'	=> "Grading",
//                     'transaction_module_id'			=> $transaction_module_id,
//                     'transaction_module_code'		=> $transaction_module_code,
//                     'transaction_journal_id' 		=> $invitemstocklast['item_stock_id'],
//                     'transaction_journal_no' 		=> $invitemstocklast['item_stock_id'].$invitemstocklast['item_batch_number'],
//                     'created_id' 					=> Auth::id(),
//                 );
                
//                 AcctJournalVoucher::create($data_journal);

//                 $journalvoucher = AcctJournalVoucher::where('created_id', Auth::id())
//                 ->orderBy('journal_voucher_id', 'DESC')
//                 ->first();

//                 $journal_voucher_id 	= $journalvoucher['journal_voucher_id'];

//                 $account 		= AcctAccount::where('account_id', $invitemstockmaterial_account['inv_account_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $account_id_default_status 		= $account['account_default_status'];

//                 $data_credit = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $invitemstockmaterial_account['inv_account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($total_amount_material),
//                     'journal_voucher_credit_amount'	=> ABS($total_amount_material),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 0,
//                 );

//                 AcctJournalVoucherItem::create($data_credit);
                
//                 $total_amount_package = 0;
//                 foreach($item['package'] as $package){
//                     $itemstockpackage = array(
//                         'item_stock_id'        => $invitemstocklast['item_stock_id'],
//                         'package_stock_id'     => $package['package_id'],
//                         'quantity'             => $package['item_package_quantity'],
//                         'package_unit_id'      => $package['item_package_unit_id'],
//                         'created_id'           => Auth::id(),
//                     );

//                     InvItemStockPackage::create($itemstockpackage);
                    
//                     $purchaseorderitempackage              = InvItemStock::select('purchase_order_item.item_unit_cost', 'purchase_order_item.item_unit_id')
//                     ->join('inv_goods_received_note_item', 'inv_goods_received_note_item.goods_received_note_item_id', 'inv_item_stock.goods_received_note_item_id')
//                     ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'inv_goods_received_note_item.purchase_order_item_id')
//                     ->where('inv_item_stock.item_stock_id', $package['package_id'])
//                     ->first();
                    
//                     $itemunitfirst = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $purchaseorderitempackage['item_unit_id'])
//                     ->first();

//                     $itemunitsecond = InvItemUnit::where('data_state', 0)
//                     ->where('item_unit_id', $package['item_package_unit_id'])
//                     ->first();

//                     $amount_package               = $package['item_package_quantity'] * ($purchaseorderitempackage['item_unit_cost'] * $itemunitsecond['item_unit_default_quantity'] / $itemunitfirst['item_unit_default_quantity']);

//                     $invitemstockpackage_account = InvItemStock::select('inv_item_type.*')
//                     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
//                     ->where('item_stock_id', $package['package_id'])
//                     ->first();

//                     $account 		= AcctAccount::where('account_id', $invitemstockpackage_account['inv_account_id'])
//                     ->where('data_state', 0)
//                     ->first();

//                     $account_id_default_status 		= $account['account_default_status'];

//                     $data_credit = array (
//                         'journal_voucher_id'			=> $journal_voucher_id,
//                         'account_id'					=> $invitemstockpackage_account['inv_account_id'],
//                         'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                         'journal_voucher_amount'		=> ABS($amount_package),
//                         'journal_voucher_credit_amount'	=> ABS($amount_package),
//                         'account_id_default_status'		=> $account_id_default_status,
//                         'account_id_status'				=> 0,
//                     );

//                     AcctJournalVoucherItem::create($data_credit);
//                     $total_amount_package += $amount_package;
//                 }

//                 $total_amount = $total_amount_material + $total_amount_package;
                
//                 $account 		= AcctAccount::where('account_id', $invitemstocklast_account['inv_account_id'])
//                 ->where('data_state', 0)
//                 ->first();

//                 $account_id_default_status 		= $account['account_default_status'];

//                 $data_debit = array (
//                     'journal_voucher_id'			=> $journal_voucher_id,
//                     'account_id'					=> $invitemstocklast_account['inv_account_id'],
//                     'journal_voucher_description'	=> $data_journal['journal_voucher_description'],
//                     'journal_voucher_amount'		=> ABS($total_amount),
//                     'journal_voucher_debit_amount'	=> ABS($total_amount),
//                     'account_id_default_status'		=> $account_id_default_status,
//                     'account_id_status'				=> 1,
//                 );

//                 AcctJournalVoucherItem::create($data_debit);
// //--------------------------------------------------------End Journal Voucher-----------------------------------------------------------------//

    //         }else{
    //             $msg = 'Edit Grade Barang Gagal';
    //             return redirect('/grading')->with('msg',$msg);
    //         }
            
    //     }

    //     $msg = 'Edit Grade Barang Berhasil';
    //     return redirect('/grading')->with('msg',$msg);
    // }

    public function elements_add(Request $request){
        $gradingelements= Session::get('gradingelements');
        if(!$gradingelements || $gradingelements == ''){
            $gradingelements['item_id']             = '';
            $gradingelements['item_quantity']       = '';
            $gradingelements['item_stock_unit_id']  = '';
        }

        $gradingelements[$request->name] = $request->value;
        Session::put('gradingelements', $gradingelements);
    }

    public function getInvItemUnitName($item_unit_id){
        $unit = InvtItemUnit::select('item_unit_name')
        ->where('item_unit_id', $item_unit_id)
        ->where('data_state', 0)
        ->first();

        if($unit == null){
            return '-';
        }
        return $unit['item_unit_name'];
    }

    // public function getCorePackageName($package_id){
    //     $unit = CorePackage::select('package_name')
    //     ->where('package_id', $package_id)
    //     ->where('data_state', 0)
    //     ->first();
        
    //     if($unit == null){
    //         '-';
    //     }else{
    //         return $unit['package_name'];
    //     }

    // }

    // public function getCorePackagesName($item_stock_id){
    //     $packages = InvItemStockPackage::select('package_stock_id')
    //     ->where('item_stock_id', $item_stock_id)
    //     ->get();
        
    //     $package_name = "";
        
    //     foreach($packages as $key => $val){
    //         $package = InvItemStock::select('inv_item_type.item_type_name')
    //         ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //         ->where('inv_item_stock.item_stock_id', $val['package_stock_id'])
    //         ->first();
            
    //         $package_name .= $val['item_type_name'];
    //     }

    //     return $package_name;
    // }

    public function getInvWarehouseName($warehouse_id){
        $warehouse = InvtWarehouse::select('warehouse_name')

        ->where('warehouse_id', $warehouse_id)
        ->where('data_state', 0)
        ->first();

        return $warehouse['warehouse_name'];
    }

    public function getInvItemCategoryName($item_category_id){
        $item = InvtItemCategory::select('item_category_name',DB::raw('CONCAT(invt_item_category.item_category_name, " ", sales_merchant.merchant_name) AS item_name'))
        ->join('sales_merchant', 'sales_merchant.merchant_id', 'invt_item_category.merchant_id')
        ->where('invt_item_category.item_category_id', $item_category_id)
        ->where('invt_item_category.data_state', 0)
        ->first();

        if($item == null){
            return "-";
        }
        return $item['item_name'];
    }

    public function getInvItemName($item_id){
        $item = InvtItem::select('item_name')
        ->where('item_id', $item_id)
        ->where('data_state', 0)
        ->first();

        if($item == null){
            return "-";
        }
        return $item['item_name'];
    }

    // public function getCoreGradeName($item_id){
    //     $item = InvItem::select('core_grade.grade_name')
    //     ->join('core_grade','core_grade.grade_id','inv_item.grade_id')
    //     ->where('inv_item.item_id', $item_id)
    //     ->where('inv_item.data_state', 0)
    //     ->first();

    //     if($item == null){
    //         return "-";
    //     }
    //     return $item['grade_name'];
    // }

    public function getItemName($item_id){
        $invitem = InvtItem::select('invt_item.item_id', DB::raw('CONCAT(invt_item_category.item_category_name, " ", invt_item.item_name) AS item_name'))
        ->join('invt_item_category', 'invt_item_category.item_category_id', 'invt_item.item_category_id')
        // ->join('invt_item_type', 'invt_item_type.item_type_id', 'invt_item.item_type_id')
        // ->join('core_grade', 'core_grade.grade_id', 'invt_item.grade_id')
        ->where('item_id', $item_id)
        ->where('invt_item.data_state','=',0)
        ->first();

        return $invitem['item_name'];
    }

    // public function getPackageName($item_stock_id){
    //     $invitem = InvItemStock::select(DB::raw('CONCAT(inv_item_category.item_category_name, " ", inv_item_type.item_type_name) AS item_name'))
    //     ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
    //     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //     ->where('item_stock_id', $item_stock_id)
    //     ->where('inv_item_stock.data_state','=',0)
    //     ->first();

    //     return $invitem['item_name'];
    // }

    // public function getInvItemStockBatchNumber($item_stock_id){
    //     $invitem = InvItemStock::select('item_batch_number')
    //     ->where('item_stock_id', $item_stock_id)
    //     ->where('inv_item_stock.data_state','=',0)
    //     ->first();
        
    //     if($invitem == null){
    //         return "-";
    //     }
    //     return $invitem['item_batch_number'];
    // }

    // public function addCorePackage(Request $request){
    //     $package_name = $request->package_name;
    //     $data='';
        
    //     $corepackage = CorePackage::create([  
    //         'package_name'  => $package_name,
    //         'created_id'    => Auth::id()
    //     ]);

    //     $corepackage = CorePackage::where('data_state', 0)
    //     ->get();

    //     $data .= "<option value=''>--Choose One--</option>";
    //     foreach ($corepackage as $mp){
    //         $data .= "<option value='$mp[package_id]'>$mp[package_name]</option>\n";	
    //     }

    //     return $data;
    // }

    // public function addInvItemUnit(Request $request){
    //     $item_unit_code             = $request->item_unit_code;
    //     $item_unit_name             = $request->item_unit_name;
    //     $item_unit_default_quantity = $request->item_unit_default_quantity;
    //     $item_unit_remark           = $request->item_unit_remark;
    //     $data='';
        
    //     $itemunit = InvItemUnit::create([  
    //         'item_unit_code'                => $item_unit_code,
    //         'item_unit_name'                => $item_unit_name,
    //         'item_unit_default_quantity'    => $item_unit_default_quantity,
    //         'item_unit_remark'              => $item_unit_remark,
    //         'created_id'                    => Auth::id()
    //     ]);

    //     $itemunit = InvItemUnit::where('data_state', 0)
    //     ->get();

    //     $data .= "<option value=''>--Choose One--</option>";
    //     foreach ($itemunit as $mp){
    //         $data .= "<option value='$mp[item_unit_id]'>$mp[item_unit_name]</option>\n";	
    //     }

    //     return $data;
    // }

    // public function addInvItem(Request $request){
    //     $item_type_id                    = $request->item_type_id;
    //     $item_category_id                = $request->item_category_id;
    //     $grade_id                        = $request->grade_id;
    //     $item_unit_id                    = $request->item_unit_id;
    //     $item_remark                     = $request->item_remark;
    //     $item_barcode                    = $request->item_barcode;
    //     $purchase_account_id             = $request->purchase_account_id;
    //     $purchase_return_account_id      = $request->purchase_return_account_id;
    //     $purchase_discount_account_id    = $request->purchase_discount_account_id;
    //     $sales_account_id                = $request->sales_account_id;
    //     $sales_return_account_id         = $request->sales_return_account_id;
    //     $sales_discount_account_id       = $request->sales_discount_account_id;
    //     $item_stock_id                   = $request->item_stock_id;
    //     $data='';
        
    //     $invitem = InvItem::create([  
    //         'item_type_id'	                => $item_type_id,
    //         'item_category_id'	            => $item_category_id,
    //         'grade_id'	                    => $grade_id,
    //         'item_unit_id'	                => $item_unit_id,
    //         'item_remark'	                => $item_remark,
    //         'item_barcode'	                => $item_barcode,
    //         'purchase_account_id'	        => $purchase_account_id,
    //         'purchase_return_account_id'	=> $purchase_return_account_id,
    //         'purchase_discount_account_id'	=> $purchase_discount_account_id,
    //         'sales_account_id'	            => $sales_account_id,
    //         'sales_return_account_id'	    => $sales_return_account_id,
    //         'sales_discount_account_id'	    => $sales_discount_account_id,
    //         'created_id'    => Auth::id()
    //     ]);

    //     $invitem = InvItemStock::select(DB::raw('inv_item.item_id AS item_id'), DB::raw('CONCAT(inv_item_category.item_category_name, " ", inv_item_type.item_type_name, " ", core_grade.grade_name) AS item_name'))
    //     ->join('inv_item', 'inv_item.item_type_id', 'inv_item_stock.item_type_id')
    //     ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
    //     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //     ->join('core_grade', 'core_grade.grade_id', 'inv_item.grade_id')
    //     ->where('item_stock_id', $item_stock_id)
    //     ->where('inv_item_stock.data_state','=',0)
    //     ->get();

    //     $data .= "<option value=''>--Choose One--</option>";
    //     foreach ($invitem as $mp){
    //         $data .= "<option value='$mp[item_id]'>$mp[item_name]</option>\n";	
    //     }

    //     return $data;
    // }

    // public function getPackageDetail(Request $request){

    //     $data = InvItemStock::select(DB::raw('CONCAT(inv_item_category.item_category_name, " ", inv_item_type.item_type_name) AS item_name'))
    //     ->join('inv_item_category', 'inv_item_category.item_category_id', 'inv_item_stock.item_category_id')
    //     ->join('inv_item_type', 'inv_item_type.item_type_id', 'inv_item_stock.item_type_id')
    //     ->where('item_stock_id', $request->package_id)
    //     ->where('inv_item_stock.data_state','=',0)
    //     ->first();

    //     $datas = new stdClass;
        
    //     $datas->package_name    = $data['item_name'];

    //     return response()->json(json_encode($datas));
    // }
    
}

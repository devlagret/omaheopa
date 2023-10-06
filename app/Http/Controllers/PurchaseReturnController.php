<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\CoreSupplier;
use App\Models\InvGoodsReceivedNote;
use App\Models\InvGoodsReceivedNoteItem;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Session::get('start_date')) {
            $start_date     = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }

        if (!Session::get('end_date')) {
            $end_date     = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        Session::put('editarraystate', 0);
        Session::forget('datases');
        Session::forget('arraydatases');
        $data = PurchaseReturn::where('data_state', 0)
            ->where('purchase_return_date', '>=', $start_date)
            ->where('purchase_return_date', '<=', $end_date)
            // ->where('company_id', Auth::user()->company_id)
            ->where('data_state', 0)
            ->get();
        return view('content.PurchaseReturn.ListPurchaseReturn', compact('data', 'start_date', 'end_date'));
    }

    public function filterPurchaseReturn(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/purchase-return');
    }



    public function searchGoodsReceivedNote()
    {

        $GoodsReceivedNote = InvGoodsReceivedNote::select('invt_goods_received_note.*','invt_goods_received_note_item.*')
           ->join('invt_goods_received_note_item','invt_goods_received_note_item.goods_received_note_id','invt_goods_received_note.goods_received_note_id')
            ->where('invt_goods_received_note.data_state', '=', 0)
            ->where('invt_goods_received_note.return_status', '=', 0)
            ->where('invt_goods_received_note_item.merchant_id',Auth::user()->merchant_id)
            ->groupBy('invt_goods_received_note.goods_received_note_id')
            ->get();
        //  dd($GoodsReceivedNote);

        return view('content/PurchaseReturn/SearchGooodsNote', compact('GoodsReceivedNote'));
    }



    public function addPurchaseReturn($goods_received_note_id)
    {
        $GoodsReceivedNote = InvGoodsReceivedNote::select('invt_goods_received_note.*')
            ->where('data_state', '=', 0)
            ->where('goods_received_note_id', $goods_received_note_id)
            ->first();

        $GoodsReceivedNoteItem = InvGoodsReceivedNoteItem::select('invt_goods_received_note_item.*')
            ->where('data_state', '=', 0)
            ->where('goods_received_note_id', $goods_received_note_id)
            ->get();
        //   dd($GoodsReceivedNote);
        return view('content.PurchaseReturn.FormAddPurchaseReturn', compact('GoodsReceivedNote', 'GoodsReceivedNoteItem'));
    }

    public function addResetPurchaseReturn()
    {
        Session::forget('datases');
        Session::forget('arraydatases');
        return redirect('/purchase-return/add');
    }

    public function addElementsPurchaseReturn(Request $request)
    {
        $datases = Session::get('datases');
        if (!$datases || $datases == '') {
            $datases['supplier_id']    = '';
            $datases['warehouse_id']                = '';
            $datases['purchase_return_date']        = '';
            $datases['purchase_return_remark']      = '';
        }
        $datases[$request->name] = $request->value;
        $datases = Session::put('datases', $datases);
    }

    public function processAddPurchaseReturn(Request $request)
    {

        // $purchaseinvoiceitem_temporary = Session::get('purchaseinvoiceitem');
        // dd($request->all());
        $fields = $request->validate([
            'purchase_return_date'        => 'required',
            'supplier_id'                 => 'required',
            // 'warehouse_id'                => 'required',
        ]);

        $purchasereturn = array(
            'purchase_return_date'                  => $request->purchase_return_date,
            'purchase_invoice_id'                   => $request->purchase_invoice_id,
            'goods_received_note_id'                => $request->goods_received_note_id,
            'supplier_id'                           => $request->supplier_id,
            'warehouse_id'                          => 5,
            'purchase_order_return_remark'          => $request->purchase_order_return_remark,
            // 'subtotal_item'                         => $request->quantity_return_total,
            'created_id'                            => Auth::id(),
        );
        // dd($purchasereturn);

        if (PurchaseReturn::create($purchasereturn)) {
            $first_return = PurchaseReturn::select('purchase_return_id', 'purchase_return_no')
                ->where('created_id', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->first();

            $temprequest = $request->all();
            // dd($temprequest);

            //----------------------------------------------------------Journal Voucher-------------------------------------------------------------------//

            // $preferencecompany             = PreferenceCompany::first();

            // $transaction_module_code     = "PR";

            // $transactionmodule             = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
            //     ->first();


            // $transaction_module_id         = $transactionmodule['transaction_module_id'];

            // $journal_voucher_period     = date("Ym", strtotime($purchasereturn['purchase_return_date']));

            // $data_journal = array(
            //     'branch_id'                        => 1,
            //     'journal_voucher_period'         => $journal_voucher_period,
            //     'journal_voucher_date'           => $purchasereturn['purchase_return_date'],
            //     'journal_voucher_title'          => 'Return Pembelian Barang ' . $first_return['purchase_return_no'],
            //     'journal_voucher_no'             => $first_return['purchase_return_no'],
            //     'journal_voucher_description'    => $purchasereturn['purchase_return_remark'],
            //     'transaction_module_id'          => $transaction_module_id,
            //     'transaction_module_code'        => $transaction_module_code,
            //     'transaction_journal_id'         => $first_return['purchase_return_id'],
            //     'transaction_journal_no'         => $first_return['purchase_return_no'],
            //     'created_id'                     => Auth::id(),
            // );

            // JournalVoucher::create($data_journal);
            //---------------------------------------------------------End Journal Voucher----------------------------------------------------------------//



            $total_no = $request->total_no;
            // $total_received_item = $temprequest['quantity_return_total'];
            // dd($total_no);

            for ($i = 1; $i <= $total_no; $i++) {
                $purchasereturnitem = array(
                    'purchase_return_id'                            => $first_return['purchase_return_id'],
                    'purchase_invoice_id'                           => $temprequest['purchase_invoice_id'],
                    'goods_received_note_id'                        => $temprequest['goods_received_note_id'],
                    'purchase_goods_returned_note_id'               => $temprequest['goods_received_note_id'],
                    'goods_received_note_item_id'                   => $temprequest['goods_received_note_item_id_' . $i],
                    'purchase_invoice_item_id'                      => $temprequest['purchase_invoice_item_id_' . $i],
                    'merchant_id'                                   => $temprequest['merchant_id_'.$i],
                    'item_category_id'                              => $temprequest['item_category_id_' . $i],
                    'item_id'                                       => $temprequest['item_id_'.$i],
                    'item_unit_id'                                  => $temprequest['item_unit_id_' . $i],
                    'item_unit_cost'                                => $temprequest['item_unit_cost_' . $i],
                    'quantity'                                      => $temprequest['quantity_return_' . $i],
                    'created_id'                                    => Auth::id(),
                );

                //dd($purchasereturnitem);
                 PurchaseReturnItem::create($purchasereturnitem);

                // //update purchase order item
                $purchaseinvoiceitem = PurchaseInvoiceItem::findOrFail($temprequest['purchase_invoice_item_id_' . $i]);
                $purchaseinvoiceitem->quantity_return    = $temprequest['quantity_return_' . $i];
                $purchaseinvoiceitem->save();

                InvtItemStock::create([
                    'goods_received_note_id'            =>   '',
                    'goods_received_note_item_id'       =>   '',
                    'item_stock_date'                   =>   \Carbon\Carbon::now(), # new \Datetime()
                    'purchase_order_item_id'            =>   $temprequest['purchase_order_item_id_' . $i],
                    'warehouse_id'                      =>   7,
                    'item_category_id'                  =>   $temprequest['item_category_id_' . $i],
                    'item_id'                           =>   $temprequest['item_id_' . $i],
                    'item_unit_id'                      =>   $temprequest['item_unit_id_' . $i],
                    'item_total'                        =>   '',
                    'item_unit_id_default'              =>   $temprequest['item_unit_id_' . $i],
                    'item_default_quantity_unit'        =>   1,
                    'quantity_unit'                     =>   $temprequest['quantity_return_' . $i],
                    'item_weight_default'               =>   '',
                    'item_weight_unit'                  =>   '',
                    'package_id'                        =>   '',
                    'package_total'                     =>   '',
                    'package_unit_id'                   =>   '',
                    'package_price'                     =>   '',
                    'data_state'                        =>   0,
                    'created_id'                        =>   Auth::id(),
                    'created_at'                        =>   \Carbon\Carbon::now(),
                ]);




                // $total_received_item = $total_received_item + $purchaseinvoiceitem['quantity_return'] + $invgoodsreceivednoteitem['quantity'];


                //----------------------------------------------------------Journal Voucher Item-------------------------------------------------------------------//


                // $purchaseinvoiceitem          = PurchaseinvoiceItem::where('purchase_order_item_id', $temprequest['purchase_order_item_id_'.$i])
                // $purchaseinvoiceitem          = PurchaseInvoiceItem::where('purchase_invoice_item_id', $temprequest['purchase_invoice_item_id_' . $i])
                //     ->first();
                // //dd($purchaseinvoiceitem);

                // $purchaseinvoice              = PurchaseInvoice::findOrFail($purchasereturn['purchase_return_id']);
                // //dd($purchaseinvoiceitem,$purchaseinvoice);

                // $journalvoucher = JournalVoucher::where('created_id', Auth::id())
                //     ->orderBy('journal_voucher_id', 'DESC')
                //     ->first();
                // //dd($data_journal);

                // $journal_voucher_id     = $journalvoucher['journal_voucher_id'];


                // //------account_id Persedian Barang Dagang------//
                // $account         = AcctAccount::where('account_id', $preferencecompany['account_inventory_trade_id'])
                //     ->where('data_state', 0)
                //     ->first();

                // $total_amount               = $temprequest['quantity_return_' . $i] * $purchaseinvoiceitem['item_unit_cost'];

                // $account_id_default_status         = $account['account_default_status'];
                // // dd($account_id_default_status);

                // $data_credit1 = array(
                //     'journal_voucher_id'            => $journal_voucher_id,
                //     'account_id'                    => $preferencecompany['account_inventory_trade_id'],
                //     'journal_voucher_description'    => $data_journal['journal_voucher_description'],
                //     'journal_voucher_amount'        => ABS($total_amount),
                //     'journal_voucher_credit_amount'    => ABS($total_amount),
                //     'account_id_default_status'        => $account_id_default_status,
                //     'account_id_status'                => 0,
                // );

                // //dd($data_credit1);

                // JournalVoucherItem::create($data_credit1);
            }


        //     //------account_id Hutang Suplier------//
        //     $preference_company = PreferenceCompany::first();

        //     $account = AcctAccount::where('account_id', $preference_company['account_payable_id'])
        //         ->where('data_state', 0)
        //         ->first();

        //     $subtotal_after_ppn_in = $purchaseinvoice['subtotal_after_ppn_in'];

        //     $account_id_default_status         = $account['account_default_status'];

        //     $data_debit1 = array(
        //         'journal_voucher_id'                => $journal_voucher_id,
        //         'account_id'                        => $account['account_id'],
        //         'journal_voucher_description'       => $data_journal['journal_voucher_description'],
        //         'journal_voucher_amount'            => ABS($subtotal_after_ppn_in),
        //         'journal_voucher_debit_amount'      => ABS($subtotal_after_ppn_in),
        //         'account_id_default_status'        => $account_id_default_status,
        //         'account_id_status'                => 1,
        //     );
        //     // dd($data_debit1);


        //     JournalVoucherItem::create($data_debit1);



        //     //------account_id PPN Masukan------//
        //     $account = AcctAccount::where('account_id', $preference_company['account_vat_in_id'])
        //         ->where('data_state', 0)
        //         ->first();

        //     $ppn_in_amount = $purchaseinvoice['ppn_in_amount'];

        //     $account_id_default_status         = $account['account_default_status'];

        //     $data_credit2 = array(
        //         'journal_voucher_id'               => $journal_voucher_id,
        //         'account_id'                       => $preferencecompany['account_vat_in_id'],
        //         'journal_voucher_description'      => $data_journal['journal_voucher_description'],
        //         'journal_voucher_amount'           => ABS($ppn_in_amount),
        //         'journal_voucher_credit_amount'    => ABS($ppn_in_amount),
        //         'account_id_default_status'        => $account_id_default_status,
        //         'account_id_status'                => 0,
        //     );

        //     //dd($data_debit1,$data_credit1,$data_credit2);

        //     JournalVoucherItem::create($data_credit2);
        //     //--------------------------------------------------------End Journal Voucher-----------------------------------------------------------------//


            $purchaseinvoice = InvGoodsReceivedNote::findOrFail($temprequest['purchase_invoice_id']);
            $purchaseinvoice->return_status = 1;
            $purchaseinvoice->save();

            $msg = 'Tambah Return Pembelian Berhasil';
            return redirect('/purchase-return')->with('msg', $msg);
        } else {
            $msg = 'Tambah Return Pembelian Gagal';
            return redirect('/purchase-return')->with('msg', $msg);
        }
    }





    public function addArrayPurchaseReturn(Request $request)
    {
        $request->validate([
            'item_category_id'          => 'required',
            'item_id'                   => 'required',
            'item_unit_id'              => 'required',
            'purchase_return_cost'      => 'required',
            'purchase_return_quantity'  => 'required',
            'purchase_return_subtotal'  => 'required'
        ]);

        $arraydatases = array(
            'item_category_id'          => $request->item_category_id,
            'item_id'                   => $request->item_id,
            'item_unit_id'              => $request->item_unit_id,
            'purchase_return_cost'      => $request->purchase_return_cost,
            'purchase_return_quantity'  => $request->purchase_return_quantity,
            'purchase_return_subtotal'  => $request->purchase_return_subtotal,
        );
        $lastdatases = Session::get('arraydatases');
        if ($lastdatases !== null) {
            array_push($lastdatases, $arraydatases);
            Session::put('arraydatases', $lastdatases);
        } else {
            $lastdatases = [];
            array_push($lastdatases, $arraydatases);
            Session::push('arraydatases', $arraydatases);
        }
        Session::put('editarraystate', 1);
        return redirect('/purchase-return/add');
    }

    public function getItemName($item_id)
    {
        $item = InvtItem::where('item_id', $item_id)->first();
        return $item['item_name'];
    }

    public function deleteArrayPurchaseReturn($record_id)
    {
        $arrayBaru            = array();
        $dataArrayHeader    = Session::get('arraydatases');

        foreach ($dataArrayHeader as $key => $val) {
            if ($key != $record_id) {
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('arraydatases');
        Session::put('arraydatases', $arrayBaru);

        return redirect('/purchase-return/add');
    }

    public function getWarehouseName($warehouse_id)
    {
        $warehouse = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();
        return $warehouse['warehouse_name'];
    }

    public function detailPurchaseReturn($purchase_return_id)
    {
        $categorys = InvtItemCategory::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $warehouses = InvtWarehouse::where('data_State', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('warehouse_name', 'warehouse_id');
        $units     = InvtItemUnit::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_unit_name', 'item_unit_id');
        $items     = InvtItem::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_name', 'item_id');
        $purchasereturn = PurchaseReturn::where('purchase_return_id', $purchase_return_id)
            ->where('data_state', 0)
            ->first();
        $purchasereturnitem = PurchaseReturnItem::where('purchase_return_id', $purchase_return_id)->get();

        return view('content.PurchaseReturn.FormDetailPurchaseReturn', compact('purchasereturn', 'categorys', 'warehouses', 'units', 'items', 'purchasereturnitem'));
    }

    public function filterResetPurchaseReturn()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        return redirect('/purchase-return');
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)->first();

        return $data['transaction_module_id'];
    }

    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)->first();

        return $data['transaction_module_name'];
    }

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
            ->where('account_setting_name', $account_setting_name)
            ->first();

        return $data['account_setting_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
            ->where('account_setting_name', $account_setting_name)
            ->first();

        return $data['account_id'];
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_default_status'];
    }


    public function getCoreSupplierName($supplier_id)
    {
        $supplier = CoreSupplier::where('data_state', 0)
            ->where('supplier_id', $supplier_id)
            ->first();

        if ($supplier == null) {
            return "-";
        }

        return $supplier['supplier_name'];
    }

    public function getPurchaseinvoiceNo($purchase_invoice_id)
    {
        $purchaseinvoice = PurchaseInvoice::where('data_state', 0)
            ->where('purchase_invoice_id', $purchase_invoice_id)
            ->first();

        if ($purchaseinvoice == null) {
            return "-";
        }

        return $purchaseinvoice['purchase_invoice_no'];
    }



    public function getPurchaseInvoiceDate($purchase_invoice_id)
    {
        $purchaseorder = PurchaseInvoice::where('data_state', 0)
            ->where('purchase_invoice_id', $purchase_invoice_id)
            ->first();

        if ($purchaseorder == null) {
            return "-";
        }

        return $purchaseorder['purchase_invoice_date'];
    }


    public function getQtyPurchaseInvoiceItem($purchase_invoice_item_id)
    {
        $purchaseorder = PurchaseInvoiceItem::where('data_state', 0)
            ->where('purchase_invoice_item_id', $purchase_invoice_item_id)
            ->first();

        if ($purchaseorder == null) {
            return "-";
        }

        return $purchaseorder['quantity'];
    }

    public function getInvWarehouseName($warehouse_id)
    {
        $warehouse = InvtWarehouse::where('data_state', 0)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($warehouse == null) {
            return "-";
        }

        return $warehouse['warehouse_name'];
    }

    public function getInvItemCategoryName($item_category_id)
    {
        $itemcategory = InvtItemCategory::where('data_state', 0)
            ->where('item_category_id', $item_category_id)
            ->first();

        if ($itemcategory == null) {
            return "-";
        }
        return $itemcategory['item_category_name'];
    }

    public function getInvItemUnitName($item_unit_id)
    {
        $itemunit = InvtItemUnit::where('data_state', 0)
            ->where('item_unit_id', $item_unit_id)
            ->first();

        if ($itemunit == null) {
            return "-";
        }

        return $itemunit['item_unit_name'];
    }

    public function getInvItemUnitCost($item_id)
    {
        $itemunit = PurchaseInvoiceItem::where('data_state', 0)
            ->where('item_id', $item_id)
            ->first();

        if ($itemunit == null) {
            return "-";
        }

        return $itemunit['item_unit_cost'];
    }



    public function getMerchantName($merchant_id)
    {
        $itemcategory = SalesMerchant::where('data_state', 0)
            ->where('merchant_id', $merchant_id)
            ->first();

        if ($itemcategory == null) {
            return "-";
        }
        return $itemcategory['merchant_name'];
    }
}

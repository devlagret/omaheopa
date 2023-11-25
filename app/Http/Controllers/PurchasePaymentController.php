<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CoreBank;
use App\Models\AcctAccount;
use App\Models\InvItemType;
use App\Models\InvItemUnit;
use Illuminate\Support\Str;
use App\Models\CoreSupplier;
use App\Models\InvtItemUnit;
use App\Models\InvWarehouse;
use Illuminate\Http\Request;
use App\Models\InvtWarehouse;
use App\Models\PurchaseOrder;
use App\Helpers\JournalHelper;
use App\Models\JournalVoucher;
use App\Models\InvItemCategory;
use App\Models\PurchaseInvoice;
use App\Models\PurchasePayment;
use App\Models\InvtItemCategory;
use App\Models\PreferenceCompany;
use App\Models\PurchaseOrderItem;
use App\Models\AcctJournalVoucher;
use App\Models\JournalVoucherItem;
use Illuminate\Support\Facades\DB;
use App\Models\PurchasePaymentGiro;
use App\Models\PurchasePaymentItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AcctJournalVoucherItem;
use App\Models\PurchasePaymentTransfer;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\PreferenceTransactionModule;

class PurchasePaymentController extends Controller
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
        Session::forget('payment-token');
        Session::forget('purchasepaymentelements');
        Session::forget('datapurchasepaymenttransfer');
        if (!Session::get('start_date')) {
            $start_date     = date('Y-m-d');
        } else {
            $start_date     = Session::get('start_date');
        }
        if (!Session::get('end_date')) {
            $end_date       = date('Y-m-d');
        } else {
            $end_date       = Session::get('end_date');
        }
        $supplier_id        = Session::get('supplier_id');
        $coresupplier       = CoreSupplier::where('data_state', 0)
            ->pluck('supplier_name', 'supplier_id');
        $purchasepayment    = PurchasePayment::where('data_state', 0)
            ->where('payment_date', '>=', $start_date)
            ->where('payment_date', '<=', $end_date);
        if (!$supplier_id || $supplier_id == '' || $supplier_id == null) {
        } else {
            $purchasepayment = $purchasepayment->where('supplier_id', $supplier_id);
        }
        $purchasepayment    = $purchasepayment->get();
        return view('content/PurchasePayment/ListPurchasePayment', compact('coresupplier', 'purchasepayment', 'start_date', 'end_date', 'supplier_id'));
    }
    public function filterPurchasePayment(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $supplier_id    = $request->supplier_id;
        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('supplier_id', $supplier_id);
        return redirect('/purchase-payment');
    }
    public function searchCoreSupplier()
    {
        Session::forget('purchasepaymentelements');
        Session::forget('datapurchasepaymenttransfer');
        $coresupplier = PurchaseInvoice::select('purchase_invoice.supplier_id', 'core_supplier.supplier_name', 'core_supplier.supplier_address', DB::raw("SUM(purchase_invoice.owing_amount) as total_owing_amount"))
            ->join('core_supplier', 'core_supplier.supplier_id', 'purchase_invoice.supplier_id')
            ->where('purchase_invoice.data_state', 0)
            ->where('core_supplier.data_state', 0)
            ->groupBy('purchase_invoice.supplier_id')
            ->orderBy('core_supplier.supplier_name', 'ASC')
            ->get();
        return view('content/PurchasePayment/SearchCoreSupplier', compact('coresupplier'));
    }
    public function addPurchasePayment($supplier_id)
    {
        if (empty(Session::get('payment-token'))) {
            Session::put('payment-token', Str::uuid());
        }
        $purchaseinvoiceowing = PurchaseInvoice::select('purchase_invoice.purchase_invoice_id', 'purchase_invoice.supplier_id', 'purchase_invoice.owing_amount', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.paid_amount', 'purchase_invoice.purchase_invoice_no', 'purchase_invoice.discount_percentage', 'purchase_invoice.discount_amount', 'purchase_invoice.total_amount')
            ->where('purchase_invoice.supplier_id', $supplier_id)
            ->where('purchase_invoice.owing_amount', '>', 0)
            ->where('purchase_invoice.data_state', 0)
            ->get();
        $supplier = CoreSupplier::findOrfail($supplier_id);
        $acctaccount    = AcctAccount::select('account_id', DB::raw('CONCAT(account_code, " - ", account_name) AS full_name'))
            ->where('acct_account.data_state', 0)
            ->where('parent_account_status', 0)
            ->where('account_name', 'like', '%kas-%')
            ->pluck('full_name', 'account_id');
        $corebank = AcctAccount::select('account_id as bank_id', DB::raw('CONCAT(account_code, " - ", account_name) AS bank_name'))->where('data_state', 0)->where('account_name', 'like', '%bank%')
            ->pluck('bank_name', 'bank_id');
        $purchasepaymentelements = Session::get('purchasepaymentelements');
        $purchasepaymenttransfer = Session::get('datapurchasepaymenttransfer');
        return view('content/PurchasePayment/FormAddPurchasePayment', compact('supplier_id', 'purchaseinvoiceowing', 'corebank', 'supplier', 'acctaccount', 'purchasepaymentelements', 'purchasepaymenttransfer'));
    }
    public function detailPurchasePayment($payment_id)
    {
        $purchasepayment = PurchasePayment::findOrFail($payment_id);
        $purchasepaymentitem = PurchasePaymentItem::select('purchase_payment_item.*', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.purchase_invoice_no', 'purchase_payment_item.shortover_amount AS shortover_value')
            ->join('purchase_invoice', 'purchase_invoice.purchase_invoice_id', 'purchase_payment_item.purchase_invoice_id')
            ->where('payment_id', $purchasepayment['payment_id'])
            ->get();
        $purchasepaymenttransfer = PurchasePaymentTransfer::where('payment_id', $purchasepayment['payment_id'])
            ->get();
        $supplier = CoreSupplier::where('data_state', 0)
            ->where('supplier_id', $purchasepayment['supplier_id'])
            ->first();
        return view('content/PurchasePayment/FormDetailPurchasePayment', compact('payment_id', 'purchasepayment', 'purchasepaymentitem', 'purchasepaymenttransfer',  'supplier'));
    }
    public function deletePurchasePayment($payment_id)
    {
        $purchasepayment = PurchasePayment::findOrFail($payment_id);
        $purchasepaymentitem = PurchasePaymentItem::select('purchase_payment_item.*', 'purchase_invoice.purchase_invoice_date', 'purchase_invoice.purchase_invoice_no', 'purchase_payment_item.shortover_amount AS shortover_value')
            ->join('purchase_invoice', 'purchase_invoice.purchase_invoice_id', 'purchase_payment_item.purchase_invoice_id')
            ->where('payment_id', $purchasepayment['payment_id'])
            ->get();
        $purchasepaymenttransfer = PurchasePaymentTransfer::where('payment_id', $purchasepayment['payment_id'])
            ->get();
        $supplier = CoreSupplier::where('data_state', 0)
            ->where('supplier_id', $purchasepayment['supplier_id'])
            ->first();
        return view('content/PurchasePayment/FormDeletePurchasePayment', compact('payment_id', 'purchasepayment', 'purchasepaymentitem', 'purchasepaymenttransfer',  'supplier'));
    }
    public function elements_add(Request $request)
    {
        $purchasepaymentelements = Session::get('purchasepaymentelements');
        if (!$purchasepaymentelements || $purchasepaymentelements == '') {
            $purchasepaymentelements['payment_date']                = '';
            $purchasepaymentelements['payment_remark']              = '';
            $purchasepaymentelements['cash_account_id']             = '';
            $purchasepaymentelements['payment_total_cash_amount']   = '';
        }
        $purchasepaymentelements[$request->name] = $request->value;
        Session::put('purchasepaymentelements', $purchasepaymentelements);
    }
    public function  processAddTransferArray(Request $request)
    {
        $data = collect(Session::get('datapurchasepaymenttransfer'));
        $item = collect();
        $item->put('bank_id', $request->bank_id);
        $item->put('name', $request->name);
        $item->put('payment_transfer_account_name', $request->payment_transfer_account_name);
        $item->put('payment_transfer_account_no', $request->payment_transfer_account_no);
        $item->put('payment_transfer_amount', $request->payment_transfer_amount);
        $data->put(Str::uuid()->toString(), $item);
        Session::put('datapurchasepaymenttransfer', $data->toArray());
    }
    public function processAddPurchasePayment(Request $request)
    {
        if (empty(Session::get('payment-token'))) {
            return redirect()->route('purchase-payment.index')->with('msg', "Tambah Pelunasan Hutang Berhasil*");
        }
        $token = Session::get('payment-token');
        $datapurchasepaymenttransfer = Session::get('datapurchasepaymenttransfer');
        $fields = $request->validate([
            'payment_date'                  => 'required',
            'payment_allocated_move_view'   => 'required',
            'allocation_total'              => 'required|numeric|min:0|not_in:0',
        ], [
            'payment_date.required'                => 'Kolom Tanggal Pembayaran Harus Diisi',
            'payment_allocated_move_view.required' => 'Harap Isi Alokasi',
            'allocation_total.required'            => 'Harap Isi Alokasi',
            'allocation_total.min:0'               => 'Alokasi Harus Lebih Dari 0',
            'allocation_total.not_in:0'            => 'Alokasi Harus Lebih Dari 0'
        ]);
        try {
            DB::beginTransaction();
            PurchasePayment::create([
                'supplier_id'                   => $request->supplier_id,
                'payment_date'                  => $request->payment_date,
                'payment_remark'                => $request->payment_remark,
                'payment_amount'                => $request->payment_amount,
                'payment_allocated'             => $request->allocation_total,
                'payment_shortover'             => $request->shortover_total,
                'payment_total_amount'          => $request->payment_amount,
                'payment_total_cash_amount'     => $request->payment_total_cash_amount,
                'payment_total_transfer_amount' => $request->payment_total_transfer_amount,
                'payment_token'                 => $token,
                'created_id'                    => Auth::id()
            ]);
            $pid = PurchasePayment::where('payment_token', $token)->latest()->first();
            $journal = JournalHelper::make('Purchase Payment',($request->allocation_total+$request->shortover_total));
            $journal->item(intval($request->cash_account_id),1,$request->payment_total_cash_amount);
            $journal->item('purchase_payment_account',null,$request->payment_total_cash_amount);
            if (is_array($datapurchasepaymenttransfer) && !empty($datapurchasepaymenttransfer)) {
                foreach ($datapurchasepaymenttransfer as $key => $val) {
                    PurchasePaymentTransfer::create([
                        'payment_id'                    => $pid->payment_id,
                        'account_id'                    => $val['bank_id'],
                        'payment_transfer_amount'       => $val['payment_transfer_amount'],
                        'payment_transfer_account_name' => $val['payment_transfer_account_name'],
                        'payment_transfer_account_no'   => $val['payment_transfer_account_no'],
                        'created_id'                    => Auth::id()
                    ]);
                    $journal->item(intval($val['bank_id']),1,$val['payment_transfer_amount']);
                    $journal->item('purchase_non_cash_payment_account',null,$val['payment_transfer_amount']);
                }
            }
            foreach ($request->item as $key => $value) {
                if($value['allocation']>0){
                    PurchasePaymentItem::create([
                        'payment_id'             => $pid->payment_id,
                        'purchase_invoice_id'    => $value['purchase_invoice_id'],
                        'purchase_invoice_no'    => $value['purchase_invoice_no'],
                        'purchase_invoice_date'  => $value['purchase_invoice_date'],
                        'purchase_invoice_amount'=> $value['purchase_invoice_amount'],
                        'total_amount'           => $value['total_amount'],
                        'paid_amount'            => $value['paid_amount'],
                        'owing_amount'           => $value['owing_amount'],
                        'allocation_amount'      => $value['allocation'],
                        'shortover_amount'       => $value['shortover'],
                        'last_balance'           => $value['last_balance'],
                        'created_id'             => Auth::id()
                    ]);
                    $purchaseinvoice = PurchaseInvoice::find($value['purchase_invoice_id']);
                    $purchaseinvoice->paid_amount       = ($purchaseinvoice->paid_amount + $value['allocation'] + $value['shortover']);
                    $purchaseinvoice->owing_amount      = $value['last_balance'];
                    $purchaseinvoice->shortover_amount  = ($purchaseinvoice->shortover_amount + $value['shortover']);
                    $purchaseinvoice->save();
                }
            }
            DB::commit();
            return redirect()->route('purchase-payment.index')->with('msg', "Tambah Pelunasan Hutang Berhasil");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            report($e);
            return redirect()->route('purchase-payment.index')->with('msg', "Tambah Pelunasan Hutang Gagal");
        }
    }
    public function processVoidPurchasePayment(Request $request)
    {
        $payment_no                     = $request->payment_no;
        $purchasepayment                = PurchasePayment::findOrFail($request->payment_id);
        $purchasepayment->voided_remark = $request->voided_remark;
        $purchasepayment->voided_on     = date('Y-m-d H:i:s');
        $purchasepayment->voided_id     = Auth::id();
        $purchasepayment->data_state    = 2;
        if ($purchasepayment->save()) {
            $purchasepaymentitem     = PurchasePaymentItem::where('payment_id', $request->payment_id)->get();
            foreach ($purchasepaymentitem as $ki => $vi) {
                $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $vi['purchase_invoice_id'])->first();
                // print_r((float)$purchaseinvoice['paid_amount'] - ((float)$vi['allocation_amount'] + (float)$vi['shortover_amount']));
                // print_r("|||||||||");
                // print_r((float)$purchaseinvoice['owing_amount'] + ((float)$vi['allocation_amount'] + (float)$vi['shortover_amount']));
                // print_r("|||||||||");
                // print_r((float)$purchaseinvoice['shortover_amount'] - (float)$vi['shortover_amount']);exit;
                $purchaseinvoice->paid_amount       = $purchaseinvoice['paid_amount'] - ($vi['allocation_amount'] + $vi['shortover_amount']);
                $purchaseinvoice->owing_amount      = $purchaseinvoice['owing_amount'] + ($vi['allocation_amount'] + $vi['shortover_amount']);
                $purchaseinvoice->shortover_amount  = $purchaseinvoice['shortover_amount'] - $vi['shortover_amount'];
                $purchaseinvoice->save();
            }
            $journalvoucher         = JournalVoucher::where('transaction_journal_no', $payment_no)->first();
            $journal_voucher_id     = $journalvoucher['journal_voucher_id'];
            $acctjournalvoucheritem = JournalVoucherItem::where('journal_voucher_id', $journal_voucher_id)->get();
            $journalvoucher                 = JournalVoucher::where('journal_voucher_id', $journal_voucher_id)->first();
            $journalvoucher->voided         = 1;
            $journalvoucher->voided_id      = Auth::id();
            $journalvoucher->voided_on      = date('Y-m-d H:i:s');
            $journalvoucher->voided_remark  = $request->voided_remark;
            $journalvoucher->data_state     = 2;
            if ($journalvoucher->save()) {
                foreach ($acctjournalvoucheritem as $keyItem => $valItem) {
                    $journalvoucheritem = JournalVoucherItem::where('journal_voucher_item_id', $valItem['journal_voucher_item_id'])->first();
                    $journalvoucheritem->data_state = 2;
                    $journalvoucheritem->save();
                }
            }
            $msg = "Pembatalan Pelunasan Hutang Berhasil";
            return redirect('/purchase-payment')->with('msg', $msg);
        } else {
            $msg = "Pembatalan Pelunasan Hutang Gagal";
            return redirect('/purchase-payment/delete/' . $request->payment_id)->with('msg', $msg);
        }
    }
    public function deleteTransferArray($record_id, $supplier_id)
    {
        $data            = collect(Session::get('datapurchasepaymenttransfer'));
        $data->forget($record_id);
        Session::put('datapurchasepaymenttransfer', $data->toArray());
        return redirect('/purchase-payment/add/' . $supplier_id);
    }
    public function getItemCategoryName($item_category_id)
    {
        $itemcategory = InvtItemCategory::where('data_state', 0)
            ->where('item_category_id', $item_category_id)
            ->first();
        return $itemcategory['item_category_name'];
    }
    public function getItemUnitName($item_unit_id)
    {
        $itemunit = InvtItemUnit::where('data_state', 0)
            ->where('item_unit_id', $item_unit_id)
            ->first();
        return $itemunit['item_unit_name'];
    }
    public function getCoreSupplierName($supplier_id)
    {
        $supplier = CoreSupplier::where('data_state', 0)
            ->where('supplier_id', $supplier_id)
            ->first();
        return $supplier['supplier_name'];
    }
    public function getInvWarehouseName($warehouse_id)
    {
        $warehouse = InvtWarehouse::where('data_state', 0)
            ->where('warehouse_id', $warehouse_id)
            ->first();
        return $warehouse['warehouse_name'];
    }
    public function getAccountName($account_id)
    {
        $account = AcctAccount::where('data_state', 0)
            ->where('account_id', $account_id)
            ->first();
        return $account['account_name'] ?? '';
    }
    public function getCoreBankName($bank_id)
    {
        $bank = CoreBank::where('data_state', 0)
            ->where('bank_id', $bank_id)
            ->first();
        if ($bank) {
            return $bank['bank_name'];
        } else {
            return '';
        }
    }
    public function addCoreBank(Request $request)
    {
        $bank_code          = $request->bank_code;
        $bank_name          = $request->bank_name;
        $account_id         = $request->account_id;
        $bank_remark        = $request->bank_remark;
        $data               = '';
        $corebank = CoreBank::create([
            'bank_code'     => $bank_code,
            'bank_name'     => $bank_name,
            'account_id'    => $account_id,
            'bank_remark'   => $bank_remark,
            'created_id'    => Auth::id()
        ]);
        $corebank = CoreBank::where('data_state', 0)
            ->get();
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($corebank as $mp) {
            $data .= "<option value='$mp[bank_id]'>$mp[bank_name]</option>\n";
        }
        return $data;
    }
    public function searchPurchasePayment()
    {
        Session::forget('purchasepaymentelements');
        $coresupplier = PurchaseInvoice::with('supplier')->select('*', DB::raw("SUM(purchase_invoice.owing_amount) as total_owing_amount"), DB::raw("SUM(purchase_invoice.return_amount) as total_return_amount"))->where('data_state', 0)
            ->groupBy('supplier_id')
            ->get();
        return view('content.PurchasePayment.SearchCoreSupplier', compact('coresupplier'));
    }
}

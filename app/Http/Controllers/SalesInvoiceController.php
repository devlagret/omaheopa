<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use App\Models\SalesCustomer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SalesInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        Session::forget('arraydatases');
        $data = SalesInvoice::where('data_state',0)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.SalesInvoice.ListSalesInvoice',compact('data', 'start_date', 'end_date'));
    }

    public function addSalesInvoice()
    {
        $arraydatases   = Session::get('arraydatases');
        $date           = date('Y-m-d');
        $items          = InvtItem::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_name','item_id');
        $units          = InvtItemUnit::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        $categorys      = InvtItemCategory::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_category_name','item_category_id');
        $customers      = SalesCustomer::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('customer_name','customer_id');
        return view('content.SalesInvoice.FormAddSalesInvoice',compact('date','categorys','items','units','arraydatases','customers'));
    }

    public function addArraySalesInvoice(Request $request)
    {
        $request->validate([
            'item_category_id'                  => 'required',
            'item_unit_id'                      => 'required',
            'item_id'                           => 'required',
            'item_unit_price'                   => 'required',
            'quantity'                          => 'required',
            'subtotal_amount'                   => 'required',
            'subtotal_amount_after_discount'    => 'required'
        ]);
        if (empty($request->discount_percentage)){
            $discount_percentage = 0;
            $discount_amount = 0;
        }else{
            $discount_percentage = $request->discount_percentage;
            $discount_amount = $request->discount_amount;
        }
        $arraydatases = array(
            'item_category_id'                  => $request->item_category_id,
            'item_unit_id'                      => $request->item_unit_id,
            'item_id'                           => $request->item_id,
            'item_unit_price'                   => $request->item_unit_price,
            'quantity'                          => $request->quantity,
            'subtotal_amount'                   => $request->subtotal_amount,
            'discount_percentage'               => $discount_percentage,
            'discount_amount'                   => $discount_amount,
            'subtotal_amount_after_discount'    => $request->subtotal_amount_after_discount
        );

        $lastdatases = Session::get('arraydatases');
        if($lastdatases !== null){
            array_push($lastdatases, $arraydatases);
            Session::put('arraydatases', $lastdatases);
        } else {
            $lastdatases = [];
            array_push($lastdatases, $arraydatases);
            Session::push('arraydatases', $arraydatases);
        }
        Session::put('editarraystate',1);

        return redirect('/sales-invoice/add');
    }

    public function deleteArraySalesInvoice($record_id)
    {
        $arrayBaru = array();
        $dataArrayHeader = Session::get('arraydatases');

        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }

        Session::forget('arraydatases');
        Session::put('arraydatases', $arrayBaru);

        return redirect('/sales-invoice/add');
    }

    public function processAddSalesInvoice(Request $request)
    {
        $transaction_module_code = 'PJL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'sales_invoice_date'        => 'required',
            'subtotal_item'            => 'required',
            'subtotal_amount1'           => 'required',
            'total_amount'              => 'required',
            'paid_amount'               => 'required',
            'change_amount'             => 'required'
        ]);
        if (empty($request->discount_percentage_total)){
            $discount_percentage_total = 0;
            $discount_amount_total = 0;
        }else{
            $discount_percentage_total = $request->discount_percentage_total;
            $discount_amount_total = $request->discount_amount_total;
        }
        $data = array(
            'customer_id'               => $request->customer_id,
            'sales_invoice_date'        => $fields['sales_invoice_date'],
            'subtotal_item'             => $fields['subtotal_item'],
            'subtotal_amount'           => $fields['subtotal_amount1'],
            'discount_percentage_total' => $discount_percentage_total,
            'discount_amount_total'     => $discount_amount_total,
            'total_amount'              => $fields['total_amount'],
            'paid_amount'               => $fields['paid_amount'],
            'change_amount'             => $fields['change_amount'],
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        );
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => $fields['sales_invoice_date'],
            'journal_voucher_period'        => date('Ym'),
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );

        if(SalesInvoice::create($data) && JournalVoucher::create($journal)){
            $sales_invoice_id   = SalesInvoice::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();
            $arraydatases       = Session::get('arraydatases');
            foreach ($arraydatases as $key => $val) {
                $dataarray = array(
                    'sales_invoice_id'                  => $sales_invoice_id['sales_invoice_id'],
                    'item_category_id'                  => $val['item_category_id'],
                    'item_unit_id'                      => $val['item_unit_id'],
                    'item_id'                           => $val['item_id'],
                    'quantity'                          => $val['quantity'],
                    'item_unit_price'                   => $val['item_unit_price'],
                    'subtotal_amount'                   => $val['subtotal_amount'],
                    'discount_percentage'               => $val['discount_percentage'],
                    'discount_amount'                   => $val['discount_amount'],
                    'subtotal_amount_after_discount'    => $val['subtotal_amount_after_discount'],
                    'company_id'                        => Auth::user()->company_id,
                    'created_id'                        => Auth::id(),
                    'updated_id'                        => Auth::id()
                );
                SalesInvoiceItem::create($dataarray);
                $stock_item = InvtItemStock::where('item_id',$dataarray['item_id'])
                ->where('item_category_id',$dataarray['item_category_id'])
                ->where('item_unit_id', $dataarray['item_unit_id'])
                ->where('company_id', Auth::user()->company_id)
                ->first();
                if(isset($stock_item)){
                    $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                    $table->last_balance = $stock_item['last_balance'] - $dataarray['quantity'];
                    $table->updated_id = Auth::id();
                    $table->save();

                }
            }

            $account_setting_name = 'sales_cash_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $fields['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $fields['total_amount'];
            }
            $journal_debit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $fields['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_debit);

            $account_setting_name = 'sales_account';
            $account_id = $this->getAccountId($account_setting_name);
            $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            $account_default_status = $this->getAccountDefaultStatus($account_id);
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            if ($account_setting_status == 0){
                $debit_amount = $fields['total_amount'];
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $fields['total_amount'];
            }
            $journal_credit = array(
                'company_id'                    => Auth::user()->company_id,
                'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $fields['total_amount'],
                'account_id_default_status'     => $account_default_status,
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            );
            JournalVoucherItem::create($journal_credit);

            $msg = 'Tambah Invoice Penjualan Berhasil';
            return redirect('/sales-invoice/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Invoice Penjualan Gagal';
            return redirect('/sales-invoice/add')->with('msg',$msg);
        }
    }

    public function resetSalesInvoice()
    {
        Session::forget('arraydatases');

        return redirect('/sales-invoice/add');
    }

    public function getItemName($item_id)
    {
        $data   = InvtItem::where('item_id', $item_id)->first();

        return $data['item_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id', $item_category_id)->first();
        return $data['item_category_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();
        return $data['item_unit_name'];
    }

    public function detailSalesInvoice($sales_invoice_id)
    {
        $salesinvoice = SalesInvoice::where('sales_invoice_id', $sales_invoice_id)->first();
        $salesinvoiceitem = SalesInvoiceItem::where('sales_invoice_id', $sales_invoice_id)->get();
        return view('content.SalesInvoice.FormDetailSalesInvoice', compact('salesinvoice','salesinvoiceitem'));
    }

    public function deleteSalesInvoice($sales_invoice_id)
    {
        $transaction_module_code = 'KRPJL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $sales_invoice = SalesInvoice::where('sales_invoice_id', $sales_invoice_id)->first();
        $journal_voucher = JournalVoucherItem::where('created_at', $sales_invoice['created_at'])->where('company_id',Auth::user()->company_id)->first();
        // dd($journal_voucher['journal_voucher_amount']);
        $journal = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
            'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
            'transaction_module_id'         => $transaction_module_id,
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => date('Y-m-d'),
            'journal_voucher_period'        => date('Ym'),
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucher::create($journal);
            
        $account_setting_name = 'sales_cash_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if($account_setting_status == 0){
            $account_setting_status = 1;
        } else {
            $account_setting_status = 0;
        }
        if ($account_setting_status == 0){ 
            $debit_amount = $journal_voucher['journal_voucher_amount'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $journal_voucher['journal_voucher_amount'];
        }
        $journal_debit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_debit);

        $account_setting_name = 'sales_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if($account_setting_status == 1){
            $account_setting_status = 0;
        } else {
            $account_setting_status = 1;
        }
        if ($account_setting_status == 0){
            $debit_amount = $journal_voucher['journal_voucher_amount'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $journal_voucher['journal_voucher_amount'];
        }
        $journal_credit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_credit);

        $table_sales_invoice = SalesInvoice::findOrFail($sales_invoice['sales_invoice_id']);
        $table_sales_invoice->data_state = 1;
        $table_sales_invoice->updated_id = Auth::id();

        if($table_sales_invoice->save()){
            $msg = "Hapus Penjualan Berhasil";
            return redirect('/sales-invoice')->with('msg',$msg);
        } else {
            $msg = "Hapus Penjualan Gagal";
            return redirect('/sales-invoice')->with('msg',$msg);
        }
    }
    
    public function filterSalesInvoice(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date',$end_date);

        return redirect('/sales-invoice');
    }

    public function filterResetSalesInvoice()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-invoice');
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'];
    }

    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

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
        $data = AcctAccount::where('account_id',$account_id)->first();

        return $data['account_default_status'];
    }

    public function getCustomerName($customer_id)
    {
        $data = SalesCustomer::where('customer_id', $customer_id)->first();

        return $data['customer_name'];
    }
}

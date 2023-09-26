<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DownPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $status = AppHelper::status();
        $filter = Session::get('filter-dp');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status','!=',0)
        ->where('sales_order_type','=',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.ListDownPayment')->with(['booking'=>$booking,'status'=>$status,'start_date'=>$filter['start_date']??Carbon::now()->format('Y-m-d'),'end_date'=>$filter['end_date']??Carbon::now()->format('Y-m-d')]);
    }
    public function filter(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-dp',$data);
        return redirect()->route('dp.index');
    }
    public function filterAdd(Request $request) {
        $data = [
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date
        ];
        Session::put('filter-dp-add',$data);
        return redirect()->route('dp.add');
    }
    public function processAdd($sales_order_id,$source=null){
        $token = Session::get('dp-token-si');
        if(empty(Session::get('dp-token-si'))){
            if($source == 'booking'){
                return redirect()->route('booking.index')->with('msg','Tambah Booking Berhasil  -.');
            }
            elseif($source == 'direct'){
                return redirect()->route('cc.index')->with('msg','Tambah Check-In Berhasil-');
            }
            return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Berhasil-');
        }
        $order = SalesOrder::find($sales_order_id);
        try{
        DB::beginTransaction();
            SalesInvoice::create([
                'total_amount' => $order->sales_order_price,
                'sales_invoice_token' => $token,
                'sales_invoice_date' => Carbon::now()->format('Y-m-d'),
                'created_id' => Auth::id(),
                'company_id' => Auth::user()->company_id,
                'merchant_id' =>  empty(Auth::user()->merchant_id)?1:Auth::user()->merchant_id,
            ]);
        $si = SalesInvoice::where('sales_invoice_token',$token)->first();
        // * Update sales order
        $order->sales_invoice_id = $si->sales_invoice_id;
        if($source == 'booking'){
            $order->sales_order_status = 1;
            $order->save();
            DB::rollBack();
            return redirect()->route('booking.index')->with('msg','Tambah Booking Berhasil  -.');
        }
        // * buat journal dp

        //----------------------------------------------------------Journal Voucher-------------------------------------------------------------------//
            
        $preferencecompany 			= PreferenceCompany::first();
        
        $transaction_module_code 	= "DP";

        $transactionmodule 		    = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
        ->first();

        $transaction_module_id 		= $transactionmodule['transaction_module_id'];

        $journal_voucher_period 	= date("Ym", strtotime($order['order_date']));

        $data_journal = array(
            'branch_id'						=> 1,
            'journal_voucher_period' 		=> $journal_voucher_period,
            'journal_voucher_date'			=> $order['order_date'],
            'journal_voucher_title'			=> 'Down Payment '.$order['sales_order_no'],
            'journal_voucher_no'			=> $order['sales_order_no'],
            'journal_voucher_description'	=> 'Uang Muka',
            'transaction_module_id'			=> $transaction_module_id,
            'transaction_module_code'		=> $transaction_module_code,
            'transaction_journal_id' 		=> $order['sales_order_id'],
            'transaction_journal_no' 		=> $order['sales_order_no'],
            'created_id' 					=> Auth::id(),
            'company_id' 					=> 1,
        );
        
        JournalVoucher::create($data_journal);
// //---------------------------------------------------------End Journal Voucher----------------------------------------------------------------//



        // JournalVoucher::create([
        //     'journal_voucher_token' => $token,
        //     'transaction_module_code' => 'BDP',
        //     'journal_voucher_description'=> 'Booking Down Payment'
        // ]);
         //
        $jv = JournalVoucher::where('journal_voucher_token',$token)->first();
        //* buat journal item
        // JournalVoucherItem::create([
        //     // 'merchat_id' => 1,
        //     'journal_voucher_id'=>$jv->journal_voucher_id,
        // ]);
 //----------------------------------------------------------Journal Voucher  item-------------------------------------------------------------------//
        $account_setting_name = 'pre_operation_cost_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if ($account_setting_status == 0){
            $debit_amount = $order['down_payment'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $order['down_payment'];
        }
        $journal_debit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $order['down_payment'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'created_id'                    => Auth::id(),
            'updated_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_debit);

        $account_setting_name = 'down_payment_account';
        $account_id = $this->getAccountId($account_setting_name);
        $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        $account_default_status = $this->getAccountDefaultStatus($account_id);
        $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        if ($account_setting_status == 0){
            $debit_amount = $order['down_payment'];
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $order['down_payment'];
        }
        $journal_credit = array(
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            'account_id'                    => $account_id,
            'journal_voucher_amount'        => $order['down_payment'],
            'account_id_default_status'     => $account_default_status,
            'account_id_status'             => $account_setting_status,
            'journal_voucher_debit_amount'  => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'created_id'                    => Auth::id(),
            'updated_id'                    => Auth::id()
        );
        JournalVoucherItem::create($journal_credit);
 //----------------------------------------------------------end Journal Voucher  item-------------------------------------------------------------------//

        //
        $order->sales_order_status = 1;
        $order->save();
        DB::commit();
        return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Berhasil');
    }catch(\Exception $e){
            DB::rollBack();
            report($e);
            if($source == 'booking'){
                return redirect()->route('booking.index')->with('msg','Tambah Booking Gagal  -.');
            }
            return redirect()->route('dp.index')->with('msg','Bayar Uang Muka Gagal');
        }
    }
    public function add() {
        Session::put('dp-token-si',Str::uuid());
        $filter = Session::get('filter-dp-add');
        $booking = SalesOrder::with('rooms')->where('data_state',0)
        ->where('sales_order_status',0)
        ->where('checkin_date','>=',$filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('checkin_date','<=',$filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.DownPayment.AddDownPayment')->with(['booking'=>$booking,'start_date'=>$filter['start_date']??Carbon::now()->format('Y-m-d'),'end_date'=>$filter['end_date']??Carbon::now()->format('Y-m-d')]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\JournalHelper;
use App\Helpers\StockHelper;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\CoreReservation;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PaymentReservation;
use App\Models\PreferenceTransactionModule;
use App\Models\SalesCustomer;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesInvoiceReservation;
use App\Models\SalesInvoiceReservationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SalesReservationController extends Controller
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
        $data = SalesInvoiceReservation::where('data_state',0)
        ->where('sales_invoice_reservation_date','>=',$start_date)
        ->where('sales_invoice_reservation_date','<=',$end_date)
        ->where('sales_status',1)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        // dd($data);
        return view('content.SalesReservation.ListSalesReservation',compact('data', 'start_date', 'end_date'));
    }

    public function addSalesReservation()
    {
        $arraydatases   = Session::get('arraydatases');
        $date           = date('Y-m-d');
        $reservations          = CoreReservation::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $units          = InvtItemUnit::where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('item_unit_name','item_unit_id');
        
        
        $customers = SalesCustomer::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('customer_name','customer_id');
        return view('content.SalesReservation.FormAddSalesReservation',compact('date','units','arraydatases','customers','reservations'));
    }

    public function addArraySalesReservation(Request $request)
    {
        $request->validate([
            // 'item_category_id'                  => 'required',
            'reservation_id'                    => 'required',
            'reservation_price'                 => 'required',
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
            // 'item_category_id'                  => $request->item_category_id,
            'reservation_id'                    => $request->reservation_id,
            'reservation_price'                 => $request->reservation_price,
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

        // $salesinvoice = Session::get('salesinvoice');
        // Session::push('salesinvoice', $salesinvoice);

        return redirect('/sales-reservation/add');
    }
    public function deleteArraySalesReservation($record_id)
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

        return redirect('/sales-reservation/add');
    }
    public function processAddSalesReservation(Request $request)
    {
        // dd($request->all());
        // $transaction_module_code = 'SI';
        // $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'sales_invoice_reservation_date'        => 'required',
            'sales_invoice_reservation_due_date'    => 'required',
            'subtotal_item'                         => 'required',
            'subtotal_amount1'                      => 'required',
            'total_amount'                          => 'required',
            'paid_amount'                           => 'required',
            'change_amount'                         => 'required'
        ]);
        if (empty($request->discount_percentage_total)){
            $discount_percentage_total = 0;
            $discount_amount_total = 0;
        }else{
            $discount_percentage_total = $request->discount_percentage_total;
            $discount_amount_total = $request->discount_amount_total;
        }
        $data = array(
            'customer_name'             => $request->customer_name,
            'customer_phone'            => $request->customer_phone,
            'customer_address'          => $request->customer_address,
            'sales_name'                => $request->sales_name,
            // 'merchant_id'               => $request->merchant_id,
            'sales_invoice_reservation_date'        => $fields['sales_invoice_reservation_date'],
            'sales_invoice_reservation_due_date'        => $fields['sales_invoice_reservation_due_date'],
            'subtotal_item'             => $fields['subtotal_item'],
            'subtotal_amount'           => $fields['subtotal_amount1'],
            'discount_percentage_total' => $discount_percentage_total,
            'discount_amount_total'     => $discount_amount_total,
            'total_amount'              => $fields['total_amount'],
            'paid_amount'               => $fields['paid_amount'],
            'owing_amount'              => $fields['change_amount'],
            'sales_status'              => 1,
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        );
        // $journal = array(
        //     'company_id'                    => Auth::user()->company_id,
        //     'journal_voucher_status'        => 1,
        //     'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
        //     'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
        //     'transaction_module_id'         => $transaction_module_id,
        //     'transaction_module_code'       => $transaction_module_code,
        //     'journal_voucher_date'          => $fields['sales_invoice_date'],
        //     'journal_voucher_period'        => date('Ym'),
        //     'updated_id'                    => Auth::id(),
        //     'created_id'                    => Auth::id()
        // );
        
        try {
        DB::beginTransaction();
        if(SalesInvoiceReservation::create($data)){
            // if(SalesInvoice::create($data)){
            $sales_invoice_reservation_id   = SalesInvoiceReservation::orderBy('created_at','DESC')->where('company_id', Auth::user()->company_id)->first();
        
            //*jurnal
            JournalHelper::trsJournalNo($sales_invoice_reservation_id->sales_invoice_reservation_no)->title('DP Reservasi',1)->appendTitle($sales_invoice_reservation_id->sales_invoice_reservation_no,1)->make('Sales Reservation', $request->total_amount, ['sales_reservation_cash_account','sales_reservation_account']);
            
            $arraydatases       = Session::get('arraydatases');
            foreach ($arraydatases as $key => $val) {
                $dataarray = array(
                    'sales_invoice_reservation_id'      => $sales_invoice_reservation_id['sales_invoice_reservation_id'],
                    'reservation_id'                    => $val['reservation_id'],
                    'quantity'                          => $val['quantity'],
                    'reservation_price'                 => $val['reservation_price'],
                    'subtotal_amount'                   => $val['subtotal_amount'],
                    'discount_percentage'               => $val['discount_percentage'],
                    'discount_amount'                   => $val['discount_amount'],
                    'subtotal_amount_after_discount'    => $val['subtotal_amount_after_discount'],
                    'company_id'                        => Auth::user()->company_id,
                    'created_id'                        => Auth::id(),
                    'updated_id'                        => Auth::id()
                );
                // dump([(int) $val['quantity'],$val['item_unit_id']]);
                // $qty = (int) $val['quantity'];
                // dump(StockHelper::find($val['item_id']));
                // dump(StockHelper::find(3)->sub(5,$val['item_unit_id']));
                // dump(StockHelper::find($val['item_id']));
                // dd($dataarray); 
                // StockHelper::find($val['item_id'])->sub((int)$val['quantity'],$val['item_unit_id']);
                SalesInvoiceReservationItem::create($dataarray);
                // $stock_item = InvtItemStock::where('item_id',$dataarray['item_id'])
                // ->where('item_category_id',$dataarray['item_category_id'])
                // ->where('item_unit_id', $dataarray['item_unit_id'])
                // ->where('company_id', Auth::user()->company_id)
                // ->first();
                // if(isset($stock_item)){
                //     $table = InvtItemStock::findOrFail($stock_item['item_stock_id']);
                //     $table->last_balance = $stock_item['last_balance'] - $dataarray['quantity'];
                //     $table->updated_id = Auth::id();
                //     $table->save();

                // }
                //stock update
            }


            

            // $account_setting_name = 'sales_cash_account';
            // $account_id = $this->getAccountId($account_setting_name);
            // $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            // $account_default_status = $this->getAccountDefaultStatus($account_id);
            // $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            // if ($account_setting_status == 0){
            //     $debit_amount = $fields['total_amount'];
            //     $credit_amount = 0;
            // } else {
            //     $debit_amount = 0;
            //     $credit_amount = $fields['total_amount'];
            // }
            // $journal_debit = array(
            //     'company_id'                    => Auth::user()->company_id,
            //     'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            //     'account_id'                    => $account_id,
            //     'journal_voucher_amount'        => $fields['total_amount'],
            //     'account_id_default_status'     => $account_default_status,
            //     'account_id_status'             => $account_setting_status,
            //     'journal_voucher_debit_amount'  => $debit_amount,
            //     'journal_voucher_credit_amount' => $credit_amount,
            //     'updated_id'                    => Auth::id(),
            //     'created_id'                    => Auth::id()
            // );
            // JournalVoucherItem::create($journal_debit);

            // $account_setting_name = 'sales_account';
            // $account_id = $this->getAccountId($account_setting_name);
            // $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
            // $account_default_status = $this->getAccountDefaultStatus($account_id);
            // $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            // if ($account_setting_status == 0){
            //     $debit_amount = $fields['total_amount'];
            //     $credit_amount = 0;
            // } else {
            //     $debit_amount = 0;
            //     $credit_amount = $fields['total_amount'];
            // }
            // $journal_credit = array(
            //     'company_id'                    => Auth::user()->company_id,
            //     'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
            //     'account_id'                    => $account_id,
            //     'journal_voucher_amount'        => $fields['total_amount'],
            //     'account_id_default_status'     => $account_default_status,
            //     'account_id_status'             => $account_setting_status,
            //     'journal_voucher_debit_amount'  => $debit_amount,
            //     'journal_voucher_credit_amount' => $credit_amount,
            //     'updated_id'                    => Auth::id(),
            //     'created_id'                    => Auth::id()
            // );
            // JournalVoucherItem::create($journal_credit);
            }
            // dd("1");
            DB::commit();
            $msg = 'Tambah Reservasi Penjualan Berhasil';
            return redirect('/sales-reservation')->with('msg',$msg);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            dd($e);
            Session::forget('purchase-token');
            $msg = 'Tambah Reservasi Penjualan Gagal';
            return redirect('/sales-reservation/add')->with('msg',$msg);
            }

    }
    public function getCoreItem(Request $request){
        $item_category_id   = $request->item_category_id;
        $data='';
        

        $item = InvtItem::select(DB::raw("invt_item.item_id, invt_item.item_name AS item_name"))
        ->where('invt_item.item_category_id', $item_category_id)
        // ->where('invt_item.merchant_id', Auth::merchant_id())
        ->where('invt_item.data_state','=',0)
        ->get();

        $data .= "<option value=''>--Choose One--</option>";
        //     $data .= "<option value='0'>".$item['item_name']."</option>\n";
        foreach ($item as $mp){
            $data .= "<option value='$mp[item_id]'>$mp[item_name]</option>\n";	
        }

        return $data;
    }
    public function getSelectDataUnit(Request $request){
        $item_id   = $request->item_id;

        $invt_item= InvtItem::where('item_id', $item_id)
        ->first();
        
        $data= '';

        if($invt_item != null){
            $unit1 = InvtItem::select('invt_item.item_unit_id1','invt_item_unit.*')
            ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'invt_item.item_unit_id1')
            ->where('invt_item.item_unit_id1', $invt_item['item_unit_id1'])
            // ->where('invt_item.item_unit_2', $invt_item['item_unit_2'])
            // ->where('invt_item.item_unit_3', $invt_item['item_unit_3'])
            ->first();
            
            // return $unit1;
            $unit2 = InvtItem::select('invt_item.item_unit_id2','invt_item_unit.*')
            ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'invt_item.item_unit_id2')
            ->where('invt_item.item_unit_id2', $invt_item['item_unit_id2'])
            ->first();

            $unit3 = InvtItem::select('invt_item.item_unit_id3','invt_item_unit.*')
            ->join('invt_item_unit', 'invt_item_unit.item_unit_id', '=', 'invt_item.item_unit_id3')
            ->where('invt_item.item_unit_id3', $invt_item['item_unit_id3'])
            ->first();
        

        $array = [];
        if($unit1){
            array_push($array, $unit1);
        }
        if($unit2){
            array_push($array, $unit2);
        }
        if($unit3){
            array_push($array, $unit3);
        }
        // $unit = array_merge($unit1, $unit2);
        // $unit4 = array_merge($unit, $unit3);
        
        
        $data .= "<option value=''>--Choose One--</option>";
        foreach ($array as $val){
            print_r($val['item_unit_id']);
            
            $data .= "<option value='$val[item_unit_id]'>$val[item_unit_name]</option>\n";	
        }
        return $data;
        }
    }
    public function resetSalesInvoice()
    {
        Session::forget('arraydatases');

        return redirect('/sales-invoice/add');
    }
    public function getItemName($reservation_id)
    {
        $data   = CoreReservation::where('reservation_id', $reservation_id)->first();

        return $data['reservation_name']?? '';
    }
    public function getReservationName($reservation_id)
    {
        $data = CoreReservation::where('reservation_id', $reservation_id)->first();
        return $data['reservation_name']?? '';
    }
    public function getReservationPrice($reservation_id)
    {
        $data = CoreReservation::where('reservation_id', $reservation_id)->first();
        return $data['reservation_price']?? '';
    }
    public function detailSalesReservation($sales_invoice_reservation_id)
    {
        $salesinvoicereservation = SalesInvoiceReservation::where('sales_invoice_reservation_id', $sales_invoice_reservation_id)->first();
        
        $salesinvoicereservationitem = SalesInvoiceReservationItem::where('sales_invoice_reservation_id', $sales_invoice_reservation_id)->get();
        
        $paymentreservation = PaymentReservation::where('sales_invoice_reservation_id', $sales_invoice_reservation_id)->get();
        // dd($paymentreservation);
        
        return view('content.SalesReservation.FormDetailSalesReservation', compact('salesinvoicereservation','salesinvoicereservationitem','paymentreservation'));
    }
    public function deleteSalesReservation($sales_invoice_reservation_id)
    {
        $transaction_module_code = 'HPSPJL';
        $transaction_module_id  = $this->getTransactionModuleID($transaction_module_code);
        $sales_invoice_reservation = SalesInvoiceReservation::where('sales_invoice_reservation_id', $sales_invoice_reservation_id)->first();
        // $journal_voucher = JournalVoucherItem::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        // // dd($sales_invoice);
        // // dd($journal_voucher['journal_voucher_amount']);
        // $journal = array(
        //     'company_id'                    => Auth::user()->company_id,
        //     'journal_voucher_status'        => 1,
        //     'journal_voucher_description'   => $this->getTransactionModuleName($transaction_module_code),
        //     'journal_voucher_title'         => $this->getTransactionModuleName($transaction_module_code),
        //     'transaction_module_id'         => $transaction_module_id,
        //     'transaction_module_code'       => $transaction_module_code,
        //     'journal_voucher_date'          => date('Y-m-d'),
        //     'journal_voucher_period'        => date('Ym'),
        //     'updated_id'                    => Auth::id(),
        //     'created_id'                    => Auth::id()
        // );
        // JournalVoucher::create($journal);
            
        // $account_setting_name = 'sales_cash_account';
        // $account_id = $this->getAccountId($account_setting_name);
        // $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        // $account_default_status = $this->getAccountDefaultStatus($account_id);
        // $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        // if($account_setting_status == 0){
        //     $account_setting_status = 1;
        // } else {
        //     $account_setting_status = 0;
        // }
        // if ($account_setting_status == 0){ 
        //     $debit_amount = $journal_voucher['journal_voucher_amount'];
        //     $credit_amount = 0;
        // } else {
        //     $debit_amount = 0;
        //     $credit_amount = $journal_voucher['journal_voucher_amount'];
        // }
        // $journal_debit = array(
        //     'company_id'                    => Auth::user()->company_id,
        //     'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
        //     'account_id'                    => $account_id,
        //     'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
        //     'account_id_default_status'     => $account_default_status,
        //     'account_id_status'             => $account_setting_status,
        //     'journal_voucher_debit_amount'  => $debit_amount,
        //     'journal_voucher_credit_amount' => $credit_amount,
        //     'updated_id'                    => Auth::id(),
        //     'created_id'                    => Auth::id()
        // );
        // JournalVoucherItem::create($journal_debit);

        // $account_setting_name = 'sales_account';
        // $account_id = $this->getAccountId($account_setting_name);
        // $account_setting_status = $this->getAccountSettingStatus($account_setting_name);
        // $account_default_status = $this->getAccountDefaultStatus($account_id);
        // $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
        // if($account_setting_status == 1){
        //     $account_setting_status = 0;
        // } else {
        //     $account_setting_status = 1;
        // }
        // if ($account_setting_status == 0){
        //     $debit_amount = $journal_voucher['journal_voucher_amount'];
        //     $credit_amount = 0;
        // } else {
        //     $debit_amount = 0;
        //     $credit_amount = $journal_voucher['journal_voucher_amount'];
        // }
        // $journal_credit = array(
        //     'company_id'                    => Auth::user()->company_id,
        //     'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
        //     'account_id'                    => $account_id,
        //     'journal_voucher_amount'        => $journal_voucher['journal_voucher_amount'],
        //     'account_id_default_status'     => $account_default_status,
        //     'account_id_status'             => $account_setting_status,
        //     'journal_voucher_debit_amount'  => $debit_amount,
        //     'journal_voucher_credit_amount' => $credit_amount,
        //     'updated_id'                    => Auth::id(),
        //     'created_id'                    => Auth::id()
        // );
        // JournalVoucherItem::create($journal_credit);

        $table_sales_invoice_reservation = SalesInvoiceReservation::findOrFail($sales_invoice_reservation['sales_invoice_reservation_id']);
        $table_sales_invoice_reservation->data_state = 1;
        $table_sales_invoice_reservation->updated_id = Auth::id();

        if($table_sales_invoice_reservation->save()){
            $msg = "Hapus Reservasi Berhasil";
            return redirect('/sales-reservation')->with('msg',$msg);
        } else {
            $msg = "Hapus Reservasi Gagal";
            return redirect('/sales-reservation')->with('msg',$msg);
        }
    }


    
    public function reject($sales_invoice_reservation_id)
    {
        $sales_invoice_reservation = SalesInvoiceReservation::where('sales_invoice_reservation_id', $sales_invoice_reservation_id)->first();
        $table_sales_invoice_reservation = SalesInvoiceReservation::findOrFail($sales_invoice_reservation['sales_invoice_reservation_id']);
        $table_sales_invoice_reservation->sales_invoice_reservation_status = 2;
        $table_sales_invoice_reservation->updated_id = Auth::id();

        if($table_sales_invoice_reservation->save()){
            $msg = "Batal Reservasi Berhasil";
            return redirect('/sales-reservation')->with('msg',$msg);
        } else {
            $msg = "Batal Reservasi Gagal";
            return redirect('/sales-reservation')->with('msg',$msg);
        }
    }



    public function addElementsSalesTiket(Request $request)
    {
        $salesinvoice  = Session::get('salesinvoice');
        if(!$salesinvoice || $salesinvoice == ''){
            $salesinvoice['customer_name'] = '';
        }
        $salesinvoice[$request->name] = $request->value;
        Session::put('salesinvoice', $salesinvoice);
    }
    public function filterSalesReservation(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date',$end_date);

        return redirect('/sales-reservation');
    }
    public function filterResetSalesReservation()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-reservation');
    }
    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'] ?? '';
    }
    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_name'] ?? '';
    }
    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_setting_status'] ?? '';
    }
    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
        ->where('account_setting_name', $account_setting_name)
        ->first();

        return $data['account_id'] ?? '';
    }
    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();

        return $data['account_default_status'] ?? '';
    }
    public function getCustomerName($customer_id)
    {
        $data = SalesCustomer::where('customer_id', $customer_id)->first();

        return $data['customer_name']?? '';
    }
    public function getUnitPrice(Request $request){

        $item_id            = $request->item_id;   

        $item = InvtItem::select('*')
        ->where('invt_item.item_id', $item_id)
        ->where('invt_item.data_state','=',0)
        ->first();

            if($item['item_unit_id1'] == $request->item_unit_id){
                $item_unit_price = $item['item_unit_price1'];
            }
            if($item['item_unit_id2'] == $request->item_unit_id){
                $item_unit_price = $item['item_unit_price2'];
            }
            if($item['item_unit_id3'] == $request->item_unit_id){
                $item_unit_price = $item['item_unit_price3'];
            }
            if($item['item_unit_id4'] == $request->item_unit_id){
                $item_unit_price = $item['item_unit_price4'];
            }
        return $item_unit_price;
    }
}

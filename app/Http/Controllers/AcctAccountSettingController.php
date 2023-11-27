<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcctAccountSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {

        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        // ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        $data= AcctAccountSetting::where('company_id',Auth::user()->company_id)->get();
        return view('content.AcctAccountSetting.AcctAccountSetting',compact('accountlist','status','data'));
    }

    public function processAddAcctAccountSetting(Request $request)
    {
// dd($request->all());


        $data = array(
            '1_account_id'               => $request->input('purchase_cash_account_id'),
            '1_account_setting_status'   => $request->input('purchase_cash_account_status'),
            '1_account_setting_name'     => 'purchase_account',
            '1_account_default_status'     => $this->getAccountDefault($request->input('purchase_cash_account_id')),

            '2_account_id'               => $request->input('account_cash_purchase_id'),
            '2_account_setting_status'   => $request->input('account_cash_purchase_status'),
            '2_account_setting_name'     => 'purchase_cash_account',
            '2_account_default_status'     => $this->getAccountDefault($request->input('account_cash_purchase_id')),

            '3_account_id'               => $request->input('purchase_return_account_id'),
            '3_account_setting_status'   => $request->input('purchase_return_account_status'),
            '3_account_setting_name'     => 'purchase_return_account',
            '3_account_default_status'     => $this->getAccountDefault($request->input('purchase_return_account_id')),

            '4_account_id'               => $request->input('account_payable_return_account_id'),
            '4_account_setting_status'   => $request->input('account_payable_return_account_status'),
            '4_account_setting_name'     => 'purchase_return_cash_account',
            '4_account_default_status'     => $this->getAccountDefault($request->input('account_payable_return_account_id')),

            '5_account_id'               => $request->input('sales_account_id'),
            '5_account_setting_status'   => $request->input('sales_account_status'),
            '5_account_setting_name'     => 'sales_account',
            '5_account_default_status'     => $this->getAccountDefault($request->input('sales_account_id')),

            '6_account_id'               => $request->input('account_receivable_account_id'),
            '6_account_setting_status'   => $request->input('account_receivable_account_status'),
            '6_account_setting_name'     => 'sales_cash_account',
            '6_account_default_status'     => $this->getAccountDefault($request->input('account_receivable_account_id')),

            '7_account_id'               => $request->input('expenditure_account_id'),
            '7_account_setting_status'   => $request->input('expenditure_account_status'),
            '7_account_setting_name'     => 'expenditure_account',
            '7_account_default_status'     => $this->getAccountDefault($request->input('expenditure_account_id')),

            '8_account_id'               => $request->input('expenditure_cash_account_id'),
            '8_account_setting_status'   => $request->input('expenditure_cash_account_status'),
            '8_account_setting_name'     => 'expenditure_cash_account',
            '8_account_default_status'     => $this->getAccountDefault($request->input('expenditure_cash_account_id')),

            '9_account_id'               => $request->input('hotel_account_id'),
            '9_account_setting_status'   => $request->input('hotel_account_status'),
            '9_account_setting_name'     => 'hotel_account',
            '9_account_default_status'   => $this->getAccountDefault($request->input('hotel_account_id')),

            '10_account_id'               => $request->input('hotel_cash_account_id'),
            '10_account_setting_status'   => $request->input('hotel_cash_account_status'),
            '10_account_setting_name'     => 'hotel_cash_account',
            '10_account_default_status'   => $this->getAccountDefault($request->input('hotel_cash_account_id')),

            '11_account_id'               => $request->input('purchase_payable_account_id'),
            '11_account_setting_status'   => $request->input('purchase_payable_account_status'),
            '11_account_setting_name'     => 'purchase_payable_account',
            '11_account_default_status'   => $this->getAccountDefault($request->input('purchase_payable_account_id')),

            '12_account_id'               => $request->input('purchase_cash_payable_account_id'),
            '12_account_setting_status'   => $request->input('purchase_cash_payable_account_status'),
            '12_account_setting_name'     => 'purchase_cash_payable_account',
            '12_account_default_status'   => $this->getAccountDefault($request->input('purchase_cash_payable_account_id')),

            '13_account_id'               => $request->input('purchase_payment_account_id'),
            '13_account_setting_status'   => $request->input('purchase_payment_account_status'),
            '13_account_setting_name'     => 'purchase_payment_account',
            '13_account_default_status'   => $this->getAccountDefault($request->input('purchase_payment_account_id')),

            '14_account_id'               => $request->input('purchase_cash_payment_account_id'),
            '14_account_setting_status'   => $request->input('purchase_cash_payment_account_status'),
            '14_account_setting_name'     => 'purchase_cash_payment_account',
            '14_account_default_status'   => $this->getAccountDefault($request->input('purchase_cash_payment_account_id')),

            '15_account_id'               => $request->input('purchase_non_cash_payment_account_id'),
            '15_account_setting_status'   => $request->input('purchase_non_cash_payment_account_status'),
            '15_account_setting_name'     => 'purchase_non_cash_payment_account',
            '15_account_default_status'   => $this->getAccountDefault($request->input('purchase_non_cash_payment_account_id')),

            '16_account_id'               => $request->input('purchase_non_cash_cash_payment_account_id'),
            '16_account_setting_status'   => $request->input('purchase_non_cash_cash_payment_account_status'),
            '16_account_setting_name'     => 'purchase_non_cash_cash_payment_account',
            '16_account_default_status'   => $this->getAccountDefault($request->input('purchase_non_cash_cash_payment_account_id')),


            '17_account_id'               => $request->input('sales_reservation_cash_account_id'),
            '17_account_setting_status'   => $request->input('sales_reservation_cash_reservation_account_status'),
            '17_account_setting_name'     => 'sales_reservation_cash_account',
            '17_account_default_status'   => $this->getAccountDefault($request->input('sales_reservation_cash_account_id')),

            '18_account_id'               => $request->input('sales_reservation_account_id'),
            '18_account_setting_status'   => $request->input('sales_reservation_account_status'), 
            '18_account_setting_name'     => 'sales_reservation_account',
            '18_account_default_status'   => $this->getAccountDefault($request->input('sales_reservation_account_id')),


            '19_account_id'               => $request->input('payment_reservation_cash_account_id'),
            '19_account_setting_status'   => $request->input('payment_reservation_cash_account_status'),
            '19_account_setting_name'     => 'payment_reservation_cash_account',
            '19_account_default_status'   => $this->getAccountDefault($request->input('payment_reservation_cash_account_id')),

            '20_account_id'               => $request->input('payment_reservation_account_id'),
            '20_account_setting_status'   => $request->input('payment_reservation_account_status'), 
            '20_account_setting_name'     => 'payment_reservation_account',
            '20_account_default_status'   => $this->getAccountDefault($request->input('payment_reservation_account_id')),


        );

        // dd($data);

        $company_id = AcctAccountSetting::where('company_id', Auth::user()->company_id)->first();
        for($key = 1; $key<=20;$key++){
            $data_item = array(
                'account_id' 				=> $data[$key."_account_id"],
                'account_setting_status'	=> $data[$key."_account_setting_status"],
                'account_setting_name' 		=> $data[$key."_account_setting_name"],
                'account_default_status'    => $data[$key."_account_default_status"],
                'company_id'                => Auth::user()->company_id
            );
            AcctAccountSetting::updateOrCreate([
                'account_setting_name'  => $data_item['account_setting_name'],
                'company_id'            => Auth::user()->company_id
            ],[
                'account_id' 			  => $data_item['account_id'],
                'account_setting_status'  => $data_item['account_setting_status'],
                'account_default_status'  => $data_item['account_default_status'],
            ]);
        }
        // if(!empty($company_id)){
        //     for($key = 1; $key<=8;$key++){
        //         $data_item = array(
        //             'account_id' 				=> $data[$key."_account_id"],
        //             'account_setting_status'	=> $data[$key."_account_setting_status"],
        //             'account_setting_name' 		=> $data[$key."_account_setting_name"],
        //             'account_default_status'    => $data[$key."_account_default_status"],
        //             'company_id'                => Auth::user()->company_id
        //         );
        //         AcctAccountSetting::where('account_setting_name',$data_item['account_setting_name'])
        //         ->where('company_id', Auth::user()->company_id)
        //         ->update($data_item);
        //     }
        // } else {
        //     for($key = 1; $key<=8;$key++){
        //         $data_item = array(
        //             'account_id' 				=> $data[$key."_account_id"],
        //             'account_setting_status'	=> $data[$key."_account_setting_status"],
        //             'account_setting_name' 		=> $data[$key."_account_setting_name"],
        //             'account_default_status'    => $data[$key."_account_default_status"],
        //             'company_id'                => Auth::user()->company_id
        //         );
        //         AcctAccountSetting::create($data_item);
        //     }
        // }
        $msg = 'Setting Jurnal Berhasil';
        return redirect('/acct-account-setting')->with('msg',$msg);

    }

    public function getAccountDefault($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_default_status'] ?? '';
    }
}

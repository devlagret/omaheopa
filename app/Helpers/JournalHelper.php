<?php
namespace App\Helpers;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
class JournalHelper extends AppHelper
{
    public static function make($token,string $journal_voucher_description, array $account_setting_name,int $total_amount,string $transaction_module_code = null){
        if(is_null($transaction_module_code)){
            $transaction_module_code = preg_replace('/[^A-Z]/', '',$journal_voucher_description);
        }
        JournalVoucher::create([
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $journal_voucher_description,
            'journal_voucher_title'         => AppHelper::getTransactionModule($transaction_module_code)->name??'',
            'transaction_module_id'         => AppHelper::getTransactionModule($transaction_module_code)->id??'',
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => Carbon::now()->format('Y-m-d'),
            'journal_voucher_period'        => Carbon::now()->format('Ym'),
            'created_id'                    => Auth::id(),
            'journal_voucher_token'         => $token,
        ]);
        $jv = JournalVoucher::where('journal_voucher_token',$token)->first();
        foreach ($account_setting_name as $name){
            $account_id = AppHelper::getAccountSetting($name)->account_id;
            $account_setting_status = AppHelper::getAccountSetting($name)->status;
            if ($account_setting_status == 0){
                $debit_amount = $total_amount;
                $credit_amount = 0;
            } else {
                $debit_amount = 0;
                $credit_amount = $total_amount;
            }
            //* buat journal item
            JournalVoucherItem::create([
                'merchat_id' => Auth::user()->merchant_id??1,
                'company_id'        => Auth::user()->company_id,
                'journal_voucher_id'=>$jv->journal_voucher_id,
                'account_id'                    => $account_id,
                'journal_voucher_amount'        => $total_amount,
                'account_id_default_status'     => AppHelper::getAccountDefaultStatus($account_id),
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            ]);
        }
    }
}
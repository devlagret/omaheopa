<?php
namespace App\Helpers;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class JournalHelper extends AppHelper
{
    protected static $token;
    protected static $journal_voucher_date;
    /**
     * Make journal voucher and journal voucher item
     *
     * @param [type] token
     * @param string $journal_voucher_description
     * @param array $account_setting_name
     * @param integer $total_amount
     * @param string|null $transaction_module_code
     * @return void
     */
    public static function make(string $journal_voucher_description, array $account_setting_name,int $total_amount,string $transaction_module_code = null){
        if(is_null($transaction_module_code)){
            $transaction_module_code = preg_replace('/[^A-Z]/', '',$journal_voucher_description);
        }
        $token = self::$token;
        if(empty($token)){
            $token = Str::uuid();
        }
        $date = self::$journal_voucher_date;
        if(empty($date)){
            $jvd=Carbon::now()->format('Y-m-d');
            $jvp=Carbon::now()->format('Ym');
        }else{
            $jvd=Carbon::parse($date)->format('Y-m-d');
            $jvp=Carbon::parse($date)->format('Ym');
        }
        JournalVoucher::create([
            'company_id'                    => Auth::user()->company_id,
            'journal_voucher_status'        => 1,
            'journal_voucher_description'   => $journal_voucher_description,
            'journal_voucher_title'         => self::getTransactionModule($transaction_module_code)->name??'',
            'transaction_module_id'         => self::getTransactionModule($transaction_module_code)->id??'',
            'transaction_module_code'       => $transaction_module_code,
            'journal_voucher_date'          => $jvd,
            'journal_voucher_period'        => $jvp,
            'created_id'                    => Auth::id(),
            'journal_voucher_token'         => $token,
        ]);
        $jv = JournalVoucher::where('journal_voucher_token',$token)->first();
        foreach ($account_setting_name as $name){
            $account_id = self::getAccountSetting($name)->account_id;
            $account_setting_status = self::getAccountSetting($name)->status;
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
                'account_id_default_status'     => self::getAccountDefaultStatus($account_id),
                'account_id_status'             => $account_setting_status,
                'journal_voucher_debit_amount'  => $debit_amount,
                'journal_voucher_credit_amount' => $credit_amount,
                'updated_id'                    => Auth::id(),
                'created_id'                    => Auth::id()
            ]);
        }
    }
    /**
     * Reverse journal
     *
     * @param integer $journal_voucher_id
     * @return void
     */
    public static function reverse(int $journal_voucher_id){
        $token = Str::uuid();
        $journal = JournalVoucher::with('items')->find($journal_voucher_id);
        JournalVoucher::create([
            'company_id' => $journal['company_id'],
            'transaction_module_id' => $journal['transaction_module_id'],
            'journal_voucher_status' => $journal['journal_voucher_status'],
            'transaction_journal_no' =>  $journal['transaction_journal_no'],
            'transaction_module_code' => 'H'.$journal['transaction_module_code'],
            'journal_voucher_date' =>date('Y-m-d'),
            'journal_voucher_description' =>  'Hapus '.$journal['journal_voucher_description'],
            'journal_voucher_period' => $journal['journal_voucher_period'],
            'journal_voucher_title' =>  'Hapus '. $journal['journal_voucher_title'],
            "data_state" => $journal['data_state'],
            "journal_voucher_token" => $token,
            "reverse_state" => 1,
            'created_id' => Auth::id()
        ]);
        $journal->reverse_state = 1;
        $journal->save();
        $jv = JournalVoucher::where('journal_voucher_token', $token)->first();
        foreach ($journal->items as $key ){
        JournalVoucherItem::create([
            'company_id' => $key['company_id'],
            'journal_voucher_id' => $jv['journal_voucher_id'],
            'account_id' => $key['account_id'],
            'journal_voucher_amount' => $key['journal_voucher_amount'],
            'account_id_status' => (1-$key['account_id_status']),
            'account_id_default_status' => $key['account_id_default_status'],
            'journal_voucher_debit_amount' => $key['journal_voucher_credit_amount'],
            'journal_voucher_credit_amount' => $key['journal_voucher_debit_amount'],
            "data_state" => $key['data_state'],
            "reverse_state" => 1,
            'updated_id' => Auth::id(),
            'created_id' => Auth::id()
        ]);
        }
        $journal->items()->update(['acct_journal_voucher_item.reverse_state' => 1]);
    }

    /**
     * Set the value of token
     *
     * @return  self
     */ 
    public static function token($token)
    {
        self::$token = $token;
        return new self;
    }

    /**
     * Set Journal date
     *
     * @return  self
     */ 
    public static function date($journal_voucher_date)
    {
        $date = Carbon::parse($journal_voucher_date)->format('Ym');
        $now = Carbon::now()->format('Ym');
        if($date<$now){
            throw new \Exception("Can't Back Date");
        }
        self::$journal_voucher_date = $journal_voucher_date;
        return new self;
    }
}
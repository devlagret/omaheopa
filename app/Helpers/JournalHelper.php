<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\AcctAccount;
use Illuminate\Support\Str;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class JournalHelper extends AppHelper
{
    protected static $token;
    protected static $journal_voucher_date;
    protected static $journal_voucher_id;
    protected static $appendTitle;
    protected static $prependTitle;
    protected static $appendDescription;
    protected static $prependDescription;
    protected static $title;
    protected static $description;
    protected static $defaultTitle = '';
    protected static $account_id;
    protected static $total_amount;
    protected static $merchant_id;
    protected static $transaction_journal_no;
    /**
     * Make journal voucher and journal voucher item
     * leave account_setting_name empty to return self
     * @param string $journal_voucher_description
     * @param array $account_setting_name
     * @param integer $total_amount
     * @param string|null $transaction_module_code
     * @return void|self
     */
    public static function make(string $journal_voucher_description, int $total_amount, array $account_setting_name = [], string $transaction_module_code = null)
    {
        self::$total_amount = $total_amount;
        if (is_null($transaction_module_code)) {
            $transaction_module_code = preg_replace('/[^A-Z]/', '', $journal_voucher_description);
        }
        $token = self::$token;
        if (empty($token)) {
            $token = Str::uuid();
            self::$token = $token;
        }
        $date = self::$journal_voucher_date;
        if (empty($date)) {
            $jvd = Carbon::now()->format('Y-m-d');
            $jvp = Carbon::now()->format('Ym');
        } else {
            $jvd = Carbon::parse($date)->format('Y-m-d');
            $jvp = Carbon::parse($date)->format('Ym');
        }
        $title = parent::getTransactionModule($transaction_module_code)->name ?? self::$defaultTitle;
        if (!empty(self::$title)) {
            $title = self::$title;
        }
        if (!empty(self::$description)) {
            $journal_voucher_description = self::$description;
        }
        $transactionModuleId = parent::getTransactionModule($transaction_module_code)->id ?? '';
        JournalVoucher::create([
            'company_id' => Auth::user()->company_id,
            'journal_voucher_status' => 1,
            'transaction_journal_no' => self::$transaction_journal_no,
            'journal_voucher_description' => self::$prependDescription . $journal_voucher_description . self::$appendDescription,
            'journal_voucher_title' => self::$prependTitle . $title . self::$appendTitle,
            'transaction_module_id' => $transactionModuleId,
            'transaction_module_code' => $transaction_module_code,
            'journal_voucher_date' => $jvd,
            'journal_voucher_period' => $jvp,
            'created_id' => Auth::id(),
            'journal_voucher_token' => $token,
        ]);
        $jv = JournalVoucher::where('journal_voucher_token', $token)->first();
        self::$journal_voucher_id = $jv->journal_voucher_id;
        $account = $account_setting_name;
        if (!empty(self::$account_id)) {
            $account = self::$account_id;
        }
        if (!empty($account)) {
            $mid = self::$merchant_id;
            if (empty($mid)) {
                $mid = Auth::user()->merchant_id;
            }
            foreach ($account as $name) {
                if (empty(self::$account_id)) {
                    $account_id = parent::getAccountSetting($name)->account_id;
                    $account_setting_status = parent::getAccountSetting($name)->status;
                } else {
                    $account_id = $name->account_id;
                    $account_setting_status = self::getAccountStatus($account_id)->status;
                }
                if ($account_setting_status == 0) {
                    $debit_amount = $total_amount;
                    $credit_amount = 0;
                } else {
                    $debit_amount = 0;
                    $credit_amount = $total_amount;
                }
                //* buat journal item
                JournalVoucherItem::create([
                    'merchat_id' => $mid,
                    'company_id' => Auth::user()->company_id,
                    'journal_voucher_id' => $jv->journal_voucher_id,
                    'account_id' => $account_id,
                    'journal_voucher_amount' => $total_amount,
                    'account_id_default_status' => self::getAccountDefaultStatus($account_id),
                    'account_id_status' => $account_setting_status,
                    'journal_voucher_debit_amount' => $debit_amount,
                    'journal_voucher_credit_amount' => $credit_amount,
                    'updated_id' => Auth::id(),
                    'created_id' => Auth::id(),
                ]);
            }
            return new self();
        } else {
            return new self();
        }
    }
    /**
     * Reverse journal
     *
     * @param integer $journal_voucher_id
     * @return void
     */
    public static function reverse(int $journal_voucher_id)
    {
        $token = Str::uuid();
        $journal = JournalVoucher::with('items')->find($journal_voucher_id);
        JournalVoucher::create([
            'company_id' => $journal['company_id'],
            'transaction_module_id' => $journal['transaction_module_id'],
            'journal_voucher_status' => $journal['journal_voucher_status'],
            'transaction_journal_no' => $journal['transaction_journal_no'],
            'transaction_module_code' => 'H' . $journal['transaction_module_code'],
            'journal_voucher_date' => date('Y-m-d'),
            'journal_voucher_description' => 'Hapus ' . $journal['journal_voucher_description'],
            'journal_voucher_period' => $journal['journal_voucher_period'],
            'journal_voucher_title' => 'Hapus ' . $journal['journal_voucher_title'],
            'data_state' => $journal['data_state'],
            'journal_voucher_token' => $token,
            'reverse_state' => 1,
            'created_id' => Auth::id(),
        ]);
        $journal->reverse_state = 1;
        $journal->save();
        $jv = JournalVoucher::where('journal_voucher_token', $token)->first();
        foreach ($journal->items as $key) {
            JournalVoucherItem::create([
                'company_id' => $key['company_id'],
                'journal_voucher_id' => $jv['journal_voucher_id'],
                'account_id' => $key['account_id'],
                'journal_voucher_amount' => $key['journal_voucher_amount'],
                'account_id_status' => 1 - $key['account_id_status'],
                'account_id_default_status' => $key['account_id_default_status'],
                'journal_voucher_debit_amount' => $key['journal_voucher_credit_amount'],
                'journal_voucher_credit_amount' => $key['journal_voucher_debit_amount'],
                'data_state' => $key['data_state'],
                'reverse_state' => 1,
                'updated_id' => Auth::id(),
                'created_id' => Auth::id(),
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
        return new self();
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
        if ($date < $now) {
            throw new \Exception("Can't Back Date");
        }
        self::$journal_voucher_date = $journal_voucher_date;
        return new self();
    }
    /**
     * Make journal with account id accoun seting name wont be used
     *
     * @param array $account_id
     * @return self
     */
    public static function account(array $account_id)
    {
        self::$account_id = $account_id;
        return new self();
    }
    /**
     * Get Account Status by account id
     *
     * @param string $account_id
     * @return mixed|Collection
     */
    public static function getAccountStatus(string $account_id)
    {
        return AcctAccount::select(['account_default_status as status'])->find($account_id);
    }
    /**
     * Make Journal Item, call this function after calling make() or token() or journalVoucherId()
     * All parameter that has ben passed here is became priority after any set parameter before it
     * @param string|int $account_id_or_setting account id or account setting name
     * @param integer|null $total_amount if null get from make()
     * @param integer|null $account_setting_status use account default status(if using account id) if null. (0=D,1=K)
     * @param integer $merchant_id
     * @return void|self
     */
    public function item($account_id_or_setting, int $account_setting_status = null, int $total_amount = null, int $merchant_id = null)
    {
        $journal_voucher_id = self::$journal_voucher_id;
        if (empty($journal_voucher_id)) {
            if (empty(self::$token)) {
                throw new \Exception('unspecified journal token. use this funtion after call make() or token()');
            }
            $jv = JournalVoucher::where('journal_voucher_token', self::$token)->first();
            $journal_voucher_id = $jv->journal_voucher_id;
        }
        if (is_int($account_id_or_setting)) {
            $account_id = $account_id_or_setting;
            $asts = self::getAccountStatus($account_id_or_setting)->status;
        } else {
            $account_id = parent::getAccountSetting($account_id_or_setting)->account_id;
            $asts = parent::getAccountSetting($account_id_or_setting)->status;
        }
        if (empty($account_setting_status)) {
            $account_setting_status = $asts;
        }
        if (empty($total_amount)) {
            $total_amount = self::$total_amount;
        }
        if ($account_setting_status == 0) {
            $debit_amount = $total_amount;
            $credit_amount = 0;
        } else {
            $debit_amount = 0;
            $credit_amount = $total_amount;
        }
        $mid = $merchant_id;
        if (empty($mid)) {
            $mid = self::$merchant_id;
        }
        //* buat journal item
        JournalVoucherItem::create([
            'merchat_id' => $mid,
            'company_id' => Auth::user()->company_id,
            'journal_voucher_id' => $journal_voucher_id,
            'account_id' => $account_id,
            'journal_voucher_amount' => $total_amount,
            'account_id_default_status' => self::getAccountDefaultStatus($account_id),
            'account_id_status' => $account_setting_status,
            'journal_voucher_debit_amount' => $debit_amount,
            'journal_voucher_credit_amount' => $credit_amount,
            'updated_id' => Auth::id(),
            'created_id' => Auth::id(),
        ]);
        return new self();
    }
    /**
     * Set Journal defaul title
     *
     * @return  self
     */
    public static function setDefaultTitle($defaultTitle = '')
    {
        self::$defaultTitle = $defaultTitle;
        return new self();
    }
    /**
     * Prepend Journal Title (and description), space provided
     *
     * @param string $prepend
     * @return  self
     */
    public static function prependTitle($prepend, int $withDesc = 0)
    {
        self::$prependTitle = "{$prepend} ";
        if ($withDesc) {
            self::$prependDescription = "{$prepend} ";
        }
        return new self();
    }
    /**
     * Append Journal Title (and description), space provided
     *
     * @param string $append
     * @return  self
     */
    public static function appendTitle($append, int $withDesc = 0)
    {
        self::$appendTitle = " {$append}";
        if ($withDesc) {
            self::$appendDescription = " {$append}";
        }
        return new self();
    }
    /**
     * Set Journal item merchant id
     *
     * @param int $merchant_id
     * @return  self
     */
    public static function merchantId($merchant_id)
    {
        self::$merchant_id = $merchant_id;
        return new self();
    }
    /**
     * Set journal voucher id
     *
     * @param int $journal_voucher_id
     * @return  self
     */
    public static function journalVoucherId($journal_voucher_id)
    {
        self::$journal_voucher_id = $journal_voucher_id;
        return new self();
    }
    /**
     * Set transaction journal no
     *
     * @param string $transaction_journal_no
     * @return  self
     */
    public static function trsJournalNo($transaction_journal_no)
    {
        self::$transaction_journal_no = $transaction_journal_no;
        return new self();
    }
    /**
     * Update journal voucher (parent) that has been created
     *
     * @param array $updates
     * @return void
     */
    public static function update(array $updates)
    {
        $journal_voucher_id = self::$journal_voucher_id;
        if (empty($journal_voucher_id)) {
            if (empty(self::$token)) {
                throw new \Exception('unspecified journal token. use this funtion after call make() or token()');
            }
            $jv = JournalVoucher::where('journal_voucher_token', self::$token)
                ->latest()
                ->first();
            $journal_voucher_id = $jv->journal_voucher_id;
        }
        if (!empty($updates['journal_voucher_date'])) {
            $date = Carbon::parse($updates['journal_voucher_date'])->format('Ym');
            $now = Carbon::now()->format('Ym');
            if ($date < $now) {
                throw new \Exception("Can't Back Date");
            }
        }
        return JournalVoucher::where('journal_voucher_id', $journal_voucher_id)->update($updates);
    }
    /**
     * Set Journal Title (and Description)
     *
     * @param string $title
     * @param integer $withDesc
     * @return self
     */
    public static function title(string $title, $withDesc = 0)
    {
        self::$title = $title;
        if ($withDesc) {
            self::$description = $title;
        }
        return new self;
    }
    /**
     * Undocumented function
     *
     * @param string $description
     * @param integer $withtitle
     * @return self
     */
    public static function description(string $description, $withtitle = 0)
    {
        self::$description = $description;
        if ($withtitle) {
            self::$title = $description;
        }
        return new self;
    }
}

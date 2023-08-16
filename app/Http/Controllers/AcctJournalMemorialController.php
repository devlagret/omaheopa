<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AcctJournalMemorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }
    
    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        }else{
            $end_date = Session::get('end_date');
        }
        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher_item.journal_voucher_id','=','acct_journal_voucher.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->where('acct_journal_voucher.journal_voucher_status', 1)
        ->get();
        return view('content.AcctJournalMemorial.ListAcctJournalMemorial', compact('start_date','end_date','data'));
    }

    public function filterJournalMemorial(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('journal-memorial');
    }

    public function resetFilterJournalMemorial()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('journal-memorial');
    }

    public function getMinID($journal_voucher_id)
    {
        $data = JournalVoucherItem::where('journal_voucher_id', $journal_voucher_id)->first();

        return $data['journal_voucher_item_id'];
    }
}

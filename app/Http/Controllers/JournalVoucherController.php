<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use DateTime;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class JournalVoucherController extends Controller
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

        Session::forget('journal');
        Session::forget('arraydatases');
        $data = JournalVoucherItem::join('acct_journal_voucher','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->where('acct_journal_voucher.journal_voucher_date', '>=', $start_date)
        ->where('acct_journal_voucher.journal_voucher_date', '<=', $end_date)
        ->where('acct_journal_voucher.data_state',0)
        ->where('acct_journal_voucher.transaction_module_code', 'JU')
        ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
        ->get();
        return view('content.JournalVoucher.ListJournalVoucher', compact('data','start_date','end_date'));
    }

    public function addJournalVoucher()
    {   
        $journal = Session::get('journal');
        $arraydata = Session::get('arraydatases');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        $account = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        return view('content.JournalVoucher.FormAddJournalVoucher',compact('status','account','journal','arraydata'));
    }

    public function addElementsJournalVoucher(Request $request)
    {
        $journal = Session::get('journal');
        if(!$journal || $journal == ''){
            $journal['journal_voucher_date']        = '';
            $journal['journal_voucher_description'] = '';
        }

        $journal[$request->name] = $request->value;
        Session::put('journal',$journal);
    }

    public function addArrayJournalVoucher(Request $request)
    {
        $request->validate([
            'account_id'                => 'required',
            'account_status'            => 'required',
            'journal_voucher_amount'    => 'required',
            
        ]);

        $arraydatases = array(
            'account_id'                => $request->account_id,
            'account_status'            => $request->account_status,
            'journal_voucher_amount'    => $request->journal_voucher_amount,
            
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

        return redirect('/journal-voucher/add');
    }

    public function resetAddJournalVoucher()
    {
        Session::forget('journal');
        Session::forget('arraydatases');

        return redirect('/journal-voucher/add');
    }

    
    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();
        // dd($data);exit;
        return $data['account_default_status'];
    }


    public function processAddJournalVoucher(Request $request)
    {
        $transaction_module_code = 'JU';
        $transaction_module_id = $this->getTransactionModuleID($transaction_module_code);
        $fields = $request->validate([
            'journal_voucher_date'          => 'required',
            'journal_voucher_description'   => 'required',
            
        ]);

        $datases = array(
            'journal_voucher_date'          => $fields['journal_voucher_date'],
            'journal_voucher_description'   => $fields['journal_voucher_description'],
            'journal_voucher_title'         => $fields['journal_voucher_description'],
            'journal_voucher_period'        => date('Ym'),
            'transaction_module_code'       => $transaction_module_code,
            'transaction_module_id'         => $transaction_module_id,
            'company_id'                    => Auth::user()->company_id,
            'created_id'                    => Auth::id(),
            'updated_id'                    => Auth::id()
        );

        
        if(JournalVoucher::create($datases)){
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();
            // $account_default_status = $this->getAccountDefaultStatus($data['account_id']);
            $arraydata = Session::get('arraydatases');
            foreach($arraydata as $val){
                if($val['account_status'] == 0){
                    $data = array(
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $val['account_id'],
                        'account_id_status'             => $val['account_status'],
                        'account_id_default_status'     => $this->getAccountDefaultStatus($val['account_id']),
                        'journal_voucher_amount'        => $val['journal_voucher_amount'],
                        'journal_voucher_debit_amount'  => $val['journal_voucher_amount'],
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalVoucherItem::create($data);
                } else {
                    $data = array(
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $val['account_id'],
                        'account_id_status'             => $val['account_status'],
                        'account_id_default_status'     => $this->getAccountDefaultStatus($val['account_id']),
                        'journal_voucher_amount'        => $val['journal_voucher_amount'],
                        'journal_voucher_credit_amount' => $val['journal_voucher_amount'],
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalVoucherItem::create($data);

                }

            }

            $msg = 'Tambah Jurnal Umum Berhasil';
            return redirect('/journal-voucher/add')->with('msg',$msg);
        } else {
            $msg = 'Tambah Jurnal Umum Gagal';
            return redirect('/journal-voucher/add')->with('msg',$msg);
        }
    }

    public function filterJournalVoucher(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        
        return redirect('/journal-voucher');
    }

    public function resetFilterJournalVoucher()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        
        return redirect('/journal-voucher');
    }

    public function getAccountCode($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_code'];
    }

    public function getAccountName($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_name'];
    }

    public function getStatus($account_status)
    {
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        return $status[$account_status];
    }

    public function getMinID($journal_voucher_id)
    {
        $data = JournalVoucherItem::where('journal_voucher_id', $journal_voucher_id)->first();

        return $data['journal_voucher_item_id'];
    }

    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'] ?? '';
    }

    public function reverseJournalVoucher($journal_voucher_id)
    {
        $journal = JournalVoucher::find($journal_voucher_id);
        $journalItem = JournalVoucher::join('acct_journal_voucher_item', 'acct_journal_voucher_item.journal_voucher_id', '=', 'acct_journal_voucher.journal_voucher_id')
            ->select('acct_journal_voucher_item.*')
            ->where('acct_journal_voucher.company_id', Auth::user()->company_id)
            ->where('acct_journal_voucher.journal_voucher_id', $journal_voucher_id);
        $data = array(
            'company_id'                    => $journal['company_id'],
            'transaction_module_id'         => $journal['transaction_module_id'],
            'journal_voucher_status'        => $journal['journal_voucher_status'],
            'transaction_journal_no'        =>  $journal['transaction_journal_no'],
            'transaction_module_code'       => $journal['transaction_module_code'],
            'journal_voucher_date'          => date('Y-m-d'),
            'journal_voucher_description'   =>  'HAPUS ' . $journal['journal_voucher_description'],
            'journal_voucher_period'        => $journal['journal_voucher_period'],
            'journal_voucher_title'         =>  'HAPUS ' . $journal['journal_voucher_title'],
            "data_state"                    => $journal['data_state'],
            "reverse_state"                 => 1,
            'updated_id'                    => Auth::id(),
            'created_id'                    => Auth::id()
        );
        try {
            DB::beginTransaction();
            JournalVoucher::create($data);
            $arr = array();
            $journal->reverse_state = 1;
            $journal->save();
            $journalVoucherId = JournalVoucher::orderBy('journal_voucher_id', 'DESC')->where('company_id', $journal['company_id'])->first();
            foreach ($journalItem->get() as $key) {

                $reverse_journal = array(
                    'company_id'                        => $key['company_id'],
                    'journal_voucher_id'                => $journalVoucherId['journal_voucher_id'],
                    'account_id'                        => $key['account_id'],
                    'journal_voucher_amount'            => $key['journal_voucher_amount'],
                    'account_id_status'                 => (1 - $key['account_id_status']),
                    'account_id_default_status'         => $key['account_id_default_status'],
                    'journal_voucher_debit_amount'      => $key['journal_voucher_credit_amount'],
                    'journal_voucher_credit_amount'     => $key['journal_voucher_debit_amount'],
                    "data_state"                        => $key['data_state'],
                    "reverse_state"                     => 1,
                    'updated_id'                        => Auth::id(),
                    'created_id'                        => Auth::id()
                );
                array_push($arr, $reverse_journal);
                JournalVoucherItem::create($reverse_journal);
            }
            $journalItem->update(['acct_journal_voucher_item.reverse_state' => 1]);
            //     dd([$journal,$data]);
            //     dd([$journalItem,$arr]);
            //     dd([$data,$journal_voucher_id,$journal]);
            //    exit;    
            DB::commit();
            session()->flash('msg', 'Hapus Jurnal Umum Berhasil');
            return redirect()->route('journal-voucher');
        } catch (\Throwable $th) {
            dd($th);
            DB::rollback();
            session()->flash('msg', 'Hapus Jurnal Umum Gagal');
            return redirect()->route('journal-voucher');
        }
    }

    public function printJournalVoucher($journal_voucher_id)
    {

        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('preference_company','preference_company.company_id','=','acct_journal_voucher.company_id')
        ->where('acct_journal_voucher.journal_voucher_id',$journal_voucher_id)
        ->first();

        $data1 = JournalVoucherItem::join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->where('journal_voucher_id',$journal_voucher_id)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(7, 7, 7, 7); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px;font-weight: bold\">JURNAL UMUM</div></td>
            </tr>
                <tr>
                <td><div style=\"text-align: center; font-size:10px\">".$data['company_name']."</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:10px\">Jam : ".date('H:i:s')."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tbl1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
            <tr>
                <td width=\"20%\"><div style=\"text-align: left;\">Tanggal Jurnal</div></td>
                <td width=\"80%\"><div style=\"text-align: left;\">: ".$data['journal_voucher_date']."</div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align: left;\">No. Jurnal</div></td>
                <td width=\"80%\"><div style=\"text-align: left;\">: ".$data['journal_voucher_no']."</div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align: left;\">Uraian</div></td>
                <td width=\"80%\"><div style=\"text-align: left;\">: ".$data['journal_voucher_description']."</div></td>
            </tr>		
        </table>";

        $tbl2 = "
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-weight: bold\">No.</div></td>
                <td width=\"40%\"><div style=\"text-align: center;font-weight: bold\">Perkiraan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Debet</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Kredit</div></td>
            </tr>
        ";
        $tbl3 = " ";
        $no =1;
        $total_debet = 0;
        $total_kredit = 0;
        foreach ($data1 as $key => $val) {
            $tbl3 .= "
                    <tr>
                        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\">".$no."</div></td>
                        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                    </tr>
            ";  
            $total_debet += $val['journal_voucher_debit_amount'];
            $total_kredit += $val['journal_voucher_credit_amount'];
            $no++;
        }
        $tbl4 = "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                <td width=\"40%\"><div style=\"text-align: left;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
            </tr>		
        </table>

        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td colspan=\"2\" width=\"45%\"></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_debet,2,'.',',')."</div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_kredit,2,'.',',')."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        ob_clean();

        $filename = 'Jurnal_'.$data['journal_voucher_no'].'_'.$data['journal_voucher_date'].'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function printJournalVoucherAll()
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

        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('preference_company','preference_company.company_id','=','acct_journal_voucher.company_id')
        ->first();

        $data1 = JournalVoucherItem::join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('acct_journal_voucher','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        ->where('acct_journal_voucher.reverse_state',0)
        ->where('acct_journal_voucher.transaction_module_code', 'JU')
        ->get();
        // echo json_encode($data1);exit;

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(7, 7, 7, 7); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px;font-weight: bold\">JURNAL UMUM</div></td>
            </tr>
                <tr>
                <td><div style=\"text-align: center; font-size:10px\">".$data['company_name']."</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tbl1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">

        </table>";

        $tbl2 = "
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-weight: bold\">No.</div></td>
                <td width=\"40%\"><div style=\"text-align: center;font-weight: bold\">Perkiraan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Debet</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Kredit</div></td>
            </tr>
        ";
        $tbl3 = " ";
        $no =1;
        $total_debet = 0;
        $total_kredit = 0;
        if(empty($data1)){
            echo "
                <tr>
                    <td colspan='8' align='center'>Data Kosong</td>
                </tr>
            ";
        } else {
            foreach ($data1 as $key=>$val){	
                $id = $this->getMinID($val['journal_voucher_id']);
                
                if($val['journal_voucher_item_id'] == $id ){
            $tbl3 .= "
                    <tr>
                        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\">".$no."</div></td>
                        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                    </tr>
                    "; 
                    
                    $no++;
                }else{
            $tbl3 .= "
                    <tr>
                        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                    </tr>
            ";}
            $total_debet += $val['journal_voucher_debit_amount'];
            $total_kredit += $val['journal_voucher_credit_amount'];
        }
    }

        $tbl4 = "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                <td width=\"40%\"><div style=\"text-align: left;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
            </tr>		
        </table>

        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td colspan=\"2\" width=\"45%\"></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_debet,2,'.',',')."</div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_kredit,2,'.',',')."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        // ob_clean();

        $filename = 'Jurnal_'.$data['journal_voucher_no'].'_'.$data['journal_voucher_date'].'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function printJournalVoucherDebit()
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

        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('preference_company','preference_company.company_id','=','acct_journal_voucher.company_id')
        ->first();

        $data1 = JournalVoucherItem::join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('acct_journal_voucher','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        ->where('acct_journal_voucher.reverse_state',0)
        ->where('acct_journal_voucher.transaction_module_code', 'JU')
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(7, 7, 7, 7); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px;font-weight: bold\">JURNAL UMUM DEBIT</div></td>
            </tr>
                <tr>
                <td><div style=\"text-align: center; font-size:10px\">".$data['company_name']."</div></td>
            </tr>
            <tr>
            <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tbl1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
        
        </table>";

        $tbl2 = "
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-weight: bold\">No.</div></td>
                <td width=\"40%\"><div style=\"text-align: center;font-weight: bold\">Perkiraan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Debet</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Kredit</div></td>
            </tr>
        ";
        $tbl3 = " ";
        $no =1;
        $total_debet = 0;
        $total_kredit = 0;
        if(empty($data1)){
            echo "
                <tr>
                    <td colspan='8' align='center'>Data Kosong</td>
                </tr>
            ";
        } else{
                foreach ($data1 as $val){	
                    $id = $this->getMinID($val['journal_voucher_id']);
                        if($val['journal_voucher_item_id'] == $id){
                            //filter debit amount
                            if($val['journal_voucher_debit_amount'] > 0 ){
                    $tbl3 .= "
                            <tr>
                                <td width=\"5%\"><div style=\"text-align: center;font-size:12px\">".$no."</div></td>
                                <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                            </tr>
                            "; 
                            $no++; 
                            $total_debet += $val['journal_voucher_debit_amount'];
                        }
                    }
                        elseif ($val['journal_voucher_credit_amount'] > 0 ) 
                        {
                            $tbl3 .= "
                            <tr>
                            <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                            <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                            <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                            <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                            </tr>
                            "; 
                            $total_kredit += $val['journal_voucher_credit_amount'];    
                        }
                }
            }
        $tbl4 = "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                <td width=\"40%\"><div style=\"text-align: left;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
            </tr>		
        </table>

        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td colspan=\"2\" width=\"45%\"></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_debet,2,'.',',')."</div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_kredit,2,'.',',')."</div></td>

            </tr>
        </table>";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        ob_clean();

        $filename = 'Jurnal_'.$data['journal_voucher_no'].'_'.$data['journal_voucher_date'].'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function printJournalVoucherCredit()
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

        $data = JournalVoucher::join('acct_journal_voucher_item','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('preference_company','preference_company.company_id','=','acct_journal_voucher.company_id')
        ->first();

        $data1 = JournalVoucherItem::join('acct_account', 'acct_account.account_id','=','acct_journal_voucher_item.account_id')
        ->join('acct_journal_voucher','acct_journal_voucher.journal_voucher_id','=','acct_journal_voucher_item.journal_voucher_id')
        ->where('acct_journal_voucher.journal_voucher_date','>=',$start_date)
        ->where('acct_journal_voucher.journal_voucher_date','<=',$end_date)
        ->where('acct_journal_voucher.reverse_state',0)
        ->where('acct_journal_voucher.transaction_module_code', 'JU')
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(7, 7, 7, 7); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px;font-weight: bold\">JURNAL UMUM KREDIT</div></td>
            </tr>
                <tr>
                <td><div style=\"text-align: center; font-size:10px\">".$data['company_name']."</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $tbl1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
        
        </table>";

        $tbl2 = "
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-weight: bold\">No.</div></td>
                <td width=\"40%\"><div style=\"text-align: center;font-weight: bold\">Perkiraan</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Debit</div></td>
                <td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Kredit</div></td>
            </tr>
        ";
        $tbl3 = " ";
        $no =1;
        $total_debet = 0;
        $total_kredit = 0;
        if(empty($data1)){
            echo "
                <tr>
                    <td colspan='8' align='center'>Data Kosong</td>
                </tr>
            ";
        } else {
            foreach ($data1 as $val){	
                $id = $this->getMinID($val['journal_voucher_id']);
                if($val['journal_voucher_item_id'] == $id){
                    //filter credit amount
                    if($val['journal_voucher_credit_amount'] > 0 ){
            $tbl3 .= "
                    <tr>
                        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\">".$no."</div></td>
                        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                    </tr>
                    "; 
                    $no++; 
                    $total_kredit += $val['journal_voucher_credit_amount'];    
                }
            }
                elseif ($val['journal_voucher_debit_amount'] > 0 ) 
                {
                    $tbl3 .= "
                    <tr>
                    <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                    <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">".$val['account_code']." - ".$val['account_name']."</div></td>
                    <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2,'.',',')."</div></td>
                    <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2,'.',',')."</div></td>
                    </tr>
                    "; 
                    $total_debet += $val['journal_voucher_debit_amount'];
                }
        }
    }
        $tbl4 = "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
                <td width=\"40%\"><div style=\"text-align: left;font-size:12px\"></div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
            </tr>		
        </table>

        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td colspan=\"2\" width=\"45%\"></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_debet,2,'.',',')."</div></td>
                <td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_kredit,2,'.',',')."</div></td>
            </tr>
        </table>";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        ob_clean();

        $filename = 'Jurnal_'.$data['journal_voucher_no'].'_'.$data['journal_voucher_date'].'.pdf';
        $pdf::Output($filename, 'I');
    }
}

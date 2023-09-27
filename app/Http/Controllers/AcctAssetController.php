<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAsset;
use App\Models\AcctAssetReport;
use App\Models\AcctAssetReportItem;
use App\Models\AcctAssetType;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherItem;
use App\Models\PreferenceTransactionModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AcctAssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {

        $aset = AcctAsset::where('data_state',0)
        ->get();

        return view('content.AcctAset.ListAcctAsset',compact('aset'));
    }
    public function add() {
        $sessiondata = Session::get('supplier-data');
        return view('content.AcctAsetType.FormAddAcctAssetType',compact('sessiondata'));
    }

    public function getAssetTypeName($asset_type_id){
        $aset = AcctAssetType::where('asset_type_id',$asset_type_id)
        ->where('data_state',0)
        ->first();

        return $aset['asset_type_name'];
    }

    public function getAssetTypeKode($asset_type_id){
        $aset = AcctAssetType::where('asset_type_id',$asset_type_id)
        ->where('data_state',0)
        ->first();

        return $aset['asset_type_code'];
    }

    public function detail($asset_id) {
        $sessiondata = Session::get('supplier-data');
        $acctasset  = AcctAsset::find($asset_id);
        return view('content.AcctAset.FormDetailAcctAsset',compact('sessiondata','acctasset'));
    }


    public function depreciation($asset_id){
        $acctasset  = AcctAssetReport::select('*')
        ->join('acct_asset', 'acct_asset_depreciation.asset_id','acct_asset.asset_id')
        ->where('acct_asset_depreciation.asset_id',$asset_id)
        ->first();
        $acctassetItem = $this->getAcctAssetDepreciationItem_Detail($asset_id);
        $account    = $this->getAcctAccount($asset_id);

        // return json_encode($acctassetItem);
        // exit;
        return view('content.AcctAset.FormDetailAcctAssetDepreciation',compact('acctasset','acctassetItem','account'));
    }

    public function getAcctAssetDepreciationItem_Detail($asset_id){
        $data = AcctAssetReport::select('*')
        ->join('acct_asset_depreciation_item', 'acct_asset_depreciation_item.asset_depreciation_id','acct_asset_depreciation.asset_depreciation_id')
        ->join('acct_asset', 'acct_asset_depreciation.asset_id','acct_asset.asset_id')
        ->where('acct_asset_depreciation.asset_id',$asset_id)
        ->get();

        return $data;
    }

    public function getAcctAssetDepreciationItem_DetailItem($asset_depreciation_item_id){
        $data = AcctAssetReport::select('*')
        ->join('acct_asset_depreciation_item', 'acct_asset_depreciation_item.asset_depreciation_id','acct_asset_depreciation.asset_depreciation_id')
        ->join('acct_asset', 'acct_asset_depreciation.asset_id','acct_asset.asset_id')
        ->where('acct_asset_depreciation_item.asset_depreciation_item_id',$asset_depreciation_item_id)
        ->first();

        return $data;
    }

    public function getAcctAsset_Detail($asset_id){
        $data = AcctAssetReport::select('acct_asset.asset_id, acct_asset.asset_type_id, acct_asset_type.asset_type_name, acct_asset.location_id, acct_asset.asset_purchase_date, acct_asset.asset_purchase_value, acct_asset.asset_depreciation_value, acct_asset.asset_location_detail, acct_asset.asset_description, acct_asset.asset_code, acct_asset.asset_name, acct_asset.voided_remark, acct_asset.asset_book_value, acct_asset.asset_depreciation_type, acct_asset.asset_salvage_value, acct_asset.asset_estimated_lifespan, acct_asset.item_unit_code')
        ->join('acct_asset_type', 'acct_asset.asset_type_id = acct_asset_type.asset_type_id')
        ->where('acct_asset.asset_id',$asset_id);
        return $data;
    }

    public function getAcctAccount(){
        $query = DB::select("SELECT account_id, concat(account_code,' - ',account_name) AS account_code FROM acct_account WHERE data_state = 0 AND parent_account_status = 0");	
        return $query;
    }

    public function DepreciationMethod($asset_type_id){
		
        $depreciation_method = 'Garis Lurus';
        if($asset_type_id == 1){
            $data = $depreciation_method;
        }else{
            $data = $depreciation_method;
        }
        

		return $data;
	}

 
    

    public function Journaldepreciation($asset_depreciation_item_id,$asset_id){
        $acctasset  = AcctAssetReport::select('*')
        ->join('acct_asset', 'acct_asset_depreciation.asset_id','acct_asset.asset_id')
        ->where('acct_asset_depreciation.asset_id',$asset_id)
        ->first();
        $acctassetItem = $this->getAcctAssetDepreciationItem_DetailItem($asset_depreciation_item_id);
        $acctaccount = AcctAccount::select('*')
                        ->pluck('account_name','account_id');	
        // dd($acctasset,$acctassetItem,$acctaccount);
        return view('content.AcctAset.FormAddJournalAcctAsset',compact('acctasset','acctassetItem','acctaccount'));
    }

    public function processAddJournal(Request $request)
    {

        // dd($request->all());
        $transaction_module_code = 'AS';
        $transaction_module_id = $this->getTransactionModuleID($transaction_module_code);
        // $fields = $request->validate([
        //     'journal_voucher_date'          => 'required',
        //     'journal_voucher_description'   => 'required',
            
        // ]);

        $datases = array(
            'journal_voucher_date'          => $request['depreciation_date'],
            'journal_voucher_description'   => $request['depreciation_description'],
            'journal_voucher_title'         => $request['depreciation_description'],
            'journal_voucher_period'        => date('Ym'),
            'transaction_module_code'       => $transaction_module_code,
            'transaction_module_id'         => $transaction_module_id,
            'company_id'                    => Auth::user()->company_id,
            'created_id'                    => Auth::id(),
            'updated_id'                    => Auth::id()
        );

        
        if(JournalVoucher::create($datases)){
            $journal_voucher_id = JournalVoucher::orderBy('created_at', 'DESC')->where('company_id', Auth::user()->company_id)->first();

            $account_default_status_debit = $this->getAccountDefaultStatus($request['account_id_debit']);
            $account_default_status_credit = $this->getAccountDefaultStatus($request['account_id_credit']);

              
                    $datadebit = array(
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $request['account_id_debit'],
                        'account_id_status'             => $account_default_status_debit,
                        'account_id_default_status'     => $this->getAccountDefaultStatus($request['account_id_debit']),
                        'journal_voucher_amount'        => $request['journal_voucher_debit_amount'],
                        'journal_voucher_debit_amount'  => $request['journal_voucher_debit_amount'],
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalVoucherItem::create($datadebit);
                    $datacredit = array(
                        'journal_voucher_id'            => $journal_voucher_id['journal_voucher_id'],
                        'account_id'                    => $request['account_id_credit'],
                        'account_id_status'             => $account_default_status_credit,
                        'account_id_default_status'     => $this->getAccountDefaultStatus($request['account_id_credit']),
                        'journal_voucher_amount'        => $request['journal_voucher_credit_amount'],
                        'journal_voucher_credit_amount' => $request['journal_voucher_credit_amount'],
                        'created_id'                    => Auth::id(),
                        'updated_id'                    => Auth::id()
                    );
                    JournalVoucherItem::create($datacredit);

    

            

            $msg = 'Tambah Jurnal Umum Berhasil';
            return redirect('/aset')->with('msg',$msg);
        } else {
            $msg = 'Tambah Jurnal Umum Gagal';
            return redirect('/aset')->with('msg',$msg);
        }
    }

    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id',$account_id)->first();
        // dd($data);exit;
        return $data['account_status'];
    }
    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code',$transaction_module_code)->first();

        return $data['transaction_module_id'];
    }

}
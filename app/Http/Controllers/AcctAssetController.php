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

        $acctassettype = AcctAssetType::select('asset_type_id','asset_type_name')
		->where('data_state', 0)
        ->get()
        ->pluck('asset_type_name','asset_type_id');

        $sessiondata = Session::get('supplier-data');
        $depreciation_method = array (1 => 'Garis Lurus');

        return view('content.AcctAset.FormAddAcctAsset',compact('sessiondata','depreciation_method','acctassettype'));
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

    public function processAdd(Request $request){

        $fields = $request->validate([
            // 'purchase_invoice_supplier' => 'required',
            'asset_type_id'             => 'required',
            'asset_code'                => 'required',
            'asset_name'                => 'required',
            'asset_purchase_date'       => 'required',
            'item_unit_code'            => 'required',
            'asset_purchase_value'      => 'required',  
            'asset_depreciation_type'   => 'required',
            'asset_book_value'          => 'required'
        ]);

        $data= array (
            'branch_id'					=> 1,
            'asset_type_id'				=> $fields['asset_type_id'],
            // 'location_id'				=> $fields['location_id'],
            'asset_code'				=> $fields['asset_code'],
            'asset_name'				=> $fields['asset_name'],
            'item_unit_code'			=> $fields['item_unit_code'],
            'asset_purchase_date'		=> $fields['asset_purchase_date'],
            'asset_purchase_value'		=> $fields['asset_purchase_value'],
            'asset_depreciation_type'	=> $fields['asset_depreciation_type'],
            'asset_book_value'			=> $fields['asset_book_value'],
            'asset_estimated_lifespan'	=> $request['asset_estimated_lifespan'],
            'asset_salvage_value'		=> $request['asset_salvage_value'],
            'asset_location_detail'		=> $request['asset_location_detail'],
            'asset_description'			=> $request['asset_description'],
            'created_id'				=> Auth::id()
        );

        AcctAsset::create($data);
        
                    $depreciation_month 		= $data['asset_estimated_lifespan'] * 12;
					$depreciation_start_month 	= date('m', strtotime('+1 months', strtotime($data['asset_purchase_date'])));
					$depreciation_start_year 	= date('Y', strtotime($data['asset_purchase_date']));
					$depreciation_end_month		= date('m', strtotime('+'.$depreciation_month.' months', strtotime($data['asset_purchase_date'])));
					$depreciation_end_year		= date('Y', strtotime('+'.$data['asset_estimated_lifespan'].' years', strtotime($data['asset_purchase_date'])));

                    $asset_id = AcctAsset::orderBy('created_at', 'DESC')->first();
                
                    $datadepreciation = array (
						'asset_id'							=> $asset_id['asset_id'],
						'asset_type_id'						=> $data['asset_type_id'],
						'branch_id'							=> 1,
						'asset_depreciation_date'			=> $data['asset_purchase_date'],
						'asset_depreciation_duration'		=> $data['asset_estimated_lifespan'],
						'asset_depreciation_start_month'	=> $depreciation_start_month,
						'asset_depreciation_start_year'		=> $depreciation_start_year,
						'asset_depreciation_end_month'		=> $depreciation_end_month,
						'asset_depreciation_end_year'		=> $depreciation_end_year,
						'asset_depreciation_book_value'		=> $data['asset_book_value'],
						'asset_depreciation_salvage_value'	=> $data['asset_salvage_value'],
						'created_on'						=> date('Y-m-d H:i:s'),
						'created_id'						=> Auth::id(),
					);
                        // dump($datadepreciation);

                        // }
                        
            if(AcctAssetReport::create($datadepreciation)){
                $asset_depreciation_id = AcctAssetReport::orderBy('created_at', 'DESC')->first();
                $datadepreciationitem = collect();
                        $month 				= $depreciation_start_month;
						$year 				= $depreciation_start_year;

						if($data['asset_depreciation_type'] == 1){

							
							$nilai_buku 		= $data['asset_book_value'];
							$akm_penyusutan 	= 0;

							for ($i=1; $i <= $data['asset_estimated_lifespan'] ; $i++) { 
								
								if($i == $data['asset_estimated_lifespan']){
									$by_penyusutan_tahun = $nilai_buku - $data['asset_salvage_value'];
								} else {
									$by_penyusutan_tahun 	= ($data['asset_book_value'] - $data['asset_salvage_value']) / $data['asset_estimated_lifespan'];
								}

								$by_penyusutan_bulan 	= ($by_penyusutan_tahun) / 12;

								for ($j=1; $j <= 12 ; $j++) { 
									if($month == 13){
										$month = 01;
										$year = $year + 1;
									}

									
									$akm_penyusutan 	= $akm_penyusutan + $by_penyusutan_bulan;
									$nilai_buku 		= $nilai_buku - $by_penyusutan_bulan;

									$datadepreciationitem->push([

                                        'asset_depreciation_id'							=> $asset_depreciation_id['asset_depreciation_id'],
										'asset_depreciation_item_year_to'				=> $i,
										'asset_depreciation_item_month'					=> $month,
										'asset_depreciation_item_year'					=> $year,
										'asset_depreciation_item_amount'				=> $by_penyusutan_bulan,
										'asset_depreciation_item_accumulation_amount'	=> $akm_penyusutan,
										'asset_depreciation_item_book_value'			=> $nilai_buku,
                                        ]
									);

									$month = $month + 01;
                                    // dump($datadepreciationitem);
                                    // AcctAssetReportItem ::create($datadepreciationitem);


								}							
							}

						} else if($data['asset_depreciation_type'] == 2){

							$nilai_buku 		= $data['asset_book_value'];
							$akm_penyusutan 	= 0;
							$prosentase_by 		= (100 / $data['asset_estimated_lifespan']) * 2;

							for ($i=1; $i <= $data['asset_estimated_lifespan'] ; $i++) { 

								if($i == $data['asset_estimated_lifespan']){
									$by_penyusutan_tahun = $nilai_buku - $data['asset_salvage_value'];
								} else {
									$by_penyusutan_tahun 	= ($prosentase_by * $nilai_buku) / 100;
								}

								$by_penyusutan_bulan 	= ($by_penyusutan_tahun) / 12;

								for ($j=1; $j <= 12 ; $j++) { 
									if($month == 13){
										$month = 01;
										$year = $year + 1;
									}

									
									$akm_penyusutan 	= $akm_penyusutan + $by_penyusutan_bulan;
									$nilai_buku 		= $nilai_buku - $by_penyusutan_bulan;

									$datadepreciationitem-> push ([
										'asset_depreciation_id'							=> $asset_depreciation_id['asset_depreciation_id'],
										'asset_depreciation_item_year_to'				=> $i,
										'asset_depreciation_item_month'					=> $month,
										'asset_depreciation_item_year'					=> $year,
										'asset_depreciation_item_amount'				=> $by_penyusutan_bulan,
										'asset_depreciation_item_accumulation_amount'	=> $akm_penyusutan,
										'asset_depreciation_item_book_value'			=> $nilai_buku,
									]);
									$month = $month + 01;
                                    AcctAssetReportItem ::create($datadepreciationitem);

								}							
							}

						} else if($data['asset_depreciation_type'] == 3){

							$nilai_buku 		= $data['asset_book_value'];
							$nilai_perolehan 	= $data['asset_book_value'] - $data['asset_salvage_value'];
							$akm_penyusutan 	= 0;
							$jml_angka_th 		= ( $data['asset_estimated_lifespan'] * ($data['asset_estimated_lifespan'] + 1) ) / 2;

							$n = $data['asset_estimated_lifespan'];

							for ($i=1; $i <= $data['asset_estimated_lifespan']  ; $i++) { 

								if($i == $data['asset_estimated_lifespan']){
									$by_penyusutan_tahun = $nilai_buku - $data['asset_salvage_value'];
								} else {
									$by_penyusutan_tahun 	= ($n / $jml_angka_th) * $nilai_perolehan;
								}
								
								$by_penyusutan_bulan 	= ($by_penyusutan_tahun) / 12;

								for ($j=1; $j <= 12 ; $j++) { 
									if($month == 13){
										$month = 01;
										$year = $year + 1;
									}

									
									$akm_penyusutan 	= $akm_penyusutan + $by_penyusutan_bulan;
									$nilai_buku 		= $nilai_buku - $by_penyusutan_bulan;

									$datadepreciationitem->push([
										'asset_depreciation_id'							=> $asset_depreciation_id['asset_depreciation_id'],
										'asset_depreciation_item_year_to'				=> $i,
										'asset_depreciation_item_month'					=> $month,
										'asset_depreciation_item_year'					=> $year,
										'asset_depreciation_item_amount'				=> $by_penyusutan_bulan,
										'asset_depreciation_item_accumulation_amount'	=> $akm_penyusutan,
										'asset_depreciation_item_book_value'			=> $nilai_buku,
                                    ]);
                                    
									$month = $month + 01;
								}
								$n = $n -1;
								
							}
                            
						}
                    }
                    
                    // dump($datadepreciationitem);
                    AcctAssetReportItem ::insert($datadepreciationitem->toArray());
                                            $msg = 'Tambah Asset Berhasil';
                                            return redirect('/aset')->with('msg',$msg);
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
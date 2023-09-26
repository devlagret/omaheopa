<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAssetReport;
use App\Models\AcctAssetReportitem;
use App\Models\AcctAsset;
use App\Models\AcctAssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class AcctAssetReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        if(empty($sesi['branch_id'])){
            $sesi['branch_id']		= 1;
        }

        if(empty($sesi['year_period'])){
            $sesi['year_period']		= date('Y');
        }

        if(empty($sesi['year'])){
            $sesi['year']		= date('Y');
        }

        Session::get('year', $year);
        // Session::get('branch_id', $branch_id);

        $aset = AcctAssetReport::select('*')
        ->join('acct_asset','acct_asset.asset_id','acct_asset_depreciation.asset_id')
        ->where('acct_asset_depreciation.data_state',0)
        ->get();

        // dump($sesi);
        
        $last_year 		= $sesi['year_period'] - 1;
		$prosentase		= $this->ProsentasePenyusutan();
        $acctasset = $this->getAcctAsset($sesi['branch_id'], $sesi['year']);
        // echo json_encode($acctasset);exit;
        foreach ($acctasset as $key => $val) {
            $tahun_perolehan 					= substr($val['asset_purchase_date'],0,4);
        // echo json_encode($tahun_perolehan);exit;

        
				if($last_year >= $tahun_perolehan){
					$nilai_perolehan_tahun_lalu		= $val['asset_purchase_value'];
				} else {
					$nilai_perolehan_tahun_lalu		= 0;
				}	

				if($sesi['year_period'] >= $tahun_perolehan){
					$nilai_perolehan_tahun_ini		= $val['asset_purchase_value'];
				} else {
					$nilai_perolehan_tahun_ini		= 0;
				}	
                $accumulation_amount_last_year = $this->getAssetDepreciationAccumulationAmountTotal($val['asset_id'], $last_year);
        // echo json_encode($accumulation_amount_last_year);exit;


                if(!empty($accumulation_amount_last_year)){
					$book_value_last_year 			= $this->getAssetDepreciationBookValue($val['asset_id'], $last_year);
				} else {
					if($nilai_perolehan_tahun_lalu == 0){
						$book_value_last_year			= 0;
					} else {
						$book_value_last_year			= $val['asset_purchase_value'];
					}
					
				}

                $accumulation_amount_this_year 		= $this->getAssetDepreciationAccumulationAmountTotal($val['asset_id'], $sesi['year_period']);

				if(!empty($accumulation_amount_this_year)){
					$book_value_this_year 			= $this->getAssetDepreciationBookValue($val['asset_id'], $sesi['year_period']);
				} else {
					if($nilai_perolehan_tahun_ini == 0){
						$book_value_this_year			= 0;
					} else {
						$book_value_this_year			= $val['asset_purchase_value'];
					}
					
				}


				$depreciation_amount 				= $this->getAssetDepreciationAmountTotal($val['asset_id'], $sesi['year_period']);
                // echo json_encode($depreciation_amount);exit;

				$data_assetreport[$key] = array (
					'asset_date'									=> $val['asset_purchase_date'],
					'asset_name'									=> $val['asset_name'],
					'asset_type_name'								=> $val['asset_type_name'],
					'asset_description'								=> $val['asset_description'],
					'asset_quantity'								=> $val['asset_quantity'],
					'item_unit_code'								=> $val['item_unit_code'],
					'asset_purchase_date'							=> $val['asset_purchase_date'],
					'asset_purchase_value_then'						=> $nilai_perolehan_tahun_lalu,
					'asset_purchase_value_now'						=> $nilai_perolehan_tahun_ini,
					'asset_estimated_lifespan'						=> $val['asset_estimated_lifespan'],
					// 'asset_estimated_lifespan_percentage'			=> $prosentase[nominal($val['asset_estimated_lifespan'])],
					'asset_depreciation_amount'						=> $depreciation_amount,
					'asset_depreciation_accumulation_last_year'		=> $accumulation_amount_last_year,
					'asset_depreciation_book_value_last_year'		=> $book_value_last_year,
					'asset_depreciation_accumulation_this_year'		=> $accumulation_amount_this_year,
					'asset_depreciation_book_value_this_year'		=> $book_value_this_year
				);
                // echo json_encode($data_assetreport);exit;

        }

        return view('content.AcctAssetReport.ListAcctAssetReport',compact('aset','year_now','year','yearlist','data_assetreport','last_year'));
    }


    public function getAssetDepreciationAccumulationAmountTotal($asset_id, $year){

        $data = AcctAssetReportitem::select(DB::raw("SUM(acct_asset_depreciation_item.asset_depreciation_item_accumulation_amount) AS asset_depreciation_item_accumulation_amount"))
        ->join('acct_asset_depreciation', 'acct_asset_depreciation.asset_depreciation_id','acct_asset_depreciation_item.asset_depreciation_id')
        ->where('acct_asset_depreciation_item.asset_depreciation_item_journal_status', 1)
        ->where('acct_asset_depreciation.asset_id', $asset_id)
        ->whereNotNull('acct_asset_depreciation_item.asset_depreciation_item_accumulation_amount')
        ->where('acct_asset_depreciation_item.asset_depreciation_item_year','<=', $year)
        ->get();
        // dump($asset_id);
        // dump($data);
        // echo json_encode($data);exit;
        return $data[0]['asset_depreciation_item_accumulation_amount'];

    }

    public function getAssetDepreciationAmountTotal($asset_id, $year){
        $data = AcctAssetReportitem::select(DB::raw("SUM(acct_asset_depreciation_item.asset_depreciation_item_amount) As asset_depreciation_item_amount_total"))
        ->join('acct_asset_depreciation', 'acct_asset_depreciation_item.asset_depreciation_id','acct_asset_depreciation.asset_depreciation_id')
        ->where('acct_asset_depreciation_item.asset_depreciation_item_journal_status', 1)
        ->where('acct_asset_depreciation.asset_id', $asset_id)
        ->where('acct_asset_depreciation_item.asset_depreciation_item_year', $year)
        ->get();

        return $data[0]['asset_depreciation_item_amount_total'];
    }

    public function getAcctAsset($branch_id, $year){
        $data = AcctAsset::select('acct_asset.asset_id', 'acct_asset.asset_code', 'acct_asset.asset_name', 'acct_asset.asset_purchase_value', 'acct_asset.asset_purchase_date', 'acct_asset.asset_estimated_lifespan', 'acct_asset.asset_description', 'acct_asset.asset_quantity', 'acct_asset.item_unit_code', 'acct_asset.asset_location_detail', 'acct_asset.asset_type_id','acct_asset_type.asset_type_name')
        ->join('acct_asset_type', 'acct_asset_type.asset_type_id','acct_asset.asset_type_id')
        ->where('acct_asset.branch_id', $branch_id)
        ->where('acct_asset.data_state', 0)
        ->get();
        
        if(!empty($year)){
        $data->where('acct_asset.asset_purchase_date', $year);
        }
        // echo json_encode($data);exit;
        return $data;
    }

    public function getAssetDepreciationBookValue($asset_id, $year){
        $data = AcctAssetReportitem::select('acct_asset_depreciation_item.asset_depreciation_item_id', 'acct_asset_depreciation_item.asset_depreciation_item_book_value')
        ->join('acct_asset_depreciation', 'acct_asset_depreciation.asset_depreciation_id','acct_asset_depreciation_item.asset_depreciation_id')
        // ->join('acct_asset_depreciation', 'acct_asset_depreciation_item.asset_depreciation_id','acct_asset_depreciation.asset_depreciation_id')
        ->where('acct_asset_depreciation_item.asset_depreciation_item_journal_status', 1)
        ->where('acct_asset_depreciation.asset_id', $asset_id)
        ->where('acct_asset_depreciation_item.asset_depreciation_item_year', $year)
        ->orderBy('acct_asset_depreciation_item.asset_depreciation_item_id', 'DESC')
        ->limit(1)
        ->get();
        // echo json_encode($data);exit;

        return $data;
    }

    public function ProsentasePenyusutan(){
		$prosentase_penyusutan = array ("20.00" => "5%", "8.00" => "13%", "4.00" => "25%");

		return $prosentase_penyusutan;
	}

    public function add() {
        $sessiondata = Session::get('supplier-data');
        return view('content.AcctAssetReport.FormAddAcctAssetType',compact('sessiondata'));
    }

    public function getAssetTypeName($asset_type_id){
        $aset = AcctAssetType::where('asset_type_id',$asset_type_id)
        ->where('data_state',0)
        ->first();

        return $aset['asset_type_name'];
    }

    public function detail($asset_id) {
        $sessiondata = Session::get('supplier-data');
        $acctasset  = AcctAsset::find($asset_id);
        return view('content.AcctAset.FormDetailAcctAsset',compact('sessiondata','acctasset'));
    }

    public function filter(Request $request)
    {
        $year = $request->year;
        $branch_id = $request->branch_id;


        Session::put('year', $year);
        Session::put('branch_id', $branch_id);


        return redirect('/report-aset');
    }
}
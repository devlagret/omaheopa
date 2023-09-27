<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAsset;
use App\Models\AcctAssetReport;
use App\Models\AcctAssetReportItem;
use App\Models\AcctAssetType;
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
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAsset;
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

    public function detail($asset_id) {
        $sessiondata = Session::get('supplier-data');
        $acctasset  = AcctAsset::find($asset_id);
        return view('content.AcctAset.FormDetailAcctAsset',compact('sessiondata','acctasset'));
    }


    public function depreciation($asset_id){
        $acctasset  = AcctAsset::find($asset_id);
        
        return view('content.AcctAset.FormDetailAcctAssetDepreciation',compact('acctasset'));
    }

    // public function getAcctAssetDepreciationItem_Detail($id){
    //     $data = AcctAsset::  select('acct_asset_depreciation_item.asset_depreciation_item_id, acct_asset_depreciation_item.asset_depreciation_item_year_to, acct_asset_depreciation_item.asset_depreciation_item_month, acct_asset_depreciation_item.asset_depreciation_item_year, acct_asset_depreciation_item.asset_depreciation_item_amount, acct_asset_depreciation_item.asset_depreciation_item_book_value, acct_asset_depreciation_item.asset_depreciation_item_accumulation_amount, acct_asset_depreciation_item.asset_depreciation_item_journal_status, acct_asset_depreciation_item.asset_depreciation_item_journal_date');
    //     $this->database->from('acct_asset_depreciation_item');
    //     $this->database->join('acct_asset_depreciation', 'acct_asset_depreciation_item.asset_depreciation_id = acct_asset_depreciation.asset_depreciation_id');
    //     $this->database->join('acct_asset', 'acct_asset_depreciation.asset_id = acct_asset.asset_id');
    //     $this->database->where('acct_asset_depreciation.asset_id',$id);
    //     return $this->database->get()->result_array();
    // }

    // public function getAcctAsset_Detail($id){
    //     $this->database->select('acct_asset.asset_id, acct_asset.asset_type_id, acct_asset_type.asset_type_name, acct_asset.location_id, acct_asset.asset_purchase_date, acct_asset.asset_purchase_value, acct_asset.asset_depreciation_value, acct_asset.asset_location_detail, acct_asset.asset_description, acct_asset.asset_code, acct_asset.asset_name, acct_asset.voided_remark, acct_asset.asset_book_value, acct_asset.asset_depreciation_type, acct_asset.asset_salvage_value, acct_asset.asset_estimated_lifespan, acct_asset.item_unit_code');
    //     $this->database->from('acct_asset');
    //     // $this->database->join('core_location', 'acct_asset.location_id = core_location.location_id');
    //     $this->database->join('acct_asset_type', 'acct_asset.asset_type_id = acct_asset_type.asset_type_id');
    //     $this->database->where('acct_asset.asset_id',$id);
    //     $result = $this->database->get()->row_array();
    //     return $result;
    // }

    // public function getAcctAccount(){
    //     $query = $this->database->query("SELECT account_id, concat(account_code,' - ',account_name) AS account_code FROM acct_account WHERE data_state = 0 AND parent_account_status = 0");
    //     $result = $query->result_array();	
    //     return $result;
    // }


}
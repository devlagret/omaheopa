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
}
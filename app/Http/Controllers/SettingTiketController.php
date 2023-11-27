<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PreferenceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SettingTiketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('datases');
        Session::forget('arraydatases');

        $data = PreferenceCompany::select('tiket_status','company_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $tiket = array(
            1 => 'Merchant',
            2 => 'Global', 
        );
        return view('content.SettingTiket.ListSettingTiket',compact('data','tiket'));
    }



    public function processEditTiket(Request $request)
    {
        $table                         = PreferenceCompany::findOrFail($request->company_id);
        $table->tiket_status         = $request->tiket_status;
        

        if ($table->save()) {
            $msg = 'Ubah Tiket Berhasil';
            return redirect()->back()->with('msg', $msg);
        } else {
            $msg = 'Ubah Tiket Gagal';
            return redirect()->back()->with('msg', $msg);
        }
    }
    }


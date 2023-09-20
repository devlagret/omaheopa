<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PreferenceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');    
    }
    public function Index() {
        $pref = PreferenceCompany::find(Auth::user()->company_id,['checkin_time','checkout_time']);
        return view('content.PreferenceCompany.CheckInCheckOutTime')->with('pref',$pref);
    }
    public function processEditCCTime(Request $request) {
        $field = $request->validate(['checkin_time'=>'required','checkout_time'=>'required'],['checkin_time.required'=>'Waktu Check-In Harus Diisi','checkout_time.required'=>'Waktu Check-Out Harus Diisi']);
        $pref = PreferenceCompany::updateOrCreate(['company_id'=>Auth::user()->company_id],['checkin_time'=>$field['checkin_time'],'checkout_time'=>$field['checkout_time'],'updated_id'=>Auth::id()]);
        if ($pref){
            return redirect()->back()->with('msg','Ubah Jam Check-In dan Check-Out Berhasil');
        }
        return redirect()->back()->with('msg','Ubah Jam Check-In dan Check-Out Gagal');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreDivision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreDivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index() {
        Session::forget('division-data');
        $div = CoreDivision::where('company_id', Auth::user()->company_id)->
        get();
        return view('content.CoreDivision.ListDivision')->with(['div'=>$div]);
    }
    public function add() {
        $sessiondata = Session::get('division-data');
        return view('content.CoreDivision.FormAddDivision',compact('sessiondata'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('division-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['division_name'] = '';
            $sessiondata['division_code'] = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('division-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        if(CoreDivision::create([
            'division_code'=>$request->division_code,
            'division_name'=>$request->division_name,
            'company_id'=>Auth::user()->company_id,
            'created_id'=>Auth::id()])){
           return redirect()->route('division.index')->with(['type'=>'success','msg'=>'Tambah Divisi Berhasil']);
        }
        return redirect()->route('division.index')->with(['type'=>'danger','msg'=>'Tambah Divisi Gagal']);

    }
    public function edit($division_id) {
        $sessiondata = Session::get('division-data');
        $div = CoreDivision::find($division_id);
        return view('content.CoreDivision.FormEditDivision',compact('sessiondata','div'));
    }
    public function processEdit(Request $request){
        $div = CoreDivision::find($request->division_id);
        $div->division_code = $request->division_code;
        $div->division_name = $request->division_name;
        $div->updated_id = Auth::id();
        if($div->save()){
           return redirect()->route('division.index')->with(['type'=>'success','msg'=>'Edit Divisi Berhasil']);
        }
        return redirect()->route('division.index')->with(['type'=>'danger','msg'=>'Edit Divisi Gagal']);
    }
    public function delete($division_id) {
        $div=CoreDivision::find($division_id);
        $div->data_state = '1';
        $div->deleted_id = Auth::id();
        if($div->save()){if($div->delete()){
           return redirect()->route('division.index')->with(['type'=>'success','msg'=>'Hapus Divisi Berhasil']);
        };}
        return redirect()->route('division.index')->with(['type'=>'danger','msg'=>'Hapus Divisi Gagal']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreBuildingController extends Controller
{
         public function __construct()
        {
            $this->middleware('auth');
        }
        public function index() {
            Session::forget('building-data');
            $building = CoreBuilding::get();
            return view('content.CoreBuilding.ListBuilding')->with(['building'=>$building]);
        }
        public function add() {
            $sessiondata = Session::get('building-data');
            return view('content.CoreBuilding.FormAddBuilding',compact('sessiondata'));
        }
        public function elementsAdd(Request $request){
            $sessiondata = Session::get('building-data');
            if(!$sessiondata || $sessiondata == ''){
                $sessiondata['building_name']   = '';
            }
            $sessiondata[$request->name] = $request->value;
            Session::put('building-data', $sessiondata);
        }
        public function processAdd(Request $request) {
            if(CoreBuilding::create(['building_name'=>$request->building_name,'created_id'=>Auth::id()])){
               return redirect()->route('building.index')->with(['type'=>'success','msg'=>'Tambah Bangunan Berhasil']);
            }
            return redirect()->route('building.index')->with(['type'=>'danger','msg'=>'Tambah Bangunan Gagal']);

        }
        public function edit($building_id) {
            $sessiondata = Session::get('room-data');
            $building = CoreBuilding::find($building_id);
            return view('content.CoreBuilding.FormEditBuilding',compact('sessiondata','building'));
        }
        public function processEdit(Request $request){
            $building = CoreBuilding::find($request->building_id);
            $building->building_name = $request->building_name;
            $building->updated_id = Auth::id();
            if($building->save()){
               return redirect()->route('building.index')->with(['type'=>'success','msg'=>'Edit Bangunan Berhasil']);
            }
            return redirect()->route('building.index')->with(['type'=>'danger','msg'=>'Edit Bangunan Gagal']);
        }
        public function delete($building_id) {
            $building=CoreBuilding::find($building_id);
            $building->data_state = '1';
            $building->deleted_id = Auth::id();
            if($building->save()){if($building->delete()){
               return redirect()->route('building.index')->with(['type'=>'success','msg'=>'Hapus Bangunan Berhasil']);
            };}
            return redirect()->route('building.index')->with(['type'=>'danger','msg'=>'Hapus Bangunan Gagal']);
        }
}

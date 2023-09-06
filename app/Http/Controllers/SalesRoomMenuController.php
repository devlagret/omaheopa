<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesRoomMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SalesRoomMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function menuType():Iterable {
        return collect([1 => 'Breakfast', 2 => 'Lunch', 3 => 'Dinner']);
    }
    public function index() {
        Session::forget('sales-room-menu-data');
        $roommenu = SalesRoomMenu::get();
        return view('content.SalesRoomMenu.ListSalesRoomMenu')->with(['roommenu'=>$roommenu,'tipemenu'=> $this->menuType()]);
    }
    public function add() {
        $sessiondata = Session::get('sales-room-menu-data');
        $tipemenu = $this->menuType();
        return view('content.SalesRoomMenu.FormAddSalesRoomMenu',compact('sessiondata','tipemenu'));
    }
    public function elementsAdd(Request $request){
        $sessiondata = Session::get('sales-room-menu-data');
        if(!$sessiondata || $sessiondata == ''){
            $sessiondata['room_type_name'] = '';
            $sessiondata['room_menu_type'] = '';
            $sessiondata['room_menu_price'] = '';
        }
        $sessiondata[$request->name] = $request->value;
        Session::put('sales-room-menu-data', $sessiondata);
    }
    public function processAdd(Request $request) {
        if(SalesRoomMenu::create([
            'room_menu_name'=>$request->room_menu_name,
            'room_menu_type'=>$request->room_menu_type,
            'room_menu_price'=>$request->room_menu_price,
            'room_menu_token'=>Str::uuid(),
            'created_id'=>Auth::id()])){
           return redirect()->route('sales-room-menu.index')->with(['type'=>'success','msg'=>'Tambah Menu Kamar Berhasil']);
        }
        return redirect()->route('sales-room-menu.index')->with(['type'=>'danger','msg'=>'Tambah Menu Kamar Gagal']);

    }
    public function edit($room_menu_id) {
        $sessiondata = Session::get('sales-room-menu-data');
        $roommenu = SalesRoomMenu::find($room_menu_id);
        $tipemenu = $this->menuType();
        return view('content.SalesRoomMenu.FormEditSalesRoomMenu',compact('sessiondata','tipemenu','roommenu'));
    }
    public function processEdit(Request $request){
        $roommenu = SalesRoomMenu::find($request->room_menu_id);
        $roommenu->room_menu_type = $request->room_menu_type;
        $roommenu->room_menu_price = $request->room_menu_price;
        $roommenu->room_menu_name = $request->room_menu_name;
        $roommenu->updated_id = Auth::id();
        if($roommenu->save()){
           return redirect()->route('sales-room-menu.index')->with(['type'=>'success','msg'=>'Edit Menu Kamar Berhasil']);
        }
        return redirect()->route('sales-room-menu.index')->with(['type'=>'danger','msg'=>'Edit Menu Kamar Gagal']);
    }
    public function delete($room_menu_id) {
        $roommenu=SalesRoomMenu::find($room_menu_id);
        $roommenu->data_state = '1';
        $roommenu->deleted_id = Auth::id();
        if($roommenu->save()){if($roommenu->delete()){
           return redirect()->route('sales-room-menu.index')->with(['type'=>'success','msg'=>'Hapus Menu Kamar Berhasil']);
        };}
        return redirect()->route('sales-room-menu.index')->with(['type'=>'danger','msg'=>'Hapus Menu Kamar Gagal']);
    }
}

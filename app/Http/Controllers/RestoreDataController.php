<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RestoreDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }
    private function check(){
        if(Auth::id()!=1){
        return redirect()->route('home');
        }
    }
    public function index(){
        $this->check();
        $table = collect();
        $dbName = config('app.db');
        $tables = collect(DB::select('SHOW TABLES'))
        ->whereNotIn('Tables_in_'.$dbName,[
            'preference_company',
            'purchase_payment_giro',
            'sales_collection_expense',
            'sales_collection_item',
            'sales_invoice_expense',
            'sales_invoice_item',
            'sales_order_item',
            'system_change_log',
            'system_log_user',
            'system_menu',
            'system_menu_mapping',
            'acct_journal_voucher',
            'acct_journal_voucher_item',
            'acct_account_balance_detail',
            'acct_account_balance',
            ])
        ->pluck('Tables_in_'.$dbName)->flatten();
        foreach($tables as $val){
            $data = DB::table($val)->where('deleted_at','!=',null)->orWhere('data_state','1')->count();
            if($data != 0){
                $table->push([$val=>$data]);
            }
        }
        $table = $table->collapse()->sortDesc();
        return view('content.RestoreData.RestoreData',compact('table'));
    }
    public function table($table) {
        $this->check();
        $header = collect(DB::select('DESCRIBE '.$table))->pluck('Field');
        $data = collect(DB::table($table)->where('data_state','1')->orWhere('deleted_at','!=',null)->get());
        $pk = collect(DB::select("SHOW KEYS FROM ".$table." WHERE Key_name = 'PRIMARY'"))->pluck('Column_name')[0];
        return view('content.RestoreData.RestoreDataTable',compact('header','pk','data','table'));
    }
    public function restore($table,$col,$id){
        $this->check();
        try{
        $data = DB::table($table)->where($col,$id);
        $data->update(['data_state'=>0,'deleted_at'=>null]);}catch(\Illuminate\Database\QueryException $e){
            error_log($e);
            return redirect()->route('restore.table', ['table' => $table])->with(['type'=>'danger','msg'=>'Restore Data Gagal']);
        }
        return redirect()->route('restore.table', ['table' => $table])->with(['type'=>'success','msg'=>'Restore Data Berhasil']);
    }
    public function forceDelete($table,$col,$id) {
        return [$table,$col,$id];
        $this->check();
        try{
        $data = DB::table($table)->where($col,$id);
        $data->forceDelete();}catch(\Illuminate\Database\QueryException $e){
            error_log($e);
            return redirect()->route('restore.table', ['table' => $table])->with(['type'=>'danger','msg'=>'Hapus Gagal']);
        }
        return redirect()->route('restore.table', ['table' => $table])->with(['type'=>'success','msg'=>'Hapus Berhasil']);
    }
}

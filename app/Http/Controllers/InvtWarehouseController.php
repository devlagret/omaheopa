<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtWarehouse;
use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvtWarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Session::forget('warehouses');
        $data = InvtWarehouse::with('merchant')->where('data_state', '=', 0)
            ->where('company_id', Auth::user()->company_id);
        if (Auth::id() != 1 || Auth::user()->merchant_id != null) {
            $data->where('merchant_id', Auth::user()->merchant_id);
        }
        $data = $data->get();
        return view('content.InvtWarehouse.ListInvtWarehouse', compact('data'));
    }

    public function addWarehouse()
    {
        $warehouses = Session::get('warehouses');
        $merchant   = SalesMerchant::where('data_state', 0);
        if (Auth::id() != 1 || Auth::user()->merchant_id != null) {
            $merchant->where('merchant_id', Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        return view('content.InvtWarehouse.FormAddInvtWarehouse', compact('warehouses', 'merchant'));
    }

    public function addElementsWarehouse(Request $request)
    {
        $warehouses  = Session::get('warehouses');
        if (!$warehouses || $warehouses == '') {
            $warehouses['warehouse_code'] = '';
            $warehouses['warehouse_name'] = '';
            $warehouses['warehouse_phone'] = '';
            $warehouses['warehouse_address'] = '';
        }
        $warehouses[$request->name] = $request->value;
        Session::put('warehouses', $warehouses);
    }

    public function processAddWarehouse(Request $request)
    {
        // dump($request->all());exit;
        $fields = $request->validate([
            'warehouse_code'    => 'required',
            'warehouse_name'    => 'required',
            'warehouse_phone'   => 'required',
            'warehouse_address' => 'required'
        ]);

        $data = InvtWarehouse::create([
            'merchant_id'    => $request->merchant_id,
            'warehouse_code'    => $fields['warehouse_code'],
            'warehouse_name'    => $fields['warehouse_name'],
            'warehouse_phone'   => $fields['warehouse_phone'],
            'warehouse_address' => $fields['warehouse_address'],
            'company_id'        => Auth::user()->company_id,
            'created_id'        => Auth::id(),
            'updated_id'        => Auth::id(),
        ]);

        if ($data->save()) {
            $msg = "Tambah Gudang Berhasil";
            return redirect()->route('warehouse')->with('msg', $msg);
        } else {
            $msg = "Tambah Gudang Gagal";
            return redirect()->route('warehouse')->with('msg', $msg);
        }
    }

    public function editWarehouse($warehouse_id)
    {
        $data   = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();
        $merchant   = SalesMerchant::where('data_state', 0);
        if (Auth::id() != 1 || Auth::user()->merchant_id != null) {
            $merchant->where('merchant_id', Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        return view('content.InvtWarehouse.FormEditInvtWarehouse', compact('data', 'merchant'));
    }

    public function processEditWarehouse(Request $request)
    {
        $fields = $request->validate([
            'warehouse_id'      => '',
            'warehouse_code'    => 'required',
            'warehouse_name'    => 'required',
            'warehouse_phone'   => 'required',
            'warehouse_address' => 'required'
        ]);

        $table                      = InvtWarehouse::findOrFail($fields['warehouse_id']);
        $table->merchant_id      = $request->merchant_id;
        $table->warehouse_code      = $fields['warehouse_code'];
        $table->warehouse_name      = $fields['warehouse_name'];
        $table->warehouse_phone     = $fields['warehouse_phone'];
        $table->warehouse_address   = $fields['warehouse_address'];
        $table->updated_id          = Auth::id();

        if ($table->save()) {
            $msg = "Ubah Gudang Berhasil";
            return redirect()->route('warehouse')->with('msg', $msg);
        } else {
            $msg = "Ubah Gudang Gagal";
            return redirect()->route('warehouse')->with('msg', $msg);
        }
    }

    public function deleteWarehouse($warehouse_id)
    {
        $table             = InvtWarehouse::findOrFail($warehouse_id);
        $table->data_state = 1;
        $table->updated_id = Auth::id();

        if ($table->save()) {
            $msg = "Hapus Gudang Berhasil";
            return redirect()->route('warehouse')->with('msg', $msg);
        } else {
            $msg = "Hapus Gudang Gagal";
            return redirect()->route('warehouse')->with('msg', $msg);
        }
    }

    public function addResetWarehouse()
    {
        Session::forget('warehouses');
        return redirect()->route('warehouse');
    }

    //check warehouse
    public function checkWarehouse(Request $request)
    {
        $datawarehouse = InvtWarehouse::select('*')
            ->where('merchant_id', $request->merchant_id)
            ->where('data_state', 0)
            ->first();

        if ($datawarehouse == null) {
            $return_data =  '';
            return $return_data;
        } else {
            $return_data = 1;
            return $return_data;
        }
    }
    public function checkWarehouseDtl(Request $request)
    {
        $datawarehouse = InvtWarehouse::where('merchant_id', $request->merchant_id)
            ->where('data_state', 0)
            ->first();
            
        if ($datawarehouse == null) {
            $datamerchant = SalesMerchant::find($request->merchant_id);
            return response(['count'=>0,'merchant'=>$datamerchant->merchant_name]);
        } else {
            return response(['count'=>1]);
        }
    }
}

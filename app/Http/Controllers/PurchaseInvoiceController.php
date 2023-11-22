<?php

namespace App\Http\Controllers;

use App\Models\InvtItem;
use App\Helpers\ItemHelper;
use App\Models\AcctAccount;
use Illuminate\Support\Str;
use App\Helpers\StockHelper;
use App\Models\CoreSupplier;
use App\Models\InvtItemUnit;
use Illuminate\Http\Request;
use App\Models\InvtItemStock;
use App\Models\InvtWarehouse;
use App\Models\SalesMerchant;
use App\Helpers\JournalHelper;
use App\Models\JournalVoucher;
use App\Models\PurchaseInvoice;
use App\Models\InvtItemCategory;
use App\Models\AcctAccountSetting;
use App\Models\JournalVoucherItem;
use Illuminate\Support\Facades\DB;

use App\Models\PurchaseInvoiceItem;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Session;
use App\Models\PreferenceTransactionModule;

class PurchaseInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!$start_date = Session::get('start_date')) {
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if (!$end_date = Session::get('end_date')) {
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        Session::forget('datases');
        Session::forget('items');
        Session::forget('arraydatases');
        Session::forget('purchase-token');
        $data = PurchaseInvoice::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->where('purchase_invoice_date', '>=', $start_date)
            ->where('purchase_invoice_date', '<=', $end_date)
            ->get();
        return view('content.PurchaseInvoice.ListPurchaseInvoice', compact('data', 'start_date', 'end_date'));
    }

    public function addPurchaseInvoice()
    {
        if(empty(Session::get('purchase-token'))){
            Session::put('purchase-token',Str::uuid());
        }
        $categorys = InvtItemCategory::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('item_category_name', 'item_category_id');
        $items     = Session::get('items');
        $warehouses = InvtWarehouse::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('warehouse_name', 'warehouse_id');
        $suppliers = CoreSupplier::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('supplier_name', 'supplier_id');

        $merchant   = SalesMerchant::where('data_state', 0);
        if (Auth::id() != 1 || Auth::user()->merchant_id != null) {
            $merchant->where('merchant_id', Auth::user()->merchant_id);
        }
        $purchase_payment_method = array(
            0 => 'Tunai',
            1 => 'Hutang Supplier'
        );
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id')->prepend('Barang Umum', 0);
        $datases = Session::get('datases');
        $arraydatases = Session::get('purchase-item');
        return view('content.PurchaseInvoice.FormAddPurchaseInvoice', compact('merchant', 'suppliers', 'items', 'warehouses', 'datases', 'arraydatases', 'purchase_payment_method'));
    }

    public function detailPurchaseInvoice($purchase_invoice_id)
    {
        $warehouses = InvtWarehouse::where('data_state', 0)
            ->where('company_id', Auth::user()->company_id)
            ->get()
            ->pluck('warehouse_name', 'warehouse_id');
        $purchaseinvoice = PurchaseInvoice::where('purchase_invoice_id', $purchase_invoice_id)->first();
        $purchaseinvoiceitem = PurchaseInvoiceItem::where('purchase_invoice_id', $purchase_invoice_id)->get();
        return view('content.PurchaseInvoice.DetailPurchaseInvoice', compact('purchaseinvoice', 'warehouses', 'purchaseinvoiceitem'));
    }

    public function addElementsPurchaseInvoice(Request $request)
    {
        $datases = Session::get('datases');
        if (!$datases || $datases == '') {
            $datases['purchase_invoice_supplier']   = '';
            $datases['warehouse_id']                = '';
            $datases['purchase_invoice_date']       = '';
            $datases['purchase_invoice_remark']     = '';
            $datases['supplier_id']     = '';
        }
        $datases[$request->name] = $request->value;
        Session::put('datases', $datases);
    }

    public function addArrayPurchaseInvoice(Request $request)
    {
        $itmdata = InvtItem::with('category')->find($request->item_id);
        $whsdata = InvtWarehouse::find($request->warehouse_id);
        $mrcdata = SalesMerchant::find($request->merchant_id);
        $untdata = InvtItemUnit::find($request->item_unit);
        $data = collect(Session::get('purchase-item'));
        $item = collect();
        $item->put('item_id', $request->item_id);
        $item->put('merchant_id', $request->merchant_id);
        $item->put('merchant_name', $mrcdata->merchant_name);
        $item->put('warehouse_id', $request->warehouse_id);
        $item->put('warehouse_name', $whsdata->warehouse_name);
        $item->put('item_name', $itmdata->item_name);
        $item->put('item_category_id', $request->item_category_id);
        $item->put('item_category_name', $itmdata->category->item_category_name);
        $item->put('item_unit_id', $request->item_unit);
        $item->put('item_unit_name', $untdata->item_unit_name);
        $item->put('item_unit_cost', $request->item_unit_cost);
        $item->put('quantity', $request->quantity);
        $item->put('subtotal_amount', $request->subtotal_amount);
        $item->put('item_expired_date', $request->item_expired_date);
        $item->put('discount_amount', $request->discount_amount);
        $item->put('discount_percentage', $request->discount_percentage);
        $item->put('subtotal_amount_after_discount', $request->subtotal_amount_after_discount);
        $data->put($request->item_id, $item);
        Session::put('purchase-item', $data->toArray());
        return redirect('/purchase-invoice/add');
    }

    public function deleteArrayPurchaseInvoice($record_id)
    {
        $data = collect(Session::get('purchase-item'));
        $data->forget($record_id);
        Session::put('purchase-item', $data->toArray());

        return redirect('/purchase-invoice/add');
    }

    public function processAddPurchaseInvoice(Request $request)
    {
        $token = Session::get('purchase-token');
        if(empty($token)){
            return redirect()->route('pi.add')->with('msg','Tambah Pembelian Berhasil*');
        }
        $fields = $request->validate([
            'supplier_id'               => 'required',
            'warehouse_id'              => 'required',
            'purchase_invoice_date'     => 'required',
            'subtotal_item'             => 'required',
            'subtotal_amount_total'     => 'required',
            'total_amount'              => 'required',
            'paid_amount'               => 'required',
            'purchase_payment_method'   => 'required',
            'owing_amount'              => 'required'
        ],[
            'supplier_id.required'=>'Kolom Supplier Harus diisi',
            'warehouse_id.required'=>'Kolom Gudang Harus diisi',
            'purchase_invoice_date.required'=>'Kolom Tanggal Pembelian Harus diisi',
            'purchase_payment_method.required'=>'Kolom Metode Pemmbelian Harus diisi'
        ]);
        try {
        DB::beginTransaction();
        PurchaseInvoice::create([
            'supplier_id'               => $request['supplier_id'],
            'merchant_id'               => $request['merchant_id'],
            'warehouse_id'              => $fields['warehouse_id'],
            'purchase_invoice_date'     => $fields['purchase_invoice_date'],
            'purchase_invoice_due_date' => date('Y-m-d', strtotime('+'.$request['purchase_invoice_due_day'].' days',strtotime($fields['purchase_invoice_date']))),
            'purchase_invoice_remark'   => $request->purchase_invoice_remark,
            'ppn_percentage'            => $request->tax_ppn_percentage,
            'ppn_amount'                => $request->tax_ppn_amount,
            'subtotal_item'             => $fields['subtotal_item'],
            'discount_percentage_total' => ($request->discount_percentage_total??0),
            'discount_amount_total'     => ($request->discount_amount_total??0),
            'subtotal_amount_total'     => $fields['subtotal_amount_total'],
            'total_amount'              => $fields['total_amount'],
            'paid_amount'               => $fields['paid_amount'],
            'owing_amount'              => $fields['total_amount'],
            'purchase_invoice_token'    => $token,
            'company_id'                => Auth::user()->company_id,
            'created_id'                => Auth::id(),
            'updated_id'                => Auth::id()
        ]);
        $purchase_invoice_id = PurchaseInvoice::latest()->where('purchase_invoice_token',$token)->where('company_id', Auth::user()->company_id)->first();
        $item = Session::get('purchase-item');
        foreach ($item  as $key => $val) {
            $dataarray = array(
                'purchase_invoice_id'   => $purchase_invoice_id['purchase_invoice_id'],
                'merchant_id'           => $val['merchant_id'],
                'item_category_id'      => $val['item_category_id'],
                'item_unit_id'          => $val['item_unit_id'],
                'item_id'               => $val['item_id'],
                'quantity'              => $val['quantity'],
                'item_unit_cost'        => $val['item_unit_cost'],
                'discount_amount'       => ($val['discount_amount']??0),
                'discount_percentage'   => ($val['discount_percentage']??0),
                'subtotal_amount'       => $val['subtotal_amount'],
                'item_expired_date'     => $val['item_expired_date'],
                'company_id'            => Auth::user()->company_id,
                'created_id'            => Auth::id(),
                'updated_id'            => Auth::id()
            );
            PurchaseInvoiceItem::create($dataarray);
            StockHelper::find($val['item_id'],$val['item_unit_id'])->add($val['quantity']);
        }
        if($request->purchase_payment_method){
            //* Hutang
            JournalHelper::token($token)->make('Purchase Invoice',['purchase_cash_payable_account','purchase_payable_account'],$fields['total_amount']);
        }else{
            //* Tunai
            JournalHelper::token($token)->make('Purchase Invoice',['purchase_cash_account','purchase_account'],$fields['total_amount']);
         }
        DB::commit();
        Session::forget('purchase-token');
        // Session::forget('datases');
        // Session::forget('purchase-item');
        return redirect()->route('pi.add')->with('msg','Tambah Pembelian Berhasil');
    } catch (\Exception $e) {
        DB::rollBack();
        report($e);
        Session::forget('purchase-token');
        return redirect()->route('pi.add')->with('msg','Tambah Pembelian Gagal');
        }
    
    }

    public function filterPurchaseInvoice(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/purchase-invoice');
    }

    public function addResetPurchaseInvoice()
    {
        Session::forget('datases');
        Session::forget('purchase-item');
        return redirect()->route('pi.add');
    }

    public function filterResetPurchaseInvoice()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/purchase-invoice');
    }
    public function getTransactionModuleID($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)->first();
        return $data['transaction_module_id'] ?? '';
    }
    public function getTransactionModuleName($transaction_module_code)
    {
        $data = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)->first();

        return $data['transaction_module_name'];
    }
    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
            ->where('account_setting_name', $account_setting_name)
            ->first();

        return $data['account_setting_status'];
    }
    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)
            ->where('account_setting_name', $account_setting_name)
            ->first();

        return $data['account_id'];
    }
    public function getAccountDefaultStatus($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_default_status'];
    }
    public function getCategory(Request $request)
    {
        $items = Session::get('datases');
        $g = 0;
        if ($request->merchant_id == 0) {
            $g = 1;
        }
        return response(ItemHelper::getCategory($items['item_category_id'] ?? null, $request, $g));
    }
    public function getUnit(Request $request)
    {
        $items = Session::get('datases');
        return response(ItemHelper::getItemUnit($items['item_unit'] ?? null, $request));
    }
    public function getItem(Request $request)
    {
        $items = Session::get('datases');
        return response(ItemHelper::getItem($items['item_id'] ?? null, $request));
    }
    public function getWarehouse(Request $request)
    {
        $items = Session::get('datases');
        $data = '';
        try {
            $warehouse = InvtWarehouse::select('warehouse_id', 'warehouse_name')
                ->where('merchant_id', $request->merchant_id)
                ->orWhereNull('merchant_id')
                ->get();
            $items['warehouse_id'] ?? $items['warehouse_id'] = $warehouse->first()->warehouse_id;
            foreach ($warehouse as $val) {
                $data .= "<option value='{$val['warehouse_id']}' " . ($items['warehouse_id'] == $val['warehouse_id'] ? 'selected' : '') . ">{$val['warehouse_name']}</option>\n";
            }
            if ($warehouse->count() == 0) {
                $data = "<option>Wahana / Merchant Tidak Memiliki Gudang</option>\n";
            }
            return response($data);
        } catch (\Exception $e) {
            report($e);
            return response($e);
        }
    }
}

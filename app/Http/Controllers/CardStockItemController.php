<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InvtItem;
use App\Models\InvtItemMutation;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use Carbon\Carbon;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CardStockItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // InvtItemMutation::select(DB::raw('SUM(stock_out) as stock_out'))
        // ->where('item_id',3)
        // ->where('company_id', Auth::user()->company_id)
        // ->get()->dd();
        $filter = Session::get('filter-card');
        $data = InvtItemStock::with('item.merchant','category','unit')->get();
        $mutation = InvtItemMutation::where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $filter['start_date']??Carbon::now()->format('Y-m-d'))
        ->where('transaction_date', '<=', $filter['end_date']??Carbon::now()->format('Y-m-d'))
        ->get();
        return view('content.CardStockItem.ListCardStockItem',compact('filter','data','mutation'));
    }
    public function table(Request $request)
    {
        $filter = Session::get('filter-card');
        $sd = $filter['start_date']??Carbon::now()->format('Y-m-d');
        $ed = $filter['end_date']??Carbon::now()->format('Y-m-d');
        $data_item = InvtItemStock::addSelect([
        'opening_balance' => InvtItemMutation::select('opening_balance')
        ->whereColumn('item_id', 'invt_item_stock.item_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $sd)
        ->where('transaction_date', '<=', $ed)
        ->limit(1),
        'stock_in' => InvtItemMutation::select(DB::raw('SUM(stock_in) as stock_in'))
        ->whereColumn('item_id', 'invt_item_stock.item_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $sd)
        ->where('transaction_date', '<=', $ed),
        'stock_out'=>InvtItemMutation::select(DB::raw('SUM(stock_out) as stock_out'))
        ->whereColumn('item_id', 'invt_item_stock.item_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $sd)
        ->where('transaction_date', '<=', $ed),
        'last_balence'=> InvtItemMutation::select('last_balence')
        ->whereColumn('item_id', 'invt_item_stock.item_id')
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $sd)
        ->where('transaction_date', '<=', $ed)
        ->orderByDesc('item_mutation_id')
        ->limit(1)
        ])->with('item.merchant','unit','category')
        ->where('company_id', Auth::user()->company_id);

        $draw 				= 		$request->get('draw');
        $start 				= 		$request->get("start");
        $rowPerPage 		= 		$request->get("length");
        $orderArray 	    = 		$request->get('order');
        $columnNameArray 	= 		$request->get('columns');
        $searchValue 		= 		$request->search['value'];
        $sort=collect();
        foreach ($orderArray as $key => $or){
            $sort->push([$columnNameArray[$or['column']]['data'],$or['dir']]);
        }
        // $totalFilter = $data_item;
        // if (!empty($searchValue)) {
        //     if (count($valueArray) != 1) {
        //         foreach ($valueArray as $key => $val) {
        //             $totalFilter = $totalFilter->where('invt_item.item_name','like','%'.$val.'%');
        //         }
        //     } else {
        //         $totalFilter = $totalFilter->where('invt_item.item_name','like','%'.$searchValue.'%');
        //     }
        // }
        // $totalFilter = $totalFilter->count();


        $arrData = $data_item;
        // if (!empty($searchValue)) {
        //     if (count($valueArray) != 1) {
        //         foreach ($valueArray as $key => $val) {
        //             $arrData = $arrData->where('item_name','like','%'.$val.'%');
        //         }
        //     } else {
        //         $arrData = $arrData->where('item_name','like','%'.$searchValue.'%');
        //     }
        // }
        $arrData = $arrData->skip($start)->take($rowPerPage);
        $arrData = $arrData->get();

        $no = $start;
        $data = collect();
        foreach ($arrData as $key => $val) {
            $no++;
            $row                        = collect();
            $row->put('no', "<div class='text-center'>".$no.".</div>");
            $row->put('merchant', $val->item->merchant->merchant_name);
            $row->put('item_category_name', $val->category->item_category_name);
            $row->put('item_name', $val->item->item_name);
            $row->put('item_unit_name', $val->unit->item_unit_name);
            $row->put('opening_stock', $val->opening_balance??0);
            $row->put('stock_in', $val->stock_in??0);
            $row->put('stock_out', $val->stock_out??0);
            $row->put('last_balence', $val->last_balence??0);
            $row->put('action', "<div class='text-center'><a type='button' href='".route('sc.print',$val->item_stock_id)."' class='btn btn-secondary btn-sm'><i class='fa fa-file-pdf'></i> Kartu Stok</a></div>");

            $data->push($row);
        }
        $data= $data->filter(function ($item) use($searchValue){
            return preg_match("/".$searchValue."/i",$item);
        });
        $data = $data->sortBy($sort->toArray());
        $totalFilter = $data->count();
        $response = array(
            "draw"              => intval($draw),
            "recordsTotal"      => $data_item->count(),
            "recordsFiltered"   => $totalFilter,
            "data"              => $data,
        );

        return response()->json($response);
    }

    public function getOpeningStock($item_category_id, $item_id, $item_unit_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = InvtItemMutation::select('opening_balence')
        ->where('data_state',0)
        ->where('item_id', $item_id)
        ->where('item_category_id', $item_category_id)
        ->where('item_unit_id', $item_unit_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $start_date)
        ->where('transaction_date', '<=', $end_date)
        ->first();

        if (!empty($data)) {
            return (int)$data['opening_balence'];
        } else {
            return 0;
        }
    }
    public function getLastBalance($item_category_id, $item_id, $item_unit_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = InvtItemMutation::select('last_balence')
        ->where('data_state',0)
        ->where('item_id', $item_id)
        ->where('item_category_id', $item_category_id)
        ->where('item_unit_id', $item_unit_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $start_date)
        ->where('transaction_date', '<=', $end_date)
        ->orderBy('item_mutation_id', 'DESC')
        ->first();

        if (!empty($data)) {
            return (int)$data['last_balence'];
        } else {
            return 0;
        }
    }

    public function filter(Request $request)
    {
        $filter = Session::get('filter-cards');
        $filter[$request->name] = $request->value;
        Session::put('filter-cards', $filter);
        return redirect()->route('sc.index');
    }

    public function resetFilter()
    {
        Session::forget('filter-card');
        return redirect()->route('sc.index');
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id', $item_id)
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        return $data['item_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)
        ->where('data_state', 0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        return $data['item_unit_name'];
    }

    public function getWarehouseName($warehouse_id)
    {
        $data = InvtWarehouse::where('warehouse_id', $warehouse_id)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        return $data['warehouse_name'];
    }

    public function printCardStockItem($item_stock_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data_stock = InvtItemStock::where('item_stock_id', $item_stock_id)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $data_mutation = InvtItemMutation::where('data_state',0)
        ->where('item_id', $data_stock['item_id'])
        ->where('item_category_id', $data_stock['item_category_id'])
        ->where('item_unit_id', $data_stock['item_unit_id'])
        ->where('company_id', Auth::user()->company_id)
        ->where('transaction_date', '>=', $start_date)
        ->where('transaction_date', '<=', $end_date)
        ->get();

        $pdf = new TCPDF(['P', PDF_UNIT, 'F4', true, 'UTF-8', false]);

        $pdf::setHeaderCallback(function($pdf){
            $pdf->SetFont('helvetica', '', 8);
            $header = "
            <div></div>
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr>
                        <td rowspan=\"3\" width=\"76%\"><img src=\"".asset('resources/assets/img/logo_kopkar.png')."\" width=\"120\"></td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Halaman</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".$pdf->getAliasNumPage()." / ".$pdf->getAliasNbPages()."</div></td>
                    </tr>
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Dicetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".ucfirst(Auth::user()->name)."</div></td>
                    </tr>
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Tgl. Cetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".date('d-m-Y H:i')."</div></td>
                    </tr>
                </table>
                <hr>
            ";

            $pdf->writeHTML($header, true, false, false, false, '');
        });
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 20, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">KARTU STOK</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');

        $tbl1 = "
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"13%\">Gudang</td>
                <td width=\"2%\">:</td>
                <td width=\"85%\">".$this->getWarehouseName($data_stock['warehouse_id'])."</td>
            </tr>
            <tr>
                <td width=\"13%\">Periode</td>
                <td width=\"2%\">:</td>
                <td width=\"85%\">".date('d-m-Y', strtotime($start_date))." s/d ".date('d-m-Y', strtotime($end_date))."</td>
            </tr>
            <tr>
                <td width=\"13%\">Nama Barang</td>
                <td width=\"2%\">:</td>
                <td width=\"85%\">".$this->getItemName($data_stock['item_id'])." - ".$this->getItemUnitName($data_stock['item_unit_id'])."</td>
            </tr>
        ";

        $tbl2 = "
        </table>
        <div></div>
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
            <div style=\"border-collapse:collapse;\">
                <tr style=\"line-height: 0%;\">
                    <td width=\"5%\"><div style=\"text-align: center; font-weight: bold;\">No</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold;\">Tanggal</div></td>
                    <td width=\"35%\"><div style=\"text-align: center; font-weight: bold;\">Keterangan</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold;\">Masuk</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold;\">Keluar</div></td>
                    <td width=\"15%\"><div style=\"text-align: center; font-weight: bold;\">Saldo</div></td>
                </tr>
            </div>
        </table>
        ";

        $no = 1;
        $total_stockin = 0;
        $total_stockout = 0;
        $tbl3 = "
        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center; font-weight: bold;\"></div></td>
                <td width=\"15%\"><div style=\"text-align: left; font-weight: bold;\">".date('d-m-Y', strtotime($start_date))."</div></td>
                <td width=\"65%\"><div style=\"text-align: left; font-weight: bold;\">Saldo Awal</div></td>
                <td width=\"15%\"><div style=\"text-align: right; font-weight: bold;\">".$this->getOpeningStock($data_stock['item_category_id'], $data_stock['item_id'], $data_stock['item_unit_id'])."</div></td>
            </tr>
        ";

        foreach ($data_mutation as $key => $val) {
            $tbl3 .= "
            <tr>
                <td width=\"5%\"><div style=\"text-align: center;\">".$no++.".</div></td>
                <td width=\"15%\"><div style=\"text-align: left;\">".date('d-m-Y', strtotime($val['transaction_date']))."</div></td>
                <td width=\"35%\"><div style=\"text-align: left;\">".$val['transaction_remark']." : ".$val['transaction_no']."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".$val['stock_in']."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".$val['stock_out']."</div></td>
                <td width=\"15%\"><div style=\"text-align: right;\">".$val['last_balence']."</div></td>
            </tr>
            ";

            $total_stockin += $val['stock_in'];
            $total_stockout += $val['stock_out'];
        }

        $tbl4 = "
        <tr>
            <td width=\"20%\"><div style=\"text-align: left; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;\">Jumlah Mutasi</div></td>
            <td width=\"35%\"><div style=\"text-align: left; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;\">:</div></td>
            <td width=\"15%\"><div style=\"text-align: right; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;\">".$total_stockin."</div></td>
            <td width=\"15%\"><div style=\"text-align: right; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;\">".$total_stockout."</div></td>
            <td width=\"15%\"><div style=\"text-align: right; font-weight: bold; border-top: 1px solid black; border-bottom: 1px solid black;\"></div></td>
        </tr>
        </table>
        ";

        $pdf::writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


        $filename = 'Kartu Stok.pdf';
        $pdf::Output($filename, 'I');
    }
    public function hotFix() {
        $collect = 0;
        $item = InvtItem::orderBy('item_id','ASC')->get('item_id');
        DB::beginTransaction();
        try{
            foreach ($item as $val){
            $stok = InvtItemStock::where("item_id",$val->item_id)->get();
           foreach ($stok as $valstok){
            $mutation = InvtItemMutation::where("item_id",$val->item_id)->where('item_unit_id',$valstok->item_unit_id)
            ->where('item_category_id',$valstok->item_category_id)->orderByDesc('item_mutation_id')->first();
            if($mutation!=null&&($mutation->last_balence!=$valstok->last_balance)){ $collect++;
                if($mutation->last_balence < $valstok->last_balance){
                    //* mutasi ditambah
                    $selisih = $valstok->last_balance - $mutation->last_balence;
                    $newmutation = InvtItemMutation::create([
                        'item_id'=>$val->item_id,
                        'item_category_id'=>$valstok->item_category_id,
                        'item_unit_id'=>$valstok->item_unit_id,
                        'transaction_no'=> '-',
                        'transaction_date'=>Carbon::now()->format('Y-m-d'),
                        'transaction_remark'=>'Penyesuaian Stok',
                        'opening_balence'=>$mutation->last_balence,
                        'stock_in'=>abs($selisih),
                        'stock_out'=>0,
                        'last_balence'=>$valstok->last_balance,
                        'created_id'=>Auth::id(),
                        'updated_id'=>Auth::id(),
                        'company_id'=>Auth::user()->company_id,
                    ]);
                }
                if($mutation->last_balence > $valstok->last_balance){
                    //* mutasi dikurangi
                    $selisih =  $mutation->last_balence - $valstok->last_balance;
                        $newmutation = InvtItemMutation::create([
                            'item_id'=>$val->item_id,
                            'item_category_id'=>$valstok->item_category_id,
                            'item_unit_id'=>$valstok->item_unit_id,
                            'transaction_no'=> '-',
                            'transaction_date'=>Carbon::now()->format('Y-m-d'),
                            'transaction_remark'=>'Penyesuaian Stok',
                            'opening_balence'=>$mutation->last_balence,
                            'stock_in'=>0,
                            'stock_out'=>abs($selisih),
                            'last_balence'=>$valstok->last_balance,
                            'created_id'=>Auth::id(),
                            'updated_id'=>Auth::id(),
                            'company_id'=>Auth::user()->company_id,
                        ]);
            }
           }
        }
            }
        DB::commit();
        return "sukses : Data diubah : ".$collect;
        }catch(\Exception $e){
            DB::rollBack();
            return $e;
        }
    }
}

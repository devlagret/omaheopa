<?php

namespace App\Http\Controllers;

use App\Models\AcctAccount;
use App\Models\PreferenceCompany;
use App\Models\PreferenceTransactionModule;
use App\Models\AcctJournalVoucher;
use App\Models\AcctJournalVoucherItem;
use App\Models\InvWarehouse;
use App\Models\CoreSupplier;
use App\Models\InvItemType;
use App\Models\InvItemCategory;
use App\Models\InvItemUnit;
use App\Models\PurchaseOrderReturn;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrder;
use App\Models\InvItemStock;
use App\Models\PurchaseOrderReturnItem;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PurchaseOrderReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Session::get('start_date')) {
            $start_date     = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }

        if (!Session::get('end_date')) {
            $end_date     = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $purchaseordereturn = PurchaseOrderReturn::where('data_state', '=', 0)
            ->where('created_at', '>=', $start_date)
            ->where('created_at', '<=', $end_date)
            ->get();

        return view('content/PurchaseOrder/ListPurchaseOrderReturn', compact('purchaseordereturn', 'start_date', 'end_date'));
    }

    public function filterPurchaseOrderReturn(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/purchase-order-return');
    }

    public function resetFilterPurchaseOrderReturn()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/purchase-order-return');
    }

    public function searchPurchaseOrder()
    {
        Session::forget('purchaseorderitem');

        $purchaseorder = PurchaseOrder::select('purchase_order_item.*', 'purchase_order.*')
            ->where('purchase_order_item.data_state', '=', 0)
            ->where('purchase_order.approved', '=', 1)
            ->join('purchase_order_item', 'purchase_order.purchase_order_id', 'purchase_order_item.purchase_order_id')
            ->where('purchase_order_item.quantity_outstanding', '>', 0)
            ->get();

        return view('content/PurchaseOrder/SearchPurchaseOrder', compact('purchaseorder'));
    }

    public function addPurchaseOrderReturn($purchase_order_id)
    {
        $purchaseorder = PurchaseOrder::where('purchase_order.data_state', 0)
            ->where('purchase_order.purchase_order_id', $purchase_order_id)
            ->join('purchase_order_item', 'purchase_order.purchase_order_id', 'purchase_order_item.purchase_order_id')
            ->where('purchase_order_item.quantity_outstanding', '>', 0)
            ->first();

        $purchaseorderitem = PurchaseOrderItem::where('data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->where('purchase_order_item.quantity_outstanding', '>', 0)
            ->get()->toArray();

        $purchaseorderitem_temporary = Session::get('purchaseorderitem');

        if ($purchaseorderitem_temporary == null) {
            $merge_data = $purchaseorderitem;
        } else {
            $merge_data = array_merge($purchaseorderitem, $purchaseorderitem_temporary);
            $key_type = array_column($merge_data, 'item_type_id');
            $key_qty = array_column($merge_data, 'quantity');
            array_multisort($key_type, SORT_ASC, $merge_data, SORT_DESC, $merge_data);
        }
        // dd($merge_data);

        $add_type_purchaseorderitem = PurchaseOrderItem::where('purchase_order_item.data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->join('inv_item_type', 'inv_item_type.item_type_id', '=', 'purchase_order_item.item_type_id')
            // ->join('inv_item_unit', 'inv_item_unit.item_unit_id', '=', 'purchase_order_item.item_unit_id')
            ->pluck('item_type_name', 'purchase_order_item.purchase_order_item_id');

        $add_unit_purchaseorderitem = PurchaseOrderItem::where('purchase_order_item.data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            // ->join('inv_item_type', 'inv_item_type.item_type_id', '=', 'purchase_order_item.item_type_id')
            ->join('inv_item_unit', 'inv_item_unit.item_unit_id', '=', 'purchase_order_item.item_unit_id')
            ->pluck('item_unit_name', 'purchase_order_item.item_unit_id');

        $null_add_purchaseorderitem = Session::get('purchase_order_item_id');
        $null_add_unit_purchaseorderitem = Session::get('item_unit_id');

        return view('content/PurchaseOrder/FormAddPurchaseOrderReturn', compact('merge_data', 'purchaseorderitem_temporary', 'purchaseorder', 'purchaseorderitem', 'add_type_purchaseorderitem', 'null_add_purchaseorderitem', 'add_unit_purchaseorderitem', 'null_add_unit_purchaseorderitem'));
    }

    public function getItemCategoryName($item_category_id)
    {
        $itemcategory = InvItemCategory::where('data_state', 0)
            ->where('item_category_id', $item_category_id)
            ->first();

        if ($itemcategory == null) {
            return "-";
        }

        return $itemcategory['item_category_name'];
    }

    public function getItemTypeName($item_type_id)
    {
        $itemtype = InvItemType::where('data_state', 0)
            ->where('item_type_id', $item_type_id)
            ->first();

        if ($itemtype == null) {
            return "-";
        }

        return $itemtype['item_type_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $itemunit = InvItemUnit::where('data_state', 0)
            ->where('item_unit_id', $item_unit_id)
            ->first();

        if ($itemunit == null) {
            return "-";
        }

        return $itemunit['item_unit_name'];
    }

    public function getCoreSupplierName($supplier_id)
    {
        $supplier = CoreSupplier::where('data_state', 0)
            ->where('supplier_id', $supplier_id)
            ->first();

        if ($supplier == null) {
            return "-";
        }

        return $supplier['supplier_name'];
    }

    public function getInvWarehouseName($warehouse_id)
    {
        $warehouse = InvWarehouse::where('data_state', 0)
            ->where('warehouse_id', $warehouse_id)
            ->first();

        if ($warehouse == null) {
            return "-";
        }

        return $warehouse['warehouse_name'];
    }

    public function getInvItemCategoryName($item_category_id)
    {
        $itemcategory = InvItemCategory::where('data_state', 0)
            ->where('item_category_id', $item_category_id)
            ->first();

        if ($itemcategory == null) {
            return "-";
        }
        return $itemcategory['item_category_name'];
    }

    public function getInvItemTypeName($item_type_id)
    {
        $itemtype = InvItemType::where('data_state', 0)
            ->where('item_type_id', $item_type_id)
            ->first();
        if ($itemtype == null) {
            return "-";
        }
        return $itemtype['item_type_name'];
    }

    public function getInvItemUnitName($item_unit_id)
    {
        $itemunit = InvItemUnit::where('data_state', 0)
            ->where('item_unit_id', $item_unit_id)
            ->first();

        if ($itemunit == null) {
            return "-";
        }

        return $itemunit['item_unit_name'];
    }

    // public function deleteInvGoodsReceivedNote($purchase_order_return_id){
    //     $this->testing();
    // }

    public function getPurchaseOrderNo($purchase_order_id)
    {
        $purchaseorder = PurchaseOrder::where('data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->first();

        if ($purchaseorder == null) {
            return "-";
        }

        return $purchaseorder['purchase_order_no'];
    }

    public function getPurchaseOrderDate($purchase_order_id)
    {
        $purchaseorder = PurchaseOrder::where('data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->first();

        if ($purchaseorder == null) {
            return "-";
        }

        return $purchaseorder['purchase_order_date'];
    }

    public function detailPurchaseOrder($purchase_order_id)
    {
        $purchaseorder = PurchaseOrder::where('data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->first();

        $purchaseorderitem = PurchaseOrderItem::where('data_state', 0)
            ->where('purchase_order_id', $purchase_order_id)
            ->get();

        return view('content/PurchaseOrder/FormDetailPurchaseOrder', compact('purchaseorderitem', 'purchaseorder'));
    }


    public function cetakPurchaseOrderReturn($purchase_order_return_id)
    {
        $purchaseorderreturn = PurchaseOrderReturn::select('*')
            ->join('core_supplier', 'core_supplier.supplier_id', 'purchase_order_return.supplier_id')
            ->join('purchase_order', 'purchase_order.purchase_order_id', 'purchase_order_return.purchase_order_id')
            ->leftjoin('purchase_invoice', 'purchase_invoice.purchase_order_id', 'purchase_order_return.purchase_order_id')
            ->where('purchase_order_return.data_state', 0)
            ->where('purchase_order_return.purchase_order_return_id', $purchase_order_return_id)
            ->first();

        $purchaseorderreturnitem = PurchaseOrderReturnItem::where('purchase_order_return_item.data_state', 0)
            ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'purchase_order_return_item.purchase_order_item_id')
            ->where('purchase_order_return_item.purchase_order_return_id', $purchase_order_return_id)
            ->get();

        $company = PreferenceCompany::select('*')
            ->first();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <p style=\"text-align: center;font-weight: bold\">Form Retur Pembelian Barang</p>
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
            <tr>
            <td >
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr style=\"text-align: left;\">
                            <td>No. </td>   
                            <td>: " . $purchaseorderreturn['purchase_order_return_no'] . "</td>
                            <td></td>
                    </tr>
                    <tr style=\"text-align: left;\">
                            <td>Customer </td>   
                            <td colspan=\"4\">: PBF KOP MENJAGAN ENAM</td>
                            <td></td>
                    </tr>
                    <tr width=\"50%\" style=\"text-align: left;\">
                            <td>Supplier</td>
                            <td>: " . $purchaseorderreturn['supplier_name'] . "</td>
                            <td></td>
                    </tr>
                </table>
            </td>
            <td  width=\"4%\"></td>
            </tr>

        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');

        $html2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
                        <tr style=\"text-align: center;\">
                            <td width=\"4%\" ><div style=\"text-align: center;\">No</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">No Faktur Pajak</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Nomor Batch</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Qty</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Harga </div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Discount </div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Jumlah(DPP) </div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Total </div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">ED</div></td>
                        </tr>";
        $no = 1;
        $total = 0;
        $totalJumlah = 0;
        $date = \Carbon\Carbon::now()->isoFormat('D MMM Y');
        foreach ($purchaseorderreturnitem as $key => $val) {
            $Jumlah = $val['quantity_return'] * $val['item_unit_cost'] - $val['discount_amount'] ;
            $totalJumlah += $Jumlah;
            $total = $totalJumlah  + $purchaseorderreturn['ppn_in_amount'];
            $html2 .= "<tr>
                            <td style=\"text-align: center;\">" . $no . "</td>
                            <td style=\"text-align: right;\">" . $purchaseorderreturn['faktur_tax_no'] . "</td>
                            <td style=\"text-align: right;\">" . $val['item_batch_number'] . "</td>
                            <td style=\"text-align: right;\">" . $val['quantity_return'] . "</td>
                            <td style=\"text-align: right;\">" . number_format($val['item_unit_cost'], 2) . "</td>
                            <td style=\"text-align: right;\">" . number_format($val['discount_percentage'] , 2) . "%</td>
                            <td style=\"text-align: right;\">" . number_format($Jumlah , 2) . "</td>
                            <td style=\"text-align: right;\">" . number_format($Jumlah, 2) . "</td>
                            <td style=\"text-align: right;\">" . $val['item_expired_date'] . "</td>
                        </tr> 
                        ";
            $no++;
        }

        $html2  .= "
                    <tr>
                        <td colspan=\"7\" style=\"text-align: center;font-weight: bold\";>PPN</td>
                        <td style=\"text-align: right;\">" . number_format($purchaseorderreturn['ppn_in_amount'], 2) . "</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan=\"7\" style=\"text-align: center;font-weight: bold\";>Total</td>
                        <td style=\"text-align: right;\">" . number_format($total, 2) . "</td>
                        <td></td>
                    </tr>
                    ";
        $html2 .= "</table>";
        $path = '<img width="60"; height="60" src="resources/assets/img/ttd.png">';
        $html2 .= "
                    <table style=\"text-align: left;\" cellspacing=\"0\";>
                        <tr>
                            <th  width=\"20%\" >".$purchaseorderreturn['purchase_order_return_remark']."</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th  width=\"20%\" >Semarang , ".$date." Hormat Kami</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th>".$path."</th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th  width=\"15%\">(Isti Ramadhani) PBF KME</th>
                            <th></th>
                            <th></th>
                        </tr>
                </table>
                    ";
        $pdf::writeHTML($html2, true, false, true, false, '');




        // ob_clean();

        $filename = 'PO_' . $purchaseorderreturn['purchase_order_return_no'] . '.pdf';
        $pdf::Output($filename, 'I');
    }







    // Nota
    public function notaPurchaseOrderReturn($purchase_order_return_id)
    {
        $purchaseorderreturn = PurchaseOrderReturn::select('*')
            ->join('core_supplier', 'core_supplier.supplier_id', 'purchase_order_return.supplier_id')
            ->join('purchase_order', 'purchase_order.purchase_order_id', 'purchase_order_return.purchase_order_id')
            ->leftjoin('purchase_invoice', 'purchase_invoice.purchase_order_id', 'purchase_order_return.purchase_order_id')
            ->where('purchase_order_return.data_state', 0)
            ->where('purchase_order_return.purchase_order_return_id', $purchase_order_return_id)
            ->first();

        $purchaseorderreturnitem = PurchaseOrderReturnItem::where('purchase_order_return_item.data_state', 0)
            ->join('purchase_order_item', 'purchase_order_item.purchase_order_item_id', 'purchase_order_return_item.purchase_order_item_id')
            ->where('purchase_order_return_item.purchase_order_return_id', $purchase_order_return_id)
            ->get();

        $company = PreferenceCompany::select('*')
            ->first();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "


        <table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
            <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
            <p style=\"text-align: center;font-weight: bold\">NOTA RETUR</p>

            <tr>
                <td>
                    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                        <tr style=\"text-align: left;\">
                                <td>No.Faktur Pajak </td>   
                                <td>: " . $purchaseorderreturn['faktur_tax_no'] . "</td>
                                <td></td>
                        </tr>
                        <tr width=\"50%\" style=\"text-align: left;\">
                                <td>Tanggal Faktur Pajak</td>
                                <td>: " . $purchaseorderreturn['purchase_order_return_date'] . "</td>
                                <td></td>
                        </tr>
                </table>
                </td>
                <td  style=\"text-align: right;\">" . $purchaseorderreturn['purchase_order_return_no'] ."</td>
            </tr>

            </table>


            <table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                        <tr style=\"text-align: left;font-weight: bold\">
                                <td>Pembeli : </td>   
                                <td></td>
                                <td></td>
                        </tr>
                        <tr style=\"text-align: left;\">
                                <td>Nama </td>   
                                <td>: " . $company['company_name'] . "</td>
                                <td></td>
                        </tr>
                        <tr style=\"text-align: left;\">
                                <td>Alamat </td>   
                                <td>: " . $company['company_address'] . "</td>
                                <td></td>
                        </tr>
                        <tr width=\"50%\" style=\"text-align: left;\">
                                <td>NPWP</td>
                                <td>: " . $company['pharmacist_license_no'] . "</td>
                                <td></td>
                        </tr>

                        <tr style=\"text-align: left;font-weight: bold\">
                            <td></td>   
                            <td></td>
                            <td></td>
                        </tr>

                        <tr style=\"text-align: left;font-weight: bold\">
                            <td>Kepada Penjual : </td>   
                            <td></td>
                            <td></td>
                            </tr>
                            <tr style=\"text-align: left;\">
                                    <td>Nama </td>   
                                    <td>: " . $purchaseorderreturn['supplier_name'] . "</td>
                                    <td></td>
                            </tr>
                            <tr style=\"text-align: left;\">
                                    <td>Alamat </td>   
                                    <td>: " . $purchaseorderreturn['supplier_address'] . "</td>
                                    <td></td>
                            </tr>
                            <tr width=\"50%\" style=\"text-align: left;\">
                                    <td>NPWP</td>
                                    <td>: " . $purchaseorderreturn['supplier_npwp_no'] . "</td>
                                    <td></td>
                        </tr>
                </table>
            </table>

        </table>";
        $pdf::writeHTML($tbl, true, false, false, false, '');

        $html2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
                        <tr style=\"text-align: center;\">
                            <td width=\"5%\" ><div style=\"text-align: center;\">No Urut.</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Nomor Batch</div></td>
                            <td width=\"25%\" ><div style=\"text-align: center;\">Nama Barang dan Jenis Kena Pajak</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Qty</div></td>
                            <td width=\"15%\" ><div style=\"text-align: center;\">Harga Satuan</div></td>
                            <td width=\"10%\" ><div style=\"text-align: center;\">Discount </div></td>
                            <td width=\"25%\" ><div style=\"text-align: center;\">Harga BKP yang dikembalikan</div></td>
                        </tr>";
        $no = 1;
        $total = 0;
        $totalJumlah = 0;

        $jumlahHarga = 0;
        $jumlahDiskon = 0;
        $jumlahNetto = 0;
        $jumlahPPN =  0;

        $date = \Carbon\Carbon::now()->isoFormat('D MMM Y');
        foreach ($purchaseorderreturnitem as $key => $val) {
            $Jumlah = $val['quantity_return'] * $val['item_unit_cost'] - $val['discount_amount'] ;
            $totalJumlah += $Jumlah;
            $total = $totalJumlah  + $purchaseorderreturn['ppn_in_amount'];

            $jumlahHarga += $val['quantity_return'] * $val['item_unit_cost'];
            $jumlahDiskon += $val['discount_amount'];
            $jumlahNetto += $val['item_unit_cost'];
            $jumlahPPN +=  $purchaseorderreturn['ppn_in_amount'];



            $html2 .= "<tr>
                            <td style=\"text-align: center;\">" . $no . "</td>
                            <td style=\"text-align: right;\">" . $val['item_batch_number'] . "</td>
                            <td  width=\"25%\" style=\"text-align: right;\">" . $this->getInvItemTypeName($val['item_type_id']) . "</td>
                            <td style=\"text-align: right;\">" . $val['quantity_return'] . "</td>
                            <td style=\"text-align: right;\">" . number_format($val['item_unit_cost'], 2) . "</td>
                            <td style=\"text-align: right;\">" . number_format($val['discount_percentage'] , 2) . "%</td>
                            <td style=\"text-align: right;\">" . number_format($Jumlah , 2) . "</td>
                        </tr> 
                        ";
            $no++;
        }

        $html2  .= "
                    <tr>
                    <td colspan=\"8\" style=\"text-align: center;\" height=\"100\"></td>
                    </tr> 
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Jumlah Harga BKP yang dikembalikan</td>
                        <td style=\"text-align: right;\">" . number_format($jumlahHarga, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Dikurangi Potongan harga</td>
                        <td style=\"text-align: right;\">" . number_format($jumlahDiskon, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Jumlah Harga BKP Netto yang dikembalikan</td>
                        <td style=\"text-align: right;\">" . number_format($jumlahNetto, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Pajak Pertambahan Nilai yang diminta kembali</td>
                        <td style=\"text-align: right;\">" . number_format($jumlahPPN, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Pajak Penjualan Atas Barang Mewah  yang diminta kembali</td>
                        <td style=\"text-align: right;\">-</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\" style=\"text-align: left;font-weight: bold\";>Jumlah</td>
                        <td style=\"text-align: right;\">" . number_format($jumlahHarga-$jumlahDiskon+$jumlahPPN, 2) . "</td>
                    </tr>
                    ";
        $html2 .= "</table>";
        $path = '<img width="60"; height="60" src="">';
        $html2 .= "  <table style=\"text-align: left;\" cellspacing=\"0\"; border=\"1\";>
                        <table style=\"text-align: left;\" cellspacing=\"0\";>
                            <tr>
                                <th></th>
                                <th></th>
                                <th  width=\"20%\" >".$purchaseorderreturn['purchase_order_return_remark']."</th>
                            
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th  width=\"20%\" >Semarang , ".$date." Pembeli</th>
                            
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th  width=\"15%\">Deden Kurniadi</th>
                            
                            </tr>
                        </table>
                        <hr>
                        <table style=\"text-align: left;\" cellspacing=\"0\"; border=\"0\";>
                            <tr>
                                <td>Lembar ke-1 : untuk pengusaha kena Pajak yang memenrbitkan faktur Pajak</td>
                            </tr>
                            <tr>
                                <td>Lembar ke-2  : Untuk Pembeli</td>
                            </tr>
                        </table>
                </table>
                    ";
        $pdf::writeHTML($html2, true, false, true, false, '');




        // ob_clean();

        $filename = 'PO_' . $purchaseorderreturn['purchase_order_return_no'] . '.pdf';
        $pdf::Output($filename, 'I');
    }








    public function processAddPurchaseOrderReturn(Request $request)
    {

        // $purchaseorderitem_temporary = Session::get('purchaseorderitem');

        $fields = $request->validate([
            'purchase_order_id'           => 'required',
            'purchase_order_return_date'  => 'required',
            'supplier_id'                 => 'required',
            'warehouse_id'                => 'required',
        ]);

        $fileNameToStore = '';

        if ($request->hasFile('receipt_image')) {

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('receipt_image')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('receipt_image')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $path = $request->file('receipt_image')->storeAs('public/receipt', $fileNameToStore);
        }

        $purchaseorderreturn = array(
            'purchase_order_return_date'            => $fields['purchase_order_return_date'],
            'purchase_order_id'                     => $fields['purchase_order_id'],
            'supplier_id'                           => $fields['supplier_id'],
            'warehouse_id'                          => 7,
            'purchase_order_return_remark'          => $request->purchase_order_return_remark,
            'subtotal_item'                         => $request->quantity_return_total,
            'receipt_image'                         => $fileNameToStore,
            'created_id'                             => Auth::id(),
        );
        // dd($purchaseorderreturn);

        if (PurchaseOrderReturn::create($purchaseorderreturn)) {
            $first_po_return = PurchaseOrderReturn::select('purchase_order_return_id', 'purchase_order_return_no')
                ->where('created_id', Auth::id())
                ->orderBy('created_at', 'DESC')
                ->first();

            $temprequest = $request->all();
            // dd($temprequest);

            //----------------------------------------------------------Journal Voucher-------------------------------------------------------------------//

            $preferencecompany             = PreferenceCompany::first();

            $transaction_module_code     = "POR";

            $transactionmodule             = PreferenceTransactionModule::where('transaction_module_code', $transaction_module_code)
                ->first();


            $transaction_module_id         = $transactionmodule['transaction_module_id'];

            $journal_voucher_period     = date("Ym", strtotime($purchaseorderreturn['purchase_order_return_date']));

            $data_journal = array(
                'branch_id'                        => 1,
                'journal_voucher_period'         => $journal_voucher_period,
                'journal_voucher_date'            => $purchaseorderreturn['purchase_order_return_date'],
                'journal_voucher_title'            => 'Return Pembelian Barang ' . $first_po_return['purchase_order_return_no'],
                'journal_voucher_no'            => $first_po_return['purchase_order_return_no'],
                'journal_voucher_description'    => $purchaseorderreturn['purchase_order_return_remark'],
                'transaction_module_id'            => $transaction_module_id,
                'transaction_module_code'        => $transaction_module_code,
                'transaction_journal_id'         => $first_po_return['purchase_order_return_id'],
                'transaction_journal_no'         => $first_po_return['purchase_order_return_no'],
                'created_id'                     => Auth::id(),
            );

            AcctJournalVoucher::create($data_journal);
            //---------------------------------------------------------End Journal Voucher----------------------------------------------------------------//



            $total_no = $request->total_no;
            $total_received_item = $temprequest['quantity_return_total'];
            // dd($total_no);

            for ($i = 1; $i <= $total_no; $i++) {
                $purchaseorderreturnitem = array(
                    'purchase_order_return_id'              => $first_po_return['purchase_order_return_id'],
                    'purchase_order_id'                        => $temprequest['purchase_order_id_' . $i],
                    'purchase_order_item_id'                => $temprequest['purchase_order_item_id_' . $i],
                    'item_category_id'                        => $temprequest['item_category_id_' . $i],
                    'item_type_id'                            => $temprequest['item_type_id_' . $i],
                    'item_unit_id'                            => $temprequest['item_unit_id_' . $i],
                    'quantity'                                => $temprequest['quantity_return_' . $i],
                    'quantity_ordered'                        => $temprequest['quantity_return_' . $i],
                    'quantity_return'                        => $temprequest['quantity_return_' . $i],
                    'item_batch_number'                     => $temprequest['item_batch_number_' . $i],
                    'item_expired_date'                     => $temprequest['item_expired_date_' . $i],
                    'created_id'                            => Auth::id(),
                );

                //dd($purchaseorderreturnitem);
                PurchaseOrderReturnItem::create($purchaseorderreturnitem);

                //update purchase order item
                $purchaseorderitem = PurchaseOrderItem::findOrFail($purchaseorderreturnitem['purchase_order_item_id']);
                $purchaseorderitem->quantity_outstanding = $purchaseorderitem['quantity_outstanding'] - $purchaseorderreturnitem['quantity'];
                $purchaseorderitem->quantity_return    = $purchaseorderitem['quantity_return'] + $purchaseorderreturnitem['quantity'];
                $purchaseorderitem->save();

                InvItemStock::create([
                    'goods_received_note_id'            =>   '',
                    'goods_received_note_item_id'       =>   '',
                    'item_stock_date'                   =>   \Carbon\Carbon::now(), # new \Datetime()
                    'item_stock_expired_date'           =>   $temprequest['item_expired_date_' . $i],
                    'item_batch_number'                 =>   $temprequest['item_batch_number_' . $i],
                    'purchase_order_item_id'            =>   $temprequest['purchase_order_item_id_' . $i],
                    'warehouse_id'                      =>   7,
                    'item_category_id'                  =>   $temprequest['item_category_id_' . $i],
                    'item_type_id'                      =>   $temprequest['item_type_id_' . $i],
                    'item_id'                           =>   '',
                    'item_unit_id'                      =>   $temprequest['item_unit_id_' . $i],
                    'item_total'                        =>   '',
                    'item_unit_id_default'              =>   $temprequest['item_unit_id_' . $i],
                    'item_default_quantity_unit'        =>   1,
                    'quantity_unit'                     =>   $temprequest['quantity_return_' . $i],
                    'item_weight_default'               =>   '',
                    'item_weight_unit'                  =>   '',
                    'package_id'                        =>   '',
                    'package_total'                     =>   '',
                    'package_unit_id'                   =>   '',
                    'package_price'                     =>   '',
                    'data_state'                        =>   0,
                    'created_id'                        =>   Auth::id(),
                    'created_at'                        =>   \Carbon\Carbon::now(),
                ]);




                // $total_received_item = $total_received_item + $purchaseorderitem['quantity_return'] + $invgoodsreceivednoteitem['quantity'];


                //----------------------------------------------------------Journal Voucher Item-------------------------------------------------------------------//


                // $purchaseorderitem          = PurchaseOrderItem::where('purchase_order_item_id', $temprequest['purchase_order_item_id_'.$i])
                $purchaseorderitem          = PurchaseOrderItem::where('purchase_order_item_id', $temprequest['purchase_order_item_id_' . $i])
                    ->first();
                //dd($purchaseorderitem);

                $purchaseorder              = PurchaseOrder::findOrFail($purchaseorderreturn['purchase_order_id']);
                //dd($purchaseorderitem,$purchaseorder);

                $journalvoucher = AcctJournalVoucher::where('created_id', Auth::id())
                    ->orderBy('journal_voucher_id', 'DESC')
                    ->first();
                //dd($data_journal);

                $journal_voucher_id     = $journalvoucher['journal_voucher_id'];


                //------account_id Persedian Barang Dagang------//
                $account         = AcctAccount::where('account_id', $preferencecompany['account_inventory_trade_id'])
                    ->where('data_state', 0)
                    ->first();

                $total_amount               = $temprequest['quantity_return_' . $i] * $purchaseorderitem['item_unit_cost'];

                $account_id_default_status         = $account['account_default_status'];
                // dd($account_id_default_status);

                $data_credit1 = array(
                    'journal_voucher_id'            => $journal_voucher_id,
                    'account_id'                    => $preferencecompany['account_inventory_trade_id'],
                    'journal_voucher_description'    => $data_journal['journal_voucher_description'],
                    'journal_voucher_amount'        => ABS($total_amount),
                    'journal_voucher_credit_amount'    => ABS($total_amount),
                    'account_id_default_status'        => $account_id_default_status,
                    'account_id_status'                => 0,
                );

                //dd($data_credit1);

                AcctJournalVoucherItem::create($data_credit1);
            }


            //------account_id Hutang Suplier------//
            $preference_company = PreferenceCompany::first();

            $account = AcctAccount::where('account_id', $preference_company['account_payable_id'])
                ->where('data_state', 0)
                ->first();

            $subtotal_after_ppn_in = $purchaseorder['subtotal_after_ppn_in'];

            $account_id_default_status         = $account['account_default_status'];

            $data_debit1 = array(
                'journal_voucher_id'            => $journal_voucher_id,
                'account_id'                    => $account['account_id'],
                'journal_voucher_description'    => $data_journal['journal_voucher_description'],
                'journal_voucher_amount'        => ABS($subtotal_after_ppn_in),
                'journal_voucher_debit_amount'    => ABS($subtotal_after_ppn_in),
                'account_id_default_status'        => $account_id_default_status,
                'account_id_status'                => 1,
            );
            // dd($data_debit1);


            AcctJournalVoucherItem::create($data_debit1);



            //------account_id PPN Masukan------//
            $account = AcctAccount::where('account_id', $preference_company['account_vat_in_id'])
                ->where('data_state', 0)
                ->first();

            $ppn_in_amount = $purchaseorder['ppn_in_amount'];

            $account_id_default_status         = $account['account_default_status'];

            $data_credit2 = array(
                'journal_voucher_id'            => $journal_voucher_id,
                'account_id'                    => $preferencecompany['account_vat_in_id'],
                'journal_voucher_description'    => $data_journal['journal_voucher_description'],
                'journal_voucher_amount'        => ABS($ppn_in_amount),
                'journal_voucher_credit_amount'    => ABS($ppn_in_amount),
                'account_id_default_status'        => $account_id_default_status,
                'account_id_status'                => 0,
            );

            //dd($data_debit1,$data_credit1,$data_credit2);

            AcctJournalVoucherItem::create($data_credit2);
            //--------------------------------------------------------End Journal Voucher-----------------------------------------------------------------//




            $purchaseorder = PurchaseOrder::findOrFail($purchaseorderreturn['purchase_order_id']);
            $purchaseorder->total_received_item = $purchaseorder['total_received_item'] + $total_received_item;
            if ($purchaseorder['total_item'] == $purchaseorder->total_received_item) {
                $purchaseorder->purchase_order_status = 3;
            } else {
                $purchaseorder->purchase_order_status = 1;
            }
            $purchaseorder->save();

            $msg = 'Tambah Return Pembelian Berhasil';
            return redirect('/purchase-order-return')->with('msg', $msg);
        } else {
            $msg = 'Tambah Return Pembelian Gagal';
            return redirect('/purchase-order-return')->with('msg', $msg);
        }
    }

    public function detailPurchaseOrderReturn($purchase_order_return_id)
    {
        $purchaseorderreturn = PurchaseOrderReturn::where('data_state', 0)
            ->where('purchase_order_return_id', $purchase_order_return_id)
            ->first();

        $purchaseorderreturnitem = PurchaseOrderReturnItem::where('data_state', 0)
            ->where('purchase_order_return_id', $purchase_order_return_id)
            ->get();

        return view('content/PurchaseOrder/FormDetailPurchaseOrderReturn', compact('purchaseorderreturn', 'purchaseorderreturnitem'));
    }

    public function voidPurchaseOrderReturn($purchase_order_return_id)
    {
        $purchaseorderreturn = PurchaseOrderReturn::where('data_state', 0)
            ->where('purchase_order_return_id', $purchase_order_return_id)
            ->first();

        $purchaseorderreturnitem = PurchaseOrderReturnItem::where('data_state', 0)
            ->where('purchase_order_return_id', $purchase_order_return_id)
            ->get();

        return view('content/PurchaseOrder/FormVoidPurchaseOrderReturn', compact('purchaseorderreturn', 'purchaseorderreturnitem', 'purchase_order_return_id'));
    }

    public function processVoidPurchaseOrderReturn($purchase_order_return_id)
    {


        $purchaseorderreturn = PurchaseOrderReturn::findOrFail($purchase_order_return_id);
        $purchaseorderreturn->data_state = 1;
        $purchaseorderreturn->save();

        $purchaseorderreturnitem = PurchaseOrderReturnItem::where('data_state', 0)
            ->where('purchase_order_return_id', $purchase_order_return_id)
            ->get();

        foreach ($purchaseorderreturnitem as $item) {
            $returnitem = PurchaseOrderReturnItem::findOrFail($item['purchase_order_return_item_id']);
            $returnitem->data_state = 1;
            $returnitem->save();

            $purchaseorderitem = PurchaseOrderItem::findOrFail($item['purchase_order_item_id']);
            // $purchaseorderitem->quantity_outstanding = $purchaseorderitem['quantity_outstanding'] + $item['quantity'];
            // $purchaseorderitem->quantity_return    = $purchaseorderitem['quantity_return'] - $item['quantity'];
            $purchaseorderitem->save();
        }

        $msg = 'Hapus Return Pembelian Berhasil';
        return redirect('/purchase-order-return')->with('msg', $msg);
    }

    public function addNewPurchaseOrderItem(Request $request)
    {
        $add_purchaseorderitem = PurchaseOrderItem::where('purchase_order_item.data_state', 0)
            ->where('purchase_order_item.purchase_order_item_id', $request['purchase_order_item_id'])
            ->first();

        $fields = $request->validate([
            'purchase_order_item_id'           => 'required',
            'quantity'                         => 'required',
            // 'item_unit_id'           => 'required',
        ]);

        $purchaseorderitem = array(
            'purchase_order_item_id' =>  $fields['purchase_order_item_id'],
            'purchase_order_id'     =>  $request['purchase_order_id'],
            'item_type_id'            =>  $add_purchaseorderitem['item_type_id'],
            'quantity'                =>  $fields['quantity'],
            'quantity_outstanding'  =>  $request['quantity'],
            'item_category_id'      =>  $add_purchaseorderitem['item_category_id'],
            'item_unit_cost'        =>  $add_purchaseorderitem['item_unit_cost'],
            'item_unit_id'          =>  $add_purchaseorderitem['item_unit_id'],
        );

        // dd($purchaseorderitem);

        $newpurchaseorderitem = Session::get('purchaseorderitem');
        if ($newpurchaseorderitem !== null) {
            array_push($newpurchaseorderitem, $purchaseorderitem);
            Session::put('purchaseorderitem', $newpurchaseorderitem);
        } else {
            $newpurchaseorderitem = [];
            array_push($newpurchaseorderitem, $purchaseorderitem);
            Session::push('purchaseorderitem', $purchaseorderitem);
        }

        return redirect()->back();
    }

    public function deleteNewPurchaseOrderItem($purchase_order_id)
    {
        Session::forget('purchaseorderitem');
        return redirect('purchase-order-return/add/' . $purchase_order_id);
    }

    public function getNewPurchaseOrderItemId()
    {
        $purchaseorderitem_id = PurchaseOrderItem::where('data_state', 0)
            // ->where('purchase_order_item_id', $purchase_order_item_id)
            ->first();

        if ($purchaseorderitem_id == null) {
            return "-";
        }

        return $purchaseorderitem_id['purchase_order_item_id'];
    }
}

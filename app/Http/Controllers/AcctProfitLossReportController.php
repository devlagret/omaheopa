<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expenditure;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AcctProfitLossReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
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
        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();
        $total_expenditure_amount = 0;
        foreach($expenditure as $row){
            $total_expenditure_amount += $row['expenditure_amount'];
        }

        return view('content.AcctProfitLossReport.ListAcctProfitLossReport',compact('start_date','end_date','total_sales_amount','total_purchase_amount','total_expenditure_amount'));
    }

    public function filterProfitLossReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/profit-loss-report');
    }

    public function resetFilterProfitLossReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/profit-loss-report');
    }

    public function printProfitLossReport()
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
        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();
        $total_expenditure_amount = 0;
        foreach($expenditure as $row){
            $total_expenditure_amount += $row['expenditure_amount'];
        }

        $subtotal_expenditure = $total_purchase_amount + $total_expenditure_amount;
        $subtotal_difference = $total_sales_amount - $subtotal_expenditure;


        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(40, 10, 40, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 10);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PERHITUNGAN LABA / RUGI</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
            <br>
            <br>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\" >
            <tr>
                <th colspan=\"2\" style=\"font-weight: bold\">Pendapatan</th>
            </tr>
            <tr>
                <td style=\"width: 80%\">   Penjualan Produk</td>
                <td style=\"width: 20%; text-align: right\">".number_format($total_sales_amount,2,'.',',')."</td>
            </tr>
            <tr>
                <td colspan=\"2\"></td>
            </tr>
            <tr>
                <th colspan=\"2\" style=\"font-weight: bold\">Pengeluaran</th>
            </tr>
            <tr>
                <td style=\"width: 80%\">   Pembelian Produk</td>
                <td style=\"width: 20%; text-align: right\">".number_format($total_purchase_amount,2,'.',',')."</td>
            </tr>
            <tr>
                <td style=\"width: 80%\">   Pengeluaran Lainya</td>
                <td style=\"width: 20%; text-align: right\">".number_format($total_expenditure_amount,2,'.',',')."</td>
            </tr>
            <tr>
                <td colspan=\"2\"></td>
            </tr>
            <hr>
            <tr>
                <th style=\"width: 80%\" style=\"font-weight: bold\">Total Pendapatan</th>
                <th style=\"width: 20%; text-align: right; font-weight: bold \">".number_format($total_sales_amount,2,'.',',')."</th>
            </tr>
            <tr>
                <th style=\"width: 80%\" style=\"font-weight: bold\">Total Pengeluaran</th>
                <th style=\"width: 20%; text-align: right; font-weight: bold\">".number_format($subtotal_expenditure,2,'.',',')."</th>
            </tr>
            <tr>
                <th style=\"width: 80%\" style=\"font-weight: bold\">Selisih</th>
                <th style=\"width: 20%; text-align: right; font-weight: bold\" >".number_format($subtotal_difference,2,'.',',')."</th>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1, true, false, false, false, '');

        ob_clean();

        $filename = 'Laporan_Perhitungan_Laba_Rugi_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportProfitLossReport()
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
        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->where('sales_invoice.sales_invoice_date','>=',$start_date)
        ->where('sales_invoice.sales_invoice_date','<=',$end_date)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->where('purchase_invoice.purchase_invoice_date','>=',$start_date)
        ->where('purchase_invoice.purchase_invoice_date','<=',$end_date)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->where('expenditure_date','>=',$start_date)
        ->where('expenditure_date','<=',$end_date)
        ->get();
        $total_expenditure_amount = 0;
        foreach($expenditure as $row){
            $total_expenditure_amount += $row['expenditure_amount'];
        }

        $subtotal_expenditure = $total_purchase_amount + $total_expenditure_amount;
        $subtotal_difference = $total_sales_amount - $subtotal_expenditure;


        $spreadsheet = new Spreadsheet();

        // if(!empty($sales_invoice || $purchase_invoice || $expenditure)){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Profit Loss Report")
                                        ->setSubject("")
                                        ->setDescription("Profit Loss Report")
                                        ->setKeywords("Profit, Loss, Report")
                                        ->setCategory("Profit Loss Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:C1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->mergeCells("B2:C2");
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B11')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B12')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B13')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('C4')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('C7')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('C11')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('C12')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('C13')->getFont()->setBold(true);

            // $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            // $spreadsheet->getActiveSheet()->getStyle('B4:C4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // $spreadsheet->getActiveSheet()->getStyle('B4:C4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Perhitungan Laba / Rugi ");	
            $sheet->setCellValue('B2', 'Period '.date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));
            $sheet->setCellValue('B4',"Pendapatan");
            $sheet->setCellValue('B5',"Penjualan Produk");
            $sheet->setCellValue('B7',"Pengeluaran");
            $sheet->setCellValue('B8',"Pembelian Produk");
            $sheet->setCellValue('B9',"Pengeluaran Lainya"); 
            $sheet->setCellValue('B11',"Total Pendapatan"); 
            $sheet->setCellValue('B12',"Total Pengeluaran"); 
            $sheet->setCellValue('B13',"Selisih"); 
            
                    
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan Perhitungan Laba Rugi");
                $spreadsheet->getActiveSheet()->getStyle('B4:C4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B5:C5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B6:C6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B7:C7')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B8:C8')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B9:C9')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B10:C10')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B11:C11')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B12:C12')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('B13:C13')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $spreadsheet->getActiveSheet()->getStyle('C5')->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C8')->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C9')->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C11')->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C12')->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C13')->getNumberFormat()->setFormatCode('0.00');
        
                // $spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('C8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('C9')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('C11')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('C12')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('C13')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);




                $sheet->setCellValue('C5', number_format($total_sales_amount,2,'.',','));
                $sheet->setCellValue('C8', number_format($total_purchase_amount,2,'.',','));
                $sheet->setCellValue('C9', number_format($total_expenditure_amount,2,'.',','));
                $sheet->setCellValue('C11', number_format($total_sales_amount,2,'.',','));
                $sheet->setCellValue('C12', number_format($subtotal_expenditure,2,'.',','));
                $sheet->setCellValue('C13', number_format($subtotal_difference,2,'.',','));

            
            ob_clean();
            $filename='Laporan_Perhitungan_Laba_Rugi_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        // }else{
        //     echo "Maaf data yang di eksport tidak ada !";
        // }
    }

}

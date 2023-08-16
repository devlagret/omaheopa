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

class AcctProfitLossYearReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }
        $monthlist = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->whereMonth('sales_invoice.sales_invoice_date','>=',01)
        ->whereMonth('sales_invoice.sales_invoice_date','<=',$month)
        ->whereYear('sales_invoice.sales_invoice_date',$year)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->whereMonth('purchase_invoice.purchase_invoice_date','>=',01)
        ->whereMonth('purchase_invoice.purchase_invoice_date','<=',$month)
        ->whereYear('purchase_invoice.purchase_invoice_date',$year)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->whereMonth('expenditure_date','>=',01)
        ->whereMonth('expenditure_date','<=',$month)
        ->whereYear('expenditure_date',$year)
        ->get();
        $total_expenditure_amount = 0;
        foreach($expenditure as $row){
            $total_expenditure_amount += $row['expenditure_amount'];
        }
        return view('content.AcctProfitLossYearReport.ListAcctProfitLossYearReport', compact('monthlist','yearlist','month','year','total_sales_amount','total_purchase_amount','total_expenditure_amount'));
    }

    public function filterProfitLossYearReport(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        Session::put('month', $month);
        Session::put('year', $year);

        return redirect('/profit-loss-year-report');
    }

    public function resetFilterProfitLossYearReport()
    {
        Session::forget('month');
        Session::forget('year');

        return redirect('/profit-loss-year-report');
    }

    public function getMonthName($month_id)
    {
        $monthlist = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );

        return $monthlist[$month_id];
    }

    public function printProfitLossYearReport()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }
       
        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->whereMonth('sales_invoice.sales_invoice_date','>=',01)
        ->whereMonth('sales_invoice.sales_invoice_date','<=',$month)
        ->whereYear('sales_invoice.sales_invoice_date',$year)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->whereMonth('purchase_invoice.purchase_invoice_date','>=',01)
        ->whereMonth('purchase_invoice.purchase_invoice_date','<=',$month)
        ->whereYear('purchase_invoice.purchase_invoice_date',$year)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->whereMonth('expenditure_date','>=',01)
        ->whereMonth('expenditure_date','<=',$month)
        ->whereYear('expenditure_date',$year)
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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PERHITUNGAN LABA / RUGI TAHUNAN</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">Januari - ".$this->getMonthName($month).' '. $year."</div></td>
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

        $filename = 'Laporan_Perhitungan_Laba_Rugi_1_'.$month.'_'.$year.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportProfitLossYearReport()
    {
        if(!$month = Session::get('month')){
            $month = date('m');
        }else{
            $month = Session::get('month');
        }
        if(!$year = Session::get('year')){
            $year = date('Y');
        }else{
            $year = Session::get('year');
        }

        $year_now 	=	date('Y');
        for($i=($year_now-2); $i<($year_now+2); $i++){
            $yearlist[$i] = $i;
        } 

        $sales_invoice = SalesInvoice::join('sales_invoice_item','sales_invoice_item.sales_invoice_id','=','sales_invoice.sales_invoice_id')
        ->whereMonth('sales_invoice.sales_invoice_date','>=',01)
        ->whereMonth('sales_invoice.sales_invoice_date','<=',$month)
        ->whereYear('sales_invoice.sales_invoice_date',$year)
        ->where('sales_invoice.data_state',0)
        ->where('sales_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_sales_amount = 0;
        foreach($sales_invoice as $row){
            $total_sales_amount += $row['total_amount'];
        }

        $purchase_invoice = PurchaseInvoice::join('purchase_invoice_item','purchase_invoice_item.purchase_invoice_id','=','purchase_invoice.purchase_invoice_id')
        ->whereMonth('purchase_invoice.purchase_invoice_date','>=',01)
        ->whereMonth('purchase_invoice.purchase_invoice_date','<=',$month)
        ->whereYear('purchase_invoice.purchase_invoice_date',$year)
        ->where('purchase_invoice.data_state',0)
        ->where('purchase_invoice.company_id', Auth::user()->company_id)
        ->get();
        $total_purchase_amount = 0;
        foreach($purchase_invoice as $row){
            $total_purchase_amount += $row['total_amount'];
        }

        $expenditure = Expenditure::where('company_id', Auth::user()->company_id)
        ->where('data_state',0)
        ->whereMonth('expenditure_date','>=',01)
        ->whereMonth('expenditure_date','<=',$month)
        ->whereYear('expenditure_date',$year)
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
                                        ->setTitle("Profit Loss Year Report")
                                        ->setSubject("")
                                        ->setDescription("Profit Loss Year Report")
                                        ->setKeywords("Profit, Loss, Year, Report")
                                        ->setCategory("Profit Loss Year Report");
                                 
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

            $sheet->setCellValue('B1',"Laporan Perhitungan Laba / Rugi Tahunan ");	
            $sheet->setCellValue('B2', 'Januari - '.$this->getMonthName($month).' '. $year);
            $sheet->setCellValue('B4',"Pendapatan");
            $sheet->setCellValue('B5',"Penjualan Produk");
            $sheet->setCellValue('B7',"Pengeluaran");
            $sheet->setCellValue('B8',"Pembelian Produk");
            $sheet->setCellValue('B9',"Pengeluaran Lainya"); 
            $sheet->setCellValue('B11',"Total Pendapatan"); 
            $sheet->setCellValue('B12',"Total Pengeluaran"); 
            $sheet->setCellValue('B13',"Selisih"); 
            
                    
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Perhitungan Laba Rugi Tahunan");
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
            $filename='Laporan_Perhitungan_Laba_Rugi_01_'.$month.'_'.$year.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SalesMerchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\InvtItem;
use App\Models\InvtItemType;
use App\Models\InvtItemUnit;
use App\Models\InvtItemStock;
use App\Models\InvtItemCategory;
use App\Models\InvtWarehouse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InvItemStockController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $invitemcategory    = InvtItemCategory::pluck('item_category_name', 'item_category_id');

        // $invitemcategory = InvtItemCategory::select('item_category_id',DB::raw('CONCAT(invt_item_category.item_category_name, " ", sales_merchant.merchant_name) AS item_name'))
        // ->join('sales_merchant', 'sales_merchant.merchant_id', 'invt_item_category.merchant_id')
        // ->where('invt_item_category.data_state', 0)
        // ->pluck('item_name', 'item_category_id');
        $merchant   = SalesMerchant::where('data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $invitem        = InvtItem::pluck('item_name', 'item_id');

        // $coregrade          = CoreGrade::pluck('grade_name', 'grade_id');

        $invwarehouse       = InvtWarehouse::pluck('warehouse_name', 'warehouse_id');

        $item_category_id   = Session::get('filteritemcategoryid');

        $item_id       = Session::get('filteritemtypeid');

        $grade_id           = Session::get('filtergradeid');
        
        $warehouse_id       = Session::get('filterwarehouseid');

        $merchant_id       = Session::get('filtermerchantid');
        
        $invitemstock       = InvtItemStock::with('item.merchant','unit','category','warehouse')
        // ->join('')
        ->where('data_state','=',0)
      ;
        if($item_category_id||$item_category_id!=null||$item_category_id!=''){
            $invitemstock   = $invitemstock->where('item_category_id', $item_category_id);
        }
        if($item_id||$item_id!=null||$item_id!=''){
            $invitemstock   = $invitemstock->where('item_id', $item_id);
        }
        if($warehouse_id||$warehouse_id!=null||$warehouse_id!=''){
            $invitemstock   = $invitemstock->where('warehouse_id', $warehouse_id);
        }
        if($merchant_id||$warehouse_id!=null||$warehouse_id!=''){
            $invitemstock   = $invitemstock->where('warehouse_id', $warehouse_id);
        }
        $invitemstock       = $invitemstock->get();
       // dd($invitemstock);

        return view('content.InvItemStock.ListInvItemStock',compact('invitemstock', 'merchant','merchant_id','invitemcategory', 'invitem', 'invwarehouse', 'item_category_id', 'item_id', 'grade_id', 'warehouse_id'));
    }

    // public function  getCoreGradeName($item_id){
    //     $grade = InvItem::select('core_grade.grade_name')
    //     ->where('item_id', $item_id)
    //     ->join('core_grade', 'core_grade.grade_id', 'inv_item.grade_id')
    //     ->first();
        
    //     if($grade == null){
    //         return "-";
    //     }
    //     return $grade['grade_name'];
    // }

    public function filterInvItemStock(Request $request){
        $item_category_id   = $request->item_category_id;
        $item_id       = $request->item_id;
        $grade_id           = $request->grade_id;
        $warehouse_id       = $request->warehouse_id;
        // print_r('|||item_category_id'.$item_category_id);
        // print_r('|||item_id'.$item_id);
        // print_r('|||grade_id'.$grade_id);
        // print_r('|||warehouse_id'.$warehouse_id);
        // exit;

        Session::put('filtermerchantid', $item_category_id);
        Session::put('filteritemcategoryid', $request->merchant_id);
        Session::put('filteritemid', $item_id);
        Session::put('filtergradeid', $grade_id);
        Session::put('filterwarehouseid', $warehouse_id);

        return redirect('/item-stock');
    }

    // public function getInvItemType(Request $request){
    //     $item_category_id = $request->item_category_id;
    //     $data='';
    //     $type = InvItemType::where('item_category_id', $item_category_id)
    //     ->where('data_state','=',0)
    //     ->get();

    //     $data .= "<option value=''>--Choose One--</option>";
    //     foreach ($type as $mp){
    //         $data .= "<option value='$mp[item_type_id]'>$mp[item_type_name]</option>\n";	
    //     }

    //     return $data;
    // }

    // public function getCoreGrade(Request $request){
    //     $item_category_id   = $request->item_category_id;
    //     $item_type_id       = $request->item_type_id;
    //     $data='';

    //     $type = InvItem::select('core_grade.grade_name', 'core_grade.grade_id')
    //     ->join('core_grade', 'core_grade.grade_id', 'inv_item.grade_id')
    //     ->where('item_category_id', $item_category_id)
    //     ->where('item_type_id', $item_type_id)
    //     ->where('data_state','=',0)
    //     ->get();

    //     $data .= "<option value=''>--Choose One--</option>";
    //     foreach ($type as $mp){
    //         $data .= "<option value='$mp[grade_id]'>$mp[grade_name]</option>\n";	
    //     }

    //     return $data;
    // }

    // public function getInvItemCategoryName($item_category_id){
    //     $item = InvItemCategory::select('item_category_name')
    //     ->where('item_category_id', $item_category_id)
    //     ->where('data_state', 0)
    //     ->first();

    //     return $item['item_category_name'];
    // }

    // public function getInvItemTypeName($item_type_id){
    //     $item = InvItemType::select('item_type_name')
    //     ->where('item_type_id', $item_type_id)
    //     ->where('data_state', 0)
    //     ->first();

    //     return $item['item_type_name'];
    // }

    // public function getInvItemUnitName($item_unit_id){
    //     $unit = InvItemUnit::select('item_unit_name')
    //     ->where('item_unit_id', $item_unit_id)
    //     ->where('data_state', 0)
    //     ->first();

    //     return $unit['item_unit_name'];
    // }

    // public function getInvWarehouseName($warehouse_id){
    //     $warehouse = InvWarehouse::select('warehouse_name')
    //     ->where('warehouse_id', $warehouse_id)
    //     ->where('data_state', 0)
    //     ->first();

    //     return $warehouse['warehouse_name'];
    // }



    public function getInvWarehouseName($warehouse_id){
        $warehouse = InvtWarehouse::select('warehouse_name')

        ->where('warehouse_id', $warehouse_id)
        ->where('data_state', 0)
        ->first();

        return $warehouse['warehouse_name'];
    }

    public function getInvItemCategoryName($item_category_id){
        $item = InvtItemCategory::select('item_category_name',DB::raw('CONCAT(invt_item_category.item_category_name, " ", sales_merchant.merchant_name) AS item_name'))
        ->join('sales_merchant', 'sales_merchant.merchant_id', 'invt_item_category.merchant_id')
        ->where('invt_item_category.item_category_id', $item_category_id)
        ->where('invt_item_category.data_state', 0)
        ->first();

        if($item == null){
            return "-";
        }
        return $item['item_name'];
    }

    public function getInvItemName($item_id){
        $item = InvtItem::select('item_name')
        ->where('item_id', $item_id)
        ->where('data_state', 0)
        ->first();

        if($item == null){
            return "-";
        }
        return $item['item_name'];
    }


    public function getItemName($item_id){
        $invitem = InvtItem::select('invt_item.item_id', DB::raw('CONCAT(invt_item_category.item_category_name, " ", invt_item.item_name) AS item_name'))
        ->join('invt_item_category', 'invt_item_category.item_category_id', 'invt_item.item_category_id')
        // ->join('invt_item_type', 'invt_item_type.item_type_id', 'invt_item.item_type_id')
        // ->join('core_grade', 'core_grade.grade_id', 'invt_item.grade_id')
        ->where('item_id', $item_id)
        ->where('invt_item.data_state','=',0)
        ->first();

        return $invitem['item_name'];
    }
    public function getInvItemUnitName($item_unit_id){
        $unit = InvtItemUnit::select('item_unit_name')
        ->where('item_unit_id', $item_unit_id)
        ->where('data_state', 0)
        ->first();

        if($unit == null){
            return '-';
        }
        return $unit['item_unit_name'];
    }



    public function export(){

        $invitemcategory    = InvtItemCategory::pluck('item_category_name', 'item_category_id');

        // $invitemcategory = InvtItemCategory::select('item_category_id',DB::raw('CONCAT(invt_item_category.item_category_name, " ", sales_merchant.merchant_name) AS item_name'))
        // ->join('sales_merchant', 'sales_merchant.merchant_id', 'invt_item_category.merchant_id')
        // ->where('invt_item_category.data_state', 0)
        // ->pluck('item_name', 'item_category_id');
        $merchant   = SalesMerchant::where('sales_merchant.data_state', 0);
        if(Auth::id()!=1||Auth::user()->merchant_id!=null){
            $merchant->where('sales_merchant.merchant_id',Auth::user()->merchant_id);
        }
        $merchant = $merchant->get()->pluck('merchant_name', 'merchant_id');
        $invitem        = InvtItem::pluck('item_name', 'item_id');

        // $coregrade          = CoreGrade::pluck('grade_name', 'grade_id');

        $invwarehouse       = InvtWarehouse::pluck('warehouse_name', 'warehouse_id');

        $item_category_id   = Session::get('filteritemcategoryid');

        $item_id       = Session::get('filteritemtypeid');

        $grade_id           = Session::get('filtergradeid');
        
        $warehouse_id       = Session::get('filterwarehouseid');

        $merchant_id       = Session::get('filtermerchantid');
        
        $invitemstock       = InvtItemStock::with('item.merchant','unit','category','warehouse')
        ->where('invt_item_stock.data_state',0);
        if($item_category_id||$item_category_id!=null||$item_category_id!=''){
            $invitemstock   = $invitemstock->where('invt_item_stock.item_category_id', $item_category_id);
        }
        if($item_id||$item_id!=null||$item_id!=''){
            $invitemstock   = $invitemstock->where('invt_item_stock.item_id', $item_id);
        }
        if($warehouse_id||$warehouse_id!=null||$warehouse_id!=''){
            $invitemstock   = $invitemstock->where('invt_item_stock.warehouse_id', $warehouse_id);
        }
        if($merchant_id||$warehouse_id!=null||$warehouse_id!=''){
            $invitemstock   = $invitemstock->where('invt_item_stock.warehouse_id', $warehouse_id);
        }
        $invitemstock       = $invitemstock->get();
    //    dd($invitemstock);
        $spreadsheet = new Spreadsheet();

        if(count($invitemstock)>=0){
            $spreadsheet->getProperties()->setCreator("TRADING SYSTEM")
                ->setLastModifiedBy("TRADING SYSTEM")
                ->setTitle("Stock Barang")
                ->setSubject("")
                ->setDescription("Stock Barang")
                ->setKeywords("Stock Barang")
                ->setCategory("Stock Barang");

            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Stock Barang");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(25);


    
            $spreadsheet->getActiveSheet()->mergeCells("B1:H1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1', "Stock Barang Periode ".date('d M Y'));	
            $sheet->setCellValue('B3', "No");
            $sheet->setCellValue('C3', "Kategori");
            $sheet->setCellValue('D3', "Barang");
            $sheet->setCellValue('E3', "Qty");
            $sheet->setCellValue('F3', "Satuan");
            $sheet->setCellValue('G3', "Gudang");
            $sheet->setCellValue('H3', "Tanggal Datang");
            
            $j  = 4;
            $no = 1;
            if(count($invitemstock)==0){
                $lastno = 2;
                $lastj = 4;
               }else{
            foreach($invitemstock as $key => $val){
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Stock Barang");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, $this->getInvItemCategoryName($val['item_category_id']));
                $sheet->setCellValue('D'.$j, $this->getInvItemName($val['item_id']));
                $sheet->setCellValue('E'.$j, $val['last_balance']);
                $sheet->setCellValue('F'.$j, $this->getInvItemUnitName($val['item_unit_id']));
                $sheet->setCellValue('G'.$j, $this->getInvWarehouseName($val['warehouse_id']));
                $sheet->setCellValue('H'.$j, date('Y-m-d', strtotime($val['created_at'])));
                

                $no++;
                $j++;
                $lastno = $no;
                $lastj = $j;
            }

           
            // $sheet = $spreadsheet->getActiveSheet(0);
            // $spreadsheet->getActiveSheet()->getStyle('B'.$lastj.':H'.$lastj)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // $sheet->setCellValue('B' . $lastj , 'Jumlah Total:');
            // $sumrangeQty = 'F'. $lastno - 1 .':F'.$j;
            // $sheet->setCellValue('H' . $lastj , '=SUM('.$sumrangeQty.')');

            // $sheet->setCellValue('F' . $lastj + 1, 'Mengetahui');
            // $sheet->setCellValue('K' . $lastj + 1, 'Dibuat Oleh');


            // $spreadsheet->getActiveSheet()->getStyle('E'.$lastj + 5)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // $spreadsheet->getActiveSheet()->getStyle('H'.$lastj + 5)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            // $spreadsheet->getActiveSheet()->getStyle('K'.$lastj + 5)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
           
           
            // $sheet->setCellValue('E' . $lastj + 5, 'Apoteker');
            // $sheet->setCellValue('H' . $lastj + 5, 'Administrasi Pajak');
            // $sheet->setCellValue('K' . $lastj + 5, 'Dibuat Oleh');

        }
        
            // 
            $filename='Stock Barang '.date('d M Y').'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}

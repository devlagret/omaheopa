<?php

use App\Http\Controllers\AcctAccountController;
use App\Http\Controllers\AcctAccountSettingController;
use App\Http\Controllers\AcctDisbursementReportController;
use App\Http\Controllers\AcctJournalMemorialController;
use App\Http\Controllers\AcctBalanceSheetReportController;
use App\Http\Controllers\AcctLedgerReportController;
use App\Http\Controllers\AcctProfitLossReportController;
use App\Http\Controllers\AcctProfitLossYearReportController;
use App\Http\Controllers\AcctReceiptsController;
use App\Http\Controllers\AcctReceiptsReportController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckInCheckOutController;
use App\Http\Controllers\CoreBuildingController;
use App\Http\Controllers\CoreDivisionController;
use App\Http\Controllers\CorePriceTypeController;
use App\Http\Controllers\CoreRoomController;
use App\Http\Controllers\CoreRoomTypeController;
use App\Http\Controllers\CoreSupplierController;
use App\Http\Controllers\DownPaymentController;
use App\Http\Controllers\GeneralLedgerController;
use App\Http\Controllers\PreferenceCompanyController;
use App\Http\Controllers\RestoreDataController;
use App\Http\Controllers\SalesMerchantController;
use App\Http\Controllers\SalesRoomMenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvGoodsReceivedNoteController;
use App\Http\Controllers\InvtItemBarcodeController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\SystemUserGroupController;
use App\Http\Controllers\InvtItemCategoryController;
use App\Http\Controllers\InvtItemController;
use App\Http\Controllers\InvtItemPackgeController;
use App\Http\Controllers\InvtItemUnitController;
use App\Http\Controllers\InvtStockAdjustmentController;
use App\Http\Controllers\InvtStockAdjustmentReportController;
use App\Http\Controllers\InvtWarehouseController;
use App\Http\Controllers\InvWarehouseTransferController;
use App\Http\Controllers\InvWarehouseTransferReceivedNoteController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PurchaseInvoicebyItemReportController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseInvoiceReportController;
use App\Http\Controllers\PurchasePaymentController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseReturnReportController;
use App\Http\Controllers\SalesCustomerController;
use App\Http\Controllers\SalesInvoicebyItemReportController;
use App\Http\Controllers\SalesInvoiceByUserReportController;
use App\Http\Controllers\SalesInvoiceByYearReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesInvoiceReportController;
use App\Http\Controllers\SalesRoomFacilityController;
use App\Http\Controllers\SalesRoomPriceController;
use App\Http\Controllers\SystemLogsController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/quote', [PublicController::class, 'quote'])->name('quote');

Route::get('/item-unit',[InvtItemUnitController::class, 'index'])->name('item-unit');
Route::get('/item-unit/add',[InvtItemUnitController::class, 'addInvtItemUnit'])->name('add-item-unit');
Route::post('/item-unit/elements-add',[InvtItemUnitController::class, 'elementAddElementsInvtItemUnit'])->name('add-item-unit-elements');
Route::post('/item-unit/process-add',[InvtItemUnitController::class,'processAddElementsInvtItemUnit'])->name('process-add');
Route::get('/item-unit/reset-add',[InvtItemUnitController::class, 'addReset'])->name('add-reset-item-unit');
Route::get('/item-unit/edit/{item_unit_id}', [InvtItemUnitController::class, 'editInvtItemUnit'])->name('edit-item-unit');
Route::post('/item-unit/process-edit-item-unit', [InvtItemUnitController::class, 'processEditInvtItemUnit'])->name('process-edit-item-unit');
Route::get('/item-unit/delete/{item_unit_id}', [InvtItemUnitController::class, 'deleteInvtItemUnit'])->name('delete-item-unit');

Route::get('/item-category',[InvtItemCategoryController::class, 'index'])->name('item-category');
Route::get('/item-category/add/{merchant_id?}',[InvtItemCategoryController::class, 'addItemCategory'])->name('add-item-category');
Route::post('/item-category/elements-add',[InvtItemCategoryController::class, 'elementsAddItemCategory'])->name('elements-add-category');
Route::post('/item-category/process-add-category', [InvtItemCategoryController::class, 'processAddItemCategory'])->name('process-add-item-category');
Route::get('/item-category/reset-add',[InvtItemCategoryController::class, 'addReset'])->name('add-reset-category');
Route::get('/item-category/edit-category/{item_category_id}', [InvtItemCategoryController::class, 'editItemCategory'])->name('edit-item-category');
Route::post('/item-category/process-edit-item-category', [InvtItemCategoryController::class, 'processEditItemCategory'])->name('process-edit-item-category');
Route::get('/item-category/delete-category/{item_category_id}', [InvtItemCategoryController::class, 'deleteItemCategory'])->name('delete-item-category');
Route::get('/item-category/check-delete-category/{item_category_id}', [InvtItemCategoryController::class, 'checkDeleteItemCategory'])->name('check-delete-item-category');

Route::get('/item',[InvtItemController::class, 'index'])->name('item');
Route::post('/item/unit',[InvtItemController::class, 'getItemUnit'])->name('get-item-unit');
Route::post('/item/cost',[InvtItemController::class, 'getItemCost'])->name('get-item-cost');
Route::post('/item/cost/process-edit',[InvtItemController::class, 'processEditCost'])->name('process-edit-cost-item');
Route::post('/item/category',[InvtItemController::class, 'getCategory'])->name('get-item-category');
Route::post('/merchant/item/',[InvtItemController::class, 'getMerchantItem'])->name('get-merchant-item');
Route::get('/item/add-kemasan',[InvtItemController::class, 'addKemasan'])->name('add-kemasan');
Route::get('/item/remove-kemasan',[InvtItemController::class, 'removeKemasan'])->name('remove-kemasan');
Route::get('/item/add-item', [InvtItemController::class, 'addItem'])->name('add-item');
Route::get('/item/add-reset', [InvtItemController::class, 'addResetItem'])->name('add-reset-item');
Route::post('/item/add-item-elements', [InvtItemController::class, 'addItemElements'])->name('add-item-elements');
Route::post('/item/process-add-item', [InvtItemController::class,'processAddItem'])->name('process-add-item');
Route::get('/item/edit-item/{item_id}', [InvtItemController::class, 'editItem'])->name('edit-item');
Route::post('/item/process-edit-item', [InvtItemController::class, 'processEditItem'])->name('process-edit-item');
Route::get('/item/delete-item/{item_id}', [InvtItemController::class, 'deleteItem'])->name('delete-item');
Route::get('/item/check-delete-item/{item_id}', [InvtItemController::class, 'checkDeleteItem'])->name('check-delete-item');

Route::get('/warehouse',[InvtWarehouseController::class, 'index'])->name('warehouse');
Route::get('/warehouse/add-warehouse', [InvtWarehouseController::class, 'addWarehouse'])->name('add-warehouse');
Route::get('/warehouse/add-reset', [InvtWarehouseController::class, 'addResetWarehouse'])->name('add-reset-warehouse');
Route::post('/warehouse/add-warehouse-elements', [InvtWarehouseController::class, 'addElementsWarehouse'])->name('add-warehouse-elements');
Route::post('/warehouse/process-add-warehouse', [InvtWarehouseController::class,'processAddWarehouse'])->name('process-add-warehouse');
Route::get('/warehouse/edit-warehouse/{warehouse_id}',[InvtWarehouseController::class, 'editWarehouse'])->name('edit-warehouse');
Route::post('/warehouse/process-edit-warehouse', [InvtWarehouseController::class, 'processEditWarehouse'])->name('process-edit-warehouse');
Route::get('/warehouse/delete-warehouse/{warehouse_id}', [InvtWarehouseController::class, 'deleteWarehouse'])->name('delete-warehouse');

Route::get('/sales-invoice',[SalesInvoiceController::class, 'index'])->name('sales-invoice');
Route::get('/sales-invoice/add', [SalesInvoiceController::class,'addSalesInvoice'])->name('add-sales-invoice');
Route::post('/sales-invoice/add-elements', [SalesInvoiceController::class,'addElementsSalesInvoice'])->name('add-elements-sales-invoice');
Route::post('/sales-invoice/process-add', [SalesInvoiceController::class, 'processAddSalesInvoice'])->name('process-add-sales-invoice');
Route::get('/sales-invoice/reset-add',[SalesInvoiceController::class, 'resetSalesInvoice'])->name('add-reset-sales-invoice');
Route::post('/sales-invoice/add-array',[SalesInvoiceController::class,'addArraySalesInvoice'])->name('add-array-sales-invoice');
Route::get('/sales-invoice/delete-array/{record_id}',[SalesInvoiceController::class,'deleteArraySalesInvoice'])->name('delete-array-sales-invoice');
Route::get('/sales-invoice/detail/{sales_invoice_id}',[SalesInvoiceController::class, 'detailSalesInvoice'])->name('detail-sales-invoice');
Route::get('/sales-invoice/delete/{sales_invoice_id}',[SalesInvoiceController::class, 'deleteSalesInvoice'])->name('delete-sales-invoice');
Route::get('/sales-invoice/filter-reset',[SalesInvoiceController::class, 'filterResetSalesInvoice'])->name('filter-reset-sales-invoice');
Route::post('/sales-invoice/filter',[SalesInvoiceController::class, 'filterSalesInvoice'])->name('filter-sales-invoice');

Route::get('/purchase-invoice', [PurchaseInvoiceController::class, 'index'])->name('purchase-invoice');
Route::get('/purchase-invoice/add', [PurchaseInvoiceController::class, 'addPurchaseInvoice'])->name('add-purchase-invoice');
Route::get('/purchase-invoice/add-reset', [PurchaseInvoiceController::class, 'addResetPurchaseInvoice'])->name('add-reset-purchase-invoice');
Route::post('/purchase-invoice/add-elements', [PurchaseInvoiceController::class, 'addElementsPurchaseInvoice'])->name('add-elements-purchase-invoice');
Route::post('/purchase-invoice/add-array',[PurchaseInvoiceController::class, 'addArrayPurchaseInvoice'])->name('add-array-purchase-invoice');
Route::get('/purchase-invoice/delete-array/{record_id}', [PurchaseInvoiceController::class, 'deleteArrayPurchaseInvoice'])->name('delete-array-purchase-invoice');
Route::post('/purchase-invoice/process-add', [PurchaseInvoiceController::class, 'processAddPurchaseInvoice'])->name('process-add-purchase-invoice');
Route::get('/purchase-invoice/detail/{purchase_invoice_id}',[PurchaseInvoiceController::class, 'detailPurchaseInvoice'])->name('detail-purchase-invoice');
Route::post('/purchase-invoice/filter', [PurchaseInvoiceController::class,'filterPurchaseInvoice'])->name('filter-purchase-invoice');
Route::get('/purchase-invoice/filter-reset', [PurchaseInvoiceController::class,'filterResetPurchaseInvoice'])->name('filter-reset-purchase-invoice');

Route::get('/system-user', [SystemUserController::class, 'index'])->name('system-user');
Route::get('/system-user/add', [SystemUserController::class, 'addSystemUser'])->name('add-system-user');
Route::post('/system-user/process-add-system-user', [SystemUserController::class, 'processAddSystemUser'])->name('process-add-system-user');
Route::get('/system-user/edit/{user_id}', [SystemUserController::class, 'editSystemUser'])->name('edit-system-user');
Route::post('/system-user/process-edit-system-user', [SystemUserController::class, 'processEditSystemUser'])->name('process-edit-system-user');
Route::get('/system-user/delete-system-user/{user_id}', [SystemUserController::class, 'deleteSystemUser'])->name('delete-system-user');
Route::get('/system-user/change-password/{user_id}  ', [SystemUserController::class, 'changePassword'])->name('change-password');
Route::post('/system-user/process-change-password', [SystemUserController::class, 'processChangePassword'])->name('process-change-password');


Route::get('/system-user-group', [SystemUserGroupController::class, 'index'])->name('system-user-group');
Route::get('/system-user-group/add', [SystemUserGroupController::class, 'addSystemUserGroup'])->name('add-system-user-group');
Route::post('/system-user-group/process-add-system-user-group', [SystemUserGroupController::class, 'processAddSystemUserGroup'])->name('process-add-system-user-group');
Route::get('/system-user-group/edit/{user_id}', [SystemUserGroupController::class, 'editSystemUserGroup'])->name('edit-system-user-group');
Route::post('/system-user-group/process-edit-system-user-group', [SystemUserGroupController::class, 'processEditSystemUserGroup'])->name('process-edit-system-user-group');
Route::get('/system-user-group/delete-system-user-group/{user_id}', [SystemUserGroupController::class, 'deleteSystemUserGroup'])->name('delete-system-user-group');

Route::get('/stock-adjustment',[InvtStockAdjustmentController::class,'index'])->name('stock-adjustment');
Route::get('/stock-adjustment/add', [InvtStockAdjustmentController::class,'addStockAdjustment'])->name('add-stock-adjustment');
Route::get('/stock-adjustment/add-reset', [InvtStockAdjustmentController::class,'addReset'])->name('add-reset-stock-adjustment');
Route::get('/stock-adjustment/list-reset', [InvtStockAdjustmentController::class,'listReset'])->name('list-reset-stock-adjustment');
Route::post('/stock-adjustment/add-elements',[InvtStockAdjustmentController::class, 'addElementsStockAdjustment'])->name('add-elements-stock-adjustment');
Route::post('/stock-adjustment/filter-add', [InvtStockAdjustmentController::class, 'filterAddStockAdjustment'])->name('filter-add-stock-adjustment');
Route::post('/stock-adjustment/filter-list', [InvtStockAdjustmentController::class, 'filterListStockAdjustment'])->name('filter-list-stock-adjustment');
Route::post('/stock-adjustment/process-add', [InvtStockAdjustmentController::class, 'processAddStockAdjustment'])->name('process-add-stock-adjustment');
Route::get('/stock-adjustment/detail/{stock_adjustment_id}',[InvtStockAdjustmentController::class, 'detailStockAdjustment'])->name('detail-stock-adjustment');

Route::get('/stock-adjustment-report',[InvtStockAdjustmentReportController::class, 'index'])->name('stock-adjustment-report');
Route::post('/stock-adjustment-report/filter',[InvtStockAdjustmentReportController::class, 'filterStockAdjustmentReport'])->name('stock-adjustment-report-filter');
Route::get('/stock-adjustment-report/reset',[InvtStockAdjustmentReportController::class, 'resetStockAdjustmentReport'])->name('stock-adjustment-report-reset');
Route::get('/stock-adjustment-report/print',[InvtStockAdjustmentReportController::class, 'printStockAdjustmentReport'])->name('stock-adjustment-report-print');
Route::get('/stock-adjustment-report/export',[InvtStockAdjustmentReportController::class, 'exportStockAdjustmentReport'])->name('stock-adjustment-report-export');

Route::get('/purchase-invoice-report', [PurchaseInvoiceReportController::class, 'index'])->name('purchase-invoice-report');
Route::post('/purchase-invoice-report/filter',[PurchaseInvoiceReportController::class, 'filterPurchaseInvoiceReport'])->name('filter-purchase-invoice-report');
Route::get('/purchase-invoice-report/filter-reset',[PurchaseInvoiceReportController::class, 'filterResetPurchaseInvoiceReport'])->name('filter-reset-purchase-invoice-report');
Route::get('/purchase-invoice-report/print',[PurchaseInvoiceReportController::class, 'printPurchaseInvoiceReport'])->name('print-purchase-invoice-report');
Route::get('/purchase-invoice-report/export',[PurchaseInvoiceReportController::class, 'exportPurchaseInvoiceReport'])->name('export-purchase-invoice-report');

Route::get('/purchase-invoice-by-item-report',[PurchaseInvoicebyItemReportController::class, 'index'])->name('purchase-invoice-by-item-report');
Route::post('/purchase-invoice-by-item-report/filter',[PurchaseInvoicebyItemReportController::class, 'filterPurchaseInvoicebyItemReport'])->name('filter-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/filter-reset',[PurchaseInvoicebyItemReportController::class, 'filterResetPurchaseInvoicebyItemReport'])->name('filter-reset-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/print',[PurchaseInvoicebyItemReportController::class, 'printPurchaseInvoicebyItemReport'])->name('print-purchase-invoice-by-item-report');
Route::get('/purchase-invoice-by-item-report/export',[PurchaseInvoicebyItemReportController::class, 'exportPurchaseInvoicebyItemReport'])->name('export-purchase-invoice-by-item-report');

Route::get('/sales-invoice-report', [SalesInvoiceReportController::class, 'index'])->name('sales-invoice-report');
Route::post('/sales-invoice-report/filter', [SalesInvoiceReportController::class, 'filterSalesInvoiceReport'])->name('filter-sales-invoice-report');
Route::get('/sales-invoice-report/filter-reset', [SalesInvoiceReportController::class, 'filterResetSalesInvoiceReport'])->name('filter-reset-sales-invoice-report');
Route::get('/sales-invoice-report/print', [SalesInvoiceReportController::class, 'printSalesInvoiceReport'])->name('print-sales-invoice-report');
Route::get('/sales-invoice-report/export', [SalesInvoiceReportController::class, 'exportSalesInvoiceReport'])->name('export-sales-invoice-report');

Route::get('/sales-invoice-by-item-report',[SalesInvoicebyItemReportController::class, 'index'])->name('sales-invoice-by-item-report');
Route::post('/sales-invoice-by-item-report/filter',[SalesInvoicebyItemReportController::class, 'filterSalesInvoicebyItemReport'])->name('filter-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/filter-reset',[SalesInvoicebyItemReportController::class, 'filterResetSalesInvoicebyItemReport'])->name('filter-reset-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/print',[SalesInvoicebyItemReportController::class, 'printSalesInvoicebyItemReport'])->name('print-sales-invoice-by-item-report');
Route::get('/sales-invoice-by-item-report/export',[SalesInvoicebyItemReportController::class, 'exportSalesInvoicebyItemReport'])->name('export-sales-invoice-by-item-report');

Route::get('/sales-invoice-by-item-report/not-sold',[SalesInvoicebyItemReportController::class, 'notSold'])->name('sales-invoice-by-item-not-sold-report');
Route::post('/sales-invoice-by-item-report/filter-not-sold',[SalesInvoicebyItemReportController::class, 'filterSalesInvoicebyItemNotSoldReport'])->name('filter-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/not-sold-filter-reset',[SalesInvoicebyItemReportController::class, 'filterResetSalesInvoicebyItemNotSoldReport'])->name('filter-reset-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/print-not-sold',[SalesInvoicebyItemReportController::class, 'printSalesInvoicebyItemNotSoldReport'])->name('print-sales-invoice-by-item-not-sold-report');
Route::get('/sales-invoice-by-item-report/export-not-sold',[SalesInvoicebyItemReportController::class, 'exportSalesInvoicebyItemNotSoldReport'])->name('export-sales-invoice-by-item-not-sold-report');

Route::get('/sales-invoice-by-year-report',[SalesInvoiceByYearReportController::class, 'index'])->name('sales-invoice-by-year-report');
Route::post('/sales-invoice-by-year-report/filter',[SalesInvoiceByYearReportController::class, 'filterSalesInvoicebyYearReport'])->name('filter-sales-invoice-by-year-report');
Route::get('/sales-invoice-by-year-report/print',[SalesInvoiceByYearReportController::class, 'printSalesInvoicebyYearReport'])->name('print-sales-invoice-by-year-report');
Route::get('/sales-invoice-by-year-report/export',[SalesInvoiceByYearReportController::class, 'exportSalesInvoicebyYearReport'])->name('export-sales-invoice-by-year-report');

Route::get('/sales-invoice-by-user-report',[SalesInvoiceByUserReportController::class, 'index'])->name('sales-invoice-by-user-report');
Route::post('/sales-invoice-by-user-report/filter',[SalesInvoicebyUserReportController::class, 'filterSalesInvoicebyUserReport'])->name('filter-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/filter-reset',[SalesInvoicebyUserReportController::class, 'filterResetSalesInvoicebyUserReport'])->name('filter-reset-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/print',[SalesInvoicebyUserReportController::class, 'printSalesInvoicebyUserReport'])->name('print-sales-invoice-by-user-report');
Route::get('/sales-invoice-by-user-report/export',[SalesInvoicebyUserReportController::class, 'exportSalesInvoicebyUserReport'])->name('export-sales-invoice-by-user-report');

Route::get('/acct-account', [AcctAccountController::class, 'index'])->name('acct-account');
Route::get('/acct-account/add',[AcctAccountController::class, 'addAcctAccount'])->name('add-acct-account');
Route::post('/acct-account/process-add',[AcctAccountController::class, 'processAddAcctAccount'])->name('process-add-acct-account');
Route::post('/acct-account/add-elements',[AcctAccountController::class, 'addElementsAcctAccount'])->name('add-elements-acct-account');
Route::get('/acct-account/add-reset',[AcctAccountController::class, 'addResetAcctAccount'])->name('add-reset-acct-account');
Route::get('/acct-account/edit/{account_id}',[AcctAccountController::class, 'editAcctAccount'])->name('edit-acct-account');
Route::post('/acct-account/process-edit',[AcctAccountController::class, 'processEditAcctAccount'])->name('process-edit-acct-account');
Route::get('/acct-account/delete/{account_id}',[AcctAccountController::class, 'deleteAcctAccount'])->name('delete-edit-acct-account');

Route::get('/acct-account-setting',[AcctAccountSettingController::class, 'index'])->name('acct-account-setting');
Route::post('/acct-account-setting/process-add',[AcctAccountSettingController::class, 'processAddAcctAccountSetting'])->name('process-add-acct-account-setting');

Route::get('/journal-voucher', [JournalVoucherController::class, 'index'])->name('journal-voucher');
Route::get('/journal-voucher/add', [JournalVoucherController::class, 'addJournalVoucher'])->name('add-journal-voucher');
Route::post('/journal-voucher/add-array', [JournalVoucherController::class, 'addArrayJournalVoucher'])->name('add-array-journal-voucher');
Route::post('/journal-voucher/add-elements', [JournalVoucherController::class, 'addElementsJournalVoucher'])->name('add-elements-journal-voucher');
Route::get('/journal-voucher/reset-add', [JournalVoucherController::class, 'resetAddJournalVoucher'])->name('reset-add-journal-voucher');
Route::post('/journal-voucher/process-add', [JournalVoucherController::class, 'processAddJournalVoucher'])->name('process-add-journal-voucher');
Route::post('/journal-voucher/filter', [JournalVoucherController::class, 'filterJournalVoucher'])->name('filter-journal-voucher');
Route::get('/journal-voucher/reset-filter', [JournalVoucherController::class, 'resetFilterJournalVoucher'])->name('reset-filter-journal-voucher');
Route::get('/journal-voucher/print/{journal_voucher_id}', [JournalVoucherController::class, 'printJournalVoucher'])->name('print-journal-voucher');


Route::get('/ledger-report',[AcctLedgerReportController::class, 'index'])->name('ledger-report');
Route::post('/ledger-report/filter',[AcctLedgerReportController::class, 'filterLedgerReport'])->name('filter-ledger-report');
Route::get('/ledger-report/reset-filter',[AcctLedgerReportController::class, 'resetFilterLedgerReport'])->name('reset-filter-ledger-report');
Route::get('/ledger-report/print',[AcctLedgerReportController::class, 'printLedgerReport'])->name('print-ledger-report');
Route::get('/ledger-report/export',[AcctLedgerReportController::class, 'exportLedgerReport'])->name('export-ledger-report');

Route::get('/journal-memorial',[AcctJournalMemorialController::class, 'index'])->name('journal-memorial');
Route::post('/journal-memorial/filter',[AcctJournalMemorialController::class, 'filterJournalMemorial'])->name('filter-journal-memorial');
Route::get('/journal-memorial/reset-filter',[AcctJournalMemorialController::class, 'resetFilterJournalMemorial'])->name('reset-filter-journal-memorial');

Route::get('/profit-loss-report',[AcctProfitLossReportController::class, 'index'])->name('profit-loss-report');
Route::post('/profit-loss-report/filter',[AcctProfitLossReportController::class, 'filterProfitLossReport'])->name('filter-profit-loss-report');
Route::get('/profit-loss-report/reset-filter',[AcctProfitLossReportController::class, 'resetFilterProfitLossReport'])->name('reset-filter-profit-loss-report');
Route::get('/profit-loss-report/print',[AcctProfitLossReportController::class, 'printProfitLossReport'])->name('print-profit-loss-report');
Route::get('/profit-loss-report/export',[AcctProfitLossReportController::class, 'exportProfitLossReport'])->name('export-profit-loss-report');

Route::get('/profit-loss-year-report',[AcctProfitLossYearReportController::class, 'index'])->name('profit-loss-year-report');
Route::post('/profit-loss-year-report/filter',[AcctProfitLossYearReportController::class, 'filterProfitLossYearReport'])->name('filter-profit-loss-year-report');
Route::get('/profit-loss-year-report/reset-filter',[AcctProfitLossYearReportController::class, 'resetFilterProfitLossYearReport'])->name('reset-filter-profit-loss-year-report');
Route::get('/profit-loss-year-report/print',[AcctProfitLossYearReportController::class, 'printProfitLossYearReport'])->name('print-profit-loss-year-report');
Route::get('/profit-loss-year-report/export',[AcctProfitLossYearReportController::class, 'exportProfitLossYearReport'])->name('export-profit-loss-year-report');

Route::get('/sales-customer',[SalesCustomerController::class, 'index'])->name('sales-customer');
Route::get('/sales-customer/add',[SalesCustomerController::class, 'addSalesCustomer'])->name('add-sales-customer');
Route::post('/sales-customer/process-add',[SalesCustomerController::class, 'processAddSalesCustomer'])->name('process-add-sales-customer');
Route::get('/sales-customer/edit/{customer_id}',[SalesCustomerController::class, 'editSalesCustomer'])->name('edit-sales-customer');
Route::post('/sales-customer/process-edit',[SalesCustomerController::class, 'processEditSalesCustomer'])->name('process-edit-sales-customer');
Route::get('/sales-customer/delete/{customer_id}',[SalesCustomerController::class, 'deleteSalesCustomer'])->name('delete-sales-customer');

Route::get('/cash-receipts-report', [AcctReceiptsReportController::class, 'index'])->name('cash-receipts-report');
Route::post('/cash-receipts-report/filter',[AcctReceiptsReportController::class, 'filterAcctReceiptsReport'])->name('fiter-cash-receipts-report');
Route::get('/cash-receipts-report/reset-filter',[AcctReceiptsReportController::class, 'resetFilterAcctReceiptsReport'])->name('reset-filter-cash-receipts-report');
Route::get('/cash-receipts-report/print',[AcctReceiptsReportController::class, 'printAcctReceiptsReport'])->name('print-cash-receipts-report');
Route::get('/cash-receipts-report/export',[AcctReceiptsReportController::class, 'exportAcctReceiptsReport'])->name('export-cash-receipts-report');

Route::get('/cash-disbursement-report',[AcctDisbursementReportController::class, 'index'])->name('cash-disbursement-report');
Route::post('/cash-disbursement-report/filter',[AcctDisbursementReportController::class, 'filterDisbursementReport'])->name('filter-cash-disbursement-report');
Route::get('/cash-disbursement-report/reset-filter',[AcctDisbursementReportController::class, 'resetFilterDisbursementReport'])->name('reset-filter-cash-disbursement-report');
Route::get('/cash-disbursement-report/print',[AcctDisbursementReportController::class, 'printDisbursementReport'])->name('print-cash-disbursement-report');
Route::get('/cash-disbursement-report/export',[AcctDisbursementReportController::class, 'exportDisbursementReport'])->name('export-cash-disbursement-report');

 // Restore Data pages
 Route::prefix('restore')->name('restore.')->group(function () {
    Route::get('/', [RestoreDataController::class, 'index'])->name('index');
    Route::get('/{table}', [RestoreDataController::class, 'table'])->name('table');
    Route::get('/{table}/{col}/{id}', [RestoreDataController::class, 'restore'])->name('data');
    Route::get('/force/{table}/{col}/{id}', [RestoreDataController::class, 'forceDelete'])->name('force-delete');
});
 // Division pages
 Route::prefix('division')->name('division.')->group(function () {
    Route::get('/', [CoreDivisionController::class, 'index'])->name('index');
    Route::get('/add', [CoreDivisionController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreDivisionController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{division_id}', [CoreDivisionController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreDivisionController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{division_id}', [CoreDivisionController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreDivisionController::class, 'elementsAdd'])->name('elements-add');
});
 // Building pages
 Route::prefix('building')->name('building.')->group(function () {
    Route::get('/', [CoreBuildingController::class, 'index'])->name('index');
    Route::get('/add', [CoreBuildingController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreBuildingController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{building_id}', [CoreBuildingController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreBuildingController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{building_id}', [CoreBuildingController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreBuildingController::class, 'elementsAdd'])->name('elements-add');
});
 // Room pages
 Route::prefix('room')->name('room.')->group(function () {
    Route::get('/', [CoreRoomController::class, 'index'])->name('index');
    Route::get('/add', [CoreRoomController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreRoomController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_id}', [CoreRoomController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreRoomController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_id}', [CoreRoomController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreRoomController::class, 'elementsAdd'])->name('elements-add');
});
 // Room Price Type pages
 Route::prefix('price-type')->name('price-type.')->group(function () {
    Route::get('/', [CorePriceTypeController::class, 'index'])->name('index');
    Route::get('/add', [CorePriceTypeController::class, 'add'])->name('add');
    Route::post('/process-add', [CorePriceTypeController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{price_type_id}', [CorePriceTypeController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CorePriceTypeController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{price_type_id}', [CorePriceTypeController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CorePriceTypeController::class, 'elementsAdd'])->name('elements-add');
});
 // Room Price pages
 Route::prefix('room-price')->name('room-price.')->group(function () {
    Route::get('/', [SalesRoomPriceController::class, 'index'])->name('index');
    Route::get('/add', [SalesRoomPriceController::class, 'add'])->name('add');
    Route::post('/room-type', [SalesRoomPriceController::class, 'getType'])->name('get-room-type');
    Route::post('/room', [SalesRoomPriceController::class, 'getRoom'])->name('get-room');
    Route::post('/process-add', [SalesRoomPriceController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_price_id}', [SalesRoomPriceController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [SalesRoomPriceController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_price_id}', [SalesRoomPriceController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [SalesRoomPriceController::class, 'elementsAdd'])->name('elements-add');
});
 // Room Type pages
 Route::prefix('room-type')->name('room-type.')->group(function () {
    Route::get('/', [CoreRoomTypeController::class, 'index'])->name('index');
    Route::get('/add', [CoreRoomTypeController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreRoomTypeController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_type_id}', [CoreRoomTypeController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreRoomTypeController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_type_id}', [CoreRoomTypeController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreRoomTypeController::class, 'elementsAdd'])->name('elements-add');
});
 // Sales Room Menu pages
 Route::prefix('sales-room-menu')->name('sales-room-menu.')->group(function () {
    Route::get('/', [SalesRoomMenuController::class, 'index'])->name('index');
    Route::get('/add', [SalesRoomMenuController::class, 'add'])->name('add');
    Route::post('/process-add', [SalesRoomMenuController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_menu_id}', [SalesRoomMenuController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [SalesRoomMenuController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_menu_id}', [SalesRoomMenuController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [SalesRoomMenuController::class, 'elementsAdd'])->name('elements-add');
});
 // Sales Room Facility pages
 Route::prefix('sales-room-facility')->name('sales-room-facility.')->group(function () {
    Route::get('/', [SalesRoomFacilityController::class, 'index'])->name('index');
    Route::get('/add', [SalesRoomFacilityController::class, 'add'])->name('add');
    Route::post('/process-add', [SalesRoomFacilityController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_facility_id}', [SalesRoomFacilityController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [SalesRoomFacilityController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_facility_id}', [SalesRoomFacilityController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [SalesRoomFacilityController::class, 'elementsAdd'])->name('elements-add');
});
 // Suppplier pages
 Route::prefix('core-supplier')->name('supplier.')->group(function () {
    Route::get('/', [CoreSupplierController::class, 'index'])->name('index');
    Route::get('/add', [CoreSupplierController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreSupplierController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{supplier_id}', [CoreSupplierController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreSupplierController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{supplier_id}', [CoreSupplierController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreSupplierController::class, 'elementsAdd'])->name('elements-add');
});
 // Merchant (wahana) pages
 Route::prefix('sales-merchant')->name('sales-merchant.')->group(function () {
    Route::get('/', [SalesMerchantController::class, 'index'])->name('index');
    Route::get('/add', [SalesMerchantController::class, 'add'])->name('add');
    Route::post('/process-add', [SalesMerchantController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{merchant_id}', [SalesMerchantController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [SalesMerchantController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{merchant_id}', [SalesMerchantController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [SalesMerchantController::class, 'elementsAdd'])->name('elements-add');
});
 // Item barcode pages
 Route::prefix('item-barcode')->name('item-barcode.')->group(function () {
    Route::get('/{item_id}', [InvtItemBarcodeController::class, 'index'])->name('index');
    Route::post('/process-add', [InvtItemBarcodeController::class, 'processAdd'])->name('process-add');
    Route::get('/delete/{item_id}/{item_barcode_id}', [InvtItemBarcodeController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [InvtItemBarcodeController::class, 'elementsAdd'])->name('elements-add');
});
 // Package pages
 Route::prefix('package')->name('package.')->group(function () {
    Route::post('/add-item', [InvtItemPackgeController::class, 'processAddItem'])->name('process-add-item');
    Route::get('/delete-item/{item_id}/{item_unit}', [InvtItemPackgeController::class, 'processDeleteItem'])->name('delete-item');
    Route::get('/item/change-qty/{item_id}/{unit_id}/{value}', [InvtItemPackgeController::class, 'changeItemQty'])->name('change-qty');
    Route::post('/process-add', [InvtItemPackgeController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{item_package_id}', [InvtItemPackgeController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [InvtItemPackgeController::class, 'processEdit'])->name('process-edit');
    Route::get('/clear-item', [InvtItemPackgeController::class, 'clearItem'])->name('clear-item');
    Route::get('/delete/{item_package_id}', [InvtItemPackgeController::class, 'delete'])->name('delete');
});
 // Booking pages
 Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/add', [BookingController::class, 'add'])->name('add');
    Route::get('/detail/{sales_order_id}', [BookingController::class, 'detail'])->name('detail');
    Route::post('/add-room', [BookingController::class, 'addRoom'])->name('add-room');
    Route::post('/add-facility', [BookingController::class, 'addFacility'])->name('add-facility');
    Route::post('/change-facility-qty', [BookingController::class, 'changeFacilityQty'])->name('facility-qty');
    Route::post('/change-menu-qty', [BookingController::class, 'changeMenuQty'])->name('menu-qty');
    Route::post('/add-person', [BookingController::class, 'addPersonBooked'])->name('add-person');
    Route::post('/room', [BookingController::class, 'getRoom'])->name('get-room');
    Route::post('/check-room', [BookingController::class, 'checkRoom'])->name('check-room');
    Route::post('/get-price-list', [BookingController::class, 'getRoomPriceList'])->name('get-price-list');
    Route::get('/delete-room/{room_id?}', [BookingController::class, 'deleteBookedRoom'])->name('delete-booked-room');
    Route::get('/delete-facility/{room_facility_id?}', [BookingController::class, 'deleteFacility'])->name('delete-facility');
    Route::get('/delete-menu/{room_menu_id?}', [BookingController::class, 'deleteMenu'])->name('delete-menu');
    Route::post('/room-price', [BookingController::class, 'getRoomPrice'])->name('get-room-price');
    Route::post('/room-menu', [BookingController::class, 'getRoomMenus'])->name('get-room-menu');
    Route::post('/add-menu-item', [BookingController::class, 'addMenuItem'])->name('add-menu-item');
    Route::get('/clear-booked', [BookingController::class, 'clearBooked'])->name('clear-booked');
    Route::get('/clear-facility', [BookingController::class, 'clearFacility'])->name('clear-facility');
    Route::get('/clear-menu', [BookingController::class, 'clearMenu'])->name('clear-menu');
    Route::post('/room-type', [BookingController::class, 'getType'])->name('get-room-type');
    Route::post('/filter', [BookingController::class, 'filter'])->name('filter');
    Route::post('/process-add', [BookingController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{sales_order_id}', [BookingController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [BookingController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{sales_order_id}', [BookingController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [BookingController::class, 'elementsAdd'])->name('elements-add');
    Route::get('/reset', [BookingController::class, 'resetSession'])->name('reset');
    Route::get('/rescedule/{sales_order_id}', [BookingController::class, 'rescedule'])->name('rescedule');
    Route::post('/process-rescedule', [BookingController::class, 'processRescedule'])->name('process-rescedule');
});
 // DP (Down Paymwnt) pages
 Route::prefix('down-payment')->name('dp.')->group(function () {
    Route::get('/', [DownPaymentController::class, 'index'])->name('index');
    Route::get('/add', [DownPaymentController::class, 'add'])->name('add');
    Route::get('/process-add/{sales_order_id}/{source?}', [DownPaymentController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{sales_order_id}', [DownPaymentController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [DownPaymentController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{sales_order_id}', [DownPaymentController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [DownPaymentController::class, 'elementsAdd'])->name('elements-add');
    Route::post('/filter', [DownPaymentController::class, 'filter'])->name('filter');
    Route::post('/filter-add', [DownPaymentController::class, 'filterAdd'])->name('filter-add');
});
 // Check-in Check-Out pages
 Route::prefix('checkin-checkout')->name('cc.')->group(function () {
    Route::get('/', [CheckInCheckOutController::class, 'index'])->name('index');
    Route::get('/add', [CheckInCheckOutController::class, 'add'])->name('add');
    Route::post('/check', [CheckInCheckOutController::class, 'check'])->name('check');
    Route::post('/check-ext', [CheckInCheckOutController::class, 'checkExtend'])->name('check-extend');
    Route::post('/get-penalty', [CheckInCheckOutController::class, 'getPenalty'])->name('get-penalty');
    Route::get('/extend/{sales_order_id?}', [CheckInCheckOutController::class, 'extend'])->name('extend');
    Route::post('/process-extend', [CheckInCheckOutController::class, 'processExtend'])->name('process-extend');
    Route::post('/process-add', [CheckInCheckOutController::class, 'processAdd'])->name('process-add');
    Route::post('/process-checkout', [CheckInCheckOutController::class, 'processCheckOut'])->name('process-checkout');
    Route::get('/edit/{sales_order_id}', [CheckInCheckOutController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CheckInCheckOutController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{sales_order_id}', [CheckInCheckOutController::class, 'delete'])->name('delete');
    Route::post('/filter', [CheckInCheckOutController::class, 'filter'])->name('filter');
    Route::post('/elements-add', [CheckInCheckOutController::class, 'elementsAdd'])->name('elements-add');
});
 Route::prefix('cc-time')->name('ct.')->group(function () {
    Route::get('/', [PreferenceCompanyController::class, 'index'])->name('index');
    Route::post('/process-edit', [PreferenceCompanyController::class, 'processEditCCTime'])->name('process-edit-cc-time');
});

Route::prefix('log')->name('log.')->group(function () {
    Route::resource('system', SystemLogsController::class)->only(['index', 'destroy']);
    Route::get('system/del', [SystemLogsController::class,'destroy'])->name('destroy');
});


//penerimaan barang / invgoods received
Route::get('/goods-received-note', [InvGoodsReceivedNoteController::class, 'index'])->name('goods-received-note');
Route::get('/goods-received-note/search-purchase-order', [InvGoodsReceivedNoteController::class, 'searchPurchaseOrder'])->name('search-po-goods-received-note');
Route::get('/goods-received-note/add/{purchase_invoice_id}', [InvGoodsReceivedNoteController::class, 'addInvGoodsReceivedNote'])->name('add-goods-received-note');
Route::get('/goods-received-note/detail/{goods_received_note_id}', [InvGoodsReceivedNoteController::class, 'detailInvGoodsReceivedNote'])->name('detail-goods-received-note');
Route::post('/goods-received-note/process-add-goods-received-note', [InvGoodsReceivedNoteController::class, 'processAddInvGoodsReceivedNote'])->name('process-add-goods-received-note');
Route::get('/goods-received-note/delete-goods-received-note/{goods_received_note_id}', [InvGoodsReceivedNoteController::class, 'voidInvGoodsReceivedNote'])->name('delete-goods-received-note');
Route::get('/goods-received-note/process-delete/{goods_received_note_id}', [InvGoodsReceivedNoteController::class, 'processVoidInvGoodsReceivedNote'])->name('process-delete-goods-received-note');
Route::post('/goods-received-note/filter', [InvGoodsReceivedNoteController::class, 'filterInvGoodsReceivedNote'])->name('filter-goods-received-note');
Route::get('/goods-received-note/filter-reset', [InvGoodsReceivedNoteController::class, 'resetFilterInvGoodsReceivedNote'])->name('filter-reset-goods-received-note');
Route::post('/goods-received-note/add-new-purchase-order-item/{purchase_invoice_id}', [InvGoodsReceivedNoteController::class, 'addNewPurchaseOrderItem'])->name('add-new-purchase-order-item');
Route::get('/goods-received-note/delete-new_purchase_order_item/{purchase_invoice_id}', [InvGoodsReceivedNoteController::class, 'deleteNewPurchaseOrderItem'])->name('delete-new-purchase-order-item');


//warehouse transfer
Route::get('/warehouse-transfer', [InvWarehouseTransferController::class, 'index'])->name('warehouse-transfer');
Route::get('/warehouse-transfer/add', [InvWarehouseTransferController::class, 'addInvWarehouseTransfer'])->name('add-warehouse-transfer');
Route::post('/warehouse-transfer/process-add-warehouse-transfer', [InvWarehouseTransferController::class, 'processAddInvWarehouseTransfer'])->name('process-add-warehouse-transfer');
Route::get('/warehouse-transfer/detail/{product_type_id}', [InvWarehouseTransferController::class, 'detailInvWarehouseTransfer'])->name('edit-warehouse-transfer');
Route::post('/warehouse-transfer/process-edit-warehouse-transfer', [InvWarehouseTransferController::class, 'processEditInvWarehouseTransfer'])->name('process-edit-warehouse-transfer');
Route::get('/warehouse-transfer/void/{product_type_id}', [InvWarehouseTransferController::class, 'voidInvWarehouseTransfer'])->name('void-warehouse-transfer');
Route::post('/warehouse-transfer/process-void', [InvWarehouseTransferController::class, 'processVoidInvWarehouseTransfer'])->name('process-void-warehouse-transfer');
Route::post('/warehouse-transfer/filter', [InvWarehouseTransferController::class, 'filterInvWarehouseTransfer'])->name('filter-warehouse-transfer');
Route::get('/warehouse-transfer/filter-reset', [InvWarehouseTransferController::class, 'resetFilterInvWarehouseTransfer'])->name('filter-reset-warehouse-transfer');
Route::post('/warehouse-transfer/city', [InvWarehouseTransferController::class, 'getInvCity'])->name('warehouse-transfer-city');
Route::post('/warehouse-transfer/type', [InvWarehouseTransferController::class, 'getCoreType'])->name('warehouse-transfer-change-type');
Route::post('/warehouse-transfer/item', [InvWarehouseTransferController::class, 'getCoreItem'])->name('warehouse-transfer-item');
Route::post('/warehouse-transfer/add-array', [InvWarehouseTransferController::class, 'processAddArrayWarehouseTransferItem'])->name('warehouse_transfer-add-array');
Route::get('/warehouse-transfer/delete-array/{record_id}', [InvWarehouseTransferController::class, 'deleteArrayWarehouseTransferItem'])->name('warehouse-transfer-delete-array');
Route::get('/warehouse-transfer/search-purchase-invoice', [InvWarehouseTransferController::class, 'search'])->name('warehouse-transfer-search-purchase-invoice');
Route::post('/warehouse-transfer/elements-add', [InvWarehouseTransferController::class, 'elements_add'])->name('elements-add-warehouse-transfer');
Route::post('/warehouse-transfer/add-transfer-type', [InvWarehouseTransferController::class, 'addWarehouseTransferType'])->name('add-transfer-type-warehouse-transfer');
Route::post('/warehouse-transfer/select-data-unit', [InvWarehouseTransferController::class, 'getSelectDataUnit'])->name('select-data-unit');
Route::post('/warehouse-transfer/select-data-item', [InvWarehouseTransferController::class, 'getQtyStock'])->name('select-data-item');
Route::post('/warehouse-transfer/select-data-stock', [InvWarehouseTransferController::class, 'getIdStock'])->name('select-data-stock');

//penerimaan transfer gudang
Route::get('/warehouse-transfer-received-note', [InvWarehouseTransferReceivedNoteController::class, 'index'])->name('warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/search-warehouse-transfer', [InvWarehouseTransferReceivedNoteController::class, 'searchWarehouseTransfer'])->name('search-wt-warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/add/{purchase_order_item_id}', [InvWarehouseTransferReceivedNoteController::class, 'addInvWarehouseTransferReceivedNote'])->name('add-warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/detail/{purchase_order_item_id}', [InvWarehouseTransferReceivedNoteController::class, 'detailInvWarehouseTransferReceivedNote'])->name('detail-warehouse-transfer-received-note');
Route::post('/warehouse-transfer-received-note/process-add-warehouse-transfer-received-note', [InvWarehouseTransferReceivedNoteController::class, 'processAddInvWarehouseTransferReceivedNote'])->name('process-add-warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/delete-warehouse-transfer-received-note/{id}', [InvWarehouseTransferReceivedNoteController::class, 'voidInvWarehouseTransferReceivedNote'])->name('delete-warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/process-delete/{id}', [InvWarehouseTransferReceivedNoteController::class, 'processVoidInvWarehouseTransferReceivedNote'])->name('process-delete-warehouse-transfer-received-note');
Route::post('/warehouse-transfer-received-note/filter', [InvWarehouseTransferReceivedNoteController::class, 'filterInvWarehouseTransferReceivedNote'])->name('filter-warehouse-transfer-received-note');
Route::get('/warehouse-transfer-received-note/filter-reset', [InvWarehouseTransferReceivedNoteController::class, 'resetFilterInvWarehouseTransferReceivedNote'])->name('filter-reset-transfer-received-note');


//purchase - payment
Route::get('/purchase-payment', [PurchasePaymentController::class, 'index'])->name('purchase-payment');
Route::post('/purchase-payment/filter', [PurchasePaymentController::class, 'filterPurchasePayment'])->name('filter-purchase-payment');
Route::get('/purchase-payment/search', [PurchasePaymentController::class, 'searchCoreSupplier'])->name('search-core-supplier-purchase-payment');
Route::get('/purchase-payment/add/{supplier_id}', [PurchasePaymentController::class, 'addPurchasePayment'])->name('add-purchase-payment');
Route::get('/purchase-payment/detail/{supplier_id}', [PurchasePaymentController::class, 'detailPurchasePayment'])->name('detail-purchase-payment');
Route::get('/purchase-payment/delete/{supplier_id}', [PurchasePaymentController::class, 'deletePurchasePayment'])->name('delete-purchase-payment');
Route::post('/purchase-payment/process-delete', [PurchasePaymentController::class, 'processVoidPurchasePayment'])->name('process-delete-purchase-payment');
Route::post('/purchase-payment/process-add/', [PurchasePaymentController::class, 'processAddPurchasePayment'])->name('process-add-purchase-payment');
Route::post('/purchase-payment/elements-add/', [PurchasePaymentController::class, 'elements_add'])->name('elements-add-purchase-payment');
Route::post('/purchase-payment/add-bank/', [PurchasePaymentController::class, 'addCoreBank'])->name('add-bank-purchase-payment');
Route::post('/purchase-payment/add-transfer-array/', [PurchasePaymentController::class, 'processAddTransferArray'])->name('add-transfer-array-purchase-payment');
Route::get('/purchase-payment/delete-transfer-array/{record_id}/{supplier_id}', [PurchasePaymentController::class, 'deleteTransferArray'])->name('delete-transfer-array-purchase-payment');

//neraca
Route::get('balance-sheet-report',[AcctBalanceSheetReportController::class, 'index'])->name('balance-sheet-report');
Route::post('balance-sheet-report/filter',[AcctBalanceSheetReportController::class, 'filterAcctBalanceSheetReport'])->name('filter-balance-sheet-report');
Route::get('balance-sheet-report/reset-filter',[AcctBalanceSheetReportController::class, 'resetFilterAcctBalanceSheetReport'])->name('reset-filter-balance-sheet-report');
Route::get('balance-sheet-report/print',[AcctBalanceSheetReportController::class, 'printAcctBalanceSheetReport'])->name('print-balance-sheet-report');
Route::get('balance-sheet-report/export',[AcctBalanceSheetReportController::class, 'exportAcctBalanceSheetReport'])->name('export-balance-sheet-report');


//purchase return
Route::get('/purchase-return', [PurchaseReturnController::class, 'index'])->name('purchase-return');
Route::get('/purchase-return/search-goods-received-note', [PurchaseReturnController::class, 'searchGoodsReceivedNote'])->name('search-goods-received-note');
Route::get('/purchase-return/add/{goods_received_note_id}', [PurchaseReturnController::class, 'addPurchaseReturn'])->name('add-purchase-return');
Route::get('/purchase-return/add-reset', [PurchaseReturnController::class, 'addResetPurchaseReturn'])->name('add-reset-purchase-return');
Route::post('/purchase-return/add-elements', [PurchaseReturnController::class, 'addElementsPurchaseReturn'])->name('add-elements-purchase-return');
Route::post('/purchase-return/process-add',[PurchaseReturnController::class, 'processAddPurchaseReturn'])->name('process-add-purchase-return');
Route::post('/purchase-return/add-array',[PurchaseReturnController::class, 'addArrayPurchaseReturn'])->name('add-array-purchase-return');
Route::get('/purchase-return/delete-array/{record_id}',[PurchaseReturnController::class, 'deleteArrayPurchaseReturn'])->name('delete-array-purchase-return');
Route::get('/purchase-return/detail/{purchase_return_id}',[PurchaseReturnController::class, 'detailPurchaseReturn'])->name('detail-purchase-return');
Route::post('/purchase-return/filter',[PurchaseReturnController::class, 'filterPurchaseReturn'])->name('filter-purchase-return');
Route::get('/purchase-return/filter-reset',[PurchaseReturnController::class, 'filterResetPurchaseReturn'])->name('filter-reset-purchase-return');
Route::get('/purchase-return/edit', [PurchaseReturnController::class, 'editPurchaseReturn'])->name('edit-purchase-return');
Route::post('/purchase-return/process-edit',[PurchaseReturnController::class, 'processeditPurchaseReturn'])->name('process-edit-purchase-return');
Route::get('/purchase-return/delete', [PurchaseReturnController::class, 'deletePurchaseReturn'])->name('delete-purchase-return');


Route::get('/purchase-return-report',[PurchaseReturnReportController::class, 'index'])->name('purchase-return-report');
Route::post('/purchase-return-report/filter',[PurchaseReturnReportController::class, 'filterPurchaseReturnReport'])->name('filter-purchase-return-report');
Route::get('/purchase-return-report/filter-reset',[PurchaseReturnReportController::class, 'filterResetPurchaseReturnReport'])->name('filter-reset-purchase-return-report');
Route::get('/purchase-return-report/print',[PurchaseReturnReportController::class, 'printPurchaseReturnReport'])->name('print-purchase-return-report');
Route::get('/purchase-return-report/export',[PurchaseReturnReportController::class, 'exportPurchaseReturnReport'])->name('export-purchase-return-report');
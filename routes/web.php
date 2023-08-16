<?php

use App\Http\Controllers\AcctAccountController;
use App\Http\Controllers\AcctAccountSettingController;
use App\Http\Controllers\AcctDisbursementReportController;
use App\Http\Controllers\AcctJournalMemorialController;
use App\Http\Controllers\AcctLedgerReportController;
use App\Http\Controllers\AcctProfitLossReportController;
use App\Http\Controllers\AcctProfitLossYearReportController;
use App\Http\Controllers\AcctReceiptsController;
use App\Http\Controllers\AcctReceiptsReportController;
use App\Http\Controllers\CoreRoomController;
use App\Http\Controllers\CoreRoomTypeController;
use App\Http\Controllers\GeneralLedgerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SystemUserController;
use App\Http\Controllers\SystemUserGroupController;
use App\Http\Controllers\InvtItemCategoryController;
use App\Http\Controllers\InvtItemController;
use App\Http\Controllers\InvtItemUnitController;
use App\Http\Controllers\InvtStockAdjustmentController;
use App\Http\Controllers\InvtStockAdjustmentReportController;
use App\Http\Controllers\InvtWarehouseController;
use App\Http\Controllers\JournalVoucherController;
use App\Http\Controllers\PurchaseInvoicebyItemReportController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\PurchaseInvoiceReportController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseReturnReportController;
use App\Http\Controllers\SalesCustomerController;
use App\Http\Controllers\SalesInvoicebyItemReportController;
use App\Http\Controllers\SalesInvoiceByUserReportController;
use App\Http\Controllers\SalesInvoiceByYearReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesInvoiceReportController;
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

Route::get('/item-unit',[InvtItemUnitController::class, 'index'])->name('item-unit');
Route::get('/item-unit/add',[InvtItemUnitController::class, 'addInvtItemUnit'])->name('add-item-unit');
Route::post('/item-unit/elements-add',[InvtItemUnitController::class, 'elementAddElementsInvtItemUnit'])->name('add-item-unit-elements');
Route::post('/item-unit/process-add',[InvtItemUnitController::class,'processAddElementsInvtItemUnit'])->name('process-add');
Route::get('/item-unit/reset-add',[InvtItemUnitController::class, 'addReset'])->name('add-reset-item-unit');
Route::get('/item-unit/edit/{item_unit_id}', [InvtItemUnitController::class, 'editInvtItemUnit'])->name('edit-item-unit');
Route::post('/item-unit/process-edit-item-unit', [InvtItemUnitController::class, 'processEditInvtItemUnit'])->name('process-edit-item-unit');
Route::get('/item-unit/delete/{item_unit_id}', [InvtItemUnitController::class, 'deleteInvtItemUnit'])->name('delete-item-unit');

Route::get('/item-category',[InvtItemCategoryController::class, 'index'])->name('item-category');
Route::get('/item-category/add',[InvtItemCategoryController::class, 'addItemCategory'])->name('add-item-category');
Route::post('/item-category/elements-add',[InvtItemCategoryController::class, 'elementsAddItemCategory'])->name('elements-add-category');
Route::post('/item-category/process-add-category', [InvtItemCategoryController::class, 'processAddItemCategory'])->name('process-add-item-category');
Route::get('/item-category/reset-add',[InvtItemCategoryController::class, 'addReset'])->name('add-reset-category');
Route::get('/item-category/edit-category/{item_category_id}', [InvtItemCategoryController::class, 'editItemCategory'])->name('edit-item-category');
Route::post('/item-category/process-edit-item-category', [InvtItemCategoryController::class, 'processEditItemCategory'])->name('process-edit-item-category');
Route::get('/item-category/delete-category/{item_category_id}', [InvtItemCategoryController::class, 'deleteItemCategory'])->name('delete-item-category');

Route::get('/item',[InvtItemController::class, 'index'])->name('item');
Route::get('/item/add-item', [InvtItemController::class, 'addItem'])->name('add-item');
Route::get('/item/add-reset', [InvtItemController::class, 'addResetItem'])->name('add-reset-item');
Route::post('/item/add-item-elements', [InvtItemController::class, 'addItemElements'])->name('add-item-elements');
Route::post('/item/process-add-item', [InvtItemController::class,'processAddItem'])->name('process-add-item');
Route::get('/item/edit-item/{item_id}', [InvtItemController::class, 'editItem'])->name('edit-item');
Route::post('/item/process-edit-item', [InvtItemController::class, 'processEditItem'])->name('process-edit-item');
Route::get('/item/delete-item/{item_id}', [InvtItemController::class, 'deleteItem'])->name('delete-item');

Route::get('/warehouse',[InvtWarehouseController::class, 'index'])->name('warehouse');
Route::get('/warehouse/add-warehouse', [InvtWarehouseController::class, 'addWarehouse'])->name('add-warehouse');
Route::get('/warehouse/add-reset', [InvtWarehouseController::class, 'addResetWarehouse'])->name('add-reset-warehouse');
Route::post('/warehouse/add-warehouse-elements', [InvtWarehouseController::class, 'addElementsWarehouse'])->name('add-warehouse-elements');
Route::post('/warehouse/process-add-warehouse', [InvtWarehouseController::class,'processAddWarehouse'])->name('process-add-warehouse');
Route::get('/warehouse/edit-warehouse/{warehouse_id}',[InvtWarehouseController::class, 'editWarehouse'])->name('edit-warehouse');
Route::post('/warehouse/process-edit-warehouse', [InvtWarehouseController::class, 'processEditWarehouse'])->name('process-edit-warehouse');
Route::get('/warehouse/delete-warehouse/{warehouse_id}', [InvtWarehouseController::class, 'deleteWarehouse'])->name('delete-warehouse');


Route::get('/purchase-return', [PurchaseReturnController::class, 'index'])->name('purchase-return');
Route::get('/purchase-return/add', [PurchaseReturnController::class, 'addPurchaseReturn'])->name('add-purchase-return');
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

Route::get('/purchase-return-report',[PurchaseReturnReportController::class, 'index'])->name('purchase-return-report');
Route::post('/purchase-return-report/filter',[PurchaseReturnReportController::class, 'filterPurchaseReturnReport'])->name('filter-purchase-return-report');
Route::get('/purchase-return-report/filter-reset',[PurchaseReturnReportController::class, 'filterResetPurchaseReturnReport'])->name('filter-reset-purchase-return-report');
Route::get('/purchase-return-report/print',[PurchaseReturnReportController::class, 'printPurchaseReturnReport'])->name('print-purchase-return-report');
Route::get('/purchase-return-report/export',[PurchaseReturnReportController::class, 'exportPurchaseReturnReport'])->name('export-purchase-return-report');

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
 // Building pages
 Route::prefix('room')->name('room.')->group(function () {
    Route::get('/', [CoreRoomController::class, 'index'])->name('index');
    Route::get('/add', [CoreRoomController::class, 'add'])->name('add');
    Route::post('/process-add', [CoreRoomController::class, 'processAdd'])->name('process-add');
    Route::get('/edit/{room_id}', [CoreRoomController::class, 'edit'])->name('edit');
    Route::post('/process-edit', [CoreRoomController::class, 'processEdit'])->name('process-edit');
    Route::get('/delete/{room_id}', [CoreRoomController::class, 'delete'])->name('delete');
    Route::post('/elements-add', [CoreRoomController::class, 'elementsAdd'])->name('elements-add');
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
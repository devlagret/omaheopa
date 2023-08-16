<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=> ['auth:sanctum']], function(){
    Route::post('/logout', [APIController::class, 'logout']);
    Route::post('/profile', [APIController::class, 'userProfile']);
    Route::post('/change-password', [APIController::class, 'changePassword']);

    Route::post('/printer-address', [APIController::class, 'printerAddress']);
    Route::post('/printer-address/update', [APIController::class, 'updatePrinterAddress']);
    
    Route::post('/capital-money', [APIController::class, 'insertCapitalMoney']);
    
    Route::post('/expenditure', [APIController::class, 'insertExpenditure']);
    Route::post('/expenditure-today', [APIController::class, 'getExpenditure']);
    Route::post('/expenditure/print', [APIController::class, 'getExpenditurePrintData']);
    
    Route::post('/item-category', [APIController::class, 'getInvtItemCategory']);
    Route::post('/item-category/add', [APIController::class, 'insertInvtItemCategory']);
    Route::post('/item', [APIController::class, 'getInvtItem']);
    Route::post('/item/add', [APIController::class, 'insertInvtItem']);
    Route::post('/item/update', [APIController::class, 'updateInvtItem']);
    Route::post('/item-unit', [APIController::class, 'getInvtItemUnit']);
    Route::post('/item-unit/add', [APIController::class, 'insertInvtItemUnit']);
    Route::post('/item-detail', [APIController::class, 'getInvtItemDetail']);
    
    Route::post('/sales/add', [APIController::class, 'insertSalesInvoice']);
    Route::post('/sales/save', [APIController::class, 'insertSaveSalesInvoice']);
    Route::post('/sales/print', [APIController::class, 'getSalesPrintData']);
    
    Route::post('/sales-list-today/unpaid', [APIController::class, 'getUnpaidSalesListToday']);
    Route::post('/sales-list-today/paid', [APIController::class, 'getPaidSalesListToday']);
    Route::post('/sales-list-today/paid/menu', [APIController::class, 'getPaidSalesListMenuToday']);
    
    Route::post('/sales/saved', [APIController::class, 'getSavedSalesOrder']);
    Route::post('/sales/saved/pay', [APIController::class, 'paySavedSalesOrder']);

    Route::post('/dashboard', [APIController::class, 'getDashboard']);
    Route::post('/dashboard/print', [APIController::class, 'getDashboardPrintData']);

    Route::post('/login-state', [APIController::class, 'getLoginState']);
});

Route::post('/login', [APIController::class, 'login']); 
Route::get('/register-guest', [APIController::class, 'registerGuest']); 
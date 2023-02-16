<?php

use Illuminate\Support\Facades\Route;
use Modules\Repay\Http\Controllers\LoanRepaymentController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('loan')->middleware(['is.customer'])->group(function () {
        Route::post('/{id}/repayments', [LoanRepaymentController::class, 'update'])->name('customer.loan.repay');
    });
});

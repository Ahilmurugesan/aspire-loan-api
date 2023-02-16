<?php

use Illuminate\Support\Facades\Route;
use Modules\Proposal\Http\Controllers\LoanRequestController;

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
        Route::post('/create', [LoanRequestController::class, 'store'])->name('customer.loan.create');
    });
});

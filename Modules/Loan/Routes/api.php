<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Loan\Http\Controllers\LoanController;

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
    Route::prefix('loan')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('loans');
        Route::get('/{loan}', [LoanController::class, 'show'])->name('loan.show');

        Route::prefix('admin')->middleware(['is.admin'])->group(function () {
            Route::post('/approval/{id}', [LoanController::class, 'update'])->name('admin.loanApproval');
        });
    });
});

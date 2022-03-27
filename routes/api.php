<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\EmployeeCategoryController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientCategoryController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaymentController;
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

Route::prefix('/users')->group(function(){
    Route::post('/store', [UserController::class, 'store']);
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/{id}/show', [UserController::class,'show']);
    Route::post('/update', [UserController::class,'update']);
    Route::post('/{id}/delete', [UserController::class,'destroy']);
});

Route::prefix('/currencies')->group(function(){
    Route::post('/store', [CurrencyController::class, 'store']);
    Route::get('/all', [CurrencyController::class, 'all']);
    Route::get('/{id}/show', [CurrencyController::class,'show']);
    Route::post('/update', [CurrencyController::class,'update']);
    Route::post('/{id}/delete', [CurrencyController::class,'destroy']);
});

Route::prefix('/employee_categories')->group(function(){
    Route::post('/store', [EmployeeCategoryController::class, 'store']);
    Route::get('/all', [EmployeeCategoryController::class, 'all']);
    Route::get('/{id}/show', [EmployeeCategoryController::class,'show']);
    Route::post('/update', [EmployeeCategoryController::class,'update']);
    Route::post('/{id}/delete', [EmployeeCategoryController::class,'destroy']);
});

Route::prefix('/employees')->group(function(){
    Route::post('/store', [EmployeeController::class, 'store']);
    Route::get('/all', [EmployeeController::class, 'all']);
    Route::get('/{id}/show', [EmployeeController::class,'show']);
    Route::post('/{id}/detail', [EmployeeController::class,'detail']);
    Route::post('/update', [EmployeeController::class,'update']);
    Route::post('/{id}/delete', [EmployeeController::class,'destroy']);
    Route::post('/search', [EmployeeController::class, 'search']);
});

Route::prefix('/clients-categories')->group(function(){
    Route::post('/store', [ClientCategoryController::class, 'store']);
    Route::get('/all', [ClientCategoryController::class, 'all']);
    Route::get('/{id}/show', [ClientCategoryController::class,'show']);
    Route::post('/update', [ClientCategoryController::class,'update']);
    Route::post('/{id}/delete', [ClientCategoryController::class,'destroy']);
});

Route::prefix('/taxes')->group(function(){
    Route::post('/store', [TaxController::class, 'store']);
    Route::get('/all', [TaxController::class, 'all']);
    Route::get('/{id}/show', [TaxController::class,'show']);
    Route::post('/update', [TaxController::class,'update']);
    Route::post('/{id}/delete', [TaxController::class,'destroy']);
});

Route::prefix('/clients')->group(function(){
    Route::post('/store', [ClientController::class, 'store']);
    Route::get('/all', [ClientController::class, 'all']);
    Route::get('/{id}/show', [ClientController::class,'show']);
    Route::post('/{id}/detail', [ClientController::class,'detail']);
    Route::post('/update', [ClientController::class,'update']);
    Route::post('/{id}/delete', [ClientController::class,'destroy']);
    Route::post('/search', [ClientController::class,'search']);
});

Route::prefix('/contracts')->group(function(){
    Route::post('/store', [ContractController::class, 'store']);
    Route::post('/all', [ContractController::class, 'all']);
    Route::get('/{id}/show', [ContractController::class,'show']);
    Route::post('/update', [ContractController::class,'update']);
    Route::post('/{id}/delete', [ContractController::class,'destroy']);
    Route::post('/{id}/detail', [ContractController::class,'detail']);
    Route::post('/search', [ContractController::class,'search']);
    Route::post('/doughnut', [ContractController::class,'doughnut']);
    Route::post('/{id}/attendance', [ContractController::class,'attendance']);
});

Route::prefix('/payments')->group(function(){
    Route::post('/payment', [PaymentController::class, 'payment']);
});

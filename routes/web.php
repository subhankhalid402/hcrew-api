<?php

use Illuminate\Support\Facades\Route;

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
Route::prefix('/incomes')->group(function () {
    Route::get('/income-report', 'App\Http\Controllers\IncomeController@incomeFilterReport');
});

Route::prefix('/expenses')->group(function () {
    Route::get('/expense-report', 'App\Http\Controllers\ExpenseController@expenseFilterReport');
});

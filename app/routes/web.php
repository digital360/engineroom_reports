<?php

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

Route::get('/reports/test', 'ReportsController@test');

Route::get('/reports/make-business-plan', 'ReportsController@makeBusinessPlan');
Route::get('/reports/business-plan/{key}/{page?}', 'ReportsController@businessPlan');

Route::get('/reports/{reportId}/export', 'ReportsController@pdf');
Route::get('/reports/{reportId}/{page?}', 'ReportsController@report');

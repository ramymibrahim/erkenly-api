<?php

use Illuminate\Http\Request;

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

Route::post('/register/company','Auth\RegisterController@registerCompany');
Route::post('/register/client','Auth\RegisterController@registerClient');
Route::post('login', 'Auth\LoginController@login');

Route::group(['middleware' => 'jwt.auth:client'], function () {
    Route::get('/branches','BranchController@index');    
});

Route::group(['middleware' => 'jwt.auth:company'], function () {
    Route::get('/companies','CompanyController@index');
    Route::put('/change-capacity','CompanyController@changeCapacity');
    Route::put('/change-price','CompanyController@changePrice');
    Route::put('/change-hours','CompanyController@changeHours');
    Route::post('/add-branch','CompanyController@addBranch');    
});
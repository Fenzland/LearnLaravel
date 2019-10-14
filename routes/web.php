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

Route::get('/', function () { return view('welcome'); });
Route::get('/from-request-to-response', function () { return view('from-request-to-response'); });
Route::get('/first-guide', function () { return view('first-guide'); });
Route::get('/ioc-container', function () { return view('ioc-container'); });

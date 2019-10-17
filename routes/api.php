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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('update-deputados', 'Api\DeputadoController@update')->name('update.deputados');

Route::get('update-verbas-indenizatorias', 'Api\VerbasIndenizatoriasController@update')->name('update.verbas.ndenizatorias');

Route::get('update', 'Api\TesteController@update');

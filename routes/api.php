<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::resource('patient', PatientController::class);
Route::resource('diagnostic', DiagnosticController::class);

Route::post('assignment', 'PatientController@assignment');
Route::get('getPatients', 'PatientController@getPatients');
Route::get('searchPatients', 'PatientController@searchPatients');
Route::get('getTopFiveDiagnostics', 'PatientController@getTopFiveDiagnostics');
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdafruitController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\controlcontroller;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\SensorController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    
}); 




Route::get('control/{id}', [controlcontroller::class, 'index'])->name('activacion')->middleware('signed');


//CREACION DE SENSORES

Route::post('/sensor',[SensorController::class,'agregarsensor'])->middleware('auth.jwt');

//CREACION DE MONITORES

Route::post('/monitor',[MonitorController::class,'crearm_s'])->middleware('auth.jwt');

//ELEGIR SENSORES

Route::get('/monitor/{id}',[MonitorController::class,'elegir_sensores'])->middleware('auth.jwt');

//REENVIO DE ACTIVACION DE LA CUENTA


//REESTABLECIMIENTO DE CONTRASEÑA


//CONTROL DE ADAFRUIT
Route::post('/adafruit/mandar', [AdafruitController::class, 'crear'])->middleware('auth.jwt');
Route::get('/adafruit/{id}', [AdafruitController::class, 'obtener'])->middleware('auth.jwt');


//CONTROL DE ADAFRUIT


//ACCTUALIZAR DATOS DEL USUARIO

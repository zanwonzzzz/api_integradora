<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdafruitController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\controlcontroller;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\CronJobController; 

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
    Route::get('me', [AuthController::class, 'me']);
    
}); 



//CONTROL DE ACTIVACION DE LA CUENTA POR CORREO
Route::get('control/{id}', [controlcontroller::class, 'index'])->name('activacion')->middleware('signed');

//SENSORES 
Route::post('/sensor/agregar',[SensorController::class,'agregarsensor'])->middleware('auth.jwt');
Route::get('/sensores',[SensorController::class,'obtenersensores'])->middleware('auth.jwt');
Route::delete('/sensor/eliminar/{id}',[SensorController::class,'eliminarsensor'])->middleware('auth.jwt');

//MONITORES
Route::post('/monitor',[MonitorController::class,'crearm_s'])->middleware('auth.jwt');
Route::get('/monitores',[MonitorController::class,'monitor_usuario'])->middleware('auth.jwt');
Route::delete('/monitor/{id}',[MonitorController::class,'borrarmonitor'])->middleware('auth.jwt');

//MONITORES Y SUS SENSORES

Route::get('/sensor/{idmonitor}/{id}',[MonitorController::class,'elegir_sensores'])->middleware('auth.jwt');
Route::delete('/sensores/{idmonitor}/',[MonitorController::class,'eliminar_sensores'])->middleware('auth.jwt');
Route::get('/sensores/{id}',[MonitorController::class,'SensoresMonitor'])->middleware('auth.jwt');

//REENVIO DE ACTIVACION DE LA CUENTA
Route::post('reenvio/', [controlcontroller::class, 'reenvio'])->name('reenvio');

//REESTABLECIMIENTO DE CONTRASEÑA


//CONTROL DE ADAFRUIT
Route::post('/adafruit/mandar', [AdafruitController::class, 'crear'])->middleware('auth.jwt');
Route::get('/adafruit/{id}', [AdafruitController::class, 'obtener'])->middleware('auth.jwt');

//ULTIMOS 10 DATOS
Route::get('/envivo/{idmonitor}', [AdafruitController::class, 'Envivo'])->middleware('auth.jwt');

//DATOS EN VIVO LAST DATA
Route::get('/ultimo/{idmonitor}', [AdafruitController::class, 'UltimoDato'])->middleware('auth.jwt');

//ACTUALIZAR DATOS DEL USUARIO
Route::put('/actualizar/usuario', [AuthController::class, 'ActualizarUsuario'])->middleware('auth.jwt');

//APAGAR BOCINA
Route::post('/apagar/bocina/', [AdafruitController::class, 'ApagarBocina'])->middleware('auth.jwt');

//MANDAR LOS SENSORES DE UN MONITOR A ADAFRUIT
Route::get('/adafruit/mandar/sensores/', [AdafruitController::class, 'AdafruitSensor'])->middleware('auth.jwt');

//FOTO DE PERFIL DEL USUARIO
Route::post('/foto', [ImagenController::class, 'SubirFoto'])->middleware('auth.jwt');
Route::get('/foto', [ImagenController::class, 'MostrarFoto'])->middleware('auth.jwt');


//PROMEDIO
Route::get('/promedio/{idsensor}', [AdafruitController::class, 'Promedio'])->middleware('auth.jwt');
//PROMEDIO POR HORA
Route::get('/hora/{idsensor}/{fechalimite}', [AdafruitController::class, 'PromedioPorHora'])->middleware('auth.jwt');

//SUBIR FOTO DEL BEBE
Route::post('/fotobebe', [ImagenController::class, 'Fotobebe'])->middleware('auth.jwt'); 

//RECUPERAR FOTO DEL ESTADO DEL BEBE
Route::get('/fotobebe/{idestado}', [ImagenController::class, 'RecuperarEstadoBebe'])->middleware('auth.jwt'); 

//GUARDAR DATOS DE ADAFRUIT EN LA BASE DE DATOS
Route::get('/adafruit/guardar/datos', [AdafruitController::class, 'CronJobParaPromedio'])->middleware('auth.jwt');

//CONSULTAR ESTADO DE LA BOCINA
Route::get('/bocina/estado', [AdafruitController::class, 'ConsultarEstadoBocina'])->middleware('auth.jwt');

//LOGOUT
Route::get('/logout', [AuthController::class, 'SalidaUsuario'])->middleware('auth.jwt');





























//PROBAR CRONJOBS
Route::get('/cronjobs', [CronJobController::class, 'CronJobParaGuardarDatos'])->middleware('auth.jwt');
Route::get('/cronjobs2', [CronJobController::class, 'CronJobParaDatosNuevos'])->middleware('auth.jwt');


//BORRAR SENSORES
Route::get('/sensorsitos/{id}',[AdafruitController::class,'BorrarSensores'])->middleware('auth.jwt');
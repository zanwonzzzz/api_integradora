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
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\SendToMongoDataController;
use App\Http\Controllers\AdminController;
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
Route::get('/monitores/{idmonitor}',[MonitorController::class,'monitorPorId'])->middleware('auth.jwt');
Route::delete('/monitor/{id}',[MonitorController::class,'borrarmonitor'])->middleware('auth.jwt');
Route::put('/monitor/{idmonitor}',[MonitorController::class,'actualizarmonitor'])->middleware('auth.jwt');
//MONITORES Y SUS SENSORES

Route::get('/sensor/{idmonitor}/{id}',[MonitorController::class,'elegir_sensores'])->middleware('auth.jwt'); //EN REPARACION
Route::delete('/sensores/{idmonitor}/{idsensor}',[MonitorController::class,'eliminar_sensores'])->middleware('auth.jwt');
Route::get('/sensores/{id}',[MonitorController::class,'SensoresMonitor'])->middleware('auth.jwt');

//MANDAR SENSORES QUE ELGIO A ADAFRUIT
Route::get('/sensor/ada',[AdafruitController::class,'SensorAda'])->middleware('auth.jwt');//DUDA

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


//PROMEDIO DE ADAFRUIT
Route::get('/promedio/adafruit/{idsensor}', [AdafruitController::class, 'Promedio'])->middleware('auth.jwt');
//PROMEDIO POR HORA DE ADAFRUIT
Route::get('/hora/adafruit/{idsensor}/{fechalimite}', [AdafruitController::class, 'PromedioPorHora'])->middleware('auth.jwt');

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




//PROMEDIOS PERO CON DATOS EN LA BD
Route::get('/promedio/{idsensor}', [AdafruitController::class, 'Promediobd'])->middleware('auth.jwt');

//PROMEDIOS POR HORA PERO CON DATOS EN LA BD
Route::get('/hora/{idsensor}/{fechalimite}', [AdafruitController::class, 'promediobdporhora'])->middleware('auth.jwt');
















// Route::post('/prueba', [gaelcontroller::class, 'obtenerdatosporrequest']);







//PROBAR CRONJOBS
Route::get('/cronjobs', [CronJobController::class, 'CronJobParaGuardarDatos'])->middleware('auth.jwt');
Route::get('/cronjobs2', [CronJobController::class, 'CronJobParaDatosNuevos'])->middleware('auth.jwt');


//BORRAR SENSORES
Route::get('/sensorsitos/{id}',[AdafruitController::class,'BorrarSensores'])->middleware('auth.jwt');

//DATA SENSORES 
Route::get('/sensor-data/{id}', [SensorDataController::class, 'index'])->middleware('auth.jwt');
Route::post('/sensor-data', [SensorDataController::class, 'store']);

//DATOS DEL MONITOR A MONGO
Route::post('/datos-mongo',[MonitorController::class,'MonitorAMongo']);



//CONSULTAS DEL ADMIN
Route::get('/todos', [AdminController::class, 'UsuariosTodos'])->middleware('auth.jwt');
Route::get('/activos', [AdminController::class, 'UsuariosActivos'])->middleware('auth.jwt');
Route::get('/inactivos', [AdminController::class, 'UsuariosInactivos'])->middleware('auth.jwt');
Route::get('/desactivar/{id}', [AdminController::class, 'DesactivarCuenta'])->middleware('auth.jwt');
Route::get('/activar/{id}', [AdminController::class, 'ActivarCuenta'])->middleware('auth.jwt');
Route::get('/monitores/eliminados', [AdminController::class, 'MonitoresEliminados'])->middleware('auth.jwt');
Route::get('/monitores/activos', [AdminController::class, 'MonitoresActivos'])->middleware('auth.jwt');
Route::get('/monitores/menos/activos/', [AdminController::class, 'MonitoresMenosActivos'])->middleware('auth.jwt');
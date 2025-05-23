<?php

use Aws\Middleware;
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
use App\Http\Controllers\gaelcontroller;
use App\Http\Controllers\BocinasController;
use App\Http\Middleware\Roles;
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
//Route::get('control/{id}', [controlcontroller::class, 'index'])->name('activacion')->middleware('signed');

//ACTIVACION DE LA CUENTA POR CODIGO
/* Route::post('/verificar/{id}', [controlcontroller::class, 'CodigoVerificacion'])->name('verificar');
Route::get('/activacion/{id}', [controlcontroller::class, 'VistaVerificacion'])->name('activacion')->middleware('signed'); */

//Olvidar Contraseña
Route::post('/forgot-password', [controlcontroller::class, 'OlvidarContraseña']);

//VistaResetear Contraseña
Route::get('/reset-password/{token}', function ($token) {
    return view('ResetearContraseña', ['token' => $token]);
})->name('password.reset')->middleware('signed'); 


//RESETEAR CONTRASEÑA
Route::post('/reset-password', [controlcontroller::class, 'ResetarContraseña'])->name('password.update');

//REENVIO DE ACTIVACION DE LA CUENTA
Route::post('reenvio/', [controlcontroller::class, 'reenvio'])->name('reenvio');


Route::group([
    'middleware' => ['auth.jwt','roles']
], function ($router) {

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

Route::post('/sensor/{idmonitor}',[MonitorController::class,'elegirSensores'])->middleware('auth.jwt'); //EN REPARACION
Route::delete('/sensores/{idmonitor}/{idsensor}',[MonitorController::class,'eliminar_sensores'])->middleware('auth.jwt');
Route::get('/sensores/{id}',[MonitorController::class,'SensoresMonitor'])->middleware('auth.jwt');

//MANDAR SENSORES QUE ELGIO A ADAFRUIT
Route::get('/sensor/ada',[AdafruitController::class,'SensorAda'])->middleware('auth.jwt');//DUDA

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

//PROBAR CRONJOBS
Route::get('/cronjobs', [CronJobController::class, 'CronJobParaGuardarDatos'])->middleware('auth.jwt');
Route::get('/cronjobs2', [CronJobController::class, 'CronJobParaDatosNuevos'])->middleware('auth.jwt');


//BORRAR SENSORES
Route::get('/sensorsitos/{id}',[AdafruitController::class,'BorrarSensores'])->middleware('auth.jwt');

//DATA SENSORES 
Route::get('/sensor-data/{id}', [SensorDataController::class, 'index'])->middleware('auth.jwt');

//DATOS DEL MONITOR A MONGO
Route::get('/datos-mongo/{id}',[MonitorController::class,'MonitorAMongo'])->middleware('auth.jwt');

Route::post('/auditoria',[SendToMongoDataController::class,'Auditorias'])->middleware('auth.jwt');
Route::get('/auditoria',[SendToMongoDataController::class,'ConsultaAuditorias'])->middleware('auth.jwt');


//CONSULTAS DEL ADMIN
Route::get('/todos', [AdminController::class, 'UsuariosTodos'])->middleware('auth.jwt');
Route::get('/activos', [AdminController::class, 'UsuariosActivos'])->middleware('auth.jwt');
Route::get('/inactivos', [AdminController::class, 'UsuariosInactivos'])->middleware('auth.jwt');
Route::get('/desactivar/{id}', [AdminController::class, 'DesactivarCuenta'])->middleware('auth.jwt');
Route::get('/activar/{id}', [AdminController::class, 'ActivarCuenta'])->middleware('auth.jwt');

Route::get('/monitor/elimado', [AdminController::class, 'MonitoresEliminados'])->middleware('auth.jwt');
Route::get('/monitores/activos', [AdminController::class, 'MonitoresActivos'])->middleware('auth.jwt');
Route::get('/monitores/menos/activos/', [AdminController::class, 'MonitoresMenosActivos'])->middleware('auth.jwt');
Route::get('/monitor/actividad',([AdminController::class,'MonitoresConMasActividad']))->middleware('auth.jwt');
Route::get('/monitor/promedio',([AdminController::class,'MonitoresConPromedio']))->middleware('auth.jwt');

//REPORTES CON MONGO
Route::get('/promedio-mongo/{idmonitor}/{idsensor}/', [AdafruitController::class, 'PromedioPorDiaMongo'])->middleware('auth.jwt');
Route::get('/promedio-hora/{idmonitor}/{idsensor}/{fechalimite}', [AdafruitController::class, 'PromedioPorHoraMongo'])->middleware('auth.jwt');

//DATTOS DE TODOS LOS MONITORES DEL USUARIO A MONGO
Route::get('/mongo',[MonitorController::class,'monitorUsuarioMongo'])->middleware('auth.jwt');
Route::get('/mongo/{id}',[MonitorController::class,'sensoresDelMonitorUsuario'])->middleware('auth.jwt');
});


Route::post('/prueba', [gaelcontroller::class, 'obtenerdatosporrequest']);

Route::post('/sensor-data', [SensorDataController::class, 'store']);

Route::post('/data-sensor', [SensorDataController::class, 'obtenerDataSensor']);
//AQUI APLIQUE LOS CAMBIOS PARA LA BOCINA
Route::prefix('bocina')->group(function () {
    Route::get('/estado', [BocinasController::class, 'obtenerEstado']);
    
    Route::post('/estado', [BocinasController::class, 'cambiarEstado']);
});

Route::post( '/ultimovalor', [gaelcontroller::class, 'obtener_ultimo_valor']);


Route::get('total', [AuthController::class, 'totalusuarios']);

Route::get('totalmonitores', [AuthController::class, 'totalmonitores']);

Route::get('todomonitores', [AdminController::class, 'todosMonitores']);
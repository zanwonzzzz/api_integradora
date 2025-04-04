<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\controlcontroller;

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

Route::get('/ExitoContrasena', function () {
    return view('ExitoContrasena');
})->name('exito');

//ruta del login de angular
Route::get('/log', function () {
    return redirect('https://babysave.online/login');
})->name('Inicio');

Route::post('/verificar/{id}', [controlcontroller::class, 'CodigoVerificacion'])->name('verificar');
Route::get('/activacion/{id}', [controlcontroller::class, 'VistaVerificacion'])->name('activacion')->middleware('signed');

//CONTRASEÑA

/* Route::get('/reset-password/{token}', function ($token) {
    return view('ResetearContraseña', ['token' => $token]);
})->name('password.reset');

Route::post('/reset-password', [controlcontroller::class, 'ResetarContraseña'])->name('password.update'); */
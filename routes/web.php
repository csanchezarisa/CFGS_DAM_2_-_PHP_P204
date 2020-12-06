<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controlador;

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

Route::get('/', [Controlador::class, 'landingPage']);

Route::get('/login', [Controlador::class, 'loginPage']);

Route::get('/usuari', [Controlador::class, 'mostrarDadesUsuari']);

Route::post('/login-user', [Controlador::class, 'loginUser']);

Route::post('/signup-user', [Controlador::class, 'signUpUser']);

Route::post('/update-userdata', [Controlador::class, 'actualitzarDades']);

Route::get('/del-user', [Controlador::class, 'eliminarUsuari']);

Route::get('/logout', [Controlador::class, 'logout']);
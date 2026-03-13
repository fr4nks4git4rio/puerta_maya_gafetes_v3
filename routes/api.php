<?php

use App\Http\Controllers\API\SeguridadController;
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
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/check_gafete/{gafete}', [SeguridadController::class, 'checkGafete']);

Route::get('/dar_entrada_gafete/{puerta}/{gafete}', [SeguridadController::class, 'darEntradaGafete']);
Route::get('/dar_salida_gafete/{puerta}/{gafete}', [SeguridadController::class, 'darSalidaGafete']);

Route::get('/load_puertas_virtuales_entrada', [SeguridadController::class, 'loadPuertasVirtualesEntrada']);
Route::get('/load_puertas_virtuales_salida', [SeguridadController::class, 'loadPuertasVirtualesSalida']);
Route::get('/load_variables_globales', [SeguridadController::class, 'loadVariablesGlobales']);

Route::post('/recibir_evento_stream', [SeguridadController::class, 'recibirEventoStream']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::resource('api/estudiantes', EstudianteController::class);
Route::resource('api/cursos', CursoController::class);
Route::resource('api/materias', MateriaController::class);
Route::resource('api/notas', NotaController::class);
// Api
Route::get('/apiendpoint', [ApiController::class, 'getMostUsedLetter']);

// Ruta de "catch-all" para Vue Router
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

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


// API

// Lecciones
Route::get('lecciones', 'App\Http\Controllers\LessonController@getLecciones');
// Seleccionar todo: http://127.0.0.1:8000/api/lecciones
Route::get('lecturas', 'App\Http\Controllers\LessonController@getLecturas');
// Seleccionar todo: http://127.0.0.1:8000/api/lecturas
Route::get('lecciones/{id_categoria}', 'App\Http\Controllers\LessonController@getLeccionesCat');
// Seleccionar lecciones de categoria por ID: http://127.0.0.1:8000/api/lecciones/1
Route::get('lecciones/{slug_cat}/{slug}', 'App\Http\Controllers\LessonController@getLeccionesCatSlug');
// Seleccionar leccion por slug: http://127.0.0.1:8000/api/lecciones/ib/cuerpo
Route::get('contenido/{slug_cat}/{slug}', 'App\Http\Controllers\LessonController@getLeccionContenido');
// Seleccionar contenido por slug: http://127.0.0.1:8000/api/lecciones/ib/cuerpo
Route::get('audio/{audio}', 'App\Http\Controllers\LessonController@getAudio');

// Categorias
Route::get('categorias', 'App\Http\Controllers\CategoriaController@getCategorias');
// Seleccionar todo: http://127.0.0.1:8000/api/categorias
Route::get('categoria/{slug}', 'App\Http\Controllers\CategoriaController@getCategoriasSlug');
// Seleccionar categoria y lecciones de categoria por slug: http://127.0.0.1:8000/api/categoria/fr
Route::get('catcontenido/{slug}', 'App\Http\Controllers\CategoriaController@getContenidoCat');
// Seleccionar contenido de categoria por slug: http://127.0.0.1:8000/api/catcontenido/fr

// Bloques de Inicio
Route::get('inicio', 'App\Http\Controllers\LessonController@getInicio');
// Seleccionar todos los bloques de inicio con lecciones: http://127.0.0.1:8000/api/inicio

// Páginas
Route::get('pagina/{slug}', 'App\Http\Controllers\LessonController@getPagina');
// Seleccionar todas las páginas con lecciones: http://127.0.0.1:8000/api/pagina/lectura_de_hoy
Route::get('pagina/lecciones/{slug_pag}', 'App\Http\Controllers\LessonController@getLeccionPagina');
// Seleccionar todas las páginas con lecciones: http://127.0.0.1:8000/api/pagina/lecciones/lecturas
Route::get('paginas', 'App\Http\Controllers\PaginaController@getPaginas');
// Seleccionar todas las páginas con lecciones: http://127.0.0.1:8000/api/paginas
Route::get('paginas/detalles', 'App\Http\Controllers\LessonController@getPaginasDetalles');
// Seleccionar todas las páginas con lecciones: http://127.0.0.1:8000/api/paginas/detalles

// Palabras
Route::get('palabras', 'App\Http\Controllers\DiariopalabraController@getPalabras');
// Seleccionar todo: http://127.0.0.1:8000/api/palabras

// Buscar
Route::get('buscar', 'App\Http\Controllers\LessonController@buscar');
// Retorna todos los titulos de las lecciones: http://127.0.0.1:8000/api/buscar
Route::get('buscar/{termino}', 'App\Http\Controllers\LessonController@buscarTermino');
// Buscar por temino especifico de lecciones: http://127.0.0.1:8000/api/buscar/formas de ...

// Cursos
Route::get('cursos', 'App\Http\Controllers\LessonController@getCursos');
// Selecciona todos los cursos: http://127.0.0.1:8000/api/cursos

// Material
Route::get('material', 'App\Http\Controllers\LessonController@getMaterial');
// Selecciona todos los materiales: http://127.0.0.1:8000/api/material

// Descargar pdf
Route::get('pdf/{slug}', 'App\Http\Controllers\LessonController@getPDF');

// Guardar Token
Route::post('token', 'App\Http\Controllers\PushuserController@saveToken');

// Elimina el imei del usuario
Route::delete('delete-pushuser', 'App\Http\Controllers\PushuserController@deletePushUser');

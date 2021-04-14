<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::middleware(['auth'])->group(function () {

    Route::get('/', 'App\Http\Controllers\ToDoController@index');

    Route::post('/store', 'App\Http\Controllers\ToDoController@store')->name('store');

    Route::get('/edit/{id}', 'App\Http\Controllers\ToDoController@edit')->name('edit');

    Route::post('/update/{id}', 'App\Http\Controllers\ToDoController@update')->name('update');

    Route::get('/delete/{id}', 'App\Http\Controllers\ToDoController@delete')->name('delete');

    Route::get('/updateStatus/{id}', 'App\Http\Controllers\ToDoController@updateStatus')->name('updateStatus');

    Route::post('/sendInvitation', 'App\Http\Controllers\ToDoController@sendInvitation')->name('sendInvitation');

    Route::get('/acceptInvitation/{id}', 'App\Http\Controllers\ToDoController@acceptInvitation')->name('acceptInvitation');

    Route::get('/denyInvitation/{id}', 'App\Http\Controllers\ToDoController@denyInvitation')->name('denyInvitation');

    Route::get('/deleteWorker/{id}', 'App\Http\Controllers\ToDoController@deleteWorker')->name('deleteWorker');


});

//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

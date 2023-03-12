<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', [MainController::class, 'dashboard']);
Route::get('/jadwal-panen', [MainController::class, 'harvestSchedule']);
Route::get('/reminder', [MainController::class, 'reminder']);
Route::get('/manual-reminder', [MainController::class, 'manualReminder']);
Route::get('/upload', [MainController::class, 'showUploadForm']);
Route::post('/upload', [MainController::class, 'uploadSchedule']);
Route::get('/check-upload/{year}/{subround}', [MainController::class, 'checkUpload']);
Route::post('/template', [MainController::class, 'generateTemplate']);
Route::get('/petugas', [UserController::class, 'index']);
Route::get('/pengaturan', [MainController::class, 'settings']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php

use App\Http\Controllers\HarvestScheduleController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SentMessageController;
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

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['role:Admin']], function () {
        Route::get('/', [MainController::class, 'dashboard']);
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/users/data', [UserController::class, 'getData']);
        Route::resource('users', UserController::class);

        Route::get('/jadwal-panen', [HarvestScheduleController::class, 'harvestSchedule']);
        Route::post('/jadwal-panen', [HarvestScheduleController::class, 'storeHarvestSchedule']);
        Route::put('/jadwal-panen/{id}', [HarvestScheduleController::class, 'updateHarvestSchedule']);
        Route::get('/jadwal-panen/{id}/edit', [HarvestScheduleController::class, 'editHarvestSchedule']);
        Route::get('/jadwal-panen/create', [HarvestScheduleController::class, 'createHarvestSchedule']);
        Route::delete('/jadwal-panen/{id}', [HarvestScheduleController::class, 'deleteHarvestSchedule']);
        Route::get('/jadwal-panen/data/{subround?}', [HarvestScheduleController::class, 'getScheduleData']);
        Route::post('/jadwal-panen/data', [HarvestScheduleController::class, 'downloadSchedule']);
        Route::get('/jadwal-panen/village/{id}', [HarvestScheduleController::class, 'getVillage']);
        Route::get('/jadwal-panen/bs/{id}', [HarvestScheduleController::class, 'getBs']);

        Route::get('/upload', [HarvestScheduleController::class, 'showUploadForm']);
        Route::post('/upload', [HarvestScheduleController::class, 'uploadSchedule']);
        Route::get('/check-upload/{year}/{subround}', [HarvestScheduleController::class, 'checkUpload']);
        Route::post('/template', [HarvestScheduleController::class, 'generateTemplate']);

        Route::get('/reminder', [MainController::class, 'reminder']);
        Route::get('/manual-reminder', [MainController::class, 'manualReminder']);
        Route::get('/petugas', [UserController::class, 'index']);
        Route::get('/pengaturan', [MainController::class, 'settings']);

        Route::get('/sent-messages/data', [SentMessageController::class, 'getData']);
        Route::get('/sent-messages', [SentMessageController::class, 'index']);
    });
});

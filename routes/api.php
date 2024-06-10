<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\KewarganegaraanController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\PersonalController;
use App\Http\Controllers\Requests\ResidentController;
use App\Http\Controllers\Requests\StatusPerkawinanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/get_token', [AuthController::class, 'get_token']);
});


Route::get('/agama/splp',[AgamaController::class, 'splp']);
Route::get('/gender/splp',[GenderController::class, 'splp']);

Route::resource('agama', AgamaController::class);
Route::resource('gender', GenderController::class);
Route::resource('pendidikan', PendidikanController::class);
Route::resource('pekerjaan', PekerjaanController::class);
Route::resource('status_kwn', StatusPerkawinanController::class);
Route::resource('kewarganegaraan', KewarganegaraanController::class);
Route::resource('personal', PersonalController::class);
Route::resource('resident', ResidentController::class)->middleware('auth:sanctum');

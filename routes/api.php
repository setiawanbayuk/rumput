<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\EsignController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\KewarganegaraanController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\PersonalController;
use App\Http\Controllers\Requests\RegionalController;
use App\Http\Controllers\Requests\ResidentController;
use App\Http\Controllers\Requests\StatusPerkawinanController;
use App\Http\Controllers\SuketController;
use App\Models\Regional;
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
Route::get('/esign/check/{nik}',[EsignController::class, 'check']);
Route::post('/esign/sign',[EsignController::class, 'sign']);
Route::get('/regional/kelurahan',[RegionalController::class, 'kelurahan'])->name('regional.kelurahan');
Route::get('/regional/kecamatan',[RegionalController::class, 'kecamatan'])->name('regional.kecamatan');
Route::middleware('auth:sanctum')->prefix('suket')->group(function(){
    Route::get('/', [SuketController::class, 'get']);
    Route::post('/', [SuketController::class, 'save']);
});
Route::middleware('auth:sanctum')->prefix('resident')->group(function(){
    Route::post('/simpan', [ResidentController::class, 'simpan']);
});

Route::resource('agama', AgamaController::class);
Route::resource('gender', GenderController::class);
Route::resource('pendidikan', PendidikanController::class);
Route::resource('pekerjaan', PekerjaanController::class);
Route::resource('status_kwn', StatusPerkawinanController::class);
Route::resource('kewarganegaraan', KewarganegaraanController::class);
Route::resource('personal', PersonalController::class);
Route::resource('regional', RegionalController::class);
Route::resource('resident', ResidentController::class)->middleware('auth:sanctum');
Route::resource('esign', EsignController::class);

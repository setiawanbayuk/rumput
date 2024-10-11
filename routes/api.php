<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\EsignController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\KabkoController;
use App\Http\Controllers\Requests\KecamatanController;
use App\Http\Controllers\Requests\KelurahanController;
use App\Http\Controllers\Requests\KewarganegaraanController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\PersonalController;
use App\Http\Controllers\Requests\ProvinsiController;
use App\Http\Controllers\Requests\RegionalController;
use App\Http\Controllers\Requests\ResidentController;
use App\Http\Controllers\Requests\SkpdController;
use App\Http\Controllers\Requests\StatusPerkawinanController;
use App\Http\Controllers\SkbnController;
use App\Http\Controllers\SkboroController;
use App\Http\Controllers\SkdomController;
use App\Http\Controllers\SkhslController;
use App\Http\Controllers\SktmController;
use App\Http\Controllers\SkusahaController;
use App\Http\Controllers\SuketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/login_sso', [AuthController::class, 'loginWithSSO']);
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
});

Route::get('/agama/splp',[AgamaController::class, 'splp']);
Route::get('/gender/splp',[GenderController::class, 'splp']);
Route::get('/provinsi/splp',[ProvinsiController::class, 'splp']);
Route::get('/kabko/splp',[KabkoController::class, 'splp']);
Route::get('/kecamatan/splp',[KecamatanController::class, 'splp']);
Route::get('/kelurahan/splp',[KelurahanController::class, 'splp']);
Route::get('/esign/check/{nik}',[EsignController::class, 'check']);
Route::post('/esign/sign',[EsignController::class, 'sign']);
Route::get('/regional/kelurahan',[RegionalController::class, 'kelurahan'])->name('regional.kelurahan');
Route::get('/regional/kecamatan',[RegionalController::class, 'kecamatan'])->name('regional.kecamatan');

Route::middleware('auth:sanctum')->prefix('suket')->group(function(){
    Route::get('/', [SuketController::class, 'get']);
    Route::post('/', [SuketController::class, 'save']);
});
Route::middleware('auth:sanctum')->prefix('skbn')->group(function(){
    Route::get('/', [SkbnController::class, 'get']);
    Route::post('/', [SkbnController::class, 'save']);
});
Route::middleware('auth:sanctum')->prefix('sktm')->group(function(){
    Route::get('/', [SktmController::class, 'get']);
    Route::post('/', [SktmController::class, 'save']);
});
Route::middleware('auth:sanctum')->prefix('skdom')->group(function(){
    Route::get('/', [SkdomController::class, 'get']);
    Route::post('/', [SkdomController::class, 'save']);
});

Route::middleware('auth:sanctum')->prefix('skhsl')->group(function(){
    Route::get('/', [SkhslController::class, 'get']);
    Route::post('/', [SkhslController::class, 'save']);
});

Route::middleware('auth:sanctum')->prefix('skusaha')->group(function(){
    Route::get('/', [SkusahaController::class, 'get']);
    Route::post('/', [SkusahaController::class, 'save']);
});

Route::middleware('auth:sanctum')->prefix('skboro')->group(function(){
    Route::get('/', [SkboroController::class, 'get']);
    Route::post('/', [SkboroController::class, 'save']);
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
Route::resource('provinsi', ProvinsiController::class);
Route::resource('kabko', KabkoController::class);
Route::resource('kecamatan', KecamatanController::class);
Route::resource('kelurahan', KelurahanController::class);
Route::resource('skpd', SkpdController::class);


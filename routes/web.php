<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\ResidentController;
use App\Http\Controllers\SkbnController;
use App\Http\Controllers\SkboroController;
use App\Http\Controllers\SkdomController;
use App\Http\Controllers\SkhslController;
use App\Http\Controllers\SkkelahiranController;
use App\Http\Controllers\SkkematianController;
use App\Http\Controllers\SktmController;
use App\Http\Controllers\SkusahaController;
use App\Http\Controllers\SuketController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home', ['title' => 'Dashboard']);
// });

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role_id == 2){
            return redirect()->route('warga');
        }
        else {
            return redirect()->route('home');
        }
    }
    return redirect()->route('login');
    // return view('welcome');
});
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth'])->name('home');
Route::get('/activity', [HomeController::class, 'activity'])->name('activity');
Route::get('/activity/last', [HomeController::class, 'last_activity'])->name('activity.last');

Route::middleware(['auth', 'role:1,3,4'])->prefix('skbn')->group(function(){
    Route::get('/', [SkbnController::class, 'index'])->name('skbn.index');
    Route::get('/add', [SkbnController::class, 'add'])->name('skbn.add');
    Route::post('/', [SkbnController::class, 'store'])->name('skbn.store');
    Route::get('/edit/{id}', [SkbnController::class, 'edit'])->name('skbn.edit');
    Route::post('/update/{id}', [SkbnController::class, 'update'])->name('skbn.update');
    Route::post('/naik/{id}', [SkbnController::class, 'naik'])->name('skbn.naik');
    Route::get('/preview/{id}', [SkbnController::class, 'preview'])->name('skbn.preview');
    Route::get('/cetak/{id}', [SkbnController::class, 'cetak'])->name('skbn.cetak');
    Route::post('/tolak/{id}', [SkbnController::class, 'tolak'])->name('skbn.tolak');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('suket')->group(function(){
    Route::get('/', [SuketController::class, 'index'])->name('suket.index');
    Route::get('/add', [SuketController::class, 'add'])->name('suket.add');
    Route::post('/', [SuketController::class, 'store'])->name('suket.store');
    Route::get('/edit/{id}', [SuketController::class, 'edit'])->name('suket.edit');
    Route::post('/update/{id}', [SuketController::class, 'update'])->name('suket.update');
    Route::post('/naik/{id}', [SuketController::class, 'naik'])->name('suket.naik');
    Route::get('/preview/{id}', [SuketController::class, 'preview'])->name('suket.preview') ;
    Route::get('/cetak/{id}', [SuketController::class, 'cetak'])->name('suket.cetak');
    Route::post('/tolak/{id}', [SuketController::class, 'tolak'])->name('suket.tolak');
});

Route::prefix('warga')->group(function(){
    Route::get('/', [HomeController::class, 'warga'])->middleware(['auth'])->name('warga');
    Route::get('/suket', [SuketController::class, 'warga'])->name('suket.warga');
    Route::post('/suket', [SuketController::class, 'save'])->name('suket.save');
    Route::get('/skusaha', [SkusahaController::class, 'warga'])->name('skusaha.warga');
    Route::post('/skusaha', [SkusahaController::class, 'save'])->name('skusaha.save');
    Route::get('/skhsl', [SkhslController::class, 'warga'])->name('skhsl.warga');
    Route::post('/skhsl', [SkhslController::class, 'save'])->name('skhsl.save');
    Route::get('/skdom', [SkdomController::class, 'warga'])->name('skdom.warga');
    Route::post('/skdom', [SkdomController::class, 'save'])->name('skdom.save');
    Route::get('/sktm', [SktmController::class, 'warga'])->name('sktm.warga');
    Route::post('/sktm', [SktmController::class, 'save'])->name('sktm.save');
    Route::get('/skbn', [SkbnController::class, 'warga'])->name('skbn.warga');
    Route::post('/skbn', [SkbnController::class, 'save'])->name('skbn.save');
    Route::get('/skboro', [SkboroController::class, 'warga'])->name('skboro.warga');
    Route::post('/skboro', [SkboroController::class, 'save'])->name('skboro.save');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('sktm')->group(function(){
    Route::get('/', [SktmController::class, 'index'])->name('sktm.index');
    Route::get('/add', [SktmController::class, 'add'])->name('sktm.add');
    Route::post('/', [SktmController::class, 'store'])->name('sktm.store');
    Route::get('/edit/{id}', [SktmController::class, 'edit'])->name('sktm.edit');
    Route::post('/update/{id}', [SktmController::class, 'update'])->name('sktm.update');
    Route::post('/naik/{id}', [SktmController::class, 'naik'])->name('sktm.naik');
    Route::get('/preview/{id}', [SktmController::class, 'preview'])->name('sktm.preview');
    Route::get('/cetak/{id}', [SktmController::class, 'cetak'])->name('sktm.cetak');
    Route::post('/tolak/{id}', [SktmController::class, 'tolak'])->name('sktm.tolak');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('skdom')->group(function(){
    Route::get('/', [SkdomController::class, 'index'])->name('skdom.index');
    Route::get('/add', [SkdomController::class, 'add'])->name('skdom.add');
    Route::post('/', [SkdomController::class, 'store'])->name('skdom.store');
    Route::get('/edit/{id}', [SkdomController::class, 'edit'])->name('skdom.edit');
    Route::post('/update/{id}', [SkdomController::class, 'update'])->name('skdom.update');
    Route::post('/naik/{id}', [SkdomController::class, 'naik'])->name('skdom.naik');
    Route::get('/preview/{id}', [SkdomController::class, 'preview'])->name('skdom.preview');
    Route::get('/cetak/{id}', [SkdomController::class, 'cetak'])->name('skdom.cetak');
    Route::post('/tolak/{id}', [SkdomController::class, 'tolak'])->name('skdom.tolak');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('skhsl')->group(function(){
    Route::get('/', [SkhslController::class, 'index'])->name('skhsl.index');
    Route::get('/add', [SkhslController::class, 'add'])->name('skhsl.add');
    Route::post('/', [SkhslController::class, 'store'])->name('skhsl.store');
    Route::get('/edit/{id}', [SkhslController::class, 'edit'])->name('skhsl.edit');
    Route::post('/update/{id}', [SkhslController::class, 'update'])->name('skhsl.update');
    Route::post('/naik/{id}', [SkhslController::class, 'naik'])->name('skhsl.naik');
    Route::get('/preview/{id}', [SkhslController::class, 'preview'])->name('skhsl.preview');
    Route::get('/cetak/{id}', [SkhslController::class, 'cetak'])->name('skhsl.cetak');
    Route::post('/tolak/{id}', [SkhslController::class, 'tolak'])->name('skhsl.tolak');
});


Route::middleware(['auth', 'role:1,3,4'])->prefix('skusaha')->group(function(){
    Route::get('/', [SkusahaController::class, 'index'])->name('skusaha.index');
    Route::get('/add', [SkusahaController::class, 'add'])->name('skusaha.add');
    Route::post('/', [SkusahaController::class, 'store'])->name('skusaha.store');
    Route::get('/edit/{id}', [SkusahaController::class, 'edit'])->name('skusaha.edit');
    Route::post('/update/{id}', [SkusahaController::class, 'update'])->name('skusaha.update');
    Route::post('/naik/{id}', [SkusahaController::class, 'naik'])->name('skusaha.naik');
    Route::get('/preview/{id}', [SkusahaController::class, 'preview'])->name('skusaha.preview');
    Route::get('/cetak/{id}', [SkusahaController::class, 'cetak'])->name('skusaha.cetak');
    Route::post('/tolak/{id}', [SkusahaController::class, 'tolak'])->name('skusaha.tolak');
});


Route::middleware(['auth', 'role:1,3,4'])->prefix('skboro')->group(function(){
    Route::get('/', [SkboroController::class, 'index'])->name('skboro.index');
    Route::get('/add', [SkboroController::class, 'add'])->name('skboro.add');
    Route::post('/', [SkboroController::class, 'store'])->name('skboro.store');
    Route::get('/edit/{id}', [SkboroController::class, 'edit'])->name('skboro.edit');
    Route::post('/update/{id}', [SkboroController::class, 'update'])->name('skboro.update');
    Route::post('/naik/{id}', [SkboroController::class, 'naik'])->name('skboro.naik');
    Route::get('/preview/{id}', [SkboroController::class, 'preview'])->name('skboro.preview');
    Route::get('/cetak/{id}', [SkboroController::class, 'cetak'])->name('skboro.cetak');
    Route::post('/tolak/{id}', [SkboroController::class, 'tolak'])->name('skboro.tolak');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('skkelahiran')->group(function(){
    Route::get('/', [SkkelahiranController::class, 'index'])->name('skkelahiran.index');
    Route::get('/add', [SkkelahiranController::class, 'add'])->name('skkelahiran.add');
    Route::post('/', [SkkelahiranController::class, 'store'])->name('skkelahiran.store');
    Route::get('/edit/{id}', [SkkelahiranController::class, 'edit'])->name('skkelahiran.edit');
    Route::post('/update/{id}', [SkkelahiranController::class, 'update'])->name('skkelahiran.update');
    Route::post('/naik/{id}', [SkkelahiranController::class, 'naik'])->name('skkelahiran.naik');
    Route::get('/preview/{id}', [SkkelahiranController::class, 'preview'])->name('skkelahiran.preview');
    Route::get('/cetak/{id}', [SkkelahiranController::class, 'cetak'])->name('skkelahiran.cetak');
    Route::post('/tolak/{id}', [SkkelahiranController::class, 'tolak'])->name('skkelahiran.tolak');
});

Route::middleware(['auth', 'role:1,3,4'])->prefix('skkematian')->group(function(){
    Route::get('/', [SkkematianController::class, 'index'])->name('skkematian.index');
    Route::get('/add', [SkkematianController::class, 'add'])->name('skkematian.add');
    Route::post('/', [SkkematianController::class, 'store'])->name('skkematian.store');
    Route::get('/edit/{id}', [SkkematianController::class, 'edit'])->name('skkematian.edit');
    Route::post('/update/{id}', [SkkematianController::class, 'update'])->name('skkematian.update');
    Route::post('/naik/{id}', [SkkematianController::class, 'naik'])->name('skkematian.naik');
    Route::get('/preview/{id}', [SkkematianController::class, 'preview'])->name('skkematian.preview');
    Route::get('/cetak/{id}', [SkkematianController::class, 'cetak'])->name('skkematian.cetak');
    Route::post('/tolak/{id}', [SkkematianController::class, 'tolak'])->name('skkematian.tolak');
});


Route::get('/sso', [LoginController::class, 'sso'])->name('sso.login');
Route::get('/callback', [LoginController::class, 'callback'])->name('sso.callback');
Route::get('/users/profile', [LoginController::class, 'profile'])->name('users.profile');

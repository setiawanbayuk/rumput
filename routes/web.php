<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\ResidentController;
use App\Http\Controllers\SkbnController;
use App\Http\Controllers\SkdomController;
use App\Http\Controllers\SktmController;
use App\Http\Controllers\SuketController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('home', ['title' => 'Dashboard']);
// });

Auth::routes();

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }
    return view('welcome');
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
    Route::get('/preview/{id}', [SuketController::class, 'preview'])->name('suket.preview');
    Route::get('/cetak/{id}', [SuketController::class, 'cetak'])->name('suket.cetak');
    Route::post('/tolak/{id}', [SuketController::class, 'tolak'])->name('suket.tolak');
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


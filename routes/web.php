<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Requests\AgamaController;
use App\Http\Controllers\Requests\GenderController;
use App\Http\Controllers\Requests\PekerjaanController;
use App\Http\Controllers\Requests\PendidikanController;
use App\Http\Controllers\Requests\ResidentController;
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
Route::get('/home', [HomeController::class, 'index'])->name('home');
// Route::get('/suket', [SuketController::class, 'index'])->name('suket');

Route::middleware(['auth', 'role:1,4'])->prefix('suket')->group(function(){
    Route::get('/', [SuketController::class, 'index'])->name('suket.index');
    Route::get('/add', [SuketController::class, 'add'])->name('suket.add');
    Route::post('/', [SuketController::class, 'store'])->name('suket.store');
    Route::get('/edit/{id}', [SuketController::class, 'edit'])->name('suket.edit');
    Route::post('/update/{id}', [SuketController::class, 'update'])->name('suket.update');
    Route::post('/naik/{id}', [SuketController::class, 'naik'])->name('suket.naik');
    Route::get('/preview/{id}', [SuketController::class, 'preview'])->name('suket.preview');
});

Route::middleware(['auth', 'role:1'])->prefix('sktm')->group(function(){
    Route::get('/', [SktmController::class, 'index'])->name('sktm.index');
});


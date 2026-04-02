<?php

use App\Http\Controllers\Admin\SuratAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WargaController;
use App\Models\JenisSurat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;
use App\Models\SuratUsaha;

// Route::get('/', function () {
//     return view('home', ['title' => 'Dashboard']);
// });

Auth::routes();
Route::get('/', [HomeController::class, 'landing'])->name('landing');
Route::get('/login-admin', function () {
    return view('auth.login');
})->name('login.admin');
// Route::get('/', function () {
//     if (auth()->check()) {
//         if (auth()->user()->role_id == 2) {
//             return redirect()->route('warga');
//         } else {
//             return redirect()->route('home');
//         }
//     }
//     return redirect()->route('login');
//     // return view('welcome');
// });
Route::get('/home', [HomeController::class, 'index'])->middleware(['auth'])->name('home');
Route::get('/chart/surat', [HomeController::class, 'chartDrilldown']);
Route::get('/activity', [HomeController::class, 'activity'])->name('activity');
Route::get('/activity/last', [HomeController::class, 'last_activity'])->name('activity.last');

Route::middleware(['auth', 'role:1,7'])->prefix('template')->group(function () {
    Route::get('/', [TemplateController::class, 'index'])->name('template.index');
    Route::post('/', [TemplateController::class, 'store'])->name('template.store');
    Route::get('/download/{id}', [TemplateController::class, 'download'])->name('template.download');
    Route::post('/hapus/{id}', [TemplateController::class, 'hapus'])->name('template.hapus');
    Route::get('/add', [TemplateController::class, 'add'])->name('template.add');
});

Route::middleware(['auth', 'role:7'])->prefix('jenis')->group(function () {
    Route::get('/', [JenisController::class, 'index'])->name('jenis.index');
    Route::get('/edit/{id}', [JenisController::class, 'edit'])->name('jenis.edit');
    Route::post('/update/{id}', [JenisController::class, 'update'])->name('jenis.update');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skbn')->group(function () {
    Route::get('/', [SkbnController::class, 'index'])->name('skbn.index');
    Route::get('/add', [SkbnController::class, 'add'])->name('skbn.add');
    Route::post('/', [SkbnController::class, 'store'])->name('skbn.store');
    Route::get('/edit/{id}', [SkbnController::class, 'edit'])->name('skbn.edit');
    Route::post('/update/{id}', [SkbnController::class, 'update'])->name('skbn.update');
    Route::post('/proses/{id}', [SkbnController::class, 'proses'])->name('skbn.proses');
    Route::post('/naik/{id}', [SkbnController::class, 'naik'])->name('skbn.naik');
    Route::post('/naikLurah/{id}', [SkbnController::class, 'naikLurah'])->name('skbn.naikLurah');
    Route::get('/preview/{id}', [SkbnController::class, 'preview'])->name('skbn.preview');
    Route::get('/cetak/{id}', [SkbnController::class, 'cetak'])->name('skbn.cetak');
    Route::post('/tolak/{id}', [SkbnController::class, 'tolak'])->name('skbn.tolak');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('suket')->group(function () {
    Route::get('/', [SuketController::class, 'index'])->name('suket.index');
    Route::get('/add', [SuketController::class, 'add'])->name('suket.add');
    Route::post('/', [SuketController::class, 'store'])->name('suket.store');
    Route::get('/edit/{id}', [SuketController::class, 'edit'])->name('suket.edit');
    Route::post('/update/{id}', [SuketController::class, 'update'])->name('suket.update');
    Route::post('/proses/{id}', [SuketController::class, 'proses'])->name('suket.proses');
    Route::post('/naik/{id}', [SuketController::class, 'naik'])->name('suket.naik');
    Route::post('/naikLurah/{id}', [SuketController::class, 'naikLurah'])->name('suket.naikLurah');
    Route::get('/preview/{id}', [SuketController::class, 'preview'])->name('suket.preview');
    Route::get('/cetak/{id}', [SuketController::class, 'cetak'])->name('suket.cetak');
    Route::post('/tolak/{id}', [SuketController::class, 'tolak'])->name('suket.tolak');
});

// Route::prefix('warga')->group(function () {
//     Route::get('/', [HomeController::class, 'warga'])->middleware(['auth'])->name('warga');
//     Route::get('/ajukan', [HomeController::class, 'ajukan'])->name('ajukan');
//     Route::get('/{jenisSurat}/tracking/{id}', [HomeController::class, 'tracking'])->name('tracking');
//     Route::get('/suket', [SuketController::class, 'warga'])->name('suket.warga');
//     Route::get('/suket/create', [SuketController::class, 'addwarga'])->name('suket.create');
//     Route::post('/suket', [SuketController::class, 'save'])->name('suket.save');
//     Route::get('/suket/{id}/edit', [SuketController::class, 'editwarga'])->name('suket.editwarga');
//     Route::post('/suket/{id}/update', [SuketController::class, 'updatewarga'])->name('suket.updatewarga');
//     Route::get('/suket/{id}', [SuketController::class, 'show'])->name('suket.show');
//     Route::post('/suket/{id}/hapus', [SuketController::class, 'hapus'])->name('suket.hapus');
//     Route::post('/suket/{id}/nilai', [SuketController::class, 'nilai'])->name('suket.nilai');
//     Route::get('/suket/{id}/nilai', [SuketController::class, 'lihatNilai'])->name('suket.nilai.lihat');
//     Route::get('/skusaha', [SkusahaController::class, 'warga'])->name('skusaha.warga');
//     Route::get('/skusaha/create', [SkusahaController::class, 'addwarga'])->name('skusaha.create');
//     Route::post('/skusaha', [SkusahaController::class, 'save'])->name('skusaha.save');
//     Route::get('/skusaha/{id}/edit', [SkusahaController::class, 'editwarga'])->name('skusaha.editwarga');
//     Route::post('/skusaha/{id}/update', [SkusahaController::class, 'updatewarga'])->name('skusaha.updatewarga');
//     Route::get('/skusaha/{id}', [SkusahaController::class, 'show'])->name('skusaha.show');
//     Route::post('/skusaha/{id}/hapus', [SkusahaController::class, 'hapus'])->name('skusaha.hapus');
//     Route::post('/skusaha/{id}/nilai', [SkusahaController::class, 'nilai'])->name('skusaha.nilai');
//     Route::get('/skusaha/{id}/nilai', [SkusahaController::class, 'lihatNilai'])->name('skusaha.nilai.lihat');
//     Route::get('/skhsl', [SkhslController::class, 'warga'])->name('skhsl.warga');
//     Route::get('/skhsl/create', [SkhslController::class, 'addwarga'])->name('skhsl.create');
//     Route::post('/skhsl', [SkhslController::class, 'save'])->name('skhsl.save');
//     Route::get('/skhsl/{id}/edit', [SkhslController::class, 'editwarga'])->name('skhsl.editwarga');
//     Route::post('/skhsl/{id}/update', [SkhslController::class, 'updatewarga'])->name('skhsl.updatewarga');
//     Route::get('/skhsl/{id}', [SkhslController::class, 'show'])->name('skhsl.show');
//     Route::post('/skhsl/{id}/hapus', [SkhslController::class, 'hapus'])->name('skhsl.hapus');
//     Route::post('/skhsl/{id}/nilai', [SkhslController::class, 'nilai'])->name('skhsl.nilai');
//     Route::get('/skhsl/{id}/nilai', [SkhslController::class, 'lihatNilai'])->name('skhsl.nilai.lihat');
//     Route::get('/skdom', [SkdomController::class, 'warga'])->name('skdom.warga');
//     Route::get('/skdom/create', [SkdomController::class, 'addwarga'])->name('skdom.create');
//     Route::post('/skdom', [SkdomController::class, 'save'])->name('skdom.save');
//     Route::get('/skdom/{id}/edit', [SkdomController::class, 'editwarga'])->name('skdom.editwarga');
//     Route::post('/skdom/{id}/update', [SkdomController::class, 'updatewarga'])->name('skdom.updatewarga');
//     Route::get('/skdom/{id}', [SkdomController::class, 'show'])->name('skdom.show');
//     Route::post('/skdom/{id}/hapus', [SkdomController::class, 'hapus'])->name('skdom.hapus');
//     Route::post('/skdom/{id}/nilai', [SkdomController::class, 'nilai'])->name('skdom.nilai');
//     Route::get('/skdom/{id}/nilai', [SkdomController::class, 'lihatNilai'])->name('skdom.nilai.lihat');
//     Route::get('/sktm', [SktmController::class, 'warga'])->name('sktm.warga');
//     Route::get('/sktm/create', [SktmController::class, 'addwarga'])->name('sktm.create');
//     Route::post('/sktm', [SktmController::class, 'save'])->name('sktm.save');
//     Route::get('/sktm/{id}/edit', [SktmController::class, 'editwarga'])->name('sktm.editwarga');
//     Route::post('/sktm/{id}/update', [SktmController::class, 'updatewarga'])->name('sktm.updatewarga');
//     Route::get('/sktm/{id}', [SktmController::class, 'show'])->name('sktm.show');
//     Route::post('/sktm/{id}/hapus', [SktmController::class, 'hapus'])->name('sktm.hapus');
//     Route::post('/sktm/{id}/nilai', [SktmController::class, 'nilai'])->name('sktm.nilai');
//     Route::get('/sktm/{id}/nilai', [SktmController::class, 'lihatNilai'])->name('sktm.nilai.lihat');
//     Route::get('/skbn', [SkbnController::class, 'warga'])->name('skbn.warga');
//     Route::get('/skbn/create', [SkbnController::class, 'addwarga'])->name('skbn.create');
//     Route::post('/skbn', [SkbnController::class, 'save'])->name('skbn.save');
//     Route::get('/skbn/{id}/edit', [SkbnController::class, 'editwarga'])->name('skbn.editwarga');
//     Route::post('/skbn/{id}/update', [SkbnController::class, 'updatewarga'])->name('skbn.updatewarga');
//     Route::get('/skbn/{id}', [SkbnController::class, 'show'])->name('skbn.show');
//     Route::post('/skbn/{id}/hapus', [SkbnController::class, 'hapus'])->name('skbn.hapus');
//     Route::post('/skbn/{id}/nilai', [SkbnController::class, 'nilai'])->name('skbn.nilai');
//     Route::get('/skbn/{id}/nilai', [SkbnController::class, 'lihatNilai'])->name('skbn.nilai.lihat');
//     Route::get('/skboro', [SkboroController::class, 'warga'])->name('skboro.warga');
//     Route::get('/skboro/create', [SkboroController::class, 'addwarga'])->name('skboro.create');
//     Route::post('/skboro', [SkboroController::class, 'save'])->name('skboro.save');
//     Route::get('/skboro/{id}/edit', [SkboroController::class, 'editwarga'])->name('skboro.editwarga');
//     Route::post('/skboro/{id}/update', [SkboroController::class, 'updatewarga'])->name('skboro.updatewarga');
//     Route::get('/skboro/{id}', [SkboroController::class, 'show'])->name('skboro.show');
//     Route::post('/skboro/{id}/hapus', [SkboroController::class, 'hapus'])->name('skboro.hapus');
//     Route::post('/skboro/{id}/nilai', [SkboroController::class, 'nilai'])->name('skboro.nilai');
//     Route::get('/skboro/{id}/nilai', [SkboroController::class, 'lihatNilai'])->name('skboro.nilai.lihat');
//     Route::get('/skkelahiran', [SkkelahiranController::class, 'warga'])->name('skkelahiran.warga');
//     Route::get('/skkelahiran/create', [SkkelahiranController::class, 'addwarga'])->name('skkelahiran.create');
//     Route::post('/skkelahiran', [SkkelahiranController::class, 'save'])->name('skkelahiran.save');
//     Route::get('/skkelahiran/{id}/edit', [SkkelahiranController::class, 'editwarga'])->name('skkelahiran.editwarga');
//     Route::post('/skkelahiran/{id}/update', [SkkelahiranController::class, 'updatewarga'])->name('skkelahiran.updatewarga');
//     Route::get('/skkelahiran/{id}', [SkkelahiranController::class, 'show'])->name('skkelahiran.show');
//     Route::post('/skkelahiran/{id}/hapus', [SkkelahiranController::class, 'hapus'])->name('skkelahiran.hapus');
//     Route::post('/skkelahiran/{id}/nilai', [SkkelahiranController::class, 'nilai'])->name('skkelahiran.nilai');
//     Route::get('/skkelahiran/{id}/nilai', [SkkelahiranController::class, 'lihatNilai'])->name('skkelahiran.nilai.lihat');
//     Route::get('/skkematian', [SkkematianController::class, 'warga'])->name('skkematian.warga');
//     Route::get('/skkematian/create', [SkkematianController::class, 'addwarga'])->name('skkematian.create');
//     Route::post('/skkematian', [SkkematianController::class, 'save'])->name('skkematian.save');
//     Route::get('/skkematian/{id}/edit', [SkkematianController::class, 'editwarga'])->name('skkematian.editwarga');
//     Route::post('/skkematian/{id}/update', [SkkematianController::class, 'updatewarga'])->name('skkematian.updatewarga');
//     Route::get('/skkematian/{id}', [SkkematianController::class, 'show'])->name('skkematian.show');
//     Route::post('/skkematian/{id}/hapus', [SkkematianController::class, 'hapus'])->name('skkematian.hapus');
//     Route::post('/skkematian/{id}/nilai', [SkkematianController::class, 'nilai'])->name('skkematian.nilai');
//     Route::get('/skkematian/{id}/nilai', [SkkematianController::class, 'lihatNilai'])->name('skkematian.nilai.lihat');
// });

Route::middleware(['auth', 'role:1,3,4,5,6,7,8,9'])->prefix('sktm')->group(function () {
    Route::get('/', [SktmController::class, 'index'])->name('sktm.index');
    Route::get('/add/{id}', [SktmController::class, 'add'])->name('sktm.add');
    Route::post('/', [SktmController::class, 'store'])->name('sktm.store');
    Route::get('/edit/{id}', [SktmController::class, 'edit'])->name('sktm.edit');
    Route::post('/update/{id}', [SktmController::class, 'update'])->name('sktm.update');
    Route::post('/proses/{id}', [SktmController::class, 'proses'])->name('sktm.proses');
    Route::post('/naik/{id}', [SktmController::class, 'naik'])->name('sktm.naik');
    Route::post('/naikLurah/{id}', [SktmController::class, 'naikLurah'])->name('sktm.naikLurah');
    Route::get('/preview/{id}', [SktmController::class, 'preview'])->name('sktm.preview');
    Route::get('/cetak/{id}', [SktmController::class, 'cetak'])->name('sktm.cetak');
    Route::post('/tolak/{id}', [SktmController::class, 'tolak'])->name('sktm.tolak');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skdom')->group(function () {
    Route::get('/', [SkdomController::class, 'index'])->name('skdom.index');
    Route::get('/add', [SkdomController::class, 'add'])->name('skdom.add');
    Route::post('/', [SkdomController::class, 'store'])->name('skdom.store');
    Route::get('/edit/{id}', [SkdomController::class, 'edit'])->name('skdom.edit');
    Route::post('/update/{id}', [SkdomController::class, 'update'])->name('skdom.update');
    Route::post('/proses/{id}', [SkdomController::class, 'proses'])->name('skdom.proses');
    Route::post('/naik/{id}', [SkdomController::class, 'naik'])->name('skdom.naik');
    Route::post('/naikLurah/{id}', [SkdomController::class, 'naikLurah'])->name('skdom.naikLurah');
    Route::get('/preview/{id}', [SkdomController::class, 'preview'])->name('skdom.preview');
    Route::get('/cetak/{id}', [SkdomController::class, 'cetak'])->name('skdom.cetak');
    Route::post('/tolak/{id}', [SkdomController::class, 'tolak'])->name('skdom.tolak');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skhsl')->group(function () {
    Route::get('/', [SkhslController::class, 'index'])->name('skhsl.index');
    Route::get('/add', [SkhslController::class, 'add'])->name('skhsl.add');
    Route::post('/', [SkhslController::class, 'store'])->name('skhsl.store');
    Route::get('/edit/{id}', [SkhslController::class, 'edit'])->name('skhsl.edit');
    Route::post('/update/{id}', [SkhslController::class, 'update'])->name('skhsl.update');
    Route::post('/proses/{id}', [SkhslController::class, 'proses'])->name('skhsl.proses');
    Route::post('/naik/{id}', [SkhslController::class, 'naik'])->name('skhsl.naik');
    Route::post('/naikLurah/{id}', [SkhslController::class, 'naikLurah'])->name('skhsl.naikLurah');
    Route::get('/preview/{id}', [SkhslController::class, 'preview'])->name('skhsl.preview');
    Route::get('/cetak/{id}', [SkhslController::class, 'cetak'])->name('skhsl.cetak');
    Route::post('/tolak/{id}', [SkhslController::class, 'tolak'])->name('skhsl.tolak');
});


Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skusaha')->group(function () {
    Route::get('/', [SkusahaController::class, 'index'])->name('skusaha.index');
    Route::get('/add', [SkusahaController::class, 'add'])->name('skusaha.add');
    Route::post('/', [SkusahaController::class, 'store'])->name('skusaha.store');
    Route::get('/edit/{id}', [SkusahaController::class, 'edit'])->name('skusaha.edit');
    Route::post('/update/{id}', [SkusahaController::class, 'update'])->name('skusaha.update');
    Route::post('/proses/{id}', [SkusahaController::class, 'proses'])->name('skusaha.proses');
    Route::post('/naik/{id}', [SkusahaController::class, 'naik'])->name('skusaha.naik');
    Route::post('/naikLurah/{id}', [SkusahaController::class, 'naikLurah'])->name('skusaha.naikLurah');
    Route::get('/preview/{id}', [SkusahaController::class, 'preview'])->name('skusaha.preview');
    Route::get('/cetak/{id}', [SkusahaController::class, 'cetak'])->name('skusaha.cetak');
    Route::post('/tolak/{id}', [SkusahaController::class, 'tolak'])->name('skusaha.tolak');
});


Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skboro')->group(function () {
    Route::get('/', [SkboroController::class, 'index'])->name('skboro.index');
    Route::get('/add', [SkboroController::class, 'add'])->name('skboro.add');
    Route::post('/', [SkboroController::class, 'store'])->name('skboro.store');
    Route::get('/edit/{id}', [SkboroController::class, 'edit'])->name('skboro.edit');
    Route::post('/update/{id}', [SkboroController::class, 'update'])->name('skboro.update');
    Route::post('/proses/{id}', [SkboroController::class, 'proses'])->name('skboro.proses');
    Route::post('/naik/{id}', [SkboroController::class, 'naik'])->name('skboro.naik');
    Route::post('/naikLurah/{id}', [SkboroController::class, 'naikLurah'])->name('skboro.naikLurah');
    Route::get('/preview/{id}', [SkboroController::class, 'preview'])->name('skboro.preview');
    Route::get('/cetak/{id}', [SkboroController::class, 'cetak'])->name('skboro.cetak');
    Route::post('/tolak/{id}', [SkboroController::class, 'tolak'])->name('skboro.tolak');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skkelahiran')->group(function () {
    Route::get('/', [SkkelahiranController::class, 'index'])->name('skkelahiran.index');
    Route::get('/add', [SkkelahiranController::class, 'add'])->name('skkelahiran.add');
    Route::post('/', [SkkelahiranController::class, 'store'])->name('skkelahiran.store');
    Route::get('/edit/{id}', [SkkelahiranController::class, 'edit'])->name('skkelahiran.edit');
    Route::post('/update/{id}', [SkkelahiranController::class, 'update'])->name('skkelahiran.update');
    Route::post('/proses/{id}', [SkkelahiranController::class, 'proses'])->name('skkelahiran.proses');
    Route::post('/naik/{id}', [SkkelahiranController::class, 'naik'])->name('skkelahiran.naik');
    Route::post('/naikLurah/{id}', [SkkelahiranController::class, 'naikLurah'])->name('skkelahiran.naikLurah');
    Route::get('/preview/{id}', [SkkelahiranController::class, 'preview'])->name('skkelahiran.preview');
    Route::get('/cetak/{id}', [SkkelahiranController::class, 'cetak'])->name('skkelahiran.cetak');
    Route::post('/tolak/{id}', [SkkelahiranController::class, 'tolak'])->name('skkelahiran.tolak');
});

Route::middleware(['auth', 'role:1,3,4,7,8,9'])->prefix('skkematian')->group(function () {
    Route::get('/', [SkkematianController::class, 'index'])->name('skkematian.index');
    Route::get('/add', [SkkematianController::class, 'add'])->name('skkematian.add');
    Route::post('/', [SkkematianController::class, 'store'])->name('skkematian.store');
    Route::get('/edit/{id}', [SkkematianController::class, 'edit'])->name('skkematian.edit');
    Route::post('/update/{id}', [SkkematianController::class, 'update'])->name('skkematian.update');
    Route::post('/proses/{id}', [SkkematianController::class, 'proses'])->name('skkematian.proses');
    Route::post('/naik/{id}', [SkkematianController::class, 'naik'])->name('skkematian.naik');
    Route::post('/naikLurah/{id}', [SkkematianController::class, 'naikLurah'])->name('skkematian.naikLurah');
    Route::get('/preview/{id}', [SkkematianController::class, 'preview'])->name('skkematian.preview');
    Route::get('/cetak/{id}', [SkkematianController::class, 'cetak'])->name('skkematian.cetak');
    Route::post('/tolak/{id}', [SkkematianController::class, 'tolak'])->name('skkematian.tolak');
});

Route::get('/sso', [LoginController::class, 'sso'])->name('sso.login');
Route::get('/callback', [LoginController::class, 'callback'])->name('sso.callback');
Route::get('/users/profile', [LoginController::class, 'profile'])->name('users.profile');

Route::middleware(['auth'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile');
    Route::post('/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/akun/{id}', [ProfileController::class, 'akun'])->name('profile.akun'); // update password
});

Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');

Route::group(['prefix' => 'admin/surat', 'as' => 'admin.surat.', 'middleware' => ['auth']], function () {
    // Tampilan Utama & DataTables
    Route::get('/', [SuratAdminController::class, 'index'])->name('index');

    // Input Data (Web Admin)
    Route::get('/create/{jenis}', [SuratAdminController::class, 'create'])->name('create');
    Route::post('/store', [SuratAdminController::class, 'store'])->name('store');

    // Edit & Update
    Route::get('/edit/{id}', [SuratAdminController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [SuratAdminController::class, 'update'])->name('update');

    // Alur Persetujuan (Workflow)
    Route::post('/proses/{id}', [SuratAdminController::class, 'proses'])->name('proses');
    Route::post('/naik/{id}', [SuratAdminController::class, 'naik'])->name('naik');
    Route::post('/naik-lurah/{id}', [SuratAdminController::class, 'naikLurah'])->name('naikLurah');
    Route::post('/tolak/{id}', [SuratAdminController::class, 'tolak'])->name('tolak');

    // Preview & Dokumen Akhir (TTE)
    Route::get('/preview/{id}', [SuratAdminController::class, 'preview'])->name('preview');
    Route::get('/cetak/{id}', [SuratAdminController::class, 'cetak'])->name('cetak');
});

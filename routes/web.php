<?php

use App\Services\WhatsAppService;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MustahikController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewsController;
// use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing.index');

Route::post('/cek-penerima', [LandingController::class, 'checkRecipient'])
    ->name('landing.check');

Route::get(
    '/berita/{id}',
    [LandingController::class, 'showNews']
)->name('landing.berita.show');

Route::get('/login', [
    AuthController::class,
    'loginForm'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::get('/logout', [
    AuthController::class,
    'logout'
])->name('logout');

Route::get('/test-wa', function (WhatsAppService $wa) {

    $hasil = $wa->sendMessage(

        '6287857172007',

        'Halo Rafli 👋

Ini adalah pesan percobaan dari Website ATM Beras.'

    );

    dd($hasil);
});

Route::middleware('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/mesin', [
        MachineController::class,
        'index'
    ])->name('mesin.index');

    Route::post('/mesin', [
        MachineController::class,
        'store'
    ])->name('mesin.store');

    Route::put('/mesin/{machine}', [
        MachineController::class,
        'update'
    ])->name('mesin.update');

    Route::delete('/mesin/{machine}', [
        MachineController::class,
        'destroy'
    ])->name('mesin.destroy');

    Route::get('/regencies/{province}', [
        MachineController::class,
        'getRegencies'
    ]);

    Route::get('/districts/{regency}', [
        MachineController::class,
        'getDistricts'
    ]);

    Route::get('/villages/{district}', [
        MachineController::class,
        'getVillages'
    ]);
    Route::patch('/mesin/{machine}/toggle-status', [
        MachineController::class,
        'toggleStatus'
    ])->name('mesin.toggle-status');

    Route::patch(
        '/mesin/{machine}/jadwal',
        [MachineController::class, 'updateJadwal']
    )->name('mesin.update-jadwal');

    Route::get(
        '/mustahik',
        [MustahikController::class, 'index']
    )->name('mustahik.index');

    Route::resource(
        'mustahik',
        MustahikController::class
    );

    Route::put(
        '/mustahik/{mustahik}',
        [MustahikController::class, 'update']
    )->name('mustahik.update');

    Route::post(
        '/mustahik/{mustahik}/tambah-jatah',
        [MustahikController::class, 'tambahJatah']
    )->name('mustahik.tambah-jatah');

    Route::get(
        '/mustahik/{mustahik}/riwayat-pdf',
        [MustahikController::class, 'downloadRiwayat']
    )->name('mustahik.riwayat.pdf');

    Route::get(
        '/laporan-pengambilan/pdf',
        [MustahikController::class, 'downloadLaporanPengambilan']
    )->name('laporan.pengambilan.pdf');

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::resource(
        'admin-management',
        AdminController::class
    );

    Route::get(
        '/berita',
        [NewsController::class, 'index']
    )->name('berita.index');

    Route::post(
        '/berita',
        [NewsController::class, 'store']
    )->name('berita.store');

    Route::put(
        '/berita/{beritum}',
        [NewsController::class, 'update']
    )->name('berita.update');

    Route::delete(
        '/berita/{beritum}',
        [NewsController::class, 'destroy']
    )->name('berita.destroy');
});

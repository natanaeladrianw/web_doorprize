<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\Admin\FormAdminController;
use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\Admin\AdminManagerController;
use App\Http\Controllers\Admin\PrizeController;
use App\Http\Controllers\Admin\PrizeCategoryController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [FormSubmissionController::class, 'index'])->name('forms.index');
Route::get('/forms/{form}', [FormSubmissionController::class, 'show'])->name('forms.show');
Route::post('/forms/{form}/submit', [FormSubmissionController::class, 'store'])->name('forms.submit');

/*
|--------------------------------------------------------------------------
| Admin Routes (auth + admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/peserta', [AdminController::class, 'peserta'])->name('peserta');

        /*
        |----------------------------------------------------------------------
        | Form Management (ALL ADMIN)
        |----------------------------------------------------------------------
        */
        Route::resource('forms', FormAdminController::class);
        Route::get('forms/{form}/builder', [FormAdminController::class, 'builder'])->name('forms.builder');
        Route::get('forms/{form}/preview', [FormAdminController::class, 'preview'])->name('forms.preview');

        Route::post('forms/{form}/fields', [FormFieldController::class, 'store'])->name('forms.fields.store');
        Route::put('forms/{form}/fields/{field}', [FormFieldController::class, 'update'])->name('forms.fields.update');
        Route::delete('forms/{form}/fields/{field}', [FormFieldController::class, 'destroy'])->name('forms.fields.destroy');
        Route::post('forms/{form}/fields/reorder', [FormFieldController::class, 'reorder'])->name('forms.fields.reorder');
        Route::patch('forms/{form}/update-status', [FormAdminController::class, 'updateStatus'])->name('forms.update-status');

        /*
        |----------------------------------------------------------------------
        | Hadiah (Input Hadiah)
        |----------------------------------------------------------------------
        */
        Route::get('/hadiah', [AdminController::class, 'hadiah'])
    ->middleware(['auth','admin'])
    ->name('hadiah');

        
        Route::post('forms/{form}/prizes', [PrizeController::class, 'store'])->name('prizes.store');
        Route::put('prizes/{prize}', [PrizeController::class, 'update'])->name('prizes.update');
        Route::delete('prizes/{prize}', [PrizeController::class, 'destroy'])->name('prizes.destroy');

        Route::post('forms/{form}/prize-categories', [PrizeCategoryController::class, 'store'])->name('prize-categories.store');
        Route::put('prize-categories/{category}', [PrizeCategoryController::class, 'update'])->name('prize-categories.update');
        Route::delete('prize-categories/{category}', [PrizeCategoryController::class, 'destroy'])->name('prize-categories.destroy');

        /*
        |--------------------------------------------------------------------------
        | Admin Full Access (admin.full middleware)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['admin.full'])->group(function () {

            // Kelola Admin (hanya admin penuh)
            Route::resource('admins', AdminManagerController::class);

            // Pemenang & Undian
            Route::get('/pemenang', [AdminController::class, 'pemenang'])->name('pemenang');
            Route::get('/winners', [AdminController::class, 'winners'])->name('winners');
            Route::get('/undian', [AdminController::class, 'undian'])->name('undian');

            // Kandidat Form
            Route::get('/forms/{form}/candidates', [AdminController::class, 'getFormCandidates'])->name('forms.candidates');

            // Laporan
            Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
            Route::get('/laporan/export', [AdminController::class, 'exportLaporan'])->name('laporan.export');
            Route::get('/laporan/export-pdf', [AdminController::class, 'exportLaporanPdf'])->name('laporan.export-pdf');

            // Winner Actions
            Route::post('forms/{form}/{formSubmission}/winner/manual', [WinnerController::class, 'manual'])->name('winner.manual');
            Route::post('forms/{form}/winner/automatic', [WinnerController::class, 'automatic'])->name('winner.automatic');

            // Prize Actions
            Route::post('prizes/{prize}/select-manual', [PrizeController::class, 'selectWinnerManual'])->name('prizes.select-manual');
            Route::post('prizes/{prize}/select-random', [PrizeController::class, 'selectWinnerRandom'])->name('prizes.select-random');
            Route::post('prizes/{prize}/reset', [PrizeController::class, 'resetWinner'])->name('prizes.reset');

            // Settings
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::post('settings/raffle-display', [SettingController::class, 'updateRaffleDisplay'])->name('settings.raffle-display');

            Route::get('settings/header', [SettingController::class, 'headerSettings'])->name('settings.header');
            Route::post('settings/header/upload', [SettingController::class, 'uploadHeader'])->name('settings.header.upload');
            Route::post('settings/header/text', [SettingController::class, 'updateHeaderText'])->name('settings.header.text');
            Route::delete('settings/header/delete', [SettingController::class, 'deleteHeader'])->name('settings.header.delete');
        });
    });

require __DIR__ . '/auth.php';

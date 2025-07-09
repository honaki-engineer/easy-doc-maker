<?php

use App\Http\Controllers\BentoController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReceiptSettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    // 自社情報
    Route::get('/receipt_settings', [ReceiptSettingController::class, 'show'])->name('receipt_settings.show');
    Route::get('/receipt_settings/edit', [ReceiptSettingController::class, 'edit'])->name('receipt_settings.edit');
    Route::put('/receipt_settings', [ReceiptSettingController::class, 'update'])->name('receipt_settings.update');
    
    // お弁当関連
    Route::resource('bentos', BentoController::class);
    Route::resource('brands', BrandController::class);
    
    // 領収書
    Route::resource('receipts', ReceiptController::class);

    // 領収書PDFダウンロード
    Route::get('/receipts/{id}/download-pdf', [ReceiptController::class, 'downloadPdf'])->name('receipts.download.pdf');
    // 領収書一括ダウンロード
    Route::post('/receipts/bulk-download', [ReceiptController::class, 'bulkDownload'])->name('receipts.bulkDownload');

    // 印刷
    // PDF生成
    Route::get('/receipts/pdf/print/{id}', [ReceiptController::class, 'generateAndPrint'])->name('receipts.generate_and_print');
    // 印刷表示(中継ビュー)
    Route::get('/receipts/print/show/{filename}', [ReceiptController::class, 'showPrintView'])->name('receipts.print.show');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use Nelson\ExportTest\Http\Controllers\ExportTestController;

Route::middleware(['web'])->group(function () {
    Route::get('test-result/preview', [ExportTestController::class, 'previewPDF']);
    Route::get('test-result/download', [ExportTestController::class, 'downloadPDF']);
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\importController;

Route::get('/', function () {
    return view('Home');
});

Route::get('/excel', function () {
    return view('Excel');
})->name('excel');


Route::get('/import', [importController::class, 'readExcelFile'])->name('import');
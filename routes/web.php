<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\importController;

Route::get('/', function () {
   /* return view('Home');*/
     return view('Excel');
});

Route::get('/excel', function () {
    return view('Excel');
})->name('excel');


Route::get('/import', [importController::class, 'readExcelFile'])->name('import');
Route::get('/pointage', [importController::class, 'getPointage'])->name('pointage');
Route::get('/update', [importController::class, 'updateHS'])->name('updateHS');
Route::get('/insert', [importController::class, 'insertHS'])->name('insertHS');
Route::get('/pointage_coupe', [importController::class, 'getPointageCoupe'])->name('getPointageCoupe');

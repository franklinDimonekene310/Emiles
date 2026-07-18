<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\importController;
use App\Http\Controllers\PointageCoupeController;


Route::get('/', function () {
   /* return view('Home');*/
     return view('Excel');
     // return view('Home2');
});

Route::get('/excel', function () {
    return view('Excel');
})->name('excel');


Route::get('/import', [importController::class, 'readExcelFile'])->name('import');
Route::get('/pointage', [importController::class, 'getPointage'])->name('pointage');
Route::get('/update', [importController::class, 'updateHS'])->name('updateHS');
Route::get('/insert', [importController::class, 'insertHS'])->name('insertHS');
Route::get('/pointage_coupe', [importController::class, 'getPointageCoupe'])->name('getPointageCoupe');
Route::get('/test_coupe', [PointageCoupeController::class, 'genererFichierPointageCoupe'])->name('genererFichierPointageCoupe');
Route::get('/test_coupe2', [PointageCoupeController::class, 'misAJourPointageCoupe'])->name('misAJourPointageCoupe');



<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\importController;
use App\Http\Controllers\PointageCoupeController;


Route::get('/', function () {
 //return view('toast');
   return view('Excel');
     // return view('Home2');
      //return view('Modale');
});

Route::get('/excel', function () {
    return view('Excel');
})->name('excel');


Route::get('/import', [importController::class, 'readExcelFile'])->name('import');
Route::get('/update', [importController::class, 'updateHS'])->name('updateHS');
Route::get('/insert', [importController::class, 'insertHS'])->name('insertHS');


Route::get('/exportation_pointage_en_excel', [PointageCoupeController::class, 'genererFichierPointageCoupe'])->name('genererFichierPointageCoupe');
Route::get('/mis_a_jour_pointage', [PointageCoupeController::class, 'misAJourPointageCoupe'])->name('misAJourPointageCoupe');
Route::get('/pointage_manquant', [PointageCoupeController::class, 'pointageManquant'])->name('pointageManquant');
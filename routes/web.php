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
Route::get('/pointage', [importController::class, 'getPointage'])->name('pointage');
Route::get('/update', [importController::class, 'updateHS'])->name('updateHS');
Route::get('/insert', [importController::class, 'insertHS'])->name('insertHS');
Route::get('/pointage_coupe', [importController::class, 'getPointageCoupe'])->name('getPointageCoupe');
Route::get('/exportation_pointage_en_excel', [PointageCoupeController::class, 'genererFichierPointageCoupe'])->name('genererFichierPointageCoupe');
Route::get('/mis_a_jour_pointage', [PointageCoupeController::class, 'misAJourPointageCoupe'])->name('misAJourPointageCoupe');

Route::post('/clear-erreur-session', function () {
    session()->forget('erreur');
    return response()->json(['success' => true]);
})->name('clear.erreur');



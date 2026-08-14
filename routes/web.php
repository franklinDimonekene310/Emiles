<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\importController;
use App\Http\Controllers\PointageCoupeController;
use App\Http\Controllers\PointageManquantController;
use App\Http\Controllers\FichierFlorentController;


Route::get('/', function () {
 //return view('toast');
   return view('Excel');
     // return view('Home2');
      //return view('Modale');
});

Route::get('/excel', function () {
    return view('Excel');
})->name('excel');


Route::get('/import-fichier-cnss', [importController::class, 'fichierCnss'])->name('fichierCnss');
Route::get('/update', [importController::class, 'updateHS'])->name('updateHS');
Route::get('/insert', [importController::class, 'insertHS'])->name('insertHS');
Route::get('/jour', [importController::class, 'iprCnn'])->name('iprCnn');

Route::get('/somme', [importController::class, 'sommerTypepaie'])->name('sommerTypepaie');


Route::get('/exportation_pointage_en_excel', [PointageCoupeController::class, 'genererFichierPointageCoupe'])->name('genererFichierPointageCoupe');
Route::get('/mis_a_jour_pointage_coupe', [PointageCoupeController::class, 'misAJourPointageCoupe'])->name('misAJourPointageCoupe');
Route::get('/pointage_manquant', [PointageCoupeController::class, 'pointageManquant'])->name('pointageManquant');

// Pointage Manquant
Route::get('/afficher-toutes-les-absences', [PointageManquantController::class, 'afficherToutesLesAbsences'])
->name('afficherToutesLesAbsences');


Route::get('/fichier', [FichierFlorentController::class, 'salaireJournAgri'])->name('salaireJournAgri');
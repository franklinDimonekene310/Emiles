<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;

use DB;

class FichierFlorentController extends Controller
{
    //

    public function salaireJournAgri() {

    /*$prime = DB::connection('hfsql_personnel')
    ->table('HISTORIQUE_PRIMES_DECADAIRES_AGRO')    
    ->where('DateDebutDecade', '20260801')
    ->where('Annee', 2026)
    ->get();

    $pointages = DB::connection('hfsql_journalier')
    ->table('POINTAGE_JOURNALIERS')    
    ->where('DateDebutDecade', '20260801')
    ->where('IDAnnee', 2026)
    ->orderBy('DatePointage')
    ->get();




        $resultat = $pointages->map(function ($pointage) use ($prime) {

            $jour = substr($pointage->DatePointage, -2);

            $nomColonne = 'MontantJour' . $jour;

            $tacherealise = $pointage->TacheRealisee > 0
            ? $pointage->TacheRealisee
            : '';

            return [
                'Matricule'   => $pointage->Matricule,
                'IDTacheJ'    => $pointage->IDTacheJ.$tacherealise,
                'DatePointage' => $pointage->DatePointage,
                'Montantjour' => $prime->$nomColonne ?? null,
            ];
        });

        (new FastExcel($resultat))->export(public_path('resultat.xlsx'));
        dd('$prime, $pointages, $resultat');*/

        

$prime = DB::connection('hfsql_personnel')
    ->table('HISTORIQUE_PRIMES_DECADAIRES_AGRO')
    ->whereIn('DateDebutDecade', [
        '20260501',
        '20260511',
        '20260521',
        '20260601',
        '20260611',
        '20260621',
        '20260701',
        '20260711',
        '20260721',
        '20260801'
    ])
    ->where('Annee', 2026)
    ->where('Matricule','LIKE', 'JJ%')
    ->get()
    ->keyBy(function ($item) {
        return $item->Matricule . '_' . $item->DateDebutDecade;
    });

    


$pointages = DB::connection('hfsql_journalier')
    ->table('POINTAGE_JOURNALIERS')
    ->whereIn('DateDebutDecade', [
        '20260501',
        '20260511',
        '20260521',
        '20260601',
        '20260611',
        '20260621',
        '20260701',
        '20260711',
        '20260721',
        '20260801'
    ])
    //->where('Matricule', 'JJ4217')
    ->where('Matricule','LIKE', 'JJ%')
    ->where('IDAnnee', 2026)
    ->orderBy('DatePointage')
    ->get();

$resultat = $pointages->map(function ($pointage) use ($prime) {

    // Récupération de la prime correspondant
    // au matricule ET à la décade
    $cle = $pointage->Matricule . '_' . $pointage->DateDebutDecade;

    $primeEmploye = $prime->get($cle);

    $jour = (int) substr($pointage->DatePointage, -2);

    $jourDebutDecade = (int) substr($pointage->DateDebutDecade, -2);

    $numeroJour = $jour - $jourDebutDecade + 1;

    $nomColonne = ($numeroJour == 10 || $numeroJour == 11)
        ? 'Montantjour' . $numeroJour
        : 'MontantJour' . str_pad($numeroJour, 2, '0', STR_PAD_LEFT);

    $tacherealise = $pointage->TacheRealisee > 0
        ? $pointage->TacheRealisee
        : '';

    return [
        'Matricule'    => $pointage->Matricule,
        'IDTacheJ'     => $pointage->IDTacheJ . $tacherealise,
        'DatePointage' => Carbon::parse($pointage->DatePointage)->format('d-m-Y'),
        'Montantjour'  => (float) ($primeEmploye?->$nomColonne ?? 0),
    ];
});


(new FastExcel($resultat))->export(public_path('resultat.xlsx'));

dd('fait');
}}

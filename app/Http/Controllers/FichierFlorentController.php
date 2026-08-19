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
                ->where('Matricule','LIKE', 'JJ%')
                ->where('IDAnnee', 2026)
                ->orderBy('DatePointage')
                ->get();

        $resultat = $pointages->map(function ($pointage) use ($prime) {
            // Récupération de la prime correspondant au matricule ET à la décade
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
        }


        public function primeParPose() {
            $sql = "SELECT 
                POINTAGE_JOURNALIERS.IDTacheJ, POINTAGE_JOURNALIERS.DatePointage, POINTAGE_JOURNALIERS.IDEquipeJ, POINTAGE_JOURNALIERS.TacheRealisee,
                POINTAGE_JOURNALIERS.Matricule,
                    CASE 
                        WHEN IDTacheJ <> '20' AND TacheRealisee = '6' THEN '6-18'
                        ELSE 'Ordinaire'
                    END AS Pose
                FROM [POINTAGE_JOURNALIERS]
                WHERE IDAnnee = '2026'
                and POINTAGE_JOURNALIERS.IDEquipeJ in ('14', '22', '23', '70','84','85')
                AND POINTAGE_JOURNALIERS.Matricule in ( 'JJ3096', 'JJ3095', 'JJ3092')
                AND POINTAGE_JOURNALIERS.DateDebutDecade = '20260801'";
            $pointages = DB::connection('hfsql_journalier')->select($sql);
 dd($pointages);
            $pointages = DB::connection('hfsql_journalier')
                        ->table('POINTAGE_JOURNALIERS')
                        ->whereIn('DateDebutDecade', [                   
                            '20260801'
                        ])    
                        ->where('Matricule','LIKE', 'JJ3096')
                        ->where('IDAnnee', 2026)
                        ->orderBy('DatePointage')
                        ->get();

           

            $prime = DB::connection('hfsql_personnel')
            ->table('HISTORIQUE_PRIMES_DECADAIRES_AGRO')
            ->whereIn('DateDebutDecade', [                
                '20260801'
            ])
            ->where('Annee', 2026)
            ->where('Matricule','LIKE', 'JJ3096')
            ->get()
            ->keyBy('Matricule');   
       

        $req = "SELECT 
                POINTAGE_JOURNALIERS.IDTacheJ, POINTAGE_JOURNALIERS.DatePointage, POINTAGE_JOURNALIERS.IDEquipeJ, POINTAGE_JOURNALIERS.TacheRealisee,
                POINTAGE_JOURNALIERS.Matricule,
                CASE 
                    WHEN IDTacheJ <> '20' AND TacheRealisee = '6' THEN '6-18'
                    WHEN IDTacheJ = '20' AND POINTAGE_JOURNALIERS.datepointage IN ('20260802', '20260803', '20260809') THEN 'Dim/Ferie'
                    ELSE 'Ordinaire'
                END AS Pose
                FROM [POINTAGE_JOURNALIERS]
                WHERE IDAnnee = '2026'
                AND POINTAGE_JOURNALIERS.IDEquipeJ IN ('14', '22', '23', '70','84','85')
                AND Matricule LIKE 'JJ%' 
                AND POINTAGE_JOURNALIERS.IDTacheJ = '63' 
                AND DateDebutDecade = '20260801'
                ";

        $reqDetailsJournAgri = "SELECT 
                POINTAGE_JOURNALIERS.DatePointage, POINTAGE_JOURNALIERS.IDEquipeJ,
                POINTAGE_JOURNALIERS.Matricule, 
                CASE                    
                    WHEN POINTAGE_JOURNALIERS.IDTacheJ = '20' THEN CONCAT(POINTAGE_JOURNALIERS.IDTacheJ,POINTAGE_JOURNALIERS.TacheRealisee)                    
                    ELSE POINTAGE_JOURNALIERS.IDTacheJ
                END AS Tache,
                CASE                    
                    WHEN POINTAGE_JOURNALIERS.IDTacheJ <> '20' AND POINTAGE_JOURNALIERS.TacheRealisee = '6' THEN 'Pose 6-18'
                    WHEN POINTAGE_JOURNALIERS.IDTacheJ = '20' AND POINTAGE_JOURNALIERS.datepointage IN ('20260802', '20260803', '20260809') THEN 'Dim/Férié'
                    ELSE 'Pose 6-14 ou 7-15'
                END AS Pose, JOURNALIERS.NomJournalier
                FROM [POINTAGE_JOURNALIERS] INNER JOIN JOURNALIERS ON POINTAGE_JOURNALIERS.Matricule = JOURNALIERS.Matricule
                WHERE POINTAGE_JOURNALIERS.IDAnnee = '2026'
                AND JOURNALIERS.IDAnnee = '2026'
                AND POINTAGE_JOURNALIERS.IDEquipeJ IN ('14', '22', '23', '21', '24', '28', '30', '33', '38' )
                AND POINTAGE_JOURNALIERS.Matricule LIKE 'JJ%'             
                AND POINTAGE_JOURNALIERS.DateDebutDecade = '20260801'";
 }
}



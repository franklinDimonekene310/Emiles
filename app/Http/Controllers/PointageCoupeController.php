<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use DB;

use Illuminate\Http\Request;

class PointageCoupeController extends Controller
{
    //
    public function genererFichierPointageCoupe() {
        // Role : 1. Recuperation des pointages dans la table D_POINTAGE_DECADAIRE, 2. Récupération des équipes dans la table POINTAGE_JOURNALIERS
        // Objectif : Générer un fichier Excel pour le traitement de l'insertion dans la table POINTAGE_JOURNALIERS
        // Destination : les données sont envoyées à la CNSS 
        // contraintes : IDTypePaie = '01', DateCalcul = si le calcul se fait au mois de la paie concerné on considere la date du calcul
        // si le calcul se fait au moins prochain, on prend la plage entre 25 du mois de la paie concerné et la date à laquelle le calcul se fait.
        // si le pointage d'un employé excede 26 jours, on remet à 26 jours. Le pointage doit etre un entier.

    // récupération des pointages
       /* $pointages = DB::connection('hfsql_personnel')
        ->table('D_POINTAGE_DECADAIRE')
        ->select(
            'Matricule',
            'DatePointage',
            'IDPointage',
            'IDTache',
            'Matricule_DateDebutDecade'
        )
        ->where('DatePointage', '>=', '20260620')
        ->where('DatePointage', '<=', '20260629')
        ->where('IDPointage', 20)
        ->get();*/
        

        // Récupération des équipes
        $equipes = DB::connection('hfsql_journalier')
        ->table('POINTAGE_JOURNALIERS')
        ->select(
            'DatePointage',
            'IDEquipeJ',
            'Matricule'
        )
        ->where('Matricule', 'NOT LIKE', 'JJ%')
        ->where('DatePointage', '>=', '20260620')
        ->where('DatePointage', '<=', '20260629')  
        ->where('Matricule', '=', '140604')     
        ->get();

        dd($equipes);
        // préparation pour excel
       
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

            // Entêtes
            $sheet->fromArray([
                [
                    'Matricule',
                    'DatePointage',
                    'IDPointage',
                    'IDTache',                   
                    'Matricule_DateDebutDecade'
                ]
            ]);

            $ligne = 2;

            foreach ($pointages as $pointage) {
                // Forcer le type Texte
                $sheet->setCellValueExplicit("A{$ligne}", (string) $pointage->Matricule, Datatype::TYPE_STRING);               
                $sheet->setCellValueExplicit("B{$ligne}", (string) $pointage->DatePointage, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("C{$ligne}", (string) $pointage->IDPointage, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("D{$ligne}", (string) $pointage->IDTache, DataType::TYPE_STRING);                 
                $sheet->setCellValueExplicit("E{$ligne}", (string) $pointage->Matricule_DateDebutDecade, DataType::TYPE_STRING);

                $ligne++;
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/PointageDecadaire.xlsx'));
        dd('fait');
    }

    public function copiePointageCoupe() {

    }
}

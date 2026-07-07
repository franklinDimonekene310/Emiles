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
        // contraintes : DatePointage et IDPointage

        $debutDecade = '20260620';
        $finDecade = '20260629';

    // Récupération des pointages
        $pointages = DB::connection('hfsql_personnel')
        ->table('D_POINTAGE_DECADAIRE')
        ->select(
            'Matricule',
            'DatePointage',
            'IDPointage',
            'IDTache'            
        )
        ->where('DatePointage', '>=', $debutDecade)
        ->where('DatePointage', '<=', $finDecade)
        ->where('IDPointage', 20)       
        ->get();
        
       
        // Récupération des équipes
        $equipes = DB::connection('hfsql_journalier')
        ->table('POINTAGE_JOURNALIERS')
        ->select(
            'Matricule',
            'DatePointage',
            'IDEquipeJ'            
        )
        ->where('Matricule', 'NOT LIKE', 'JJ%')
        ->where('DatePointage', '>=', $debutDecade)
        ->where('DatePointage', '<=', $finDecade)                     
        ->get();

       
        // Régrouper collections equipes par Matricule et par date
        // $equipesParMatricule = $equipes->groupBy('Matricule');   Regrouper seulement par matricule
        $matriculeDateEquipes = [];
        foreach ($equipes as $equipe) {
            $matriculeDateEquipes[$equipe->Matricule][$equipe->DatePointage] = $equipe->IDEquipeJ;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Pointages Coupe');

            // Entêtes Fichier Excel
            $sheet->fromArray([
                [
                    'IDEquipeJ',
                    'Matricule',
                    'DatePointage',
                    'IDPointage',
                    'IDTache'                     
                ]
            ]);

       // Récupération des codeEquipe dans la collections Equipes et preparation Fichier Excel
        $ligne = 2;
        foreach ($pointages as $pointage) {

            $idEquipe = $matriculeDateEquipes[$pointage->Matricule][$pointage->DatePointage] ?? null;

            if ($idEquipe !== null) {
                // traitement
                 // Forcer le type Texte
                $sheet->setCellValueExplicit("A{$ligne}", (string) $idEquipe, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("B{$ligne}", (string) $pointage->Matricule, Datatype::TYPE_STRING);               
                $sheet->setCellValueExplicit("C{$ligne}", (string) $pointage->DatePointage, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("D{$ligne}", (string) $pointage->IDPointage, DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("E{$ligne}", (string) $pointage->IDTache, DataType::TYPE_STRING);                 

                $ligne++;
            }
        }
        
        $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(10);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/PointageDecadaire.xlsx'));      
       
        dd('Fichier généré avec succès');
    }

    public function copiePointageCoupe() {

    }
}

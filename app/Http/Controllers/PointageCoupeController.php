<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use DB;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PointageCoupeController extends Controller
{
    //
    public function genererFichierPointageCoupe(Request $request) {
        // Role : 1. Recuperation des pointages dans la table D_POINTAGE_DECADAIRE, 2. Récupération des équipes dans la table POINTAGE_JOURNALIERS
        // Objectif : Générer un fichier Excel pour le traitement de l'insertion dans la table POINTAGE_JOURNALIERS
        // Destination : les données sont envoyées à la CNSS 
        // contraintes : DatePointage et IDPointage

        $dateDebutDecade = Carbon::parse($request->debutDecade); $dateFinDecade = Carbon::parse($request->finDecade);
        
        if (!$dateFinDecade->gte($dateDebutDecade)) {
            return back()->withErrors([ 'finDecade'=> 'La date de fin doit être supérieure ou égale à la date du début.']);
        }
       
        $dateDebutDecade  = $dateDebutDecade->format('Ymd') ;
        $dateFinDecade  = $dateFinDecade->format('Ymd') ;

        // Récupération des pointages
        $pointages = DB::connection('hfsql_personnel')
        ->table('D_POINTAGE_DECADAIRE')
        ->select(
            'Matricule',
            'DatePointage',
            'IDPointage',
            'IDTache'            
        )
        ->where('DatePointage', '>=', $dateDebutDecade)
        ->where('DatePointage', '<=', $dateFinDecade)
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
        ->where('DatePointage', '>=', $dateDebutDecade)
        ->where('DatePointage', '<=', $dateFinDecade)                     
        ->get();

       
        // $equipesParMatricule = $equipes->groupBy('Matricule');   Regrouper seulement par matricule
        // Régrouper collections equipes par Matricule et par date
        $matriculeDateEquipes = [];
        foreach ($equipes as $equipe) {
            $matriculeDateEquipes[$equipe->Matricule][$equipe->DatePointage] = $equipe->IDEquipeJ;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Pointages Coupe');

         $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(10);

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
        
       

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/PointageDecadaire.xlsx'));      
       
        dd('Fichier généré avec succès');
    }

    public function copiePointageCoupe() {

    }
}

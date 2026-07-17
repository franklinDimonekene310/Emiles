<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PointageCoupeController extends Controller
{
    //
    public function genererFichierPointageCoupe(Request $request) {
        // Role : 1. Recuperation des pointages dans la table D_POINTAGE_DECADAIRE, 2. Récupération des équipes dans la table POINTAGE_JOURNALIERS
        // Objectif : Générer un fichier Excel pour le traitement de l'insertion dans la table POINTAGE_JOURNALIERS       
        // contraintes : DatePointage et IDPointage        
        
        $fichierDeBase = $this->genererTableauDeBase($request);       
        
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
        foreach ($fichierDeBase as $pointage) {          

                 // Forcer le type Texte
                $sheet->setCellValueExplicit("A{$ligne}", (string) $pointage['IDEquipeJ'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("B{$ligne}", (string) $pointage['Matricule'], Datatype::TYPE_STRING);               
                $sheet->setCellValueExplicit("C{$ligne}", (string) $pointage['DatePointage'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("D{$ligne}", (string) $pointage['IDPointage'], DataType::TYPE_STRING);
                $sheet->setCellValueExplicit("E{$ligne}", (string) $pointage['IDTacheJ'], DataType::TYPE_STRING);               

                $ligne++;
          
        }
      
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/PointageDecadaire.xlsx'));      
       
        dd('Fichier généré avec succès');
    }

    public function misAJourPointageCoupe(Request $request) {
        // Role : 1 A partir du fichier Excel, actualiser les pointages dans la table POINTAGE_JOURNALIERS
        // Objectif : Mis à jour des colonnes POINTAGE_JOURNALIERS.IDTacheJ et  POINTAGE_JOURNALIERS.TacheRealisee de la table POINTAGE_JOURNALIERS         
        // Contraintes : EquipeJ, Matricule, DatePointage
/*
         $fichierDeBase = $this->genererTableauDeBase($request);
        

         foreach ($fichierDeBase as $pointage) {          

                DB::connection('hfsql_journalier')
                ->table('POINTAGE_JOURNALIERS')       
                ->where('IDEquipeJ', $pointage['IDEquipeJ'])       
                ->where('Matricule', $pointage['Matricule'])
                ->where('DatePointage', $pointage['DatePointage'])                            
                ->update(['IDTacheJ'=> intval($pointage['IDPointage']), 'TacheRealisee' => $pointage['IDTacheJ']]);
          
        }

        dd('succès ff');*/

        //$fichierDeBase = $this->genererTableauDeBase($request);

        $connexion = DB::connection('hfsql_journalier');

        $connexion->beginTransaction();

        try {

            foreach ($fichierDeBase as $pointage) {

                $nbLignes = $connexion
                    ->table('POINTAGE_JOURNALIERS')
                    ->where('IDEquipeJ', $pointage['IDEquipeJ'])
                    ->where('Matricule', $pointage['Matricule'])
                    ->where('DatePointage', $pointage['DatePointage'])
                    ->update([
                        'IDTacheJ65'      => (int) $pointage['IDPointage'],
                        'TacheRealisee65' => $pointage['IDTacheJ'],
                    ]);

                // Facultatif : vérifier qu'une ligne a bien été mise à jour
                if ($nbLignes === 0) {
                    throw new \Exception(
                        "Aucune ligne trouvée pour le matricule {$pointage['Matricule']} à la date {$pointage['DatePointage']}."
                    );
                }
            }

            $connexion->commit();

            return redirect()->back()->with('success', 'Mise à jour effectuée avec succès.');

        } catch (\Throwable $e) {

            $connexion->rollBack();

            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors de la mise à jour des pointages', [
                'message' => $e->getMessage(),
                'ligne'   => $e->getLine(),
                'fichier' => $e->getFile(),
            ]);

            return redirect()->back()->withErrors([
                'erreur' => "Une erreur est survenue : {$e->getMessage()}"
            ]);
        }

    }

    private function genererTableauDeBase(Request $request) {        
       // Role : générer un tableau ('IDEquipeJ', 'Matricule', 'DatePointage', 'IDPointage', 'IDTacheJ') à partir du pointage coupe
       // tables concernées : D_POINTAGE_DECADAIRE ET POINTAGE_JOURNALIER
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
        ->where('Matricule', '140640')
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

        // Régrouper collections equipes par Matricule et par date
        $matriculeDateEquipes = [];
        foreach ($equipes as $equipe) {
            $matriculeDateEquipes[$equipe->Matricule][$equipe->DatePointage] = $equipe->IDEquipeJ;
        }

        // Récupération des codeEquipe dans la collections Equipes et preparation Collection de base : IDEquipeJ, Matricule, DatePointage, IDPointage, IDTacheJ
        $fichierDeBase=[];
        foreach ($pointages as $pointage) {

            $idEquipe = $matriculeDateEquipes[$pointage->Matricule][$pointage->DatePointage] ?? null;

            if ($idEquipe !== null) {
                
                $fichierDeBase[]=[
                    'IDEquipeJ'=>$idEquipe, 'Matricule'=>$pointage->Matricule, 'DatePointage'=>$pointage->DatePointage, 'IDPointage'=>$pointage->IDPointage, 'IDTacheJ'=>$pointage->IDTache
                ];
            }
        }

        return $fichierDeBase;
    }

    
}

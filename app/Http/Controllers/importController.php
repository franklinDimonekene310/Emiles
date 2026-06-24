<?php

namespace App\Http\Controllers;

use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use DB;

use Illuminate\Http\Request;

class importController extends Controller
{
    //
    public function readExcelFile()
    {
        //$path = 'C:\Users\B.NIMI\Desktop\DIVERS - Copie\Cotisation Cnss.xlsx';
        //$path = 'C:\Users\HP\Downloads\ANNEXE CNSS AOUT 2024 BRUT.xlsx';
        $path = public_path('Cotisation Cnss.xlsx');
        
        $privileges = (new FastExcel)->sheet(1)->import($path);

        $cnn = [];
        $nomBrut = [];
        foreach ($privileges as $privilege) {
            
            $nomBrut = $this->decouperNom($privilege['Nom']);

            $cnn[] = [
                'NUMERO INSS' => $privilege['NUMERO INSS'],
                'Matricule' => $privilege['Matricule'],
                'Nom' => $nomBrut['nom'],
                'Post noms' =>   $nomBrut['postnom'],
                'Prenom' =>  $nomBrut['prenom'],
                'Type travailleur(1=Travailleur , 2=Assimile)' => '',
                'Commune  ou Territoire affectation' => (trim($privilege['LIBELLE SITE']) === 'KWILU-NGONGO') ? "MBANZA-NGUNGU" : "GOMBE",
                'Montant Cotise' => $privilege['COTISATION INSS'],
                'Nbre De Jours de travail' => '26',
                'Nbre De heure de travail' => '',
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
                 'IPR' => '',
            ];
        }
                      
       //(new FastExcel($cnn))->export(public_path('CNN TRAITE.xlsx'));

        /// Mise en forme avec phpSpread
            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            // Écriture des données
            $headers = array_keys($cnn[0]);
            $rows = array_map('array_values', $cnn);

            $sheet->fromArray($headers, null, 'A1');
            $sheet->fromArray($rows, null, 'A2');
            
            // Appliquer Arial 10 à toute la feuille
            $sheet->getStyle('A:Z')->applyFromArray([
                'font' => [
                    'name' => 'Arial',
                    'size' => 10,
                ],
            ]);

            $writer = new Xlsx($spreadsheet);
            $writer->save(public_path('CNN TRAITE.xlsx'));
            dd('fait');
    }

    private  function decouperNom($nomBrut)
    {
        // Nettoyage
        $nomBrut = trim($nomBrut);
        $nomBrut = preg_replace('/\s+/', ' ', $nomBrut);

        $mots = explode(' ', $nomBrut);
        $nb = count($mots);

        $nom = '';
        $postnom = '';
        $prenom = '';

        switch ($nb) {

            case 1:
                $nom = $mots[0];
                break;

            case 2:
                $nom = $mots[0];
                $postnom = $mots[1];
                break;

            case 3:

                // Exemple : MANSIANTIMA MPUNANI 1
                if (is_numeric($mots[2])) {
                    $nom = $mots[0];
                    $postnom = $mots[1] . ' ' . $mots[2];                  
                }
                elseif (in_array($mots[1], ['A', 'YE', 'WA', 'NE', 'DI'])) {
                    $nom = $mots[0];
                    $postnom = $mots[1] . ' ' . $mots[2];                    
                }
                else {
                    $nom = $mots[0];
                    $postnom = $mots[1];
                    $prenom = $mots[2];
                }
                break;

            case 4:

                // Exemple : IBUBA NTON - AYOM
                if ($mots[2] === '-') {
                    $nom = $mots[0];
                    $postnom = $mots[1] . ' ' . $mots[2] . ' ' . $mots[3];
                } else {
                    $nom = $mots[0];
                    $postnom = $mots[1];
                    $prenom = $mots[2] . ' ' . $mots[3];
                }

                break;

            default:

                // Cas général : plus de 4 mots
                $nom = $mots[0];
                $postnom = $mots[1];

                if ($nb > 2) {
                    $prenom = implode(' ', array_slice($mots, 2));
                }
        }

        return [
            'nom' => $nom,
            'postnom' => $postnom,
            'prenom' => $prenom
        ];
    }

    public function getPointage(){
        // Role : Recuperation pointage dans la table D_RESULTATS_PAIE 
        // contraintes : IDTypePaie = '01', DateCalcul = si le calcul se fait au mois de la paie concerné on considere la date du calcul
        // si le calcul se fait au moins prochain, on prend la plage entre 25 du mois de la paie concerné et la date à laquelle le calcul se fait.
        // si le pointage d'un employé excede 26 jours, on remet à 26 jours. Le pointage doit etre un entier.

        $matricule = [
               '142229'
           ];

        $matriculeTraites = [];
        $matriculeNonTraites = [];

         // $emp = DB::table('employes')->whereIn('Matricule', $matricule)->get('Matricule')->toArray();
         // dd('Code utilisé pour copie des employés de HFSQL vers PostGres');
        $employesHFSQL = DB::connection('hfsql')->table('EMPLOYES')
        ->whereIn('Matricule', $matricule)->get();

        dd($employesHFSQL);
    }
}

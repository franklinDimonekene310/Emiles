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
        // Nettoy_160
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

    public function getPoint_160(){
        // Role : Recuperation point_160 dans la table D_RESULTATS_PAIE 
        // contraintes : IDTypePaie = '01', DateCalcul = si le calcul se fait au mois de la paie concerné on considere la date du calcul
        // si le calcul se fait au moins prochain, on prend la pl_160 entre 25 du mois de la paie concerné et la date à laquelle le calcul se fait.
        // si le point_160 d'un employé excede 26 jours, on remet à 26 jours. Le point_160 doit etre un entier.

        $matricule = [
               'KWILU BRIQUES'
           ];
       
        $matriculeTraites = [];
        $matriculeNonTraites = [];

         // $emp = DB::table('employes')->whereIn('Matricule', $matricule)->get('Matricule')->toArray();
         // dd('Code utilisé pour copie des employés de HFSQL vers PostGres');
        $employesHFSQL = DB::connection('hfsql')->table('Societe')
        ->get();

        dd($employesHFSQL);
/*
        $books = DB::connection('hfsql')
            ->table('Societe')
            ->where('RaisonSociale', 'KWILU BRIQUES')
            ->get();

            dd($books);*/

    }


    public function createSqlRequete() {
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\heure_employes.xlsx';
       // $path = public_path('Cotisation Cnss.xlsx');        
        $lignes = (new FastExcel)->sheet(1)->import($path);

        $case_160 = [];
        $case_200 = [];
        $matricules = [];

        foreach ($lignes as $ligne) {

            // On conserve exactement la valeur du fichier Excel
            // $matricule = $ligne['matricule'];
            $matricule = str_pad($ligne['matricule'], 6, ' ', STR_PAD_LEFT);

            $_160 = (int) $ligne['_160'];
            $_200 = (int) $ligne['_200'];

            $case_160[] = "WHEN '{$matricule}' THEN {$_160}";
            $case_200[] = "WHEN '{$matricule}' THEN {$_200}";

            $matricules[] = "'{$matricule}'";
        }

        $sql = "
            UPDATE HS_MENSUEL
            SET
                NbreHS160 = NbreHS160 + CASE Matricule
                    " . implode("\n        ", $case_160) . "
                    ELSE 0
                END,

                NbreHS200 = NbreHS200 + CASE Matricule
                    " . implode("\n        ", $case_200) . "
                    ELSE 0
                END

            WHERE Matricule IN (" . implode(',', $matricules) . ")
            AND AnneeMoisHS = '202606'
            AND DateCreationHS = '20260629';
            ";
        dd($sql);
    }

    public function insertHS() {

        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\heure_employes.xlsx';
       // $path = public_path('Cotisation Cnss.xlsx');        
        $lignes = (new FastExcel)->sheet(4)->import($path);

        $case_160 = [];
        $case_200 = [];
        $matricules = [];

        $insertValues = [];

        foreach ($lignes as $ligne) {

            $matricule = str_pad($ligne['matricule'], 6, ' ', STR_PAD_LEFT);

            $hs160 = (float) $ligne['_160'];
            $hs200 = (float) $ligne['_200'];

            $matriculeAnneeMois = $matricule . ',202606';

            $insertValues[] = "(
                '{$matricule}',
                '202606',
                0,
                0,
                0,
                0,
                {$hs160},
                {$hs200},
                0,
                '20260629',
                '{$matriculeAnneeMois}'
            )";
        }


        $sqlInsert = "
            INSERT INTO HS_MENSUEL
            (
                Matricule,
                AnneeMoisHS,
                NbreHS35,
                NbreHS37_5,
                NbreHS100,
                NbreHS130,
                NbreHS160,
                NbreHS200,
                CodeTraitHsMens,
                DateCreationHS,
                Matricule_AnneeMois
            )
            VALUES
            " . implode(",\n", $insertValues) . ";
            ";
            dd($sqlInsert);
            //DB::statement($sqlInsert);
    }
}

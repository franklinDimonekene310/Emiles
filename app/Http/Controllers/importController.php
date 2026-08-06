<?php

namespace App\Http\Controllers;

use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use DB;

use Illuminate\Http\Request;

class importController extends Controller
{
    //
    public function fichierCnss()
    {
        // ROLE : produire un fichier excel contenant des informations à envoyer à la CNSS pour une paie donnée
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\COTISATION CNSS.xlsx';      
        
        // dd($path, file_exists($path), is_file($path), is_readable($path));

        $privileges = (new FastExcel)->sheet(2)->import($path);
        
        $jourCnn = $this->jourCnn();        
        $iprCnn = $this->iprCnn();       
        
        $cnn = [];
        $nomBrut = [];
      
        foreach ($privileges as $privilege) {
            
            $nomBrut = $this->decouperNom($privilege['Nom']);

            $cnn[] = [                
                'NUMERO INSS' => $privilege['TypePaie'] != '06' ? ($privilege['NUMERO INSS'] ?? null) : null,               
                'Matricule' => $privilege['Matricule'],
                'Nom' => $nomBrut['nom'],
                'Post noms' =>   $nomBrut['postnom'],
                'Prenom' =>  $nomBrut['prenom'],
                'Type travailleur(1=Travailleur , 2=Assimile)' => '',
                'Commune  ou Territoire affectation' => (trim($privilege['LIBELLE SITE']) === 'KWILU-NGONGO') ? "MBANZA-NGUNGU" : "GOMBE",
                'Période Cotisee (jj/mm/aaaa)' => '01/07/2026',
                'Montant Cotise' => $privilege['COTISATION INSS'],
                'Nbre De Jours de travail' => $privilege['TypePaie'] != '06' ? ($jourCnn[$privilege['Matricule']] ?? null) : null,
                'Nbre De heure de travail' => "",
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
                'IPR' => $iprCnn[$privilege['Matricule']][$privilege['TypePaie']],
                'Libellé' => $privilege['Libellé Paie']
            ];            
        }

        //$this->sommerTypepaie($cnn);
                      
        (new FastExcel($cnn))->export(public_path('CNN TRAITE.xlsx'));
dd('fait');
            // Mise en forme avec phpSpread
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
    
    private function sommerTypepaie($cnn) {
        // Role : sommer tout type des paie sauf le décompte final
        
        // filtrage des matricules multiples
        $tableauFiltre = collect($cnn)
        ->groupBy('Matricule')
        ->filter(function ($lignes) {
            return $lignes->count() >= 2;
            })
            ->flatten(1);
          // dd($tableauFiltre); 
        // Regrouper 

        $tableau2 = $tableauFiltre
            ->groupBy('Matricule')
            ->flatMap(function ($lignes, $matricule) {

                $resultat = collect();

                // Somme de tous les types sauf DECOMPTE FINAL
                $montantFusion = $lignes
                    ->where('TypePaie', '!=', 'DECOMPTE FINAL')
                    ->sum('Montant Cotise');

                if ($montantFusion > 0) {

                    $resultat->push([
                        'Matricule' => $matricule,
                        'Montant Cotise' => $montantFusion,
                        'TypePaie' => 'FUSION'
                    ]);
                }

                // Garder DECOMPTE FINAL tel quel
                $decompte = $lignes
                    ->where('TypePaie', 'DECOMPTE FINAL')
                    ->first();

                if ($decompte) {
                    $resultat->push($decompte);
                }

                return $resultat;
            })
            ->values();

            dd($tableau2);
    }

    private function jourCnn() {
        
               $sql = "
                SELECT
                    T.Matricule,
                    CASE
                        WHEN T.TotalPointage > 26 THEN 26
                        ELSE T.TotalPointage
                    END AS PointageAjuste
                FROM
                (
                    SELECT
                        E_RESULTATS_PAIE.Matricule,
                        SUM(D_RESULTATS_PAIE.Pointage) AS TotalPointage
                    FROM E_RESULTATS_PAIE
                    INNER JOIN D_RESULTATS_PAIE
                        ON E_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie =
                        D_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie
                    WHERE E_RESULTATS_PAIE.AnneeMoisPaie = '202607'
                    AND D_RESULTATS_PAIE.IDRubrique IN
                    ('1101','1102','1103','1104','1105','1106','1107',
                    '1109','1110','1119','1120','1121')
                    GROUP BY E_RESULTATS_PAIE.Matricule
                ) T
                ";    

            return collect(DB::connection('hfsql_personnel')->select($sql))->pluck('PointageAjuste','Matricule');               
    }

    
    private function iprCnn() {
        
               $sql = "
                SELECT
                    E_RESULTATS_PAIE.Matricule,
                    D_RESULTATS_PAIE.IDtypePaie,
                    SUM(D_RESULTATS_PAIE.MontantPaie) AS Ipr
                FROM E_RESULTATS_PAIE
                INNER JOIN D_RESULTATS_PAIE
                    ON E_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie =
                    D_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie
                WHERE E_RESULTATS_PAIE.AnneeMoisPaie = '202607'
                AND D_RESULTATS_PAIE.IDRubrique =
                '1570'
                GROUP BY E_RESULTATS_PAIE.Matricule, D_RESULTATS_PAIE.IDtypePaie
                ";    
            $resultats = DB::connection('hfsql_personnel')->select($sql);
            //  AND E_RESULTATS_PAIE.Matricule IN ('   523', '   539', '   714', ' 75381', ' 79345', '129091')
            $datas = [];

            foreach($resultats as $data) {
                $datas[$data->Matricule][$data->IDtypePaie] = $data->Ipr;
            }

            return $datas;               
    }

    private  function decouperNom($nomBrut)
    {
        // ROLE : formatter le nom de l'employé par un format spéficique
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

    
    public function updateHS() {
        /* Préparer une requete Sql pour la mis à jour des heures supplémentaire
           Les heures supplémentaires sont puisées dans un fichier Excel
        */
          
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\HS JUIL 2026\a modifier.xlsx';
       // $path = public_path('Cotisation Cnss.xlsx');        
        $lignes = (new FastExcel)->sheet(1)->import($path);

        $case_130 = [];
        $case_160 = [];
        $case_200 = [];
        $matricules = [];

        foreach ($lignes as $ligne) {

            // On conserve exactement la valeur du fichier Excel
            // $matricule = $ligne['matricule'];
            $matricule = str_pad($ligne['matricule'], 6, ' ', STR_PAD_LEFT);

            $_130 = (int) $ligne['_130'];
            $_160 = (int) $ligne['_160'];
            $_200 = (int) $ligne['_200'];

            $case_130[] = "WHEN '{$matricule}' THEN {$_130}";
            $case_160[] = "WHEN '{$matricule}' THEN {$_160}";
            $case_200[] = "WHEN '{$matricule}' THEN {$_200}";

            $matricules[] = "'{$matricule}'";
        }

        $sql = "
            UPDATE HS_MENSUEL
            SET
                NbreHS130 = NbreHS130 + CASE Matricule
                    " . implode("\n        ", $case_130) . "
                    ELSE 0
                END,

                NbreHS160 = NbreHS160 + CASE Matricule
                    " . implode("\n        ", $case_160) . "
                    ELSE 0
                END,

                NbreHS200 = NbreHS200 + CASE Matricule
                    " . implode("\n        ", $case_200) . "
                    ELSE 0
                END

            WHERE Matricule IN (" . implode(',', $matricules) . ")
            AND AnneeMoisHS = '202607'
            AND DateCreationHS = '20260731';
            ";

            $nbLignes = DB::connection('hfsql_personnel')
              ->affectingStatement($sql);

            dd('Lignes affectées ' . $nbLignes);
    }

    public function insertHS() {
       
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\HS JUIL 2026\a inserer.xlsx';
       // $path = public_path('Cotisation Cnss.xlsx');        
        $lignes = (new FastExcel)->sheet(1)->import($path);

        $case_130 = [];
        $case_160 = [];
        $case_200 = [];
        $matricules = [];

        $insertValues = [];

        foreach ($lignes as $ligne) {
            
        // Forcer le matricule à avoir 6 caractères
            $matricule = str_pad($ligne['matricule'], 6, ' ', STR_PAD_LEFT);

            $hs130 = (float) $ligne['_130'];
            $hs160 = (float) $ligne['_160'];
            $hs200 = (float) $ligne['_200'];

            // $matriculeAnneeMois = $matricule . ',202607';

            $insertValues[] = "(
                '{$matricule}',
                '202607',
                DEFAULT,
                DEFAULT,
                DEFAULT,
                {$hs130},
                {$hs160},
                {$hs200},
                '0',
                '20260731',
                DEFAULT
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

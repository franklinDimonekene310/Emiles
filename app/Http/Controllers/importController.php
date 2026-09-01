<?php

namespace App\Http\Controllers;

use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use DB;

use Illuminate\Http\Request;

class importController extends Controller
{
    
    public function fichierCnss(Request $request)
    {       
        // ROLE : produire un fichier excel contenant des informations à envoyer à la CNSS pour une paie donnée
        // $path = 'C:\Users\B.NIMI\Desktop\DIVERS\COTISATION CNSS.xlsx';   
        $path = public_path('COTISATION CNSS - Juillet26.xlsx');        
        
        if (!file_exists($path) || !is_file($path) || !is_readable($path)) {
            return redirect()->back()->with('Erreur', 'Fichier Invalide.');
        }

        if (!$request->anneeMois) {
            return redirect()->back()->with('Erreur', 'Année mois invalide.');
        }

        $anneeMois = str_replace('-', '',$request->anneeMois);
        
        $debutMoisAnnee = Carbon::parse($anneeMois.'01')->format('d/m/Y');        

        $jourCnn = $this->jourCnn($anneeMois);        
        $iprCnn = $this->iprCnn($anneeMois); 

        $privileges = (new FastExcel)->sheet(3)->import($path);    
                  
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
                'Période Cotisee (jj/mm/aaaa)' => $debutMoisAnnee,
                'Montant Cotise' => $privilege['COTISATION INSS'],
                'Nbre De Jours de travail' => $privilege['TypePaie'] != '06' ? ($jourCnn[$privilege['Matricule']] ?? 0) : 0,
                'Nbre De heure de travail' => "",
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
                'ALLOC FAM' => $privilege['ALLOC FAM'],
                'IPR' => (float) $iprCnn[$privilege['Matricule']][$privilege['TypePaie']],
                'Libellé Paie' => $privilege['Libellé Paie']
            ];            
        }

            $cnn = $this->sommerTypepaie($cnn); 
       
            // Mise en forme avec phpSpread
            $spreadsheet = new Spreadsheet();           

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Cotis Cnss');

            $spreadsheet->getDefaultStyle()
            ->getFont()
            ->setName('Arial')
            ->setSize(10);

           // Écriture des données
           $data = $cnn->toArray();

            if (!empty($data)) {
                $sheet->fromArray(array_keys($data[0]), null, 'A1');
                $sheet->fromArray(array_map('array_values', $data), null, 'A2');
            }           

            // Mettre les en-têtes en gras
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')
            ->getFont()
            ->setBold(true);

            $writer = new Xlsx($spreadsheet);
            $writer->save(public_path('CNN TRAITE03.xlsx'));
           
            dd('fait');
    }
    
    public function sommerTypepaie($collection) {
            
        // Role : sommer tout type des paie par matricule sauf le décompte final          
            
        // filtrage des matricules multiples
        $resultat = collect($collection)
            ->groupBy('Matricule')
            ->flatMap(function ($lignes) {

                [$fusionnables, $decomptes] = $lignes->partition(function ($ligne) {
                    return trim($ligne['Libellé Paie']) !== 'DECOMPTE FINAL';
                });

                $resultat = collect();

                if ($fusionnables->count() > 1) {
                    
                    $ligne = $fusionnables->first();                   

                    foreach (['Montant Brut Imposable', 'ALLOC FAM', 'Montant Cotise', 'IPR'] as $colonne) {
                        $ligne[$colonne] = $fusionnables->sum(fn($item) => (float) ($item[$colonne] ?: 0));
                    }

                    $ligne['Libellé Paie'] = 'Fusion';

                    $resultat->push($ligne);

                } elseif ($fusionnables->count() == 1) {

                    $resultat->push($fusionnables->first());
                }

                return $resultat->concat($decomptes->values());
            })
            ->values();          
            // Regrouper 
            return $resultat;            
    }

    private function jourCnn($anneeMois) {
                  
               $sql = "
                SELECT
                    T.Matricule,
                    CASE
                        WHEN CEIL(T.TotalPointage) > 26 THEN 26
                        ELSE CEIL(T.TotalPointage)
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
                    WHERE E_RESULTATS_PAIE.AnneeMoisPaie = '". $anneeMois. "'
                    AND D_RESULTATS_PAIE.IDRubrique IN
                    ('1101','1102','1103','1104','1105','1106','1107',
                    '1109','1110','1119','1120','1121')
                    GROUP BY E_RESULTATS_PAIE.Matricule
                ) T
                ";    
            
            return collect(DB::connection('hfsql_personnel')->select($sql))->pluck('PointageAjuste','Matricule');               
                         
    }

    
    private function iprCnn($anneeMois) {
        
               $sql = "
                SELECT
                    E_RESULTATS_PAIE.Matricule,
                    D_RESULTATS_PAIE.IDtypePaie,
                    SUM(D_RESULTATS_PAIE.MontantPaie) AS Ipr
                FROM E_RESULTATS_PAIE
                INNER JOIN D_RESULTATS_PAIE
                    ON E_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie =
                    D_RESULTATS_PAIE.Matricule_Date_Heure_TypePaie
                WHERE E_RESULTATS_PAIE.AnneeMoisPaie = '". $anneeMois ."'
                AND D_RESULTATS_PAIE.IDRubrique =
                '1570'
                GROUP BY E_RESULTATS_PAIE.Matricule, D_RESULTATS_PAIE.IDtypePaie
                ";    
            $resultats = DB::connection('hfsql_personnel')->select($sql);
           
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
          
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\HEURES SUP\HS AOUT 2026\A modifier initial.xlsx';
       // $path = public_path('Cotisation Cnss.xlsx');        
        $lignes = (new FastExcel)->sheet(1)->import($path);

        $case_130 = [];
        $case_160 = [];
        $case_200 = [];
        $matricules = [];

        $anneeMoisPaie = '202608';
        $dateCreation = '20260831';

        foreach ($lignes as $ligne) {

            // On conserve exactement la valeur du fichier Excel            
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
            AND AnneeMoisHS = '{$anneeMoisPaie}'
            AND DateCreationHS = '{$dateCreation}';
            ";

            dd($sql);

            // $nbLignes = DB::connection('hfsql_personnel')->affectingStatement($sql);

            // dd('Lignes affectées ' . $nbLignes);
    }

    public function insertHS() {
       
        $path = 'C:\Users\B.NIMI\Desktop\DIVERS\HEURES SUP\HS AOUT 2026\A insérer.xlsx';
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
                '202608',
                DEFAULT,
                DEFAULT,
                DEFAULT,
                {$hs130},
                {$hs160},
                {$hs200},
                '0',
                '20260831',
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
           // DB::connection('hfsql_personnel')->insert
           // DB::connection('hfsql_personnel')->insert($sqlInsert);

           dd("insertion effectuée");
    }


    public function test () {
           // Fonction partition de Laravel
            $nourritures = [
                ['name' => 'tomate', 'category'=> 'fruit'], ['name' => 'mangue', 'category'=> 'fruit'] , ['name' => 'banane', 'category'=> 'fruit'],
                ['name' => 'croissant', 'category'=> 'patisserie'], ['name' => 'pain', 'category'=> 'patisserie'] , ['name' => 'biscuit', 'category'=> 'patisserie']
            ];
        
            [$fruits, $patisserie] = collect($nourritures)->partition(function ($nourriture) {
                return $nourriture['category'] == 'fruit';
            });

            dd($fruits, $patisserie);
    }
}

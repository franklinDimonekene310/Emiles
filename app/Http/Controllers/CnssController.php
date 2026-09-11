<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Carbon\Carbon;
use DB;

// Cette classe traite des opérations liées à la cnss
class CnssController extends Controller
{
    //
    public function fichierCnss(Request $request){
        // ROLE : produire un fichier excel contenant des informations à envoyer à la CNSS pour une paie donnée
        // $path = 'C:\Users\B.NIMI\Desktop\DIVERS\COTISATION CNSS.xlsx';   
        $path = public_path('COTISATION CNSS AOUT.xlsx');       
        
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

        $privileges = (new FastExcel)->sheet(2)->import($path);    
              
        $cnss = [];
        $nomBrut = [];
      
        foreach ($privileges as $privilege) {
            
            $nomBrut = $this->decouperNom($privilege['Nom']);
            $libellePaie = trim($privilege['Libellé Paie']);

            $cnss[] = [                
                //'NUMERO INSS' => $privilege['TypePaie'] != '06' ? ($privilege['NUMERO INSS'] ?? null) : null,               
                'NUMERO INSS' => $libellePaie !== 'DECOMPTE FINAL' ? ($privilege['NUMERO INSS'] ?? null) : null,               
                'Matricule' => (int) $privilege['Matricule'],
                'Nom' => $nomBrut['nom'],
                'Post noms' =>   $nomBrut['postnom'],
                'Prenom' =>  $nomBrut['prenom'],
                'Type travailleur(1=Travailleur , 2=Assimile)' => '',
                'Commune  ou Territoire affectation' => (trim($privilege['LIBELLE SITE']) === 'KWILU-NGONGO') ? "MBANZA-NGUNGU" : "GOMBE",
                'Période Cotisee (jj/mm/aaaa)' => $debutMoisAnnee,
                'Montant Cotise' => $privilege['COTISATION INSS'],
                //'Nbre De Jours de travail' => $privilege['TypePaie'] != '06' ? ($jourCnn[$privilege['Matricule']] ?? 0) : 0,
                'Nbre De Jours de travail' => $libellePaie !== 'DECOMPTE FINAL' ? ($jourCnn[$privilege['Matricule']] ?? 0) : 0,
                'Nbre De heure de travail' => "",
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
                'ALLOC FAM' => $privilege['ALLOC FAM'],
                'IPR' => (float) $iprCnn[$privilege['Matricule']][$privilege['TypePaie']],
                'Libellé Paie' => $privilege['Libellé Paie']
            ];            
        }

        $cnss = $this->sommerTypepaie($cnss); 
        
        // Mise en forme avec phpSpread
        $spreadsheet = new Spreadsheet();           
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cotis Cnss '.$anneeMois);
        
        $spreadsheet->getDefaultStyle()
        ->getFont()
        ->setName('Arial')
        ->setSize(10);
        
        // Écriture des données
        $data = $cnss->toArray();        
        
        if (!empty($data)) {
            $sheet->fromArray(array_keys($data[0]), null, 'A1');
            $sheet->fromArray(array_map('array_values', $data), null, 'A2');

            // Colonne NUMERO INSS = colonne A
            $sheet->getStyle('A:A')
                  ->getNumberFormat()
                  ->setFormatCode('@');

            // Réappliquer les valeurs de la colonne A comme texte
            foreach ($data as $index => $row) {

                // Traitement du NUMERO INSS
                $numeroInss = $row['NUMERO INSS'] ?? '';

                $sheet->setCellValueExplicit(
                    'A' . ($index + 2),
                    (string) $numeroInss,
                    DataType::TYPE_STRING
                );

                // Traitement des nbre de Jours de travail
                $jours = $row['Nbre De Jours de travail'] ?? '';

                $sheet->setCellValueExplicit(
                    'J' . ($index + 2),
                    (string) $jours,
                    DataType::TYPE_NUMERIC
                );

                // Traitement des matricules
                $matricule = $row['Matricule'] ?? '';

                $sheet->setCellValueExplicit(
                    'B' . ($index + 2),
                    (string) $matricule,
                    DataType::TYPE_NUMERIC
                );
            }
        }           

            // Mettre les en-têtes en gras
            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')
            ->getFont()
            ->setBold(true);
            $writer = new Xlsx($spreadsheet);
           
            $writer->save(public_path('CNN_TRAITE_'.$anneeMois.'.xlsx'));
           
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
        // ROLE : Formatter le nom de l'employé par un format spéficique
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

}

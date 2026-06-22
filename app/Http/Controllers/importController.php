<?php

namespace App\Http\Controllers;

use Rap2hpoutre\FastExcel\FastExcel;
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
            /*$nomBrut = explode(" ", trim($privilege['Nom']));
            $prenom = null;

            if (count($nomBrut) === 3 && !trim($nomBrut[1])) {
                $postnom = trim($nomBrut[2]);
            } else {
                $postnom = trim($nomBrut[1]);
                $prenom = $nomBrut[2] ?? null;
            }*/
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
            // 'Nbre De Jours de travail' => '',
            // 'Nbre De heure de travail' => '',
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
            // 'IPR'][] '',
            ];
        }
              
        //(new FastExcel($cnn))->export('C:\Users\HP\Downloads\CNN TRAITE.xlsx');
        (new FastExcel($cnn))->export(public_path('CNN TRAITE.xlsx'));
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
}

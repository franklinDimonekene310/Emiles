<?php

namespace App\Http\Controllers;

use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;

class importController extends Controller
{
    //
        public function readExcelFile()
        {
            $path = 'C:\Users\B.NIMI\Desktop\DIVERS - Copie\Cotisation Cnss.xlsx';

            $privileges = (new FastExcel)->sheet(1)->import($path);
           
            $cnn = [];
           
            foreach ($privileges as $privilege) {
                $nomBrut = explode(" ", trim($privilege['Nom']));
                $prenom = null;

                if (count($nomBrut) === 3 && !trim($nomBrut[1])) {
                    $postnom = trim($nomBrut[2]);
                } else {
                    $postnom = trim($nomBrut[1]);
                    $prenom = $nomBrut[2] ?? null;
                }

                $cnn['NUMERO INSS'][] = $privilege['NUMERO INSS'];
                $cnn['Matricule'][] = $privilege['Matricule'];
                $cnn['Nom'][] = $nomBrut[0];
                $cnn['Post noms'][] =  $postnom;
                $cnn['Prenoms'][] = $prenom;
                $cnn['Type travailleur(1=Travailleur , 2=Assimile)'][] = '';
                $cnn['Commune  ou Territoire affectation'][] = $privilege['LIBELLE SITE'];
                $cnn['Montant Cotise'][] = $privilege['COTISATION INSS'];
                $cnn['Nbre De Jours de travail'][] = '';
                $cnn['Nbre De heure de travail'][] = '';
                $cnn['Montant Brut Imposable'][] = $privilege['BRUT INSS'];
                $cnn['IPR'][] = '';              
                    
            }

            dd($cnn);
        }
        
}

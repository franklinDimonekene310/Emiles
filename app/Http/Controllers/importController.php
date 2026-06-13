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
           
           $cnn = [][];
            
            foreach ($privileges as $privilege) {
                
                DB::table('privileges')->insert([
                        'name' => $privilege['libelle'],
                        'url' =>  $privilege['url'],
                        'description' =>  $privilege['description'],
                        'type' =>  $privilege['type'],
                        'enable' => true,
                        'application_id' => 15,
                        'created_at'=> now(),
                        'updated_at'=> now(),
                    
                ]);*/
                "SITE" => "01"
      "Matricule" => "   523"
      "Nom" => "BUETO WANGUAKU            "
      "NUMERO INSS" => "11965344372A"
      "LIBELLE SITE" => "KWILU-NGONGO"
      "CATEGORIE" => "01"
      "LIBELLE CATEGORIE" => "PERSONNEL D'EXECUTION"
      "BRUT INSS" => 541980.2
      "ALLOC FAM" => 8100
      "COTISATION INSS" => 27099.01
      "Libellé Paie" => "PAIE NORMALE"

                $cnn = ['NUMERO INSS'][];
                $cnn = ['Matricule'][];
                $cnn = ['Nom'][];
                $cnn = ['Post noms'][];
                $cnn = ['Prenoms'][];
                $cnn = ['Type travailleur(1=Travailleur , 2=Assimile)'][];
                $cnn = ['Commune  ou Territoire affectation'][];
                $cnn = ['Montant Cotise'][];
                $cnn = ['Nbre De Jours de travail'][];
                $cnn = ['Nbre De heure de travail'][];
                $cnn = ['Montant Brut Imposable'][];
                $cnn = ['IPR'][];

               // $cnn['numero'][] = $privilege['libelle'],
                    
            }
        }
        
}

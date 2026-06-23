<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\importExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class importSpreadController extends Controller
{
    //
    public function importExcel(){
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        ///// debut
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
            // 'Nbre De Jours de travail' => '',
            // 'Nbre De heure de travail' => '',
                'Montant Brut Imposable' => $privilege['BRUT INSS'],
            // 'IPR'][] '',
            ];
        }
              
      
        //// fin

        // Écriture des données
        $sheet->fromArray($cnn);

        // Appliquer Arial 10 à toute la feuille
        $sheet->getStyle('A:Z')->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'size' => 10,
            ],
        ]);

        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path('CNN TRAITE.xlsx'));
    }
}

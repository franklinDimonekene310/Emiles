<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidationDecadeRequest;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use DB;

class PointageManquantController extends Controller
{
    //
    private array $lesExceptions = [
                    '114396',
                    '131627',
                    '113148',
                    ' 87853',
                    ' 86841',
                    '137731',
                    '131669'
                ];

    public function afficherToutesLesAbsences(ValidationDecadeRequest $request) {
        
        // Affiche tableau des employés qui manquent des pontages à une plage des dates              
            $absencesParEmploye = [];

            foreach (CarbonPeriod::create($request->debutDecade, $request->finDecade) as $jour) {

                $date = $jour->format('Ymd');

                $employes = DB::connection('hfsql_personnel')
                ->table('EMPLOYES')
                ->selectRaw("
                    CONCAT(
                        Matricule,
                        ' - ',
                        TRIM(NomEmploye),
                        ' - ',
                        IDDirection
                    ) AS Matricule,
                    ? AS Date
                ", [$date])
                //->where('IDGrade', '>', '15')
                ->whereNotIn('IDDirection', ['05', '10', '12'])
                ->where('IDFinActivite', '0')
                ->where('DateEngagement', '<=', $date)
                ->whereNotIn('Matricule', function ($query) use ($date) {
                    $query->from('D_POINTAGE_DECADAIRE')
                        ->select('Matricule')
                        ->where('DatePointage', $date);
                })
                //->whereIn('Matricule', [' 82861', '82861'])
                ->whereNotIn('Matricule', $this->lesExceptions)
                ->get();

                // Organiser les pointages manquants par dates
                $absencesParEmploye[$date] = $employes->toArray();

                // Organiser les pointages manquants par Employé
                /*foreach($employes as $employe) {
                    $absencesParEmploye[$employe->Matricule][]  = 
                         Carbon::createFromFormat('Ymd', $employe->DATE)->format('d-m-Y');
                }*/
            }            
            dd($absencesParEmploye);
            return view('Excel', compact('absencesParEmploye'));
    }

    public function afficherDiragroLesAbsences(ValidationDecadeRequest $request) {
        
        // Affiche tableau des employés qui manquent des pontages à une plage des dates              
            $absencesParEmploye = [];

            foreach (CarbonPeriod::create($request->debutDecade, $request->finDecade) as $jour) {

                $date = $jour->format('Ymd');

                $employes = DB::connection('hfsql_personnel')
                ->table('EMPLOYES')
                ->selectRaw("
                    CONCAT(
                        Matricule,
                        ' - ',
                        TRIM(NomEmploye),
                        ' - ',
                        IDDirection
                    ) AS Matricule,
                    ? AS Date
                ", [$date])
                ->where('IDDirection', '=', '05')
                ->where('IDFinActivite', '0')
                ->where('DateEngagement', '<=', $date)
                ->whereNotIn('Matricule', function ($query) use ($date) {
                    $query->from('D_POINTAGE_DECADAIRE')
                        ->select('Matricule')
                        ->where('DatePointage', $date);
                })                
                ->whereIn('Matricule', ['129091', '142079', '128524'])
                ->whereNotIn('Matricule', $this->lesExceptions)
                ->get();
                
                foreach($employes as $employe) {
                    $absencesParEmploye[$employe->Matricule][]  = 
                         Carbon::createFromFormat('Ymd', $employe->DATE)->format('d-m-Y');
                }
            }            

            return view('Excel', compact('absencesParEmploye'));
    }

     
}

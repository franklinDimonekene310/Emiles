@php
    $absencesParEmploye = $absencesParEmploye ?? [];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Export</title>
    <link rel="stylesheet" href="{{asset('style.css')}}" type="text/css"> 

    {{-- Lien pour select2   --}}
    <link rel="stylesheet" href="{{asset('select2\cdn.jsdelivr.css')}}" type="text/css">  
</head>
<body>
        <h1>Opérations sur </h1> 
        
            {{--<a class="btn" href="{{ route('import')}}">Cliquer</a> --}} 
        <div id="div-modal">           
        
            <a class="btn" href="{{ route('updateHS') }}" >Update heure</a>    
        
            <a class="btn" href="{{ route('insertHS') }}" > Insert heure</a>                         
        
            <a class="btn" id="pointage_excel" onclick="ouvrirModal('{{ route('genererFichierPointageCoupe') }}', 'Fichier Excel pointage coupe', ['direction', 'grade', 'contrat'], true)">Exportation Pointage coupe</a>
            
            <a class="btn" id="mis_a_jr" onclick="ouvrirModal('{{ route('misAJourPointageCoupe') }}', 'Mise à jour pointage coupe')">Mis à jour</a>    
                  
            {{-- <a class="btn" onclick="ouvrirModal('{{ route('pointageManquant')}}', 'Pointage manquant')">Pointage manquant</a> --}}
            
            <form action="{{ route('fichierCnss') }}" method="GET" style="display: inline">            
                <button class="btn" type="submit">Fichier cnss</button>
                <input type="month" name="anneeMois" required>
            </form>

            <a class="btn" onclick="ouvrirModal('{{ route('afficherToutesLesAbsences')}}', 'Pointage manquant')">Pointage manquant</a> 
            
        </div>       
        
        <div id="container_table">
            <table>
                <thead>
                    <tr>
                        <th>Matricule - Nom Employé - Direction</th>                        
                        <th>Date</th>                                           
                    </tr>
                </thead>
                <tbody>                   
                    @forelse($absencesParEmploye as $matr_nom_direction => $dates)                   
                        <tr>
                            <td>{{ $matr_nom_direction }}</td>
                            <td>
                                <table>
                                     @foreach($dates as $date)                                        
                                        <tr>
                                            <td>{{ $date }}</td>
                                        </tr>
                                     @endforeach
                                </table>                               
                            </td>                        
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2"><em>Aucune information !</em></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="id01" class="modal">                
                <form class="modal-content" id ="pointageForm" method="GET" >
                    @csrf
                   
                    <span onclick="fermerModal()" class="modal-close-btn" title="Fermer">&times;</span>                   
                        
                        <div class="modal-title">Pointage Décadaire</div>

                        <div class="form-group-row">
                            <div class="form-field">                                
                                <input class="form-input" type="date" id="debutDecade" name="debutDecade" value="{{ old('debutDecade') }}">
                            </div>                           

                            <div class="form-field form-label" style="flex: 0 0 30px; align-items: center; justify-content: center; color: #a0aec0;">
                               au 
                            </div>

                            <div class="form-field">                                
                                <input class="form-input" type="date" id="finDecade" name="finDecade" value="{{ old('finDecade') }}">                           
                            </div>              
                        </div>

                        <div class="form-group-row champ">   
                            <div class="form-field">                                  
                                <label for="direction" class="form-label">Directions</label>              
                                <select name="directions[]" id="direction" multiple>                                
                                    <option value="00">TOUS</option>
                                    <option value="01">DIR GEN</option>
                                    <option value="03">APPROS</option>
                                    <option value="05">AGRO</option>
                                    <option value="06">GARAGE</option>
                                    <option value="07">SVG</option>
                                    <option value="08">USINE</option>
                                    <option value="09">DAF</option>
                                    <option value="10">PERSONNEL</option>
                                    <option value="11">HOPITAL</option>
                                    <option value="12">COMMERCIAL</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="contrat" class="form-label">Contrats</label>                
                                <select name="contrats[]" id="contrat" multiple>                                
                                    <option value="00">TOUS</option>
                                    <option value="0">PERMANENTS</option>
                                    <option value="1">SAISONNIERS</option>
                                </select>
                            </div>                            
                        </div>
                        <div class="form-group-row champ">
                            <label for="grade" class="form-label">Grades</label>                
                            <select name="grades[]" id="grade" multiple>                                
                                <option value="00">TOUS</option>
                                <option value="01">TA</option>
                                <option value="02">ML</option>
                                <option value="03">MS</option>
                                <option value="04">SQ1</option>
                                <option value="05">SQ2</option>
                                <option value="06">SQ3</option>
                                <option value="07">Q1</option>
                                <option value="08">Q2</option>
                                <option value="09">HQ</option>
                                <option value="10">M1</option>
                                <option value="11">M2</option>
                                <option value="12">M3</option>
                                <option value="13">CC1</option>
                                <option value="14">CC2</option>
                                <option value="15">CC3</option>
                                <option value="16">S1</option>
                                <option value="17">S2</option>
                                <option value="18">S3</option>
                                <option value="19">B4</option>
                                <option value="20">B3</option>
                                <option value="21">B2</option>
                                <option value="22">B1</option>
                            </select>
                        </div>                        
                        <p>
                            @error('finDecade')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                            @error('debutDecade')
                                <div class="text-danger">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if (session('erreur'))
                                <div class="alert text-danger" id="erreur">
                                    {{ session('erreur') }}
                                </div>
                            @endif
                        </p>

                        <div class="modal-actions">
                            <button type="button" class="btn-dialogue btn-dialogue-secondary" onclick="fermerModal()">Annuler</button>
                            <button type="submit" class="btn-dialogue btn-dialogue-primary">Valider</button>
                        </div>                   
                </form>
        </div>

        
        @if(session('success'))
           <div class="toast" id="success-toast">
                <strong>Success ! </strong> {{ session('success') }}  
                <a href="#" class="close-btn" onclick="closeToast()">&#215;</a>
                <div class="toast-progress"></div>
            </div>
        @endif
        
   
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if ($errors->any())
                    document.getElementById('id01').style.display = 'flex';
                @endif  
                
                @if (session('erreur'))
                    document.getElementById('id01').style.display = 'flex';                
                @endif 
                
                window.message = @json(session('success'));                
            });        
        </script>

        {{-- SCRIPT GENERAL --}}
        <script src="{{ asset('script.js') }}"></script>

        {{-- SCRIPT POUR SELECT 2 --}}
        <script src="{{ asset('select2\code.jquery.js') }}"></script>
        <script src="{{ asset('select2\cdn.jsdelivr.js') }}"></script>

</body>
</html>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Export</title>
    <link rel="stylesheet" href="{{asset('style.css')}}" type="text/css">    
</head>
<body>
        <h1>Opérations sur </h1>    
        
        
            {{--<a class="btn" href="{{ route('import')}}">Cliquer</a> --}} 
        <div id="div-modal">
           
        
            <a class="btn" href="{{ route('updateHS') }}" >Update heure</a>    
        
            <a class="btn" href="{{ route('insertHS') }}" > Insert heure</a>                         
        
            <a class="btn" id="pointage_excel" onclick="ouvrirModal('{{ route('genererFichierPointageCoupe') }}', 'Fichier Excel pointage coupe')">Exportation Pointage coupe</a>
            <a class="btn" id="mis_a_jr" onclick="ouvrirModal('{{ route('misAJourPointageCoupe') }}', 'Mis à jour pointage coupe')">Mis à jour</a>    
                  
            <a class="btn" onclick="ouvrirModal('{{ route('pointageManquant')}}', 'Pointage manquant')">Pointage manquant</a>
            <a class="btn" href="{{ route('fichierCnss')}}">Fichier Cnss</a>

            
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
                   
                    {{-- @forelse($datePointageManquant as $matr_nom_direction => $dates)
                   
                    <tr>
                        <td>{{ $matr_nom_direction }}</td>
                        <td>
                            @foreach($dates as $date)
                                {{ $date['Date'] }}<br>
                            @endforeach
                        </td>                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2"><em>Aucune !</em></td>
                    </tr>
                    @endforelse--}}
                </tbody>
            </table>
        </div>

        <div id="id01" class="modal">                
                <form class="modal-content" id ="pointageForm" method="GET" >
                    @csrf
                    <span onclick="fermerModal()" class="close" title="Fermer">&times;</span>
                    <div class="container">
                        <h2>Pointage Décadaire</h2>

                        <div>
                            <label for="debutDecade">Début décade du </label>
                            <input type="date" id="debutDecade" name="debutDecade" value="{{ old('debutDecade') }}">


                            <label for="finDecade">au</label>                
                            <input type="date" id="finDecade" name="finDecade" value="{{ old('finDecade') }}">
                          
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

                        <div class="clearfix">
                            <button type="button" class="cancelbtn">Annuler</button>
                            <button type="submit" class="validatebtn">Valider</button>
                        </div>
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
        <script src="{{ asset('script.js') }}"></script>

</body>
</html>
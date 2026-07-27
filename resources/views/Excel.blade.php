<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Export</title>
    <link rel="stylesheet" href="{{asset('style.css')}}" type="text/css">
</head>
<body>
    <h1>Importation du Fichier Excel</h1>
    
       
    <a class="btn" href="{{ route('import')}}">Cliquer</a>
    
        <a class="btn" href="{{ route('pointage') }}" > Aller sur pointage</a>
    
    
        <a class="btn" href="{{ route('updateHS') }}" >Update heure</a>
    
    
        <a class="btn" href="{{ route('insertHS') }}" > Insert heure</a>
        <a class="btn" href="{{ route('getPointageCoupe') }}" > Pointage coupe</a>                
      
        <a class="btn" onclick="ouvrirModal('{{ route('genererFichierPointageCoupe') }}')">Exportation Pointage coupe</a>
        <a class="btn" onclick="ouvrirModal('{{ route('misAJourPointageCoupe') }}')">Mis à jour</a>
        <a class="btn" href="{{ route('misAJourPointageCoupe') }}">Test</a>

        <div id="id01" class="modal">                
                <form class="modal-content" id ="pointageForm" method="GET" >
                    @csrf
                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Fermer">&times;</span>
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
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
    
    
        @if (session('erreur'))
            <div class="alert alert-danger">
                {{ session('erreur') }}
            </div>
        @endif
        
    <a class="btn" href="{{ route('import')}}">Cliquer</a>
    
        <a class="btn" href="{{ route('pointage') }}" > Aller sur pointage</a>
    
    
        <a class="btn" href="{{ route('updateHS') }}" >Update heure</a>
    
    
        <a class="btn" href="{{ route('insertHS') }}" > Insert heure</a>
        <a class="btn" href="{{ route('getPointageCoupe') }}" > Pointage coupe</a>
        <a class="btn" href="{{ route('genererFichierPointageCoupe') }}" >Exporter Pointage coupe en Excel</a>          
        <a class="btn" onclick="document.getElementById('id01').style.display='flex'">Open Modal</a>
        <a class="btn" href="{{ route('misAJourPointageCoupe') }}">Test</a>

        <div id="id01" class="modal">
                {{-- <form class="modal-content" action="{{ route('misAJourPointageCoupe') }}" > --}}
                <form id="monFormExcel" class="modal-content" action="{{route('genererFichierPointageCoupe') }}">
                    @csrf
                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Fermer">&times;</span>
                    <div class="container">
                        <h2>Pointage Décadaire</h2>

                        <div>
                            <label for="debutDecade">Début décade du </label>
                            <input type="date" id="debutDecade" name="debutDecade">
                            <span id="erreurDebut" class="text-danger"></span>


                            <label for="finDecade">au</label>                
                            <input type="date" id="finDecade" name="finDecade" value="{{ old('finDecade') }}">
                            <span id="erreurFin" class="text-danger"></span>
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
                        </p>

                        <div class="clearfix">
                            <button type="button" class="cancelbtn">Annuler</button>
                            <button type="submit" class="deletebtn">Valider</button>
                        </div>
                    </div>
                </form>
        </div>

        <br><br><br>         
        

        {{-- @if (session('success')) --}}
            <div class="alert-success" id="success-message">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">&#215;</button>
            </div>
        {{-- @endif --}}
   
   <script src="{{ asset('script.js') }}"></script>
   <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if ($errors->any())
                document.getElementById('id01').style.display = 'flex';
            @endif

        });
    </script>

</body>
</html>
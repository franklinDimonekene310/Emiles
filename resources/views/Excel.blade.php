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
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perferendis beatae fuga dolor cumque dolores fugit minima maiores earum magni animi reiciendis illo, voluptatum veritatis provident magnam est possimus impedit soluta!
    </p>
    <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam mollitia et harum officiis eligendi unde beatae alias voluptatem quisquam similique exercitationem modi, distinctio veniam optio aperiam numquam labore tempore ex?
        Ad mollitia rerum exercitationem officiis consectetur necessitatibus eligendi voluptatum, minus neque quam debitis? Illum eos dolore quo cum beatae aspernatur quaerat non porro, perferendis, sed quos obcaecati laboriosam exercitationem molestiae!
        Cum ipsa consectetur cumque itaque commodi harum architecto facere quisquam asperiores, molestias, mollitia quia non, temporibus corporis! Accusamus modi possimus ab itaque quis minima obcaecati ullam, reiciendis error harum debitis.
    </p>
    {{-- <button class="btn">Cliquer</button> --}}
    <a class="btn" href="{{ route('import')}}">Cliquer</a>
    
        <a class="btn" href="{{ route('pointage') }}" > Aller sur pointage</a>
    
    
        <a class="btn" href="{{ route('updateHS') }}" >Update heure</a>
    
    
        <a class="btn" href="{{ route('insertHS') }}" > Insert heure</a>
        <a class="btn" href="{{ route('getPointageCoupe') }}" > Pointage coupe</a>
        <a class="btn" href="{{ route('genererFichierPointageCoupe') }}" > test Pointage coupe</a>          
        <a class="btn" onclick="document.getElementById('id01').style.display='flex'">Open Modal</a>
        <a class="btn" href="{{ route('misAJourPointageCoupe') }}">Test</a>

        <div id="id01" class="modal">
                <form class="modal-content" action="{{ route('misAJourPointageCoupe') }}" >
                    @csrf
                    <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Fermer">&times;</span>
                    <div class="container">
                        <h2>Pointage Décadaire</h2>

                        <div>
                            <label for="fname">Début décade du </label>
                            <input type="date" id="fname" name="debutDecade">
                            <label for="lname">au</label>                
                            <input type="date" id="lname" name="finDecade" value="{{ old('finDecade') }}">
                        </div>
                        <p>
                            @error('finDecade')
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
        <div class="alert alert-success">
            <strong>Success!</strong> Your operation was completed successfully.
            <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
        </div
   
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
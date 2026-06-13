<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Emiles</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->        
    </head>
    <body>
      
       <h1>Page acceuil</h1>

        <h2>What Can JavaScript Do?</h2>

        <p id="demo">JavaScript can hide HTML elements.</p>

        <button type="button" onclick="document.getElementById('demo').style.display='none'">Masquer!</button>

        <button type="button" onclick="afficherElement()">Afficher!</button>
        
        <button type="button" onclick="toogle()">Permutter!</button>

        <button type="button" id="btnToogle">Permutter sans onclick!</button>
        <br>

        <button type="button" onclick="document.getElementById('demo')">Changer H2</button>

        <p>
           <a href="{{ route('excel') }}"  rel="noopener noreferrer"> Aller sur excel</a>
        </p>

    <script src="{{ asset('script.js') }}"></script>
    </body>
</html>

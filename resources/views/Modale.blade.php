<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Fenetre Modale</title>

    <style>
        body {
            background-color: #ecf0f1;
            font-family: 'Courier New', Courier, monospace;
        }

        #modal {
            position: fixed;
            inset: 0;
            background-color: rgba(0,0,0,0.6);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 7px;
            text-align: center;
            width: 300px;
            position: absolute;
        }

        .modal-content a {
            text-decoration: none;
            position: absolute;
            top: 15px;            
            right: 15px;
            color: red;
            font-size: 19px;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .modal-content a:hover {
            transform: scale(1.2);
            color: #a71d2a;
        }
        
    </style>
</head>
<body>
   <button id="open">ouvrir le modal</button>
   <div id="modal">
    <div class="modal-content">
        <h1>Bonjour le monde !</h1>       
        <a href="#" id="close">&#10005;</a>
    </div>
   </div>

  <script>
    let open = document.querySelector('#open');
    let close = document.querySelector('#close');
    let modale = document.querySelector('#modal');

    open.addEventListener('click', () => {
        modale.style.display = "flex";
    });

    close.addEventListener('click', () => {
        modale.style.display = "none";
    });

  </script>
</body>
</html>
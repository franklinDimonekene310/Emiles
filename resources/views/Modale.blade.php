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

        /* ============================
       BUTTON GLASMORPHISM
       =============================*/

.btn-glass {
  padding: 14px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 14px;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), 
              inset 0 1px 1px rgba(255, 255, 255, 0.4);
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-glass:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-3px);
    box-shadow:
        0 12px 35px rgba(0, 0, 0, 0.3),
        inset 0 1px 1px rgba(255, 255, 255, 0.5);
}

.btn-glass:active {
    transform: translateY(1px);
}


   .btn-glass {
    position: relative;
    overflow: hidden;
}

.btn-glass::before {
    content: "";
    position: absolute;
    top: 0;
    left: -120%;
    width: 60%;
    height: 100%;
    background: linear-gradient(
        120deg,
        transparent,
        rgba(255, 255, 255, 0.5),
        transparent
    );
    transition: left 0.6s ease;
}

.btn-glass:hover::before {
    left: 140%;
}
        a {
            text-decoration: none;
            padding: 10px;
            background: #2805c4;
            color: #ecf0f1;
            border-radius: 5px;
        
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
<button class="btn-glass">Valider</button>

  <a href="{{ route('testConnexion') }}">Test Connexion</a>

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
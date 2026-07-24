<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        /* Toast */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;

    width: 350px;
    max-width: 90%;

    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
    border-left: 6px solid #198754;
    border-radius: 8px;

    padding: 15px 40px 15px 15px;

    box-shadow: 0 5px 15px rgba(0,0,0,.2);

    opacity: 0;
    transform: translateX(120%);
    transition: all .5s ease;

    z-index: 9999;
}

/* Lorsque le toast est visible */
.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast-progress {
    position:absolute;
    bottom:0;
    left:0;
    height:4px;
    width:100%;
    background:#198754;
    animation:progress 4s linear forwards;
}

@keyframes progress {
    from{
        width:100%;
    }
    to{
        width:0%;
    }
}

/* Close button */
.close-btn {
    position: absolute;
    top: 5px;
    right: 8px;
    background: transparent;
    border: none;
    font-size: 18px;
    font-weight: bold;
    color: #db2222;
    cursor: pointer;
    text-decoration: none;
}

.close-btn:hover {
    color: #000;
}

    
</style>

<title>Toast</title>
</head>
<body>

    <div class="toast" id="success-toast">
        <strong>Success !</strong> {{ session('success') }}                
        <a href="#" class="close-btn">&#215;</a>
        <div class="toast-progress"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById("success-toast");
            const closeBtn = document.querySelector('.close-btn');

                if (toast) {
                    // Afficher le toast
                    setTimeout(() => {
                        toast.classList.add("show");
                    }, 100);

                    // Le masquer après 4 secondes
                    setTimeout(() => {
                        closeToast();
                    }, 4000);
                }

                function closeToast() {
                    const toast = document.getElementById("success-toast");
                    if (toast) {
                        toast.classList.remove("show");
                        // Supprimer complètement le toast après l'animation
                        setTimeout(() => {
                            toast.remove();
                        }, 500);
                    }
                }

                closeBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    closeToast();
                });

        });
    </script>
</body>
</html>
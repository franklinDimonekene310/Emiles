

    function afficherElement() {        
     document.querySelector('#demo').style.display = 'block'
    }


    function toogle() {
        let elt = document.querySelector('#demo');

        if (elt.style.display === 'none') {
            elt.style.display = 'block';
        } else {
            elt.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        var modal = document.getElementById('id01');
        const cancelBtn = document.querySelector('.cancelbtn');

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
      
       // Rouvrir automatiquement le modal en cas d'erreur
      
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                document.getElementById('id01').style.display = 'none';
            });
        }

        /* document.querySelector('#btnToogle').addEventListener('click', function () {
            let elt = document.querySelector('#demo');

            if (elt.style.display === 'none') {
                elt.style.display = 'block'
            } else 
                {
                    elt.style.display = 'none'
                }
        }) */

                const toast = document.getElementById("success-toast");

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
        
    });


    
   
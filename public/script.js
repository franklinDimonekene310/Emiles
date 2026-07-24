

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

     // Gestion des routes sur le formulaire
    function ouvrirModal(action) {
        const formulaire = document.getElementById('pointageForm');

        formulaire.action = action;
        document.getElementById('id01').style.display = 'flex';
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

         // Gestion du toast
        if (window.message) {
            const toast = document.getElementById("success-toast");
            const closeBtn = document.querySelector('.close-btn');
                    
            // Afficher le toast
            setTimeout(() => {
                toast.classList.add("show");
            }, 100);

            // Le masquer après 4 secondes
            setTimeout(() => {
                closeToast();
            }, 4000);


            // Bouton fermeture
            closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeToast();
            });

            function closeToast() {
                const toast = document.getElementById("success-toast");
                      
                toast.classList.remove("show");
                // Supprimer complètement le toast après l'animation
                setTimeout(() => {
                        toast.remove();
                }, 500);                       
            }
        }
    });
  
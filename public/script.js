

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
    function ouvrirModal(action, unTitre, data, masquer) {
        
        const formulaire = document.getElementById('pointageForm');
        const titre = document.querySelector('.container h2');

        formulaire.action = action;

        if (titre) {
            titre.innerText = unTitre;
        }

        // Réafficher tous les champs
        document.querySelectorAll('.champ').forEach(champ => {
            champ.style.display = '';
        });
        
        // Masquer les champs demandés
        if(data && masquer) {
            data.forEach(id => {
                const element = document.getElementById(id);

                if (element) {
                    element.closest('.champ')?.style.setProperty('display', 'none');
                }
            });
        }
        

        document.getElementById('id01').style.display = 'flex';
    }

    // Fermeture Modal
    function fermerModal() {
        document.getElementById('id01').style.display='none';        
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

        $('#grade').select2({ placeholder: 'Sélectionner grade(s)',  allowClear: true,  width: '100%' });
        $('#direction').select2({ placeholder: 'Sélectionner direction(s)',  allowClear: true,  width: '100%' });
        $('#contrat').select2({ placeholder: 'Sélectionner contrat(s)',  allowClear: true,  width: '100%' });

        $('#grade').on('select2:select', function (e) {
            let values = $(this).val();

            // Si "Tous" vient d'être sélectionné
            if (e.params.data.id === '00') {
                // Désélectionner tous les autres
                $(this).val(['00']).trigger('change');
                return;
            }

            // Si un grade est sélectionné alors que "Tous" était déjà sélectionné
            if (values.includes('00')) {
                // Garder uniquement "Tous"
                $(this).val(['00']).trigger('change');
            }
        });

        $('#direction').on('select2:select', function (e) {
            let values = $(this).val();

            // Si "Tous" vient d'être sélectionné
            if (e.params.data.id === '00') {
                // Désélectionner tous les autres
                $(this).val(['00']).trigger('change');
                return;
            }

            // Si un grade est sélectionné alors que "Tous" était déjà sélectionné
            if (values.includes('00')) {
                // Garder uniquement "Tous"
                $(this).val(['00']).trigger('change');
            }
        });

        $('#contrat').on('select2:select', function (e) {
            let values = $(this).val();

            // Si "Tous" vient d'être sélectionné
            if (e.params.data.id === '00') {
                // Désélectionner tous les autres
                $(this).val(['00']).trigger('change');
                return;
            }

            // Si un grade est sélectionné alors que "Tous" était déjà sélectionné
            if (values.includes('00')) {
                // Garder uniquement "Tous"
                $(this).val(['00']).trigger('change');
            }
        });
    });
  
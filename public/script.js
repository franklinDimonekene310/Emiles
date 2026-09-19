

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
    function ouvrirModal(action, unTitre, masquer) {
        
        const formulaire = document.getElementById('pointageForm');
        const titre = document.querySelector('.modal-title');
        const groupes = document.querySelectorAll('.form-group-row');

        formulaire.action = action;

        if (titre) {
            titre.innerText = unTitre;
        }

        groupes.forEach((div, index) => {
            if(index > 0) {
                div.style.display = masquer ? 'none' : '';
            }
        } );  

        document.getElementById('id01').style.display = 'flex';
    }

    // Fermeture Modal
    function fermerModal() {
        document.getElementById('id01').style.display='none';        
    }


    document.addEventListener('DOMContentLoaded', function () {

        var modal = document.getElementById('id01');
        const cancelBtn = document.querySelector('.cancelbtn');
        const modalTitle = document.querySelector('#modal-title');

        // Gestion de deplacement de la boite de dialogue
        let deplacement = false;
        let offsetX = 0;
        let offsetY = 0;

        modalTitle.addEventListener('mousedown', function(e) {
            
            if (e.button !== 0) return;        

            const rect = modalTitle.getBoundingClientRect();

            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;

            deplacement = true;

            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!deplacement) return;

            const modalContent = modal.querySelector('.modal-content');

            modalContent.style.left = (e.clientX - offsetX) + 'px';
            modalContent.style.top = (e.clientY - offsetY) + 'px';
        });

        document.addEventListener('mouseup', function() {
            deplacement = false;
        }); 


        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
      
       // Rouvrir automatiquement le modal en cas d'erreur      
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
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

        $('#grade').select2({ placeholder: 'grade(s)',  allowClear: true,  width: '100%' });
        $('#direction').select2({ placeholder: 'direction(s)',  allowClear: true,  width: '100%' });
        $('#contrat').select2({ placeholder: 'contrat(s)',  allowClear: true,  width: '100%' });
        $('.mon-select').select2({containerCssClass: 'custom-select2'});

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
  
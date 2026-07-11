

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

        document.querySelector('#btnToogle').addEventListener('click', function () {
            let elt = document.querySelector('#demo');

            if (elt.style.display === 'none') {
                elt.style.display = 'block'
            } else 
                {
                    elt.style.display = 'none'
                }
        })
    });
 


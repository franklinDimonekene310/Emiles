

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
 
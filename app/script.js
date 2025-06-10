document.addEventListener('DOMContentLoaded', function() {
    const toggleIconLink = document.getElementById('deleteIcon');
    const iconPath = document.getElementById('iconPath');

    // Dati dei path delle icone
    const iconPaths = {
        delete: 'M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z',
        check: 'M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z'
    };

    const redirectLinks = {
        delete: '',
        check: '?action=deleteconfirm&id='
    };

    let isDeleteIcon = true;

    if (toggleIconLink && iconPath) { // Aggiungi un controllo per assicurarti che gli elementi esistano
        toggleIconLink.addEventListener('click', function(event) {
            // event.preventDefault(); // Rimuovi o commenta se il link deve comunque fare un'azione PHP

            if (isDeleteIcon) {
                toggleIconLink.setAttribute('href', redirectLinks.check);
                iconPath.setAttribute('d', iconPaths.check);
                //iconPath.closest('svg').style.fill = '#4CAF50';
                isDeleteIcon = false;
            } else {
                toggleIconLink.setAttribute('href', redirectLinks.delete);
                iconPath.setAttribute('d', iconPaths.delete);
                //iconPath.closest('svg').style.fill = '#8B1A10';
                isDeleteIcon = true;
            }

            // Opzionale: torna automaticamente allo stato iniziale dopo un po'
            // setTimeout(function() {
            //     iconPath.setAttribute('d', iconPaths.delete);
            //     iconPath.closest('svg').style.fill = '#8B1A10';
            //     isDeleteIcon = true;
            // }, 1500);
        });
    }
});
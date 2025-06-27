document.addEventListener('DOMContentLoaded', function() {
    const deleteConfirmationLinks = document.querySelectorAll('.delete-confirm-link');

    // Dati dei path delle icone
    const iconPaths = {
        delete: 'M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z',
        check: 'M382-240 154-468l57-57 171 171 367-367 57 57-424 424Z'
    };

    const confirmFillColor = "#8B1A10";
    const originalFillColor = "#8B1A10";


    // Itera su ogni link di eliminazione trovato nella pagina
    deleteConfirmationLinks.forEach(link => {
        let confirmationTimeout; // Per memorizzare il timer di revert
        let isInConfirmationState = false; // Flag di stato per ogni singolo link

        // Trova l'elemento <path> all'interno dell'SVG di questo specifico link
        const iconPathElement = link.querySelector('.icon-path-data');
        const svgElement = link.querySelector('svg');

        // Memorizza l'URL originale di eliminazione dal data-attribute
        const originalActionHref = link.dataset.originalHref; // Accesso tramite dataset API

        if (!iconPathElement || !svgElement || !originalActionHref) {
            console.warn("Elementi SVG o URL originale non trovati per un link di eliminazione. Skipping:", link);
            return; // Salta questo link se gli elementi necessari non sono presenti
        }

        link.addEventListener('click', function(event) {
            event.preventDefault(); // Impedisce al browser di seguire subito il link

            if (!isInConfirmationState) {
                // PRIMO CLICK: Passa allo stato di conferma
                iconPathElement.setAttribute('d', iconPaths.check);
                //svgElement.style.fill = confirmFillColor;
                isInConfirmationState = true;            

                // Avvia il timer per tornare indietro dopo 3 secondi se non c'è un secondo click
                confirmationTimeout = setTimeout(() => {
                    iconPathElement.setAttribute('d', iconPaths.delete);
                    //svgElement.style.fill = originalFillColor;
                    isInConfirmationState = false;                   
                }, 3000); // 3000 ms = 3 secondi
            } else {
                // SECONDO CLICK: Esegui l'azione e reindirizza
                clearTimeout(confirmationTimeout); // Annulla il timer, perché l'utente ha confermato
                
                // Opzionale: puoi aggiungere un'animazione o un messaggio "In elaborazione..."
                // iconPathElement.setAttribute('d', iconPaths.processing); // Se hai un'icona di "loading"
                // svgElement.style.fill = '#FFD700'; // Colore giallo per "in elaborazione"

                // Esegui il reindirizzamento che attiverà l'eliminazione in PHP
                window.location.href = originalActionHref;
            }
        });
    });
});
# Milli - Scelte di progetto

Abbiamo deciso di ristrutturare il nostro sito precedentemente sviluppato utilizzando le tecnologie in Python, ovvero Django.
L'applicazione è predisposta per l'utilizzo di un database gestito da PostgreSQL.
Per il frontend, abbiamo mantenuto l'uso dei template HTML di Django combinati con CSS e Javascript per mantenere l'applicazione il più simile possibile al sito che intende ristrutturare. Abbiamo inoltre utilizzato Google Fonts per i font.

All'interno del file `models.py` si trovano i modelli delle tabelle da rappresentare: Ospedali, Cittadini, Ricoveri, Patologie e RicoveroPatologie, che associa un insieme di patologie a un ricovero.

Il file views.py gestisce la logica delle interazioni utente.

La cartella `templates` contiene invece i layout e le diverse pagine html del sito.

La popolazione del database viene fatta automaticamente dal file `seed_db.py`. Ciò richiede una stringa per la connessione che si trova nel file `.env`.

Il file `settings.py` configura il comportamento del progetto, ovvero la sicurezza, le app utilizzate, come connettersi al database, i percorsi dei file e le configurazioni per il server.

Le entità da utilizzare sono state messe in relazione tra loro utilizzando l'ORM di Django, per evitare di scrivere query sql troppo complesse.

Per semplificare l'installazione e l'utilizzo dell'applicazione su Windows abbiamo deciso di includere uno script `setup_and_run.bat` che installa automaticamente l'applicazione chiedendo all'utente solo il minimo necessario, come il nome del database, l'utente e la password. Al termine dell'installazione, esegue automaticamente il programma.

Una volta che l'installazione è completata, è sufficiente eseguire lo script `run.bat`, che permette di eseguire il programma a seguito della sua installazione.

Il programma funziona completamente in locale, e si interfaccia con un database locale.

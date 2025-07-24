# Milli - Guida all'uso

Questo progetto prevede due diverse modalità di installazione: una manuale e una automatizzata.

## Cosa è Milli
Milli consente di gestire database per aziende ospedaliere. 
Fornisce la possibilità di aggiungere, visualizzare, modificare e rimuovere ospedali, oltre alla possibilità di cercare e visualizzare dati a essi correlati,
quali cittadini, ricoveri, patologie ecc.

## Installazione automatizzata per Windows (consigliato)

Il progetto include alcuni script che automatizzano e velocizzano l'installazione.

### Prerequisiti

- Python 3.11 deve essere installato e presente nella PATH di sistema
- PostgreSQL 15 o una versione successiva deve essere installato e deve essere in esecuzione. È necessario creare un database vuoto.

## Creazione database vuoto (psql)
1. Aprire SQL Shell (psql)
2. Premere invio per confermare i valori default di server, database, port e username
3. Scegliere una password. Sarà necessaria più avanti.
4. Eseguire il comando "CREATE DATABASE nomedb;", dove nomedb è il nome scelto per il database. Il nome sarà necessario più avanti.
5. Se necessario, si può verificare che il db sia stato creato eseguendo il comando \l per visualizzare la lista di db.

Il procedimento con pgadmin è analogo ma più intuitivo grazie all'interfaccia grafica.

### Istruzioni
1. Scaricare il progetto sul proprio computer. Se si scarica come archivio, estrarre l'archivio in una cartella.
2. Eseguire il file `setup_and_run.bat`, è uno script batch per eseguire il setup automatizzato del progetto.
3. Inserire i dettagli del database creato nei prerequisiti. Lo script chiede passo passo cosa inserire.
    - Host: di default è `localhost`
    - Port: di default è `5432`
    - Utente: di default è `postgres`
    - Nome del database
    - Password del database
4. Attendere che l'installazione termini. Al termine dell'installazione, non chiudere lo script.
5. Se lo script è stato chiuso al termine dell'installazione, eseguire lo script `run.bat`. Se lo script precedente è ancora in esecuzione, saltare questo passaggio.
6. Aprire un browser a scelta e recarsi a http://127.0.0.1:8000/.

## Installazione manuale

### Prerequisiti
- Python 3.11 o successivo deve essere installato.
- Deve esistere un database PostgreSQL locale o remoto.
- (Opzionale) `git`

### Setup Iniziale

1. Clona la repository o scarica i file. Inserisci i file scaricati in una cartella.
2. Crea e attiva un ambiente virtuale. Ciò isola le dipendenze del progetto. Per fare ciò, avviare un terminale e digitare:
    ```bash
    # Su macOS o Linux
    python3 -m venv venv
    source venv/bin/activate

    # Su Windows
    python -m venv venv
    .\venv\Scripts\activate
    ```
3. Installa le dipendenze:
    ```bash
    pip install -r requirements.txt
    ```
### Configurazione del database
1. Creare un file `.env`. Copiare il file `.env.example` in un nuovo file chiamato `.env`.
2. Modificare il file `.env`. Aprire il file `.env` e inserire la stringa di connessione di PostgreSQL per intero in `DATABASE_URL`.
    - Per un DB locale, il risultato sarà simile a:
    `DATABASE_URL="postgres://utente:password@localhost:5432/nome_db"` dove bisogna sostituire il proprio nome utente, la propria password e il nome del database.
3. Aggiornare la `SECRET_KEY`. Per sicurezza, è consigliato aprire `millipw_django/settings.py` e modificare `SECRET_KEY` in una stringa unica e casuale.

### Migrazione del database e caricamento dei dati
1. Iniziare la migrazione. Il comando seguente crea le tabelle del database seguendo i modelli di Django.
    ```bash
    python manage.py makemigrations hospital
    python manage.py migrate
    ```
2. Caricare i dati iniziali. Digitare 
    ```bash
    python manage.py seed_db
    ```

### Eseguire l'applicazione
1. Avviare il server. Digitare
    ```bash
    python manage.py runserver
    ```
2. Visualiizare l'applicazione. Aprire un browser e navigare a http://127.0.0.1:8000/. Verrà visualizzata l'homepage dell'applicazione.

## Utilizzo del sito

Una volta che si apre il sito, si viene indirizzati automaticamente alla homepage. Nella homepage si possono consultare diverse informazioni relative all'azienda ospedaliera, tra cui i contatti e i servizi disponibili.

Nel sito è presente una barra di navigazione, che consente di accedere alle tabelle del database **Ospedali, Patologie, Ricoveri, Cittadini**.
Per consultare una tabella, cliccare la tabella desiderata.
Le tabelle **Patologie, Ricoveri, Cittadini** permettono di effettuare ricerche sui dati presenti nel database. Poco sopra la tabella, è presente un modulo con diversi campi. Digitare la richiesta nei campi e cliccare il pulsante **Cerca**. Verrà visualizzata una tabella contenente i risultati.
La tabella **Ospedali** permette di consultare i dati come le altre tabelle, e permette anche l'inserimento, la modifica e l'eliminazione di dati.

## Inserimento Dati

Per inserire nuovi dati, è necessario inserire tutti i campi del nuovo ospedale. Cliccare poi su **Aggiungi ospedale**. Al termine dell'inserimento, il nuovo ospedale sarà aggiunto in fondo alla tabella.

## Modifica Dati

Si trovi nella tabella l'ospedale da modificare. Sulla destra sono presenti due icone. Selezionare la matita. Si verrà reindirizzati a un modulo già riempito con i dati dell'ospedale. Per modificare i dati, inserire i dati desiderati al posto di quelli già presenti. Cliccare poi su **Modifica ospedale**.

## Eliminazione Dati

Si trovi nella tabella l'ospedale da eliminare. Sulla destra sono presenti due icone. Selezionare il cestino della spazzatura. Dopo averlo cliccato, verrà sostituito da una spunta. Cliccare la spunta se si è sicuri dell'eliminazione dell'ospedale. Se dopo 5 secondi la spunta non è stata cliccata, si ritornerà al cestino della spazzatura.



Ci contatti per qualsiasi tipo di problema riscontrato durante l'installazione o durante l'utilizzo.

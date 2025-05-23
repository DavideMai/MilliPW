<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ospedali</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php
    include 'nav.html';
?>
    <h2>Elenco Ospedali</h2>
    <div class="forms-container">
        <form method="GET" action="">
        <div class="form-group"><label for="NomeOspedale">Nome:</label>
            <input type="text" name="NomeOspedale" id="NomeOspedale">
        </div>
        <div class="form-group"><label for="Citta">Città:</label>
            <input type="text" name="Citta" id="Citta">
        </div>
        <div class="form-group">    <label for="Indirizzo">Indirizzo:</label>
            <input type="text" name="Indirizzo" id="Indirizzo">
        </div>
            <input type="submit" value="Cerca Ospedali">
        </form>
    </div>
    <h1>Aggiungi un nuovo ospedale</h1>
    <div class="forms-container">
        <form method="POST">
        <div class="form-group"><label for="nomeOspedale">Nome Ospedale:</label>
                <input type="text" id="nomeOspedale" name="nomeOspedale" required>
        </div>
        <div class="form-group">       <label for="indirizzo">Indirizzo:</label>
                <input type="text" id="indirizzo" name="indirizzo" required>
        </div>
        <div class="form-group">        <label for="numeroCivico">Numero Civico:</label>
                <input type="text" id="numeroCivico" name="numeroCivico" pattern="[0-9]*" inputmode="numeric" required>
        </div>
        <div class="form-group">      <label for="citta">Città:</label>
                <input type="text" id="citta" name="citta" required>
        </div>
        <div class="form-group">     <label for="numeroTelefonico">Numero Telefonico:</label>
                <input type="text" id="numeroTelefonico" name="numeroTelefonico" pattern="[0-9]*" inputmode="tel" required>
        </div>
        <div class="form-group">        <label for="codiceSanitarioDirettore">Codice Sanitario Direttore:</label>
                <input type="text" id="codiceSanitarioDirettore" name="codiceSanitarioDirettore" required>
        </div>
                <input type="submit" value="Inserisci Ospedale">
        </form>
    </div>
    
    <?php
    include 'connect.php';

    $message = '';
    $messageType = ''; // 'success' o 'error'

    function isCSDtaken($conn, $codice){
        $sql = "SELECT COUNT(*) FROM Ospedali WHERE CodiceSanitarioDirettore = :codiceDirettore";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':codiceDirettore', $codice);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0){
            return true;
        }else{
            return false;
        }
    }

    function getNewHospitalID($conn){
        $sql = "SELECT MAX(IDOspedale) AS IDOspedaleMassimo FROM Ospedali";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $risultato = $stmt->fetch(PDO::FETCH_ASSOC);
        $newId = 0;
    
        if ($risultato && !is_null($risultato['IDOspedaleMassimo'])) {
            $maxId = $risultato['IDOspedaleMassimo'];
            $newId = $maxId + 1;
        } else {
            $newId = 1;
        }

        return $newId;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $codiceDirettoreInserito = $_POST['codiceSanitarioDirettore'];

            if (isCSDtaken($conn, $codiceDirettoreInserito)) {
                $message = "Errore: Il codice sanitario del direttore fornito è già in uso da un altro ospedale.";
                $messageType = 'error';
            } else {
                $stmt = $conn->prepare("INSERT INTO Ospedali (IDOspedale, NomeOspedale, Indirizzo, NumeroCivico, Citta, NumeroTelefono, CodiceSanitarioDirettore) VALUES (:id, :nomeOspedale, :indirizzo, :numeroCivico, :citta, :numeroTelefonico, :codiceSanitarioDirettore)");

                $stmt->bindParam(':id', getNewHospitalID($conn));
                $stmt->bindParam(':nomeOspedale', $_POST['nomeOspedale']);
                $stmt->bindParam(':indirizzo', $_POST['indirizzo']);
                $stmt->bindParam(':numeroCivico', $_POST['numeroCivico']);
                $stmt->bindParam(':citta', $_POST['citta']);
                $stmt->bindParam(':numeroTelefonico', $_POST['numeroTelefonico']);
                $stmt->bindParam(':codiceSanitarioDirettore', $codiceDirettoreInserito);

                $stmt->execute();

                $message = "Nuovo ospedale aggiunto con successo! ID: " . $lastId;
                $messageType = 'success';
            }

        } catch(PDOException $e) {
            $message = "Errore nell'inserimento: " . $e->getMessage();
            $messageType = 'error';
        } finally {
            $conn = null; // Chiudi la connessione
        }
    }
?>
    <?php if ($message): // Mostra il messaggio se esiste ?>
        <div class="message <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

<?php
    if (!$error) {
        try {
            
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $sql = "SELECT * FROM Ospedali WHERE 1=1";
            $params = []; //Commento per commit hehehehehehe

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['NomeOspedale']) && $_GET['NomeOspedale'] != '') {
                $sql .= " AND NomeOspedale LIKE :NomeOspedale";
                $params[':NomeOspedale'] = '%' . $_GET['NomeOspedale'] . '%';
            }
            if (isset($_GET['Citta']) && $_GET['Citta'] != '') {
                $sql .= " AND Citta LIKE :Citta";
                $params[':Citta'] = '%' . $_GET['Citta'] . '%';
            }
            if (isset($_GET['Indirizzo']) && $_GET['Indirizzo'] != '') {
                $sql .= " AND Indirizzo LIKE :Indirizzo";
                $params[':Indirizzo'] = '%' . $_GET['Indirizzo'] . '%';
            }
            

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $ospedali = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count ($ospedali) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                $firstRow = $ospedali[0];
                foreach   (  $firstRow as $colonna => $valore) {
                    if ($colonna != "IDOspedale") { // Escludi la colonna IDOspedale
                        echo "<th>" . htmlspecialchars($colonna) . "</th>";
                    }
                }
                echo "</tr></thead><tbody>";

                foreach    ( $ospedali as $ospedale) {
                    echo "<tr>";
                    foreach ($ospedale as $colonna => $valore) {
                        if ($colonna != "IDOspedale") { // Escludi la colonna IDOspedale
                            echo "<td>" . htmlspecialchars($valore) . "</td>";
                        }
                    }
                    echo "<a href='?action=edit&id=" . htmlspecialchars($row["IDOspedale"]) . "'>Modifica</a>";
                    echo "<a href='?action=delete&id=" . htmlspecialchars($row["IDOspedale"]) . "'>Elimina</a>";
                    echo "</tr>";                    
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Nessun ospedale trovato con i criteri di ricerca specificati.</p>";
            }
        } catch(PDOException $e) {
            echo "Errore durante la ricerca: " . $e->getMessage();
        }
    }
    ?>
<?php
    include 'footer.html';
?>
</body>
</html>

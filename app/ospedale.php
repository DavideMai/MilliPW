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

    
    
    <?php
    include 'connect.php';

    $message = '';
    $messageType = ''; // 'success' o 'error'

    function isCSDtaken($conn, $codice, $thisId){
        $sql = "SELECT COUNT(*) FROM Ospedali WHERE CodiceSanitarioDirettore = :codiceDirettore AND IDOspedale != :idOspedale";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':codiceDirettore', $codice);
        $stmt->bindParam(':idOspedale', $thisId);
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
            if (isset($_GET['action']) && $_GET['action'] == 'edit'){
                $codiceDirettoreInserito = $_POST['codiceSanitarioDirettore'];
                if (isCSDtaken($conn, $codiceDirettoreInserito, $_GET['id'])) {
                    $message = "Errore: Il codice sanitario del direttore fornito è già in uso da un altro ospedale.";
                    $messageType = 'error';
                } else {
                    $stmt = $conn->prepare("UPDATE Ospedali SET NomeOspedale = :nomeOspedale, Indirizzo = :indirizzo, NumeroCivico = :numeroCivico, Citta = :citta, NumeroTelefono = :numeroTelefono, CodiceSanitarioDirettore = :codiceSanitarioDirettore WHERE IDOspedale = :idOspedale");

                    $stmt->bindParam(':idOspedale', $_GET['id']);
                    $stmt->bindParam(':nomeOspedale', $_POST['nomeOspedale']);
                    $stmt->bindParam(':indirizzo', $_POST['indirizzo']);
                    $stmt->bindParam(':numeroCivico', $_POST['numeroCivico']);
                    $stmt->bindParam(':citta', $_POST['citta']);
                    $stmt->bindParam(':numeroTelefono', $_POST['numeroTelefono']);
                    $stmt->bindParam(':codiceSanitarioDirettore', $codiceDirettoreInserito);

                    $stmt->execute();

                    $message = "Ospedale modificato con successo!";
                    $messageType = 'success';
                }
            }else{
                $codiceDirettoreInserito = $_POST['codiceSanitarioDirettore'];

                if (isCSDtaken($conn, $codiceDirettoreInserito, 0)) {
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
            }
            

        } catch(PDOException $e) {
            $message = "Errore nell'inserimento: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    $actionMessage = "<h1>Aggiungi un nuovo ospedale</h1>";
    if (isset($_GET['action'])) {

        $oldNomeOspedale = "";
        $oldIndirizzo = "";
        $oldNumeroCivico = "";
        $oldCitta = "";
        $oldNumeroTelefonico = "";
        $oldCodiceDirettoreSanitario = "";
        
        if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
            $idToEdit = $_GET['id'];

            $stmt = $conn->prepare("SELECT * FROM Ospedali WHERE IDOspedale = :id");
            $stmt->bindParam(':id', $idToEdit);
            $stmt->execute();
            $ospedale = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ospedale) {
                $ospedaleToEdit = $ospedale;

                $oldNomeOspedale = $ospedaleToEdit['NomeOspedale'];
                $oldIndirizzo = $ospedaleToEdit['Indirizzo'];
                $oldNumeroCivico = $ospedaleToEdit['NumeroCivico'];
                $oldCitta = $ospedaleToEdit['Citta'];
                $oldNumeroTelefonico = $ospedaleToEdit['NumeroTelefono'];
                $oldCodiceDirettoreSanitario = $ospedaleToEdit['CodiceSanitarioDirettore'];

                $actionMessage = "<h1>Modifica i dati dell'ospedale</h1>";
            } else {
                //non deve mai finire qui, se succede allora c'è un id inesistente nel link
            }           
        } else{
            if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
                $idToDelete = $_GET['id'];
                $stmt = $conn->prepare("DELETE FROM Ospedali WHERE IDOspedale = :id");
                $stmt->bindParam(':id', $idToDelete);
                $stmt->execute();
                $message = "Ospedale eliminato con successo!";
            }

            $actionMessage = "<h1>Aggiungi un nuovo ospedale</h1>";
        }
    }

    echo $actionMessage;    
    
?>

    
    <div class="forms-container">
        <form method="POST">
        <div class="form-group"><label for="nomeOspedale">Nome Ospedale:</label>
                <input type="text" id="nomeOspedale" name="nomeOspedale" required 
                value="<?php echo htmlspecialchars($oldNomeOspedale); ?>">
        </div>
        <div class="form-group">       <label for="indirizzo">Indirizzo:</label>
                <input type="text" id="indirizzo" name="indirizzo" required 
                value="<?php echo htmlspecialchars($oldIndirizzo); ?>">
        </div>
        <div class="form-group">        <label for="numeroCivico">Numero Civico:</label>
                <input type="text" id="numeroCivico" name="numeroCivico" pattern="[0-9]*" inputmode="numeric" required 
                value="<?php echo htmlspecialchars($oldNumeroCivico); ?>">
        </div>
        <div class="form-group">      <label for="citta">Città:</label>
                <input type="text" id="citta" name="citta" required 
                value="<?php echo htmlspecialchars($oldCitta); ?>">
        </div>
        <div class="form-group">     <label for="numeroTelefono">Numero Telefonico:</label>
                <input type="text" id="numeroTelefono" name="numeroTelefono" pattern="[0-9]*" inputmode="tel" required 
                value="<?php echo htmlspecialchars($oldNumeroTelefonico); ?>">
        </div>
        <div class="form-group">        <label for="codiceSanitarioDirettore">Codice Sanitario Direttore:</label>
                <input type="text" id="codiceSanitarioDirettore" name="codiceSanitarioDirettore" required 
                value="<?php echo htmlspecialchars($oldCodiceDirettoreSanitario); ?>">
        </div>
                <input type="submit" value="Inserisci Ospedale">
                <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'edit'){
                        //echo "<a href=''> Annulla </a>";
                        echo "<h1>test</h1>";
                    }
                ?>
        </form>
    </div>
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
                    $thisId = 0;
                    foreach ($ospedale as $colonna => $valore) {
                        if ($colonna != "IDOspedale") { // Escludi la colonna IDOspedale
                            echo "<td>" . htmlspecialchars($valore) . "</td>";
                        }else{
                            $thisId = $valore;
                        }
                    }

                    echo "<td>";
                    echo "<a href='?action=edit&id=" . htmlspecialchars($thisId) . "'>Modifica</a>";
                    echo "</td>";

                    echo "<td>";
                    echo "<a href='?action=delete&id=" . htmlspecialchars($thisId) . "'>Elimina</a>";
                    echo "</td>";

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

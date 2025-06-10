<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricerca Ospedali</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php
    include 'nav.html';
?>
    <div class="formheader2"><h2>Ricerca Ospedali</h2></div>
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
            <button type="submit">Cerca Ospedali</button>
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
                if ($_GET['action'] == 'deleteconfirm' && $_POST['action'] == 'delete'){

                    try {
                        //Ogni cancellazione avviene in una singola transazione così che eventuali errori non causino stati impossibili
                        $conn->beginTransaction();

                        $idToDelete = $_POST['idDelete'];

                        //Ricoveri dell'ospedale da rimuovere
                        $stmtGetRicoveri = $conn->prepare("SELECT IDRicovero FROM Ricoveri WHERE IDOspedale = :idOspedale");
                        $stmtGetRicoveri->bindParam(':idOspedale', $idToDelete);
                        $stmtGetRicoveri->execute();
                        $ricoveriIds = $stmtGetRicoveri->fetchAll(PDO::FETCH_COLUMN, 0); 

                        //Elimina i ricoveri da Ricovero_Patologie
                        if (!empty($ricoveriIds)) {
                            //Crea stringa di ricoveri da rimuovere
                            $toRemove = implode(',', array_fill(0, count($ricoveriIds), '?'));

                            $stmtRicoveroPatologie = $conn->prepare("DELETE FROM Ricovero_Patologie WHERE IDRicovero IN (" . $toRemove . ")");
         
                            $stmtRicoveroPatologie->execute($ricoveriIds);
                        }

                        //Rimuovi i ricoveri
                        $stmtRicoveri = $conn->prepare("DELETE FROM Ricoveri WHERE IDOspedale = :idOspedale");
                        $stmtRicoveri->bindParam(':idOspedale', $idToDelete);
                        $stmtRicoveri->execute();

                        //Rimuovi l'ospedale
                        $stmtOspedale = $conn->prepare("DELETE FROM Ospedali WHERE IDOspedale = :idOspedale");
                        $stmtOspedale->bindParam(':idOspedale', $idToDelete, PDO::PARAM_INT);
                        $stmtOspedale->execute();

                        $conn->commit(); //commit transazione

                        $message = "Ospedale eliminato con successo!";
                        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
                    } catch (PDOException $e) {
                        //Rollback se c'è stato un errore
                        $conn->rollBack();
                        $message = "Errore durante l'eliminazione: " . $e->getMessage();
                    }
                    
                }else{
                    //Se non ci sono azioni settate allora si tratta di un inserimento
                    $codiceDirettoreInserito = $_POST['codiceSanitarioDirettore'];

                    if (isCSDtaken($conn, $codiceDirettoreInserito, 0)) {
                        $message = "Errore: Il codice sanitario del direttore fornito è già in uso da un altro ospedale.";
                        $messageType = 'error';
                        //Riempi i campi con i valori già scritti
                        $oldNomeOspedale = $_POST['nomeOspedale'];
                        $oldIndirizzo = $_POST['indirizzo'];
                        $oldNumeroCivico = $_POST['numeroCivico'];
                        $oldCitta = $_POST['citta'];
                        $oldNumeroTelefonico = $_POST['numeroTelefono'];
                        $oldCodiceDirettoreSanitario = $codiceDirettoreInserito;
                    } else {
                        try{
                            $stmt = $conn->prepare("INSERT INTO Ospedali (IDOspedale, NomeOspedale, Indirizzo, NumeroCivico, Citta, NumeroTelefono, CodiceSanitarioDirettore) VALUES (:id, :nomeOspedale, :indirizzo, :numeroCivico, :citta, :numeroTelefono, :codiceSanitarioDirettore)");

                            $insertedId = getNewHospitalID($conn);
                            $stmt->bindParam(':id', $insertedId);
                            $stmt->bindParam(':nomeOspedale', $_POST['nomeOspedale']);
                            $stmt->bindParam(':indirizzo', $_POST['indirizzo']);
                            $stmt->bindParam(':numeroCivico', $_POST['numeroCivico']);
                            $stmt->bindParam(':citta', $_POST['citta']);
                            $stmt->bindParam(':numeroTelefono', $_POST['numeroTelefono']);
                            $stmt->bindParam(':codiceSanitarioDirettore', $codiceDirettoreInserito);

                            $stmt->execute();

                            $message = "Nuovo ospedale aggiunto con successo! ID: " . $insertedId;
                            $messageType = 'success';
                        }catch(PDOException $e){
                            $message = "Errore nell'inserimento: " . $e->getMessage();
                            //Riempi i campi con i valori già scritti
                            $oldNomeOspedale = $_POST['nomeOspedale'];
                            $oldIndirizzo = $_POST['indirizzo'];
                            $oldNumeroCivico = $_POST['numeroCivico'];
                            $oldCitta = $_POST['citta'];
                            $oldNumeroTelefonico = $_POST['numeroTelefono'];
                            $oldCodiceDirettoreSanitario = $codiceDirettoreInserito;

                        }
                        

                    }
                }  
                
            }
            
            

        } catch(PDOException $e) {
            $message = "Errore nell'inserimento: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    $actionMessage = '<div class="formheader1"><h2>Aggiungi un nuovo ospedale</h2></div>';
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

                $actionMessage = '<div class="formheader1"><h2>Modifica i dati dell\'ospedale</h2></div>';
            } else {
                //non deve mai finire qui, se succede allora c'è un id inesistente nel link
            }           
        } else{

            $actionMessage = '<div class="formheader1"><h2>Aggiungi un nuovo ospedale</h2></div>';
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
                <button type="submit">Inserisci Ospedale</button>
                 <?php
                    if (isset($_GET['action']) && $_GET['action'] == 'edit'){
                        echo "<button href='https://programmazionewebmaidavi.altervista.org/app/ospedale.php'> Annulla </button>";
                    }
                ?>
        </form>
    </div>
    
    <?php if (isset($_GET['action']) && $_GET['action'] == 'deleteconfirm') { ?>    
        <td>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="idDelete" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                <button type="submit">Elimina</button>
            </form>
            <a href='https://programmazionewebmaidavi.altervista.org/app/ospedale.php'> Annulla </a>
        </td>
        
    <?php } ?>

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
            $params = [];

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
                echo "<th>Modifica</th>"; // Aggiungi intestazione per la colonna Modifica
                echo "<th>Elimina</th>"; // Aggiungi intestazione per la colonna Elimina
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

                    echo "<td style='text-align: center;'>";
                    echo "<a href='?action=edit&id=" . htmlspecialchars($thisId) . "'><svg xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#8B1A10'><path d='M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z'/></svg></a>";
                    echo "</td>";

                    echo "<td style='text-align: center;'>";
                    echo "<a id='deleteIcon' href='?action=deleteconfirm&id=" . htmlspecialchars($thisId) . "'
                    class='delete-confirm-link' data-original-href='?action=deleteconfirm&id=" . htmlspecialchars($thisId) . "' ><svg id='iconPath' xmlns='http://www.w3.org/2000/svg' height='24px' viewBox='0 -960 960 960' width='24px' fill='#8B1A10'><path d='M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z'/></svg></a>";
                    echo "</td>";

                    echo "</tr>";                    
                }

                echo "</tbody></table>";
            } else {
                echo "<p class='noresult'>Nessun ospedale trovato con i criteri di ricerca specificati.</p>";
            }
        } catch(PDOException $e) {
            echo "Errore durante la ricerca: " . $e->getMessage();
        }
    }
    ?>
<?php
    include 'footer.html';
?>
<script src="script.js"></script> 
</body>
</html>

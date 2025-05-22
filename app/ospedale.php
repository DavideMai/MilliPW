<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ospedali</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php
    include 'header.html';  
    include 'nav.html';
?>
    <h2>Elenco Ospedali</h2>

    <form method="GET" action="">
        <label for="NomeOspedale">Nome:</label>
        <input type="text" name="NomeOspedale" id="NomeOspedale"><br>

        <label for="Citta">Città:</label>
        <input type="text" name="Citta" id="Citta"><br>

        <label for="Indirizzo">Indirizzo:</label>
        <input type="text" name="Indirizzo" id="Indirizzo"><br>

        <input type="submit" value="Cerca Ospedali">
    </form>

    <h1>Aggiungi un nuovo ospedale</h1>
    <form action="inserisci_ospedale.php" method="POST">
        <div>
            <label for="nomeOspedale">Nome Ospedale:</label>
            <input type="text" id="nomeOspedale" name="nomeOspedale" required>
        </div>
        <div>
            <label for="indirizzo">Indirizzo:</label>
            <input type="text" id="indirizzo" name="indirizzo" required>
        </div>
        <div>
            <label for="numeroCivico">Numero Civico:</label>
            <input type="text" id="numeroCivico" name="numeroCivico" pattern="[0-9]*" inputmode="numeric" required>
        </div>
        <div>
            <label for="citta">Città:</label>
            <input type="text" id="citta" name="citta" required>
        </div>
        <div>
            <label for="numeroTelefonico">Numero Telefonico:</label>
            <input type="text" id="numeroTelefonico" name="numeroTelefonico" pattern="[0-9]*" inputmode="tel" required>
        </div>
        <div>
            <label for="codiceSanitarioDirettore">Codice Sanitario Direttore:</label>
            <input type="text" id="codiceSanitarioDirettore" name="codiceSanitarioDirettore" required>
        </div>
        <input type="submit" value="Inserisci Ospedale">
    </form>
    
    <?php
    include 'connect.php';

    if (!$error) {
        try {
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

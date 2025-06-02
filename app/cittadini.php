<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricerca Cittadini</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php	
	include 'nav.html';
?>
    <h2>Ricerca Cittadini</h2>
    <div class="forms-container">
    <form method="GET" action="">
    <div class="form-group"><label for="CSSN">CSSN:</label>
        <input type="text" name="CSSN" id="CSSN">
    </div>

    <div class="form-group"><label for="Nome">Nome:</label>
        <input type="text" name="Nome" id="Nome">
    </div>

    <div class="form-group"> <label for="Cognome">Cognome:</label>
        <input type="text" name="Cognome" id="Cognome">
    </div>

    <div class="form-group"><label for="Indirizzo">Indirizzo:</label>
        <input type="text" name="Indirizzo" id="Indirizzo">
    </div>

    <div class="form-group"><label for="NumeroCivico">Numero civico:</label>
        <input type="int" name="NumeroCivico" id="NumeroCivico">
    </div>

    <div class="form-group"><label for="LuogoNascita">Luogo di nascita:</label>
        <input type="text" name="LuogoNascita" id="LuogoNascita">
    </div>

    <div class="form-group"><label for="DataNascita">Data di nascita:</label>
        <input type="date" name="DataNascita" id="DataNascita">
    </div>
    
        <input type="submit" value="Cerca Cittadini">
    </form>
    </div>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            $sql = "SELECT * FROM Cittadini WHERE 1=1"; // Inizia con una condizione sempre vera per facilitare l'aggiunta di AND
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['CSSN']) && $_GET['CSSN'] != '') {
                $sql .= " AND CSSN LIKE :CSSN ";
                $params[':CSSN'] = '%' . $_GET['CSSN'] . '%'; // Usa LIKE per la ricerca parziale
            }
            if (isset($_GET['Nome']) && $_GET['Nome'] != '') {
                $sql .= " AND Nome LIKE :Nome";
                $params[':Nome'] = '%' . $_GET['Nome'] . '%';
            }
            if (isset($_GET['Cognome']) && $_GET['Cognome'] != '') {
                $sql .= " AND Cognome LIKE :Cognome";
                $params[':Cognome'] = '%' . $_GET['Cognome'] . '%';
            }
            if (isset($_GET['Indirizzo']) && $_GET['Indirizzo'] != '') {
                $sql .= " AND Indirizzo LIKE :Indirizzo";
                $params[':Indirizzo'] = '%' . $_GET['Indirizzo'] . '%';
            }
            if (isset($_GET['NumeroCivico']) && $_GET['NumeroCivico'] != '') {
                $sql .= " AND NumeroCivico LIKE :NumeroCivico";
                $params[':NumeroCivico'] = '%' . $_GET['NumeroCivico'] . '%';
            }
            if (isset($_GET['LuogoNascita']) && $_GET['LuogoNascita'] != '') {
                $sql .= " AND LuogoNascita LIKE :LuogoNascita";
                $params[':LuogoNascita'] = '%' . $_GET['LuogoNascita'] . '%';
            }
            if (isset($_GET['DataNascita']) && $_GET['DataNascita'] != '') {
                $sql .= " AND DataNascita LIKE :DataNascita";
                $params[':DataNascita'] = '%' . $_GET['DataNascita'] . '%';
            }
            

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $cittadini = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count ($cittadini) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                foreach   (  $cittadini[0] as $colonna => $valore) {
                    echo "<th>" . htmlspecialchars($colonna) . "</th>";
                }
                echo "</tr></thead><tbody>";

                foreach    ( $cittadini as $cittadino) {
                    echo "<tr>";
                    foreach ($cittadino as $valore) {
                        echo "<td>" . htmlspecialchars($valore) . "</td>";
                    }
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Nessun cittadino trovato con i criteri di ricerca specificati.</p>";
            }
        } catch(PDOException $e) {
            echo "Errore durante la ricerca: " . $e->getMessage();
        }
    }
    ?>
<?php
    include 'footer.html'; //commento
?>
</body>
</html>
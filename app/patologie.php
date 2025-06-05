<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Ricerca Patologie</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php
    include 'nav.html';
?>
    <div class="formheader2"><h2>Ricerca Patologie</h2></div>

    <div class="forms-container">
        <form method="GET" action="">
            <div class="form-group"> <label for="NomePatologia">Nome Patologia:</label>
                <input type="text" name="NomePatologia" id="NomePatologia">
            </div>

            <div class="form-group"> <label for="Tipologia">Tipologia:</label>
                <input type="text" name="Tipologia" id="Tipologia">
            </div>

            <button type="submit">Cerca Patologie<button>
        </form>
    </div>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            $sql = "SELECT * FROM Patologie WHERE 1=1";
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['NomePatologia']) && $_GET['NomePatologia'] != '') {
                $sql .= " AND NomePatologia LIKE :NomePatologia";
                $params[':NomePatologia'] = '%' . $_GET['NomePatologia'] . '%';
            }
            if (isset($_GET['Tipologia']) && $_GET['Tipologia'] != '') {
                $sql .= " AND Tipologia LIKE :Tipologia";
                $params[':Tipologia'] = '%' . $_GET['Tipologia'] . '%';
            }


            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $patologie = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count ($patologie) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                $firstRow = $patologie[0];
                foreach   (  $firstRow as $colonna => $valore) {
                    if ($colonna != "IDPatologia") {  // Escludi la colonna IDPatologia
                        echo "<th>" . htmlspecialchars($colonna) . "</th>";
                    }
                }
                echo "</tr></thead><tbody>";

                foreach    ( $patologie as $patologia) {
                    echo "<tr>";
                    foreach ($patologia as $colonna => $valore) {
                        if ($colonna != "IDPatologia") {  // Escludi la colonna IDPatologia
                            echo "<td>" . htmlspecialchars($valore) . "</td>";
                        }
                    }
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Nessuna patologia trovata con i criteri di ricerca specificati.</p>";
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
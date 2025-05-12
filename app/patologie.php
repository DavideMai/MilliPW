<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ricoveri</title>
</head>
<body>

    <h2>Elenco Ricoveri</h2>

    <form method="GET" action="">
        <label for="IDPatologia">ID Patologia:</label>
        <input type="text" name="IDPatologia" id="IDPatologia"><br>

        <label for="NomePatologia">Nome Patologia:</label>
        <input type="text" name="NomePatologia" id="NomePatologia"><br>

        <label for="Tipologia">Tipologia:</label>
        <input type="text" name="Tipologia" id="Tipologia"><br>
        <input type="submit" value="Cerca Patologie">
    </form>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            $sql = "SELECT * FROM Patologie WHERE 1=1"; // Inizia con una condizione sempre vera per facilitare l'aggiunta di AND
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['IDPatologia']) && $_GET['IDPatologia'] != '') {
                $sql .= " AND IDPatologia LIKE :IDPatologia ";
                $params[':IDPatologia'] = '%' . $_GET['IDPatologia'] . '%'; // Usa LIKE per la ricerca parziale
            }
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
                foreach   (  $patologie[0] as $colonna => $valore) {
                    echo "<th>" . htmlspecialchars($colonna) . "</th>";
                }
                echo "</tr></thead><tbody>";

                foreach    ( $patologie as $patologia) {
                    echo "<tr>";
                    foreach ($patologia as $valore) {
                        echo "<td>" . htmlspecialchars($valore) . "</td>";
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

</body>
</html>
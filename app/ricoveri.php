<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ricoveri</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>

    <h2>Elenco Ricoveri</h2>

    <form method="GET" action="">
        <label for="IDRicovero">ID Ricovero:</label>
        <input type="text" name="IDRicovero" id="IDRicovero"><br>

        <label for="CSSNCittadino">CSSN Cittadino:</label>
        <input type="text" name="CSSNCittadino" id="CSSNCittadino"><br>

        <label for="IDOspedale">ID Ospedale:</label>
        <input type="text" name="IDOspedale" id="IDOspedale"><br>

        <label for="IDPatologia">ID Patologia:</label>
        <input type="text" name="IDPatologia" id="IDPatologia"><br>

        <label for="DataRicovero">Data Ricovero:</label>
        <input type="date" name="DataRicovero" id="DataRicovero"><br>

        <label for="DurataRicovero">Durata Ricovero:</label>
        <input type="number" name="DurataRicovero" id="DurataRicovero"><br>

        <label for="CostoRicovero">Costo Ricovero:</label>
        <input type="number" name="CostoRicovero" id="CostoRicovero"><br>

        <label for="MotivoRicovero">Motivo Ricovero:</label>
        <input type="text" name="MotivoRicovero" id="MotivoRicovero"><br>

        <input type="submit" value="Cerca Ricoveri">
    </form>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            $sql = "SELECT * FROM Ricoveri WHERE 1=1"; // Inizia con una condizione sempre vera per facilitare l'aggiunta di AND
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['IDRicovero']) && $_GET['IDRicovero'] != '') {
                $sql .= " AND IDRicovero LIKE :IDRicovero";
                $params[':IDRicovero'] = '%' . $_GET['IDRicovero'] . '%'; // Usa LIKE per la ricerca parziale
            }
            if (isset($_GET['CSSNCittadino']) && $_GET['CSSNCittadino'] != '') {
                $sql .= " AND CSSNCittadino LIKE :CSSNCittadino";
                $params[':CSSNCittadino'] = '%' . $_GET['CSSNCittadino'] . '%';
            }
            if (isset($_GET['IDOspedale']) && $_GET['IDOspedale'] != '') {
                $sql .= " AND IDOspedale LIKE :IDOspedale";
                $params[':IDOspedale'] = '%' . $_GET['IDOspedale'] . '%';
            }
            if (isset($_GET['IDPatologia']) && $_GET['IDPatologia'] != '') {
                $sql .= " AND IDPatologia LIKE :IDPatologia";
                $params[':IDPatologia'] = '%' . $_GET['IDPatologia'] . '%';
            }
            if (isset($_GET['DataRicovero']) && $_GET['DataRicovero'] != '') {
                $sql .= " AND DataRicovero = :DataRicovero";
                $params[':DataRicovero'] = $_GET['DataRicovero'];
            }
            if (isset($_GET['DurataRicovero']) && $_GET['DurataRicovero'] != '') {
                $sql .= " AND DurataRicovero = :DurataRicovero";
                $params[':DurataRicovero'] = $_GET['DurataRicovero'];
            }
            if (isset($_GET['CostoRicovero']) && $_GET['CostoRicovero'] != '') {
                $sql .= " AND CostoRicovero = :CostoRicovero";
                $params[':CostoRicovero'] = $_GET['CostoRicovero'];
            }
            if (isset($_GET['MotivoRicovero']) && $_GET['MotivoRicovero'] != '') {
                $sql .= " AND MotivoRicovero LIKE :MotivoRicovero";
                $params[':MotivoRicovero'] = '%' . $_GET['MotivoRicovero'] . '%';
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $ricoveri = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count($ricoveri) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                foreach ($ricoveri[0] as $colonna => $valore) {
                    echo "<th>" . htmlspecialchars($colonna) . "</th>";
                }
                echo "</tr></thead><tbody>";

                foreach ($ricoveri as $ricovero) {
                    echo "<tr>";
                    foreach ($ricovero as $valore) {
                        echo "<td>" . htmlspecialchars($valore) . "</td>";
                    }
                    echo "</tr>";
                }

                echo "</tbody></table>";
            } else {
                echo "<p>Nessun ricovero trovato con i criteri di ricerca specificati.</p>";
            }
        } catch(PDOException $e) {
            echo "Errore durante la ricerca: " . $e->getMessage();
        }
    }
    ?>

</body>
</html>
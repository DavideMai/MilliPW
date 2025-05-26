<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ricoveri</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<?php
    include 'header.html';
    include 'nav.html';
?>
    <h2>Elenco Ricoveri</h2>

    <form method="GET" action="">
        <label for="CSSNCittadino">CSSN Cittadino:</label>
        <input type="text" name="CSSNCittadino" id="CSSNCittadino" value="<?php echo htmlspecialchars($_GET['CSSNCittadino'] ?? ''); ?>"><br>

        <label for="NomeOspedale">Nome Ospedale:</label>
        <input type="text" name="NomeOspedale" id="NomeOspedale" value="<?php echo htmlspecialchars($_GET['NomeOspedale'] ?? ''); ?>"><br>

        <label for="DataRicovero">Data Ricovero:</label>
        <input type="date" name="DataRicovero" id="DataRicovero" value="<?php echo htmlspecialchars($_GET['DataRicovero'] ?? ''); ?>"><br>

        <label for="DurataRicovero">Durata Ricovero:</label>
        <input type="number" name="DurataRicovero" id="DurataRicovero" value="<?php echo htmlspecialchars($_GET['DurataRicovero'] ?? ''); ?>"><br>

        <label for="CostoRicovero">Costo Ricovero:</label>
        <input type="number" step="0.01" name="CostoRicovero" id="CostoRicovero" value="<?php echo htmlspecialchars($_GET['CostoRicovero'] ?? ''); ?>"><br>

        <label for="MotivoRicovero">Motivo Ricovero:</label>
        <input type="text" name="MotivoRicovero" id="MotivoRicovero" value="<?php echo htmlspecialchars($_GET['MotivoRicovero'] ?? ''); ?>"><br>

        <input type="submit" value="Cerca Ricoveri">
        <input type="button" value="Mostra Tutti" onclick="window.location.href='ricoveri.php'">
    </form>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            // Seleziona i campi della tabella Ricoveri e il NomeOspedale dalla tabella Ospedali
            // Verrà aggiunta dinamicamente la colonna delle patologie
            $sql = "SELECT 
                        r.IDRicovero, 
                        r.CSSNCittadino, 
                        o.NomeOspedale, 
                        r.DataRicovero, 
                        r.DurataRicovero, 
                        r.CostoRicovero, 
                        r.MotivoRicovero
                    FROM 
                        Ricoveri r
                    JOIN 
                        Ospedali o ON r.IDOspedale = o.IDOspedale
                    WHERE 1=1";
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['CSSNCittadino']) && $_GET['CSSNCittadino'] != '') {
                $sql .= " AND r.CSSNCittadino LIKE :CSSNCittadino";
                $params[':CSSNCittadino'] = '%' . $_GET['CSSNCittadino'] . '%';
            }
            if (isset($_GET['NomeOspedale']) && $_GET['NomeOspedale'] != '') {
                $sql .= " AND o.NomeOspedale LIKE :NomeOspedale";
                $params[':NomeOspedale'] = '%' . $_GET['NomeOspedale'] . '%';
            }
            if (isset($_GET['DataRicovero']) && $_GET['DataRicovero'] != '') {
                $sql .= " AND r.DataRicovero = :DataRicovero";
                $params[':DataRicovero'] = $_GET['DataRicovero'];
            }
            if (isset($_GET['DurataRicovero']) && $_GET['DurataRicovero'] != '') {
                $sql .= " AND r.DurataRicovero = :DurataRicovero";
                $params[':DurataRicovero'] = $_GET['DurataRicovero'];
            }
            if (isset($_GET['CostoRicovero']) && $_GET['CostoRicovero'] != '') {
                $sql .= " AND r.CostoRicovero = :CostoRicovero";
                $params[':CostoRicovero'] = $_GET['CostoRicovero'];
            }
            if (isset($_GET['MotivoRicovero']) && $_GET['MotivoRicovero'] != '') {
                $sql .= " AND r.MotivoRicovero LIKE :MotivoRicovero";
                $params[':MotivoRicovero'] = '%' . $_GET['MotivoRicovero'] . '%';
            }

            // Aggiungi un ORDER BY per garantire un ordine consistente
            $sql .= " ORDER BY r.IDRicovero DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $ricoveri = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($ricoveri) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                // Intestazioni della tabella
                foreach ($ricoveri[0] as $colonna => $valore) {
                    if ($colonna != "IDRicovero") { // Non visualizzare IDRicovero
                        echo "<th>" . htmlspecialchars($colonna) . "</th>";
                    }
                }
                echo "<th>Patologie Associate</th>"; // Nuova colonna per le patologie
                echo "</tr></thead><tbody>";

                foreach ($ricoveri as $ricovero) {
                    echo "<tr>";
                    foreach ($ricovero as $colonna => $valore) {
                        if ($colonna != "IDRicovero") { // Non visualizzare IDRicovero
                            echo "<td>" . htmlspecialchars($valore) . "</td>";
                        }
                    }

                    // Recupera e visualizza le patologie associate a questo ricovero
                    $idRicoveroCorrente = $ricovero['IDRicovero'];
                    $sqlPatologie = "SELECT p.NomePatologia
                                     FROM Ricovero_Patologie rp
                                     JOIN Patologie p ON rp.IDPatologia = p.IDPatologia
                                     WHERE rp.IDRicovero = :IDRicovero";
                    $stmtPatologie = $conn->prepare($sqlPatologie);
                    $stmtPatologie->execute([':IDRicovero' => $idRicoveroCorrente]);
                    $patologieAssociate = $stmtPatologie->fetchAll(PDO::FETCH_COLUMN, 0); // Recupera solo la colonna NomePatologia

                    echo "<td>";
                    if (!empty($patologieAssociate)) {
                        echo implode(", ", array_map('htmlspecialchars', $patologieAssociate));
                    } else {
                        echo "Nessuna";
                    }
                    echo "</td>";
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
<?php
    include 'footer.html';
?>
</body>
</html>
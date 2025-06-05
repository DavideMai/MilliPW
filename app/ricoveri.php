<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ricoveri</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <style>
        /* Puoi aggiungere qui o nel tuo style.css uno stile per gli elenchi non ordinati */
        td ul {
            margin: 0;
            padding-left: 20px; /* Indenta leggermente l'elenco */
        }
    </style>
</head>
<body>
<?php
    include 'header.html';
    include 'nav.html';
?>
    <div class="formheader2"><h2>Ricerca Ricoveri</h2></div>
        <div class="forms-container">
    <form method="GET" action="">
        <div class="form-group"><label for="CSSNCittadino">CSSN Cittadino:</label>
        <input type="text" name="CSSNCittadino" id="CSSNCittadino" value="<?php echo htmlspecialchars($_GET['CSSNCittadino'] ?? ''); ?>"><br>
        </div>
        
        <div class="form-group"><label for="NomeOspedale">Nome Ospedale:</label>
        <input type="text" name="NomeOspedale" id="NomeOspedale" value="<?php echo htmlspecialchars($_GET['NomeOspedale'] ?? ''); ?>"><br>
        </div>
        
        <div class="form-group"><label for="DataRicovero">Data Ricovero:</label>
        <input type="date" name="DataRicovero" id="DataRicovero" value="<?php echo htmlspecialchars($_GET['DataRicovero'] ?? ''); ?>"><br>
        </div>
        
        <div class="form-group"><label for="DurataRicovero">Durata Ricovero:</label>
        <input type="number" name="DurataRicovero" id="DurataRicovero" value="<?php echo htmlspecialchars($_GET['DurataRicovero'] ?? ''); ?>"><br>
        </div>
        
        <div class="form-group"><label for="CostoRicovero">Costo Ricovero:</label>
        <input type="number" step="0.01" name="CostoRicovero" id="CostoRicovero" value="<?php echo htmlspecialchars($_GET['CostoRicovero'] ?? ''); ?>"><br>
        </div>
        
        <div class="form-group"><label for="MotivoRicovero">Motivo Ricovero:</label>
        <input type="text" name="MotivoRicovero" id="MotivoRicovero" value="<?php echo htmlspecialchars($_GET['MotivoRicovero'] ?? ''); ?>"><br>
        </div>

        <div class="form-group">
        <button type="submit">Cerca Ricoveri</button>
        <button onclick="window.location.href='ricoveri.php'">Mostra Tutti</button>
        </div>
    </form>
    </div>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
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

            $sql .= " ORDER BY r.IDRicovero DESC";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $ricoveri = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($ricoveri) > 0) {
                echo "<table>";
                echo "<thead><tr>";
                foreach ($ricoveri[0] as $colonna => $valore) {
                    if ($colonna != "IDRicovero") {
                        echo "<th>" . htmlspecialchars($colonna) . "</th>";
                    }
                }
                echo "<th>Patologie Associate</th>";
                echo "</tr></thead><tbody>";

                foreach ($ricoveri as $ricovero) {
                    echo "<tr>";
                    foreach ($ricovero as $colonna => $valore) {
                        if ($colonna != "IDRicovero") {
                            echo "<td>" . htmlspecialchars($valore) . "</td>";
                        }
                    }

                    $idRicoveroCorrente = $ricovero['IDRicovero'];
                    $sqlPatologie = "SELECT p.NomePatologia
                                     FROM Ricovero_Patologie rp
                                     JOIN Patologie p ON rp.IDPatologia = p.IDPatologia
                                     WHERE rp.IDRicovero = :IDRicovero";
                    $stmtPatologie = $conn->prepare($sqlPatologie);
                    $stmtPatologie->execute([':IDRicovero' => $idRicoveroCorrente]);
                    $patologieAssociate = $stmtPatologie->fetchAll(PDO::FETCH_COLUMN, 0);

                    echo "<td>";
                    if (!empty($patologieAssociate)) {
                        echo "<ul>"; // Inizia l'elenco puntato
                        foreach (array_map('htmlspecialchars', $patologieAssociate) as $patologiaNome) {
                            echo "<li>" . $patologiaNome . "</li>"; // Ogni patologia è un elemento dell'elenco
                        }
                        echo "</ul>"; // Chiude l'elenco puntato
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
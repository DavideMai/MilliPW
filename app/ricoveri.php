<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ricoveri</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<>
<div?php
    include 'header.html';
    include 'nav.html';
?>
    <h2>Elenco Ricoveri</h2>
    <div class="forms-container">
        <form method="GET" action="">
        <div class="form-group"><label for="CSSNCittadino">CSSN Cittadino:</label>
            <input type="text" name="CSSNCittadino" id="CSSNCittadino">
        </div>

        <div class="form-group"><label for="NomeOspedale">Nome Ospedale:</label>
            <input type="text" name="NomeOspedale" id="NomeOspedale">
        </div>

        <div class="form-group"><label for="NomePatologia">Nome Patologia:</label>
            <input type="text" name="NomePatologia" id="NomePatologia">
        </div> 

        <div class="form-group"><label for="DataRicovero">Data Ricovero:</label>
            <input type="date" name="DataRicovero" id="DataRicovero">
        </div>

        <div class="form-group"><label for="DurataRicovero">Durata Ricovero:</label>
            <input type="number" name="DurataRicovero" id="DurataRicovero">
        </div>

        <div class="form-group"><label for="CostoRicovero">Costo Ricovero:</label>
            <input type="number" name="CostoRicovero" id="CostoRicovero">
        </div>

        <div class="form-group"><label for="MotivoRicovero">Motivo Ricovero:</label>
            <input type="text" name="MotivoRicovero" id="MotivoRicovero">
        </div>

            <input type="submit" value="Cerca Ricoveri">
        </form>
    </di>

    <?php
    include 'connect.php';

    if (!$error) { //commento per commit heheh
        try {
            $sql = "SELECT r.IDRicovero, r.CSSNCittadino, o.NomeOspedale, p.NomePatologia, r.DataRicovero, r.DurataRicovero, r.CostoRicovero, r.MotivoRicovero 
                    FROM Ricoveri r
                    JOIN Ospedali o ON r.IDOspedale = o.IDOspedale
                    JOIN Patologie p ON r.IDPatologia = p.IDPatologia
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
            if (isset($_GET['NomePatologia']) && $_GET['NomePatologia'] != '') {
                $sql .= " AND p.NomePatologia LIKE :NomePatologia";
                $params[':NomePatologia'] = '%' . $_GET['NomePatologia'] . '%';
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
<?php
    include 'footer.html';
?>
</body>
</html>

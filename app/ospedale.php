<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Ospedali</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #dddddd;
        }
        form {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ccc;
        }
        form label {
            display: inline-block;
            width: 150px;
            margin-bottom: 5px;
        }
        form input[type="text"], form input[type="number"], form input[type="date"] {
            width: 200px;
            padding: 5px;
            margin-bottom: 10px;
        }
        form input[type="submit"] {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h2>Elenco Ospedali</h2>

    <form method="GET" action="">
        <label for="IDOspedale">ID:</label>
        <input type="text" name="IDOspedale" id="IDOspedale"><br>

        <label for="NomeOspedale">Nome:</label>
        <input type="text" name="NomeOspedale" id="NomeOspedale"><br>

        <label for="Citta">Città:</label>
        <input type="text" name="Citta" id="Citta"><br>

        <label for="Indirizzo">Indirizzo:</label>
        <input type="text" name="Indirizzo" id="Indirizzo"><br>

        <input type="submit" value="Cerca Ospedali">
    </form>

    <?php
    include 'connect.php';

    if (!$error) {
        try {
            $sql = "SELECT * FROM Ospedali WHERE 1=1"; // Inizia con una condizione sempre vera per facilitare l'aggiunta di AND
            $params = [];

            // Costruisci la query dinamicamente in base ai campi compilati nel form
            if (isset($_GET['IDOspedale']) && $_GET['IDOspedale'] != '') {
                $sql .= " AND IDOspedale LIKE :IDOspedale ";
                $params[':IDOspedale'] = '%' . $_GET['IDOspedale'] . '%'; // Usa LIKE per la ricerca parziale
            }
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
                foreach   (  $ospedali[0] as $colonna => $valore) {
                    echo "<th>" . htmlspecialchars($colonna) . "</th>";
                }
                echo "</tr></thead><tbody>";

                foreach    ( $ospedali as $ospedale) {
                    echo "<tr>";
                    foreach ($ospedale as $valore) {
                        echo "<td>" . htmlspecialchars($valore) . "</td>";
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

</body>
</html>
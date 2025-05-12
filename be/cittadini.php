<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Elenco Cittadini</title>
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

    <h2>Elenco Cittadini</h2>

    <form method="GET" action="">
        <label for="CSSN">CSSN:</label>
        <input type="text" name="CSSN" id="CSSN"><br>

        <label for="Nome">Nome:</label>
        <input type="text" name="Nome" id="Nome"><br>

        <label for="Cognome">Cognome:</label>
        <input type="text" name="Cognome" id="Cognome"><br>

        <label for="Indirizzo">Indirizzo:</label>
        <input type="text" name="Indirizzo" id="Indirizzo"><br>

        <label for="NumeroCivico">Numero civico:</label>
        <input type="int" name="NumeroCivico" id="NumeroCivico"><br>

        <label for="LuogoNascita">Luogo di nascita:</label>
        <input type="text" name="LuogoNascita" id="LuogoNascita"><br>

        <label for="DataNascita">Data di nascita:</label>
        <input type="date" name="DataNascita" id="DataNascita"><br>

        <input type="submit" value="Cerca Cittadini">
    </form>

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

</body>
</html>
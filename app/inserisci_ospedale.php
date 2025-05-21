<?php
include 'connect.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO Ospedali (NomeOspedale, Indirizzo, NumeroCivico, Citta, NumeroTelefono, CodiceSanitarioDirettore) VALUES (:nomeOspedale, :indirizzo, :numeroCivico, :citta, :numeroTelefonico, :codiceSanitarioDirettore)");

    $stmt->bindParam(':nomeOspedale', $_POST['nomeOspedale']);
    $stmt->bindParam(':indirizzo', $_POST['indirizzo']);
    $stmt->bindParam(':numeroCivico', $_POST['numeroCivico']);
    $stmt->bindParam(':citta', $_POST['citta']);
    $stmt->bindParam(':numeroTelefonico', $_POST['numeroTelefonico']);
    $stmt->bindParam(':codiceSanitarioDirettore', $_POST['codiceSanitarioDirettore']);

    $stmt->execute();

    echo "Nuovo ospedale aggiunto con successo!";
} catch(PDOException $e) {
    echo "Errore nell'inserimento: " . $e->getMessage();
}

$conn = null;
?>

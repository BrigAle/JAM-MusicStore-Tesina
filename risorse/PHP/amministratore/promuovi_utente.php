<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}
// prendo lo stato dal db
require_once("../connection.php");
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
$id = $_GET['id'];
$sql = "UPDATE utente SET ruolo = 'gestore' WHERE id = " . $id;

if ($conn->query($sql) === TRUE) {
    echo "Ruolo utente aggiornato con successo a gestore.";
} else {
    echo "Errore durante l'aggiornamento del ruolo: " . $conn->error;
}


$conn->close();
header("Location: ../../../gestione_utenti_admin.php");
exit();

?>

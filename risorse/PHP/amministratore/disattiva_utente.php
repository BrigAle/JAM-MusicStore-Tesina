<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}
// prendo lo stato dal db
require_once("../connection.php");
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
$id = $_GET['id']; // Prende l'id passato via GET


$sql = "UPDATE utente SET stato = 0 WHERE id = $id";

// Esegue la query
if ($conn->query($sql) === TRUE) {
    $_SESSION['successo_msg'] = "Utente disattivato con successo.";
} else {
    $_SESSION['errore_msg'] =  "Errore durante l'aggiornamento dello stato: " . $conn->error;
}

$conn->close();
header("Location: ../../../gestione_utenti_admin.php");
exit();

?>

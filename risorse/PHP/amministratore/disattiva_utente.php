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
$sql = "UPDATE utente SET stato = 0 WHERE id = " . $_GET['id'];
$conn->query($sql);
$conn->close();
header("Location: ../../../gestione_utenti_admin.php");
exit();

?>

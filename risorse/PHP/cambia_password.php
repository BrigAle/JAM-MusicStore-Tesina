<?php

// Includi il file di sessione per accedere alle variabili di sessione
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    header("Location: ../../login.php");
    exit();
}
$sessionId = $_SESSION['id'];
$username_corrente = $_SESSION['username'];

$password_corrente = $_POST['current_password'] ?? '';
$nuova_password = $_POST['new_password'] ?? '';
$conferma_password = $_POST['confirm_password'] ?? '';
$_SESSION['pwd_change_message'] = "";

if (empty($password_corrente) || empty($nuova_password) || empty($conferma_password)) {
    $_SESSION['pwd_change_message'] = "Tutti i campi sono obbligatori.";
    header("Location: ../../cambia_password.php");
    exit();
}

if ($nuova_password !== $conferma_password) {
    $_SESSION['pwd_change_message'] = "Le nuove password non corrispondono.";
    header("Location: ../../cambia_password.php");
    exit();
}
// Connessione al database
require_once('connection.php');
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica la password attuale
$query = "SELECT password FROM utente WHERE username = '$username_corrente'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if (!password_verify($password_corrente, $row['password'])) {
        $_SESSION['pwd_change_message'] = "La password attuale è errata.";
        header("Location: ../../cambia_password.php");
        exit();
    }
} else {
    die("Utente non trovato.");
}
// Aggiorna la password
$hashed_password = password_hash($nuova_password, PASSWORD_DEFAULT);
$updateQuery = "UPDATE utente SET password = '$hashed_password' WHERE username = '$username_corrente'";
if (mysqli_query($conn, $updateQuery)) {
    $_SESSION['pwd_change_message'] = "Password aggiornata con successo.";
} else {
    $_SESSION['pwd_change_message'] = "Errore durante l'aggiornamento della password: " . mysqli_error($conn);
}
mysqli_close($conn);
header("Location: ../../profilo.php");
exit();

?>

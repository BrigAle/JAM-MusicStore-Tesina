<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../../../gestione_utenti.php");
    exit();
}

$idUtente = (int)$_GET['id'];

require_once("../connection.php"); 
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Non eliminare l’admin
$check = $conn->query("SELECT ruolo FROM utente WHERE id = $idUtente");
if ($check && $row = $check->fetch_assoc()) {
    if ($row['ruolo'] === 'amministratore') {
        $conn->close();
        header("Location: ../../../gestione_utenti.php");
        exit();
    }
}

// Elimina dal database
$conn->query("DELETE FROM utente WHERE id = $idUtente");
$conn->close();

// --- ELIMINA DAL FILE XML ---
$xmlFile = __DIR__ . "/../../XML/utenti.xml"; // percorso corretto da /risorse/PHP/amministratore/
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load($xmlFile);

$utenti = $doc->getElementsByTagName("utente");
foreach ($utenti as $utente) {
    if ($utente->getAttribute('id') == $idUtente) {
        $utente->parentNode->removeChild($utente);
        break;
    }
}

$doc->save($xmlFile);

// Torna alla pagina
header("Location: ../../../gestione_utenti_admin.php");
exit();
?>

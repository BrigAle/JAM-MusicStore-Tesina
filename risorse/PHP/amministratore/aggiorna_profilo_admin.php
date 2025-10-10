<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    header("Location: ../../login.php");
    exit();
}




// --- Dati inviati dal form ---
// se il campo esiste ma è vuoto, il risultato sarà una stringa vuota
// se il campo non esiste, il risultato sarà null
$id = $_POST['id'] ?? '';        
$username_corrente = $_POST['username_corrente'] ?? ''; 
$nome = $_POST['nome'] ?? '';
$cognome = $_POST['cognome'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$indirizzo = $_POST['indirizzo'] ?? '';
$email = $_POST['email'] ?? '';
$nuovo_username = $_POST['username'] ?? '';

// ==========================
// --- AGGIORNAMENTO XML ---
// ==========================

$xmlFile = "../../../risorse/XML/utenti.xml";

$doc = new DOMDocument();
$doc->load($xmlFile);

$utenti = $doc->getElementsByTagName("utente");
$found = false;

foreach ($utenti as $utente) {
    $idUtente = $utente->getAttribute('id');
    if ($idUtente === (string)$id) {
        $found = $utente;

        // Aggiorna solo i campi compilati
        if (!empty($nome)) {
            $utente->getElementsByTagName('nome')->item(0)->nodeValue = $nome;
        }
        if (!empty($cognome)) {
            $utente->getElementsByTagName('cognome')->item(0)->nodeValue = $cognome;
        }
        if (!empty($telefono)) {
            $utente->getElementsByTagName('telefono')->item(0)->nodeValue = $telefono;
        }
        if (!empty($indirizzo)) {
            $utente->getElementsByTagName('indirizzo')->item(0)->nodeValue = $indirizzo;
        }

        break;
    }
}

if ($found) {
    $doc->save($xmlFile);
} else {
    // Se l'utente non è trovato nell'XML, logga l'errore (opzionale)
    error_log("Utente NON trovato nell'XML. Session id: $sessionId");
}

// ==========================
// --- AGGIORNAMENTO DATABASE ---
// ==========================
require_once('../connection.php');
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera l'utente dal DB
$query = "SELECT * FROM utente WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result) {
    $utente = mysqli_fetch_array($result, MYSQLI_ASSOC);

    // Aggiorna solo email se compilata
    if (!empty($email)) {
        $updateEmail = "UPDATE utente SET email = '$email' WHERE username = '$username_corrente'";
        mysqli_query($conn, $updateEmail);
    }

    // Aggiorna solo username se compilato
    if (!empty($nuovo_username) && $nuovo_username !== $username_corrente) {
        $updateUsername = "UPDATE utente SET username = '$nuovo_username' WHERE username = '$username_corrente'";
        mysqli_query($conn, $updateUsername);

        // Aggiorna la sessione
        $_SESSION['username'] = $nuovo_username;
    }
}

mysqli_close($conn);
// Reindirizza alla pagina del profilo
header("Location: ../../../gestione_utenti.php");
exit();
?>

<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    $_SESSION['errore_msg'] = "Devi essere loggato per aggiornare la reputazione.";
    header("Location: ../../login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';

if (empty($id_utente)) {
    $_SESSION['errore_msg'] = "Errore: ID utente non trovato.";
    header("Location: ../../profilo.php");
    exit();
}


$recensioniFile = '../XML/recensioni.xml';
$utentiFile = '../XML/utenti.xml';


if (!file_exists($recensioniFile) || !file_exists($utentiFile)) {
    $_SESSION['errore_msg'] = "File XML mancanti.";
    header("Location: ../../profilo.php");
    exit();
}

$xmlRec = simplexml_load_file($recensioniFile);

$numRecensioni = 0;
$numLikes = 0;
$numDislikes = 0;

// Calcolo parametri dell'utente
foreach ($xmlRec->recensione as $rec) {
    if ((string)$rec->id_utente === (string)$id_utente) {
        $numRecensioni++;
        $numLikes    += (int)$rec->voti_like;
        $numDislikes += (int)$rec->voti_dislike;
    }
}

// calcolo reputazione
$reputazione = 10 * $numRecensioni + 1.5 * $numLikes - 1.2 * $numDislikes;
$reputazione = max(0, round($reputazione, 2)); // non scendere sotto 0 e arrotonda a 2 decimali


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load($utentiFile);

$utenti = $doc->getElementsByTagName('utente');
$utenteTrovato = false;

foreach ($utenti as $utente) {
    $idAttr = $utente->getAttribute('id');
    if ((string)$idAttr === (string)$id_utente) {
        $utenteTrovato = true;

        // Se il campo reputazione esiste, aggiorna; altrimenti lo crea
        $reputazioneNode = $utente->getElementsByTagName('reputazione')->item(0);

        if ($reputazioneNode) {
            $reputazioneNode->nodeValue = $reputazione;
        } else {
            $newRep = $doc->createElement('reputazione', $reputazione);
            $utente->appendChild($newRep);
        }
        break;
    }
}

if ($utenteTrovato) {
    $doc->save($utentiFile);
    $_SESSION['successo_msg'] = "Reputazione aggiornata con successo (Nuovo punteggio: {$reputazione})";
} else {
    $_SESSION['errore_msg'] = "Utente non trovato nel file XML.";
}


header("Location: ../../profilo.php");
exit();

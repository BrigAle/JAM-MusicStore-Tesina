<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] != 'gestore') {
    header("Location: ../../homepage.php");
    exit();
}

$id_utente_recensione = $_POST['id_utente_recensione'] ?? null;
$id_utente_risposta   = $_POST['id_utente_risposta'] ?? null;
$id_risposta          = $_POST['id_risposta'] ?? null;
$id_recensione        = $_POST['id_recensione'] ?? null;
$motivo               = $_POST['motivo'] ?? 'Non specificato';
$id_prodotto          = $_POST['id_prodotto'] ?? '';

$xmlFile = '../XML/segnalazioni.xml';

// --- Carica o crea il file XML ---
$doc = new DOMDocument('1.0', 'UTF-8');
$doc->preserveWhiteSpace = false; // importante: rimuove spazi bianchi inutili
$doc->formatOutput = true;        // ✅ rende il file leggibile con rientri

if (file_exists($xmlFile)) {
    $doc->load($xmlFile);
    $root = $doc->documentElement;
} else {
    // se non esiste, crea la struttura base
    $root = $doc->createElement("segnalazioni");
    $doc->appendChild($root);
}

// --- Calcola nuovo ID ---
$ultimoId = 0;
foreach ($doc->getElementsByTagName("segnalazione") as $s) {
    $id = (int)$s->getAttribute("id");
    if ($id > $ultimoId) $ultimoId = $id;
}
$nuovo_id = $ultimoId + 1;

// --- Crea nuova segnalazione ---
$segnalazione = $doc->createElement("segnalazione");
$segnalazione->setAttribute("id", $nuovo_id);

// Distinzione tra recensione e risposta
if (!empty($id_recensione)) {
    $id_contenuto = $doc->createElement("id_contenuto", $id_recensione);
    $id_contenuto->setAttribute("tipo", "recensione");
    $id_utente = $doc->createElement("id_utente", $id_utente_recensione);
} elseif (!empty($id_risposta)) {
    $id_contenuto = $doc->createElement("id_contenuto", $id_risposta);
    $id_contenuto->setAttribute("tipo", "risposta");
    $id_utente = $doc->createElement("id_utente", $id_utente_risposta);
} else {
    die("Errore: dati segnalazione mancanti");
}

// --- Aggiungi nodi alla segnalazione ---
$segnalazione->appendChild($id_contenuto);
$segnalazione->appendChild($id_utente);
$segnalazione->appendChild($doc->createElement("motivo", htmlspecialchars($motivo)));
$segnalazione->appendChild($doc->createElement("data", date("Y-m-d\TH:i:s")));

// --- Aggiungi e salva con formattazione ---
$root->appendChild($segnalazione);
$doc->save($xmlFile);

$_SESSION['successo_msg'] = "Segnalazione inviata con successo!";
// --- Redirect ---
header("Location: ../../recensioni.php?id_prodotto=" . urlencode($id_prodotto));
exit();
?>

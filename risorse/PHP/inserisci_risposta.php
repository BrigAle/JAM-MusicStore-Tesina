<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    header("Location: ../../login.php");
    exit();
}

$id_recensione = $_POST['id_recensione'] ?? null;
$id_utente     = $_POST['id_utente'] ?? null;
$id_prodotto   = $_POST['id_prodotto'] ?? null;
$commento      = trim($_POST['risposta'] ?? '');

if (!$id_recensione || !$id_utente || !$commento) {
    die("Errore: dati mancanti per inserire la risposta.");
}

// Carica il file XML delle risposte
$xmlPath = '../../risorse/XML/risposte.xml';
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
if (!$doc->load($xmlPath)) {
    die("Errore nel caricamento del file XML delle risposte.");
}

$root = $doc->documentElement;

// Trova l'ultimo ID e genera il nuovo
$ultimo = $root->lastElementChild;
$nextId = $ultimo ? ((int)$ultimo->getAttribute("id") + 1) : 1;

// Crea il nuovo nodo <risposta>
$nuova_risposta = $doc->createElement("risposta");
$nuova_risposta->setAttribute("id", $nextId);
$nuova_risposta->appendChild($doc->createElement("id_recensione", $id_recensione));
$nuova_risposta->appendChild($doc->createElement("id_utente", $id_utente));
$nuova_risposta->appendChild($doc->createElement("commento", $commento));
$nuova_risposta->appendChild($doc->createElement("data", date("Y-m-d")));
$nuova_risposta->appendChild($doc->createElement("ora", date("H:i")));

$root->appendChild($nuova_risposta);
$doc->save($xmlPath);

// Redirect alla pagina delle recensioni del prodotto
header("Location: ../../recensioni.php?id_prodotto=" . urlencode($id_prodotto));
exit();

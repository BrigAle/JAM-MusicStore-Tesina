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

$xmlFile = '../XML/segnalazioni.xml';
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load($xmlFile);

if ($doc === false) {
    die("Errore nel caricamento del file XML");
}

$root = $doc->documentElement;

// ✅ 1️⃣ Calcola nuovo ID segnalazione
$ultimoId = 0;
foreach ($doc->getElementsByTagName("segnalazione") as $s) {
    $id = (int)$s->getAttribute("id");
    if ($id > $ultimoId) $ultimoId = $id;
}
$nuovo_id = $ultimoId + 1;

// ✅ 2️⃣ Crea la nuova segnalazione
$segnalazione = $doc->createElement("segnalazione");
$segnalazione->setAttribute("id", $nuovo_id);

// ✅ 3️⃣ Decidi se si tratta di recensione o risposta

if (!empty($id_recensione)) {
    // caso recensione
    $id_contenuto = $doc->createElement("id_contenuto", $id_recensione);
    $id_contenuto->setAttribute("tipo", "recensione");
    $id_utente = $doc->createElement("id_utente", $id_utente_recensione);
} elseif (!empty($id_risposta)) {
    // caso risposta
    $id_contenuto = $doc->createElement("id_contenuto", $id_risposta);
    $id_contenuto->setAttribute("tipo", "risposta");
    $id_utente = $doc->createElement("id_utente", $id_utente_risposta);
} else {
    die("Errore: dati segnalazione mancanti");
}


// ✅ 4️⃣ Aggiungi nodi alla segnalazione
$segnalazione->appendChild($id_contenuto);
$segnalazione->appendChild($id_utente);
$segnalazione->appendChild($doc->createElement("motivo", htmlspecialchars($motivo)));
$segnalazione->appendChild($doc->createElement("data", date("Y-m-d\TH:i:s")));

// ✅ 5️⃣ Salva nel file
$root->appendChild($segnalazione);
$doc->save($xmlFile);

// ✅ 6️⃣ Redirect
header("Location: ../../recensioni.php?id_prodotto=" . $_POST['id_prodotto']);
exit();
?>

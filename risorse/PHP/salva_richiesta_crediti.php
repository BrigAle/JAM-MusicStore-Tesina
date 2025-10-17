<?php
session_start();

// Controllo login
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
    $_SESSION['errore_msg'] = "Accesso non autorizzato.";
    header("Location: ../../login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
$importo = $_POST['importo'] ?? '';

if (empty($id_utente) || empty($importo) || $importo <= 0) {
    $_SESSION['errore_msg'] = "Importo non valido.";
    header("Location: ../../richiesta_crediti.php");
    exit();
}

// Percorso XML
$xmlFile = "../../risorse/XML/richiesteCrediti.xml";

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

// Carica o crea il file XML
if (!file_exists($xmlFile)) {
    $root = $doc->createElement("richieste");
    $doc->appendChild($root);
} else {
    $doc->load($xmlFile);
    $root = $doc->documentElement;
}

// Calcolo ID progressivo
$lastId = 0;
foreach ($doc->getElementsByTagName('richiesta') as $r) {
    $id = (int)$r->getAttribute('id');
    if ($id > $lastId) $lastId = $id;
}
$newId = $lastId + 1;

// Creazione nuova richiesta
$richiesta = $doc->createElement('richiesta');
$richiesta->setAttribute('id', $newId);

$idUtenteElem = $doc->createElement('id_utente', $id_utente);
$importoElem = $doc->createElement('importo', $importo);
$dataElem = $doc->createElement('data', date('Y-m-d'));
$statoElem = $doc->createElement('stato', 'in attesa');

$richiesta->appendChild($idUtenteElem);
$richiesta->appendChild($importoElem);
$richiesta->appendChild($dataElem);
$richiesta->appendChild($statoElem);

$root->appendChild($richiesta);

// Salvataggio
if ($doc->save($xmlFile)) {
    $_SESSION['successo_msg'] = "Richiesta di crediti inviata con successo! In attesa di approvazione.";
} else {
    $_SESSION['errore_msg'] = "Errore durante il salvataggio della richiesta.";
}

header("Location: ../../richiesta_crediti.php");
exit();
?>
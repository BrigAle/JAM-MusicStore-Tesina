<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
    $_SESSION['errore'] = 'Accesso negato.';
    header("Location: ../../catalogo.php");
    exit();
}

$id_prodotto = $_POST['id'] ?? '';
$quantita = $_POST['quantita'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($id_prodotto) || empty($quantita) || $quantita < 1) {
    $_SESSION['errore'] = 'Dati non validi.';
    header("Location: ../../catalogo.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
if (empty($id_utente)) {
    $_SESSION['errore'] = 'Errore: utente non riconosciuto.';
    header("Location: ../../catalogo.php");
    exit();
}

$carrelliFile = '../XML/carrelli.xml';
$prodottiFile = '../XML/prodotti.xml';

// carico il file dei prodotti per estrarre il prezzo unitario e l'immagine
$prodottiDoc = new DOMDocument();
$prodottiDoc->preserveWhiteSpace = false;
$prodottiDoc->formatOutput = true;

if (!$prodottiDoc->load($prodottiFile)) {
    $_SESSION['errore'] = 'Errore nel caricamento del file prodotti.';
    header("Location: ../../catalogo.php");
    exit();
}

$prodottoTrovato = null;
foreach ($prodottiDoc->getElementsByTagName('prodotto') as $p) {
    $idAttr = $p->getAttribute('id');
    if ($idAttr == $id_prodotto) {
        $prodottoTrovato = $p;
        break;
    }
}

if (!$prodottoTrovato) {
    $_SESSION['errore'] = 'Prodotto non trovato.';
    header("Location: ../../catalogo.php");
    exit();
}

$prezzo_unitario = (float)$prodottoTrovato->getElementsByTagName('prezzo')->item(0)->nodeValue;


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
// se non esiste il root lo creo
if (!file_exists($carrelliFile)) {
    $root = $doc->createElement('carrelli');
    $doc->appendChild($root);
    $doc->save($carrelliFile);
}

if (!$doc->load($carrelliFile)) {
    $_SESSION['errore'] = 'Errore nel caricamento del carrello.';
    header("Location: ../../catalogo.php");
    exit();
}

$root = $doc->documentElement;

//cerco il carrello dell'utente
$carrelloUtente = null;
$ultimoId = 0;

foreach ($doc->getElementsByTagName('carrello') as $c) {
    $idAttr = $c->getAttribute('id');
    if ($idAttr > $ultimoId) $ultimoId = $idAttr;

    $idUtNode = $c->getElementsByTagName('id_utente')->item(0);
    if ($idUtNode && $idUtNode->nodeValue == $id_utente) {
        $carrelloUtente = $c;
        break;
    }
}

// Se non esiste, crealo
if (!$carrelloUtente) {
    $carrelloUtente = $doc->createElement('carrello');
    $carrelloUtente->setAttribute('id', $ultimoId + 1);

    $idUtenteNode = $doc->createElement('id_utente', $id_utente);
    $carrelloUtente->appendChild($idUtenteNode);

    $prodottiNode = $doc->createElement('prodotti');
    $carrelloUtente->appendChild($prodottiNode);

    $totaleNode = $doc->createElement('prezzo_totale_carrello', '0.00');
    $carrelloUtente->appendChild($totaleNode);

    $root->appendChild($carrelloUtente);
}

// Aggiungo o creo nuovo elemento prodotto nel carrello
$prodottiNode = $carrelloUtente->getElementsByTagName('prodotti')->item(0);
if (!$prodottiNode) {
    $prodottiNode = $doc->createElement('prodotti');
    $carrelloUtente->appendChild($prodottiNode);
}

$prodottoNelCarrello = null;
foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
    $idNode = $p->getElementsByTagName('id_prodotto')->item(0);
    if ($idNode && $idNode->nodeValue == $id_prodotto) {
        $prodottoNelCarrello = $p;
        break;
    }
}

if ($prodottoNelCarrello) {
    // se gia' si trova, aggiorna la quantita'
    $quantitaAttuale = (int)$prodottoNelCarrello->getElementsByTagName('quantita')->item(0)->nodeValue;
    $nuovaQuantita = $quantitaAttuale + $quantita;
    $prodottoNelCarrello->getElementsByTagName('quantita')->item(0)->nodeValue = $nuovaQuantita;
    $prodottoNelCarrello->getElementsByTagName('prezzo_totale')->item(0)->nodeValue = number_format($nuovaQuantita * $prezzo_unitario, 2, '.', '');
} else {
    // altrimenti creo nuovo elemento prodotto in carrelli.xml
    $nuovoProdotto = $doc->createElement('prodotto');
    $nuovoProdotto->appendChild($doc->createElement('id_prodotto', $id_prodotto));
    $nuovoProdotto->appendChild($doc->createElement('quantita', $quantita));
    $nuovoProdotto->appendChild($doc->createElement('prezzo_unitario', number_format($prezzo_unitario, 2, '.', '')));
    $nuovoProdotto->appendChild($doc->createElement('prezzo_totale', number_format($prezzo_unitario * $quantita, 2, '.', '')));
    $prodottiNode->appendChild($nuovoProdotto);
}

// Aggiorno il prezzo totale del carrello
$totaleCarrello = 0.0;
foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
    $prezzoTot = (float)$p->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;
    $totaleCarrello += $prezzoTot;
}
$carrelloUtente->getElementsByTagName('prezzo_totale_carrello')->item(0)->nodeValue = number_format($totaleCarrello, 2, '.', '');

//Salva il file XML
if ($doc->save($carrelliFile) === false) {
    $_SESSION['errore_id'] = $id_prodotto;
    $_SESSION['errore_msg'] = 'Errore durante il salvataggio del carrello.';
} else {
    $_SESSION['successo_id'] = $id_prodotto;
    $_SESSION['successo_msg'] = 'Prodotto aggiunto nel carrello!';
}

header("Location: ../../catalogo.php");
exit();
?>

<?php
session_start();


if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
    $_SESSION['errore'] = 'Accesso negato.';
    header("Location: ../../catalogo.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
$id_prodotto = $_POST['id_prodotto'] ?? '';
$nuovaQuantita = isset($_POST['quantita']) ? (int)$_POST['quantita'] : 0;

if (empty($id_utente) || empty($id_prodotto) || $nuovaQuantita < 1) {
    $_SESSION['errore'] = 'Quantità non valida.';
    header("Location: ../../cart.php");
    exit();
}

$carrelliFile = '../XML/carrelli.xml';

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($carrelliFile)) {
    $_SESSION['errore'] = 'Errore nel caricamento del file carrelli.';
    header("Location: ../../cart.php");
    exit();
}

$carrelloUtente = null;
foreach ($doc->getElementsByTagName('carrello') as $c) {
    $idUtNode = $c->getElementsByTagName('id_utente')->item(0);
    if ($idUtNode && $idUtNode->nodeValue == $id_utente) {
        $carrelloUtente = $c;
        break;
    }
}

if (!$carrelloUtente) {
    $_SESSION['errore'] = 'Nessun carrello trovato per questo utente.';
    header("Location: ../../cart.php");
    exit();
}

$prodottiNode = $carrelloUtente->getElementsByTagName('prodotti')->item(0);
if (!$prodottiNode) {
    $_SESSION['errore'] = 'Carrello vuoto.';
    header("Location: ../../cart.php");
    exit();
}

$prodottoTrovato = null;
foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
    $idNode = $p->getElementsByTagName('id_prodotto')->item(0);
    if ($idNode && $idNode->nodeValue == $id_prodotto) {
        $prodottoTrovato = $p;
        break;
    }
}

if ($prodottoTrovato) {
    $prezzoUnitario = (float)$prodottoTrovato->getElementsByTagName('prezzo_unitario')->item(0)->nodeValue;
    $prodottoTrovato->getElementsByTagName('quantita')->item(0)->nodeValue = $nuovaQuantita;
    $prodottoTrovato->getElementsByTagName('prezzo_totale')->item(0)->nodeValue = number_format($nuovaQuantita * $prezzoUnitario, 2, '.', '');

    // aggiorna totale carrello
    $totaleCarrello = 0.0;
    foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
        $prezzoTot = (float)$p->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;
        $totaleCarrello += $prezzoTot;
    }

    $carrelloUtente->getElementsByTagName('prezzo_totale_carrello')->item(0)->nodeValue = number_format($totaleCarrello, 2, '.', '');
    $doc->save($carrelliFile);

    $_SESSION['successo'] = 'Quantità aggiornata con successo.';
} else {
    $_SESSION['errore'] = 'Prodotto non trovato nel carrello.';
}

header("Location: ../../cart.php");
exit();
?>

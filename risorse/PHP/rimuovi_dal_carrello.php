<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
    $_SESSION['errore_msg'] = 'Accesso negato.';
    header("Location: ../../catalogo.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
$id_prodotto = $_POST['id_prodotto'] ?? '';

if (empty($id_utente) || empty($id_prodotto)) {
    $_SESSION['errore_msg'] = 'Dati non validi.';
    header("Location: ../../cart.php");
    exit();
}

$carrelliFile = '../XML/carrelli.xml';

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($carrelliFile)) {
    $_SESSION['errore_msg'] = 'Errore nel caricamento del file carrelli.';
    header("Location: ../../cart.php");
    exit();
}

$root = $doc->documentElement;
$carrelloUtente = null;

// trova il carrello dell'utente
foreach ($doc->getElementsByTagName('carrello') as $c) {
    $idUtNode = $c->getElementsByTagName('id_utente')->item(0);
    if ($idUtNode && $idUtNode->nodeValue == $id_utente) {
        $carrelloUtente = $c;
        break;
    }
}

if (!$carrelloUtente) {
    $_SESSION['errore_msg'] = 'Nessun carrello trovato per questo utente.';
    header("Location: ../../cart.php");
    exit();
}

$prodottiNode = $carrelloUtente->getElementsByTagName('prodotti')->item(0);
if (!$prodottiNode) {
    $_SESSION['errore_msg'] = 'Carrello vuoto.';
    header("Location: ../../cart.php");
    exit();
}

$prodottoDaRimuovere = null;
foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
    $idNode = $p->getElementsByTagName('id_prodotto')->item(0);
    if ($idNode && $idNode->nodeValue == $id_prodotto) {
        $prodottoDaRimuovere = $p;
        break;
    }
}

if ($prodottoDaRimuovere) {
    $prodottiNode->removeChild($prodottoDaRimuovere);

    // controlla se il carrello è vuoto
    if ($prodottiNode->getElementsByTagName('prodotto')->length === 0) {
        // elimina completamente il carrello dell'utente
        $root->removeChild($carrelloUtente);
        $_SESSION['successo_msg'] = 'Carrello svuotato ed eliminato.';
    } else {
        // aggiorna totale carrello
        $totaleCarrello = 0.0;
        foreach ($prodottiNode->getElementsByTagName('prodotto') as $p) {
            $prezzoTot = (float)$p->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;
            $totaleCarrello += $prezzoTot;
        }

        $carrelloUtente->getElementsByTagName('prezzo_totale_carrello')->item(0)->nodeValue = number_format($totaleCarrello, 2, '.', '');
        $_SESSION['successo_msg'] = 'Prodotto rimosso dal carrello.';
    }

    // salva il file aggiornato
    $doc->save($carrelliFile);
} else {
    $_SESSION['errore_msg'] = 'Prodotto non trovato nel carrello.';
}

header("Location: ../../cart.php");
exit();
?>

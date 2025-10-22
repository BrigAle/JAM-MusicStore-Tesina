<?php
session_start();

// --- Controllo login ---
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
    $_SESSION['errore_msg'] = "Effettua il login per completare l'acquisto.";
    header("Location: ../../login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
$metodo = $_POST['metodo_pagamento'] ?? '';
$totaleOrdine = isset($_POST['totale_carrello']) ? floatval($_POST['totale_carrello']) : 0;

if (empty($id_utente) || empty($metodo) || $totaleOrdine <= 0) {
    $_SESSION['errore_msg'] = "Errore nei dati dell'acquisto.";
    header("Location: ../../cart.php");
    exit();
}

// --- Percorsi file XML ---
$carrelliFile = '../XML/carrelli.xml';
$utentiFile = '../XML/utenti.xml';
$prodottiFile = '../XML/prodotti.xml';
$scontiFile = '../XML/sconti.xml';
$storiciFile = '../XML/storico_acquisti.xml';

// --- Caricamento carrello ---
$docCarrelli = new DOMDocument();
$docCarrelli->preserveWhiteSpace = false;
$docCarrelli->formatOutput = true;
$docCarrelli->load($carrelliFile);

$carrelloUtente = null;
foreach ($docCarrelli->getElementsByTagName('carrello') as $c) {
    $idUtNode = $c->getElementsByTagName('id_utente')->item(0);
    if ($idUtNode && $idUtNode->nodeValue == $id_utente) {
        $carrelloUtente = $c;
        break;
    }
}

if (!$carrelloUtente) {
    $_SESSION['errore_msg'] = "Il carrello è vuoto.";
    header("Location: ../../cart.php");
    exit();
}

// --- Carico altri file XML ---
$xmlProdotti = simplexml_load_file($prodottiFile);
$xmlSconti = @simplexml_load_file($scontiFile);

// --- Variabili generali ---
$oggi = date('Y-m-d');
$bonusTotale = 0.00;
$utenteTrovato = false;

// --- Caricamento utenti e gestione pagamento ---
$docUtenti = new DOMDocument();
$docUtenti->preserveWhiteSpace = false;
$docUtenti->formatOutput = true;
$docUtenti->load($utentiFile);

foreach ($docUtenti->getElementsByTagName('utente') as $utente) {
    if ((string)$utente->getAttribute('id') === (string)$id_utente) {
        $utenteTrovato = true;

        $portafoglioNode = $utente->getElementsByTagName('portafoglio')->item(0);
        $creditiNode = $utente->getElementsByTagName('crediti')->item(0);

        $saldoAttuale = $portafoglioNode ? floatval($portafoglioNode->nodeValue) : 0.00;
        $creditiAttuali = $creditiNode ? floatval($creditiNode->nodeValue) : 0.00;

        // Calcolo bonus totale
        foreach ($carrelloUtente->getElementsByTagName('prodotto') as $p) {
            $idProd = (string)$p->getElementsByTagName('id_prodotto')->item(0)->nodeValue;
            foreach ($xmlProdotti->prodotto as $prod) {
                if ((string)$prod['id'] === $idProd) {
                    $bonusTotale += floatval($prod->bonus);
                    break;
                }
            }
        }

        // Pagamento
        if ($metodo === "Portafoglio") {
            if ($saldoAttuale < $totaleOrdine) {
                $_SESSION['errore_msg'] = "❌ Fondi insufficienti nel portafoglio. Ricarica per completare l'acquisto.";
                header("Location: ../../cart.php");
                exit();
            }

            $nuovoSaldo = round($saldoAttuale - $totaleOrdine, 2);
            if ($portafoglioNode) $portafoglioNode->nodeValue = $nuovoSaldo;
            else $utente->appendChild($docUtenti->createElement('portafoglio', $nuovoSaldo));
        }

        // Aggiorna crediti
        $nuoviCrediti = round($creditiAttuali + $bonusTotale, 2);
        if ($creditiNode) $creditiNode->nodeValue = $nuoviCrediti;
        else $utente->appendChild($docUtenti->createElement('crediti', $nuoviCrediti));

        $docUtenti->save($utentiFile);
        break;
    }
}

if (!$utenteTrovato) {
    $_SESSION['errore_msg'] = "Utente non trovato durante l'elaborazione del pagamento.";
    header("Location: ../../cart.php");
    exit();
}

// --- Aggiornamento storico acquisti ---
$docStorico = new DOMDocument();
$docStorico->preserveWhiteSpace = false;
$docStorico->formatOutput = true;

if (!file_exists($storiciFile)) {
    $root = $docStorico->createElement('storici');
    $docStorico->appendChild($root);
    $docStorico->save($storiciFile);
}

$docStorico->load($storiciFile);
$root = $docStorico->documentElement;

$ultimoId = 0;
foreach ($docStorico->getElementsByTagName('storico') as $s) {
    $idAttr = (int)$s->getAttribute('id');
    if ($idAttr > $ultimoId) $ultimoId = $idAttr;
}

$newStorico = $docStorico->createElement('storico');
$newStorico->setAttribute('id', $ultimoId + 1);
$newStorico->appendChild($docStorico->createElement('id_utente', $id_utente));
$newStorico->appendChild($docStorico->createElement('data', $oggi));

$prodottiNode = $docStorico->createElement('prodotti');
$totaleEffettivo = 0.00;


foreach ($carrelloUtente->getElementsByTagName('prodotto') as $p) {
    $idProd    = (string)$p->getElementsByTagName('id_prodotto')->item(0)->nodeValue;
    $quantita  = (int)$p->getElementsByTagName('quantita')->item(0)->nodeValue;

    // Normalizza separatore decimale (es. "19,95" -> "19.95")
    $rawUnit   = (string)$p->getElementsByTagName('prezzo_unitario')->item(0)->nodeValue;
    $rawTot    = (string)$p->getElementsByTagName('prezzo_totale')->item(0)->nodeValue;

    $prezzoUnitario = (float) str_replace(',', '.', $rawUnit);
    $prezzoTotale   = (float) str_replace(',', '.', $rawTot);

    $totaleEffettivo += $prezzoTotale;

    // Scrivi nello storico con punto come separatore (valore "pulito")
    $newProd = $docStorico->createElement('prodotto');
    $newProd->appendChild($docStorico->createElement('id_prodotto', $idProd));
    $newProd->appendChild($docStorico->createElement('quantita', $quantita));
    $newProd->appendChild($docStorico->createElement('prezzo_unitario', number_format($prezzoUnitario, 2, '.', '')));
    $newProd->appendChild($docStorico->createElement('prezzo_totale',   number_format($prezzoTotale,   2, '.', '')));
    $prodottiNode->appendChild($newProd);
}

// --- Totale ordine aggiornato con sconti ---
$newStorico->appendChild($prodottiNode);
$newStorico->appendChild($docStorico->createElement('prezzo_totale_ordine', number_format($totaleEffettivo, 2, '.', '')));
$root->appendChild($newStorico);
$docStorico->save($storiciFile);

// --- Svuota il carrello ---
$carrelloUtente->parentNode->removeChild($carrelloUtente);
$docCarrelli->save($carrelliFile);

// --- Messaggio finale ---
$_SESSION['successo_msg'] = "Acquisto completato con successo! Totale: €" . number_format($totaleEffettivo, 2, ',', '.') .
                            ". Bonus ricevuti: +" . number_format($bonusTotale, 2) . " crediti.";

header("Location: ../../cart.php");
exit();
?>

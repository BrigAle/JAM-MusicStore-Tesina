<?php
session_start();


// Controllo login
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    $_SESSION['errore_msg'] = "Devi essere loggato per ricaricare il portafoglio.";
    header("Location: ../../login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'] ?? '';
$importo = isset($_POST['importo']) ? floatval($_POST['importo']) : 0;

if ($importo <= 0) {
    $_SESSION['errore_msg'] = "Inserisci un importo valido per la ricarica.";
    header("Location: ../../profilo.php");
    exit();
}

$utentiFile = '../XML/utenti.xml';

// Controllo file
if (!file_exists($utentiFile)) {
    $_SESSION['errore_msg'] = "File utenti non trovato.";
    header("Location: ../../profilo.php");
    exit();
}

// Caricamento XML con DOMDocument
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

        // Trova o crea il campo <portafoglio>
        $portafoglioNode = $utente->getElementsByTagName('portafoglio')->item(0);
        $saldoAttuale = $portafoglioNode ? floatval($portafoglioNode->nodeValue) : 0.00;

        $nuovoSaldo = round($saldoAttuale + $importo, 2);

        if ($portafoglioNode) {
            $portafoglioNode->nodeValue = $nuovoSaldo;
        } else {
            $newSaldo = $doc->createElement('portafoglio', $nuovoSaldo);
            $utente->appendChild($newSaldo);
        }

        break;
    }
}

if ($utenteTrovato) {
    $doc->save($utentiFile);
    $_SESSION['successo_msg'] = "✅ Ricarica completata! Nuovo saldo: €" . number_format($nuovoSaldo, 2, ',', '.');
} else {
    $_SESSION['errore_msg'] = "Utente non trovato nel file XML.";
}

header("Location: ../../profilo.php");
exit();
?>

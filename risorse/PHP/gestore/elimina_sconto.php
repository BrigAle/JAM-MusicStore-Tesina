<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlPath = "../../XML/sconti.xml";
$idSconto = $_GET['id_sconto'] ?? '';

if ($idSconto === '') {
    $_SESSION['errore_msg'] = "ID sconto non valido.";
    header("Location: ../../../gestione_sconti_gestore.php");
    exit();
}

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlPath)) {
    $_SESSION['errore_msg'] = "Errore nel caricamento del file XML.";
    header("Location: ../../../gestione_sconti_gestore.php");
    exit();
}

$root = $doc->documentElement;
$sconti = $doc->getElementsByTagName("sconto");
$found = false;

foreach ($sconti as $s) {
    if ($s->getAttribute('id') == $idSconto) {
        $root->removeChild($s);
        $found = true;
        break;
    }
}

if ($found) {
    if ($doc->save($xmlPath)) {
        $_SESSION['successo_msg'] = "✅ Sconto #{$idSconto} rimosso correttamente.";
    } else {
        $_SESSION['errore_msg'] = "Errore durante il salvataggio delle modifiche.";
    }
} else {
    $_SESSION['errore_msg'] = "Sconto non trovato.";
}

header("Location: ../../../gestione_sconti_gestore.php");
exit();
?>

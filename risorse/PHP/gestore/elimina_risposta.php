<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = '../../XML/risposte.xml';

$id_risposta = $_GET['id_risposta'];
if ($id_risposta === null || $id_risposta === '') {
    $_SESSION['elimina_risposta_successo'] = false;
    header("Location: ../../../gestione_contenuti_gestore.php");
    exit();
}


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlFile)) {
    die("Impossibile caricare il file XML ($xmlFile)");
}

$risposta_trovata = false;

// Cerco e rimuovo la risposta con l'ID richiesto
foreach ($doc->getElementsByTagName("risposta") as $r) {
    $id_attr = $r->getAttribute("id");
    if ((int)$id_attr === (int)$id_risposta) {
        $r->parentNode->removeChild($r);
        $risposta_trovata = true;
        break;
    }
}


if ($risposta_trovata) {
    if ($doc->save($xmlFile)) {
        $_SESSION['elimina_risposta_successo'] = true;
    } else {
        $_SESSION['elimina_risposta_successo'] = false;
        die("Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_risposta_successo'] = false;
}

// ✅ Redirect alla pagina di gestione
header("Location: ../../../gestione_contenuti_gestore.php");
exit();
?>

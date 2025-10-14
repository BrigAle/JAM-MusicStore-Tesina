<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = '../../XML/recensioni.xml';

$id_recensione = $_GET['id_recensione'];
if ($id_recensione === null || $id_recensione === '') {
    $_SESSION['elimina_recensione_successo'] = false;
    header("Location: ../../../gestione_contenuti_gestore.php");
    exit();
}


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlFile)) {
    die("Impossibile caricare il file XML ($xmlFile)");
}

$recensione_trovata = false;

// Cerco e rimuovo la recensione con l'ID richiesto
foreach ($doc->getElementsByTagName("recensione") as $r) {
    $id_attr = $r->getAttribute("id");
    if ((int)$id_attr === (int)$id_recensione) {
        $r->parentNode->removeChild($r);
        $recensione_trovata = true;
        break;
    }
}


if ($recensione_trovata) {
    if ($doc->save($xmlFile)) {
        $_SESSION['elimina_recensione_successo'] = true;
    } else {
        $_SESSION['elimina_recensione_successo'] = false;
        die("Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_recensione_successo'] = false;
}

// ✅ Redirect alla pagina di gestione
header("Location: ../../../gestione_contenuti_gestore.php");
exit();
?>

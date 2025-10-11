<?php
session_start();

// ✅ Percorso corretto del file XML
$xmlFile = __DIR__ . '/../../XML/segnalazioni.xml';

// ✅ Controllo parametro GET
$segnalazione_id = $_GET['id_segnalazione'];
if ($segnalazione_id === null || $segnalazione_id === '') {
    $_SESSION['elimina_segnalazione_successo'] = false;
    header("Location: ../../../gestione_utenti.php");
    exit();
}

// ✅ Carico il file XML
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlFile)) {
    die("❌ Errore: impossibile caricare il file XML ($xmlFile)");
}

$segnalazione_trovata = false;

// ✅ Cerco e rimuovo la segnalazione con l'ID richiesto
foreach ($doc->getElementsByTagName("segnalazione") as $s) {
    $id_attr = trim($s->getAttribute("id"));
    if ((int)$id_attr === (int)$segnalazione_id) {
        $s->parentNode->removeChild($s);
        $segnalazione_trovata = true;
        break;
    }
}

// ✅ Salvo le modifiche
if ($segnalazione_trovata) {
    if ($doc->save($xmlFile)) {
        $_SESSION['elimina_segnalazione_successo'] = true;
    } else {
        $_SESSION['elimina_segnalazione_successo'] = false;
        die("❌ Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_segnalazione_successo'] = false;
}

// ✅ Redirect alla pagina di gestione
header("Location: ../../../gestione_utenti.php");
exit();
?>

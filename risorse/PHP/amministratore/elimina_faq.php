<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = '../../XML/FAQs.xml';


$faq_id = $_GET['id_faq'];
if ($faq_id === null || $faq_id === '') {
    $_SESSION['elimina_faq_successo'] = false;
    header("Location: ../../../gestione_utenti.php");
    exit();
}


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlFile)) {
    die("Impossibile caricare il file XML ($xmlFile)");
}

$faq_trovata = false;

// Cerco e rimuovo la FAQ con l'ID richiesto
foreach ($doc->getElementsByTagName("faq") as $f) {
    $id_attr = $f->getAttribute("id");
    if ((int)$id_attr === (int)$faq_id) {
        $f->parentNode->removeChild($f);
        $faq_trovata = true;
        break;
    }
}


if ($faq_trovata) {
    if ($doc->save($xmlFile)) {
        $_SESSION['elimina_faq_successo'] = true;
    } else {
        $_SESSION['elimina_faq_successo'] = false;
        die("❌ Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_faq_successo'] = false;
}

// ✅ Redirect alla pagina di gestione
header("Location: ../../../gestione_contenuti_admin.php");
exit();
?>

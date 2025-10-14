<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = '../../XML/prodotti.xml';


$id_prodotto = $_GET['id_prodotto'];
if ($id_prodotto === null || $id_prodotto === '') {
    $_SESSION['elimina_prodotto_successo'] = false;
    header("Location: ../../../gestione_prodotti_gestore.php");
    exit();
}


$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlFile)) {
    die("Impossibile caricare il file XML ($xmlFile)");
}

$prodotto_trovato = false;

// Cerco e rimuovo il prodotto con l'ID richiesto
foreach ($doc->getElementsByTagName("prodotto") as $p) {
    $id_attr = $p->getAttribute("id");
    if ((int)$id_attr === (int)$id_prodotto) {
        $p->parentNode->removeChild($p);
        $prodotto_trovato = true;
        break;
    }
}


if ($prodotto_trovato) {
    if ($doc->save($xmlFile)) {
        $_SESSION['elimina_prodotto_successo'] = true;
    } else {
        $_SESSION['elimina_prodotto_successo'] = false;
        die("❌ Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_prodotto_successo'] = false;
}

// ✅ Redirect alla pagina di gestione
header("Location: ../../../gestione_prodotti_gestore.php");
exit();
?>


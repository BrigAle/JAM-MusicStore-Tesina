<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = '../../XML/prodotti.xml';
$id_prodotto = $_GET['id_prodotto'] ?? null;

if (empty($id_prodotto)) {
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
$nome_immagine = null;

// Cerco il prodotto da eliminare
foreach ($doc->getElementsByTagName("prodotto") as $p) {
    $id_attr = $p->getAttribute("id");
    if ((int)$id_attr === (int)$id_prodotto) {
        $imgNode = $p->getElementsByTagName("immagine")->item(0);
        if ($imgNode) {
            $nome_immagine = trim($imgNode->nodeValue);
        }
        // Rimuovo il nodo prodotto
        $p->parentNode->removeChild($p);
        $prodotto_trovato = true;
        break;
    }
}

if ($prodotto_trovato) {
    // Salvo il file XML aggiornato
    if ($doc->save($xmlFile)) {

        // Elimino l’immagine associata (se esiste fisicamente)
        if ($nome_immagine) {
            $percorso_img = "../../../risorse/IMG/prodotti/" . $nome_immagine;

            // controllo extra per sicurezza: non deve uscire dalla cartella
            $dir_base = realpath("../../../risorse/IMG/prodotti/");
            $path_reale = realpath($percorso_img);

            if ($path_reale && str_starts_with($path_reale, $dir_base) && file_exists($path_reale)) {
                unlink($path_reale);
            }
        }

        $_SESSION['elimina_prodotto_successo'] = true;
    } else {
        $_SESSION['elimina_prodotto_successo'] = false;
        die("❌ Errore nel salvataggio del file XML.");
    }
} else {
    $_SESSION['elimina_prodotto_successo'] = false;
}

header("Location: ../../../gestione_prodotti_gestore.php");
exit();
?>

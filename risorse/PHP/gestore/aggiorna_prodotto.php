<?php
session_start();

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

// Dati inviati dal form se il campo e' vuoto o non esiste il risultato sara' una stringa vuota
$id = $_POST['id'] ?? '';
$nome = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descrizione = trim($_POST['descrizione'] ?? '');
$prezzo = trim($_POST['prezzo'] ?? '');
$bonus = trim($_POST['bonus'] ?? '');
$immagine = $_FILES['immagine']['name'] ?? '';

if (empty($id)) {
    die("ID prodotto mancante.");
}

$xmlFile = "../../../risorse/XML/prodotti.xml";

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
$doc->load($xmlFile);

$prodotti = $doc->getElementsByTagName("prodotto");
$found = false;

foreach ($prodotti as $prodotto) {
    $idProdotto = $prodotto->getAttribute('id');

    if ($idProdotto === (string)$id) {
        $found = true;

        // Aggiorna solo i campi compilati
        if (!empty($nome)) {
            $prodotto->getElementsByTagName('nome')->item(0)->nodeValue = $nome;
        }
        if (!empty($categoria)) {
            $prodotto->getElementsByTagName('categoria')->item(0)->nodeValue = $categoria;
        }
        if (!empty($descrizione)) {
            $prodotto->getElementsByTagName('descrizione')->item(0)->nodeValue = $descrizione;
        }
        if (!empty($prezzo)) {
            $prodotto->getElementsByTagName('prezzo')->item(0)->nodeValue = $prezzo;
        }
        if (!empty($bonus)) {
            $prodotto->getElementsByTagName('bonus')->item(0)->nodeValue = $bonus;
        }

        // Gestione immagine nuova (se caricata)
        if (!empty($immagine)) {
            $targetDir = "../../../risorse/IMG/prodotti/";
            $targetFile = $targetDir . basename($immagine);

            // Sposta il file caricato nella cartella immagini
            if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $targetFile)) {
                $prodotto->getElementsByTagName('immagine')->item(0)->nodeValue = $immagine;
            } else {
                error_log("Errore nel caricamento dell'immagine per il prodotto ID $id");
            }
        }

        break;
    }
}


if ($found) {
    $doc->save($xmlFile);
} else {
    error_log("Prodotto non trovato nell'XML. ID: $id");
}
// Reindirizza alla pagina di gestione prodotti
header("Location: ../../../gestione_prodotti_gestore.php");
exit();
?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

// Percorsi file
$xmlFile = "../../XML/sconti.xml";

// Dati del form
$prodotti = $_POST['prodotti'] ?? [];
$condizione = trim($_POST['condizione'] ?? '');
$percentuale = trim($_POST['percentuale'] ?? '');
$data_inizio = trim($_POST['data_inizio'] ?? '');
$data_fine = trim($_POST['data_fine'] ?? '');

// Verifica dati obbligatori
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($prodotti) && !empty($percentuale) &&
    !empty($data_inizio) && !empty($data_fine)) {

    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    if (file_exists($xmlFile)) {
        $doc->load($xmlFile);
        $root = $doc->documentElement;
    } else {
        $root = $doc->createElement('sconti');
        $doc->appendChild($root);
    }

    // Calcolo ID progressivo
    $lastId = 0;
    foreach ($doc->getElementsByTagName('sconto') as $s) {
        $id = (int)$s->getAttribute('id');
        if ($id > $lastId) $lastId = $id;
    }

    // Nuovo sconto
    $sconto = $doc->createElement('sconto');
    $sconto->setAttribute('id', $lastId + 1);

    foreach ($prodotti as $idProdotto) {
        $sconto->appendChild($doc->createElement('id_prodotto', (int)$idProdotto));
    }

    if (!empty($condizione)) {
        $sconto->appendChild($doc->createElement('condizione', htmlspecialchars($condizione)));
    }

    $sconto->appendChild($doc->createElement('percentuale', number_format((float)$percentuale, 1, '.', '')));
    $sconto->appendChild($doc->createElement('data_inizio', htmlspecialchars($data_inizio)));
    $sconto->appendChild($doc->createElement('data_fine', htmlspecialchars($data_fine)));

    $root->appendChild($sconto);



    // Salvataggio
    if ($doc->save($xmlFile)) {
        $_SESSION['successo_msg'] = "✅ Sconto aggiunto correttamente! In attesa di validazione o utilizzo.";
    } else {
        $_SESSION['errore_msg'] = "❌ Errore durante il salvataggio dello sconto.";
    }

    header("Location: ../../../aggiungi_sconti.php");
    exit();
} else {
    $_SESSION['errore_msg'] = "⚠️ Compila tutti i campi obbligatori.";
    header("Location: ../../../aggiungi_sconti.php");
    exit();
}
?>

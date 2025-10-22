<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$xmlFile = "../../XML/sconti.xml";

// --- Recupero dati dal form ---
$prodotti         = $_POST['prodotti'] ?? [];
$tipo_condizione  = $_POST['tipo_condizione'] ?? '';
$valore           = trim($_POST['valore'] ?? '');
$data_riferimento = trim($_POST['data_riferimento'] ?? '');
$evento           = trim($_POST['evento'] ?? '');
$id_prodotto_rif  = trim($_POST['id_prodotto_rif'] ?? '');
$percentuale      = trim($_POST['percentuale'] ?? '');
$data_inizio      = trim($_POST['data_inizio'] ?? '');
$data_fine        = trim($_POST['data_fine'] ?? '');
$destinatari      = $_POST['utenti'] ?? []; // array di ID utente selezionati

// --- Controllo campi obbligatori ---
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !empty($prodotti)
    && !empty($percentuale)
    && !empty($data_inizio)
    && !empty($data_fine)
) {
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    $doc->formatOutput = true;

    // --- Carica o crea root ---
    if (file_exists($xmlFile)) {
        $doc->load($xmlFile);
        $root = $doc->documentElement;
    } else {
        $root = $doc->createElement('sconti');
        $doc->appendChild($root);
    }

    // --- Calcolo ID progressivo ---
    $lastId = 0;
    foreach ($doc->getElementsByTagName('sconto') as $s) {
        $id = (int)$s->getAttribute('id');
        if ($id > $lastId) $lastId = $id;
    }

    // --- Creazione nuovo sconto ---
    $sconto = $doc->createElement('sconto');
    $sconto->setAttribute('id', $lastId + 1);

    // Aggiunta prodotti
    foreach ($prodotti as $idProd) {
        $sconto->appendChild($doc->createElement('id_prodotto', (int)$idProd));
    }

    // --- Creazione condizione, se presente ---
    if (!empty($tipo_condizione)) {
        $condizione = $doc->createElement('condizione');
        $condizione->setAttribute('tipo', htmlspecialchars($tipo_condizione));

        if (!empty($valore))
            $condizione->appendChild($doc->createElement('valore', htmlspecialchars($valore)));

        if (!empty($data_riferimento))
            $condizione->appendChild($doc->createElement('data_riferimento', htmlspecialchars($data_riferimento)));

        if (!empty($evento))
            $condizione->appendChild($doc->createElement('evento', htmlspecialchars($evento)));

        if (!empty($id_prodotto_rif))
            $condizione->appendChild($doc->createElement('id_prodotto_rif', (int)$id_prodotto_rif));

        $sconto->appendChild($condizione);
    }

    // --- Percentuale e date ---
    $sconto->appendChild($doc->createElement('percentuale', (int)$percentuale));
    $sconto->appendChild($doc->createElement('data_inizio', htmlspecialchars($data_inizio)));
    $sconto->appendChild($doc->createElement('data_fine', htmlspecialchars($data_fine)));

    // --- Aggiunta destinatari ---
    if (!empty($destinatari)) {
        $dest = $doc->createElement('destinatari');
        foreach ($destinatari as $idUtente) {
            $dest->appendChild($doc->createElement('id_utente', (int)$idUtente));
        }
        $sconto->appendChild($dest);
    }

    // --- Salvataggio finale ---
    $root->appendChild($sconto);

    if ($doc->save($xmlFile)) {
        $_SESSION['successo_msg'] = "Sconto aggiunto correttamente!";
    } else {
        $_SESSION['errore_msg'] = "Errore durante il salvataggio dello sconto.";
    }

    header("Location: ../../../aggiungi_sconti.php");
    exit();

} else {
    $_SESSION['errore_msg'] = "Compila tutti i campi obbligatori.";
    header("Location: ../../../aggiungi_sconti.php");
    exit();
}
?>

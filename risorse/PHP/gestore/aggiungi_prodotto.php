<?php
session_start();
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] !== 'gestore') {
    header("Location: ../../../login.php");
    exit();
}

$nome = $_POST['nome'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$descrizione = $_POST['descrizione'] ?? '';
$prezzo = $_POST['prezzo'] ?? '';
$bonus = $_POST['bonus'] ?? '';
$immagine = $_FILES['immagine'] ?? null;


if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($nome) || empty($categoria) || empty($descrizione) || empty($prezzo) ||
    !$immagine || $immagine['error'] !== UPLOAD_ERR_OK
) {
    die("Tutti i campi obbligatori devono essere compilati correttamente.");
}

$xmlFile = '../../../risorse/XML/prodotti.xml';

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
if (!$doc->load($xmlFile)) {
    die("Impossibile caricare il file XML: $xmlFile");
}

$lastId = 0;
foreach ($doc->getElementsByTagName('prodotto') as $p) {
    $id = (int)$p->getAttribute('id');
    if ($id > $lastId) $lastId = $id;
}
$nuovoId = $lastId + 1;

$targetDir = "../../../risorse/IMG/prodotti/";
$nomeImmagine = basename($immagine['name']);
$targetFile = $targetDir . $nomeImmagine;

if (!move_uploaded_file($immagine["tmp_name"], $targetFile)) {
    die("Errore durante il caricamento dell'immagine.");
}

$newProdotto = $doc->createElement('prodotto');
$newProdotto->setAttribute('id', $nuovoId);

$elementoNome = $doc->createElement('nome', htmlspecialchars($nome));
$elementoCategoria = $doc->createElement('categoria', htmlspecialchars($categoria));
$elementoDescrizione = $doc->createElement('descrizione', htmlspecialchars($descrizione));
$elementoPrezzo = $doc->createElement('prezzo', htmlspecialchars($prezzo));
$elementoBonus = $doc->createElement('bonus', htmlspecialchars($bonus !== '' ? $bonus : '0.00'));
$elementoData = $doc->createElement('data_inserimento', date('Y-m-d'));
$elementoImmagine = $doc->createElement('immagine', htmlspecialchars($nomeImmagine));


$newProdotto->appendChild($elementoNome);
$newProdotto->appendChild($elementoCategoria);
$newProdotto->appendChild($elementoDescrizione);
$newProdotto->appendChild($elementoPrezzo);
$newProdotto->appendChild($elementoBonus);
$newProdotto->appendChild($elementoData);
$newProdotto->appendChild($elementoImmagine);


$doc->documentElement->appendChild($newProdotto);
$doc->save($xmlFile);

$_SESSION['aggiungi_prodotto_successo'] = true;
header("Location: ../../../gestione_prodotti_gestore.php");
exit();
?>

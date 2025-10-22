<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
  $_SESSION['errore_msg'] = "Accesso negato.";
  header("Location: ../../login.php");
  exit();
}

$id_utente   = $_SESSION['id_utente'] ?? '';
$id_prodotto = $_POST['id_prodotto'] ?? '';
$valutazione = $_POST['valutazione'] ?? '';
$commento    = trim($_POST['commento'] ?? '');

if (empty($id_utente) || empty($id_prodotto) || empty($valutazione) || empty($commento)) {
  $_SESSION['errore_msg'] = "Tutti i campi sono obbligatori.";
  header("Location: ../../scrivi_recensione.php?id_prodotto={$id_prodotto}");
  exit();
}

$recensioniFile = '../XML/recensioni.xml';
$utentiFile     = '../XML/utenti.xml';

// === CARICAMENTO RECENSIONI ===
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!file_exists($recensioniFile)) {
  $root = $doc->createElement('recensioni');
  $doc->appendChild($root);
  $doc->save($recensioniFile);
}

$doc->load($recensioniFile);
$root = $doc->documentElement;

// === CONTROLLA SE L'UTENTE HA GIÀ RECENSITO QUESTO PRODOTTO ===
foreach ($doc->getElementsByTagName('recensione') as $rec) {
  if (
    (string)$rec->getElementsByTagName('id_utente')[0]->nodeValue === $id_utente &&
    (string)$rec->getElementsByTagName('id_prodotto')[0]->nodeValue === $id_prodotto
  ) {
    $_SESSION['errore_msg'] = "Hai già recensito questo prodotto.";
    header("Location: ../../recensione.php?id_prodotto={$id_prodotto}");
    exit();
  }
}

// === TROVA L'ULTIMO ID E CREA LA NUOVA RECENSIONE ===
$ultimoId = 0;
foreach ($doc->getElementsByTagName('recensione') as $r) {
  $idAttr = (int)$r->getAttribute('id');
  if ($idAttr > $ultimoId) $ultimoId = $idAttr;
}

$newRec = $doc->createElement('recensione');
$newRec->setAttribute('id', $ultimoId + 1);

$newRec->appendChild($doc->createElement('id_prodotto', $id_prodotto));
$newRec->appendChild($doc->createElement('id_utente', $id_utente));
$newRec->appendChild($doc->createElement('commento', htmlspecialchars($commento)));
$newRec->appendChild($doc->createElement('valutazione', $valutazione)); // voto dell'utente
$newRec->appendChild($doc->createElement('voti_like', 0));
$newRec->appendChild($doc->createElement('voti_dislike', 0));
$newRec->appendChild($doc->createElement('voto_utenti'));
$newRec->appendChild($doc->createElement('data', date('Y-m-d')));

$root->appendChild($newRec);
$doc->save($recensioniFile);

// === AGGIORNA LA REPUTAZIONE DELL’UTENTE (+10) ===
$xmlUtenti = simplexml_load_file($utentiFile);
foreach ($xmlUtenti->utente as $u) {
  if ((int)$u['id'] === (int)$id_utente) {
    $u->reputazione = (float)$u->reputazione + 10;
    break;
  }
}
$xmlUtenti->asXML($utentiFile);

// === REDIRECT CON MESSAGGIO DI SUCCESSO ===
$_SESSION['successo_msg'] = "Recensione inviata con successo! (+10 reputazione) ✅";
header("Location: ../../recensione.php?id_prodotto={$id_prodotto}");
exit();
?>

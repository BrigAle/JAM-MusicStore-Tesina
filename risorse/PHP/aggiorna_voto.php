<?php
session_start();

if (!isset($_SESSION['id_utente'])) {
    $_SESSION['errore_voto'] = "Devi essere loggato per votare.";
    header("Location: ../../recensioni.php?id_prodotto=" . $_POST['id_prodotto']);
    exit;
}

$idUtente   = (int)$_SESSION['id_utente'];
$idRecensione = (int)$_POST['id_recensione'];
$idProdotto = (int)$_POST['id_prodotto'];
$azione      = $_POST['azione']; // "like" o "dislike"

$recensioniFile = '../XML/recensioni.xml';
$utentiFile     = '../XML/utenti.xml';

$docRec = new DOMDocument();
$docRec->preserveWhiteSpace = false;
$docRec->formatOutput = true;
$docRec->load($recensioniFile);

$xpath = new DOMXPath($docRec);

// trova la recensione da votare
$recensioneNode = null;
foreach ($docRec->getElementsByTagName('recensione') as $rec) {
    if ((int)$rec->getAttribute('id') === $idRecensione) {
        $recensioneNode = $rec;
        break;
    }
}

if (!$recensioneNode) {
    $_SESSION['errore_voto'] = "Recensione non trovata.";
    header("Location: ../../recensioni.php?id_prodotto=$idProdotto");
    exit;
}

// autore della recensione
$idAutore = (int)$recensioneNode->getElementsByTagName('id_utente')[0]->nodeValue;

// Impedisce autovoto
if ($idAutore === $idUtente) {
    $_SESSION['errore_voto'] = "Non puoi votare la tua recensione.";
    header("Location: ../../recensioni.php?id_prodotto=$idProdotto");
    exit;
}

// Se l'utente ha gia' votato non permette di votare di nuovo
$votoUtentiNode = $recensioneNode->getElementsByTagName('voto_utenti')->item(0);

if ($votoUtentiNode) {
    foreach ($votoUtentiNode->getElementsByTagName('voto') as $v) {
        if ((int)$v->getAttribute('id_utente') === $idUtente) {
            $_SESSION['errore_voto'] = "Hai già votato questa recensione.";
            header("Location: ../../recensioni.php?id_prodotto=$idProdotto");
            exit;
        }
    }
}

// Creo un nuovo nodo voto
$nuovoVoto = $docRec->createElement('voto');
$nuovoVoto->setAttribute('id_utente', $idUtente);
$nuovoVoto->setAttribute('tipo', $azione);
$votoUtentiNode->appendChild($nuovoVoto);

// Aggiorno i contatori like/dislike
if ($azione === 'like') {
    $votiLikeNode = $recensioneNode->getElementsByTagName('voti_like')->item(0);
    $votiLikeNode->nodeValue = (int)$votiLikeNode->nodeValue + 1;
    $incremento = 1.5;
} elseif ($azione === 'dislike') {
    $votiDislikeNode = $recensioneNode->getElementsByTagName('voti_dislike')->item(0);
    $votiDislikeNode->nodeValue = (int)$votiDislikeNode->nodeValue + 1;
    $incremento = -1.2;
} else {
    $_SESSION['errore_voto'] = "Tipo di voto non valido.";
    header("Location: ../../recensioni.php?id_prodotto=$idProdotto");
    exit;
}

// Salva le modifiche in recensioni.xml
$docRec->save($recensioniFile);

// aggiorno la reputazione dell'autore della recensione
$docUtenti = new DOMDocument();
$docUtenti->preserveWhiteSpace = false;
$docUtenti->formatOutput = true;
$docUtenti->load($utentiFile);

foreach ($docUtenti->getElementsByTagName('utente') as $utente) {
    if ((int)$utente->getAttribute('id') === $idAutore) {
        $reputazioneNode = $utente->getElementsByTagName('reputazione')->item(0);
        $reputazioneNode->nodeValue = (float)$reputazioneNode->nodeValue + $incremento;
        break;
    }
}

$docUtenti->save($utentiFile);

// Messaggio di successo
$_SESSION['msg_voto'] = ($azione === 'like')
    ? "Hai messo un like alla recensione. (+1.5 reputazione all'autore)"
    : "Hai messo un dislike alla recensione. (-1.2 reputazione all'autore)";

header("Location: ../../recensioni.php?id_prodotto=$idProdotto");
exit;
?>

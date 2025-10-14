<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    header("Location: ../../login.php");
    exit();
}

$id_utente = $_SESSION['id_utente']; // ID utente loggato
$id_recensione = $_GET['id_recensione'];
$id_prodotto = $_GET['id_prodotto'];
$voto = $_GET['voto']; // "utile" o "inutile"

// Carica XML recensioni
$xmlPath = "../XML/recensioni.xml";
$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlPath)) {
    die("Errore nel caricamento del file XML");
}

// Trova la recensione
$recensioni = $doc->getElementsByTagName("recensione");
foreach ($recensioni as $recensione) {
    if ($recensione->getAttribute("id") == $id_recensione) {
        $votiLike = $recensione->getElementsByTagName("voti_like")->item(0);
        $votiDislike = $recensione->getElementsByTagName("voti_dislike")->item(0);

        // Crea nodo voto_utenti se non esiste
        $votiUtenti = $recensione->getElementsByTagName("voto_utenti")->item(0);
        if (!$votiUtenti) {
            $votiUtenti = $doc->createElement("voto_utenti");
            $recensione->appendChild($votiUtenti);
        }

        // Controlla se l’utente ha già votato
        $votoEsistente = null;
        foreach ($votiUtenti->getElementsByTagName("voto") as $v) {
            if ($v->getAttribute("id_utente") == $id_utente) {
                $votoEsistente = $v;
                break;
            }
        }

        if ($votoEsistente) {
            $tipoAttuale = $votoEsistente->getAttribute("tipo");

            if ($tipoAttuale == $voto) {
                // stesso voto → annulla
                $votiUtenti->removeChild($votoEsistente);
                if ($voto == "like") $votiLike->nodeValue--;
                else $votiDislike->nodeValue--;
            } else {
                // voto diverso → cambio
                if ($tipoAttuale == "like") {
                    $votiLike->nodeValue--;
                    $votiDislike->nodeValue++;
                } else {
                    $votiDislike->nodeValue--;
                    $votiLike->nodeValue++;
                }
                $votoEsistente->setAttribute("tipo", $voto);
            }
        } else {
            // Nuovo voto
            $nuovoVoto = $doc->createElement("voto");
            $nuovoVoto->setAttribute("id_utente", $id_utente);
            $nuovoVoto->setAttribute("tipo", $voto);
            $votiUtenti->appendChild($nuovoVoto);

            if ($voto == "like") $votiLike->nodeValue++;
            else $votiDislike->nodeValue++;
        }

        break;
    }
}

// Salva modifiche
$doc->save($xmlPath);

// Redirect alla pagina recensioni
header("Location: ../../recensioni.php?id_prodotto=" . $id_prodotto);
exit();
?>

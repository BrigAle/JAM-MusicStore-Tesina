<?php
session_start();

// Controllo accesso amministratore
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'amministratore') {
    $_SESSION['errore'] = "Accesso non autorizzato.";
    header("Location: ../../../login.php");
    exit();
}

$idRichiesta = $_POST['id_richiesta'] ?? '';
$azione = $_POST['azione'] ?? '';

if (empty($idRichiesta) || !in_array($azione, ['approvata', 'rifiutata'])) {
    $_SESSION['errore_msg'] = "Dati non validi.";
    header("Location: ../../../gestione_crediti_admin.php");
    exit();
}

$xmlCrediti = "../../../risorse/XML/richiesteCrediti.xml";
$xmlUtenti = "../../../risorse/XML/utenti.xml";

$doc = new DOMDocument();
$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;

if (!$doc->load($xmlCrediti)) {
    $_SESSION['errore_msg'] = "Errore caricamento file richieste.";
    header("Location: ../../../gestione_crediti_admin.php");
    exit();
}

$richiestaTrovata = false;

foreach ($doc->getElementsByTagName('richiesta') as $richiesta) {
    if ($richiesta->getAttribute('id') == $idRichiesta) {
        $richiestaTrovata = true;

        $stato = $richiesta->getElementsByTagName('stato')->item(0);
        if ($azione === 'approvata') {
            $stato->nodeValue = 'approvata';
        } else {
            $stato->nodeValue = 'rifiutata';
        }

        // Se approvata aggiorna crediti utente
        if ($azione === 'approvata') {
            $idUtente = $richiesta->getElementsByTagName('id_utente')->item(0)->nodeValue;
            $importo = (int)$richiesta->getElementsByTagName('importo')->item(0)->nodeValue;

            $utDoc = new DOMDocument();
            $utDoc->preserveWhiteSpace = false;
            $utDoc->formatOutput = true;

            if ($utDoc->load($utentiFile)) {
                foreach ($utDoc->getElementsByTagName('utente') as $utente) {
                    if ($utente->getAttribute('id') == $idUtente) {
                        $creditiNode = $utente->getElementsByTagName('crediti')->item(0);
                        $creditiAttuali = (int)$creditiNode->nodeValue;
                        $creditiNode->nodeValue = $creditiAttuali + $importo;
                        $utDoc->save($utentiFile);
                        break;
                    }
                }
            }
        }

        break;
    }
}

if ($richiestaTrovata) {
    $doc->save($xmlCrediti);
    $_SESSION['successo_msg'] = "Richiesta aggiornata con successo.";
} else {
    $_SESSION['errore_msg'] = "Richiesta non trovata.";
}

header("Location: ../../../gestione_crediti_admin.php");
exit();

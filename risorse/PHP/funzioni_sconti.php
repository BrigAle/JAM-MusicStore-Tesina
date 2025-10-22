

<?php
/**
 * Verifica se un utente rispetta una condizione di sconto specifica.
 *
 * @param SimpleXMLElement $sconto  Nodo <sconto> del file sconti.xml
 * @param SimpleXMLElement $utente  Nodo <utente> del file utenti.xml
 * @param string $idProdotto        ID del prodotto su cui applicare la verifica
 * @param string $oggi              Data odierna (formato YYYY-MM-DD)
 * @param SimpleXMLElement|null $xmlStorico  Storico acquisti, può anche non esserci
 * @return bool True se la condizione è soddisfatta, False altrimenti
 */

// funzione per verificare se uno sconto si applica a un utente e prodotto specifici
function verificaCondizione($sconto, $utente, $idProdotto, $oggi, $xmlStorico = null)
{
    // Date di validità
    $dataInizio = (string)$sconto->data_inizio;
    $dataFine   = (string)$sconto->data_fine;
    if ($oggi < $dataInizio || $oggi > $dataFine) return false;

    // Il prodotto deve essere incluso nello sconto
    $incluso = false;
    foreach ($sconto->id_prodotto as $idS) {
        if ((string)$idS === $idProdotto) {
            $incluso = true;
            break;
        }
    }
    if (!$incluso) return false;

    // L’utente deve essere tra i destinatari
    $idUtente = (string)$utente['id'];
    $destinatario = false;
    if (isset($sconto->destinatari->id_utente)) {
        foreach ($sconto->destinatari->id_utente as $idDest) {
            if ((string)$idDest === $idUtente) {
                $destinatario = true;
                break;
            }
        }
    }
    if (!$destinatario) return false;

    // Analisi della condizione
    $condizione = $sconto->condizione;
    if (!$condizione) return false;

    $tipo       = (string)$condizione['tipo'];
    $valore     = (float)$condizione->valore;
    $dataRif    = (string)$condizione->data_riferimento;
    $idProdRif  = (string)$condizione->id_prodotto_rif;
    $crediti     = (float)$utente->crediti;
    $reputazione = (float)$utente->reputazione;
    $dataIscr    = (string)$utente->data_iscrizione;

    switch ($tipo) {
        case 'mesi_iscrizione':
            $mesi = (strtotime($oggi) - strtotime($dataIscr)) / (60 * 60 * 24 * 30);
            return $mesi >= $valore;

        case 'crediti_minimi':
            return $crediti >= $valore;

        case 'crediti_da_data':
            // L'utente deve avere almeno $valore crediti entro la data di riferimento $dataRif
            $dataRif = (string)$condizione->data_riferimento;
            $valore  = (float)$condizione->valore;
            $idUtente = (string)$utente['id'];

            $xmlCrediti = simplexml_load_file("risorse/XML/storico_crediti.xml");
            $creditiAllaData = 0;

            if ($xmlCrediti) {
                $creditiTrovati = [];

                foreach ($xmlCrediti->record as $r) {
                    $id = (string)$r->id_utente;
                    $data = (string)$r->data;
                    $cred = (float)$r->crediti;

                    // Considera solo i record dell’utente fino alla data richiesta
                    if ($id === $idUtente && $data <= $dataRif) {
                        // Se ci sono più record per la stessa data, prendiamo il valore massimo
                        if (!isset($creditiTrovati[$data]) || $cred > $creditiTrovati[$data]) {
                            $creditiTrovati[$data] = $cred;
                        }
                    }
                }

                // Prende l’ultimo record valido (data più vicina) e massimo valore in quella data
                if (!empty($creditiTrovati)) {
                    ksort($creditiTrovati); // ordina per data
                    $creditiAllaData = end($creditiTrovati); // valore finale massimo
                }
            }

            return ($creditiAllaData >= $valore);

        case 'reputazione_minima':
            return $reputazione >= $valore;

        case 'acquisto_specifico':
            if ($xmlStorico) {
                foreach ($xmlStorico->storico as $storico) {
                    if ((string)$storico->id_utente === $idUtente) {
                        foreach ($storico->prodotti->prodotto as $p) {
                            if ((string)$p->id_prodotto === $idProdRif) return true;
                        }
                    }
                }
            }
            return false;

        case 'offerta_speciale':
            return true;

        default:
            return false;
    }
}
/**
 * Calcola lo sconto totale e restituisce anche la descrizione delle condizioni soddisfatte.
 *
 * @param SimpleXMLElement $xmlSconti
 * @param SimpleXMLElement $utente
 * @param string $idProdotto
 * @param string $oggi
 * @param SimpleXMLElement|null $xmlStorico
 * @return array ['sconto' => float, 'condizioni' => array]
 */

// funzione per calcolare lo sconto massimo applicabile a un utente per un prodotto specifico
function calcolaScontoUtente($xmlSconti, $utente, $idProdotto, $oggi, $xmlStorico = null)
{
    $scontoMassimo = 0;
    $condizioneScelta = "";

    if (!$xmlSconti) return ['sconto' => 0, 'condizione' => ""];

    foreach ($xmlSconti->sconto as $sconto) {
        if (verificaCondizione($sconto, $utente, $idProdotto, $oggi, $xmlStorico)) {
            $percentuale = (float)$sconto->percentuale;

            // se questo sconto è più grande, aggiorniamo sia la percentuale che la descrizione
            if ($percentuale > $scontoMassimo) {
                $scontoMassimo = $percentuale;

                // ricava la descrizione leggibile della condizione corrispondente
                $cond = $sconto->condizione;
                if ($cond) {
                    $tipo = (string)$cond['tipo'];
                    $valore = (string)$cond->valore;
                    $dataRif = (string)$cond->data_riferimento;
                    $evento = (string)$cond->evento;
                    $idProdRif = (string)$cond->id_prodotto_rif;

                    switch ($tipo) {
                        case 'mesi_iscrizione':
                            $condizioneScelta = "Iscritto da almeno {$valore} mesi";
                            break;
                        case 'crediti_minimi':
                            $condizioneScelta = "Crediti ≥ {$valore}";
                            break;
                        case 'crediti_da_data':
                            $condizioneScelta = "{$valore} crediti entro {$dataRif}";
                            break;
                        case 'reputazione_minima':
                            $condizioneScelta = "Reputazione ≥ {$valore}";
                            break;
                        case 'acquisto_specifico':
                            $condizioneScelta = "Acquisto prodotto #{$idProdRif}";
                            break;
                        case 'offerta_speciale':
                            $condizioneScelta = "Offerta: {$evento}";
                            break;
                    }
                }
            }
        }
    }

    return ['sconto' => $scontoMassimo, 'condizione' => $condizioneScelta];
}



function aggiornaStoricoCrediti($idUtente, $nuoviCrediti, $fileXML)
{
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    // Se il file non esiste, crealo con il nodo radice
    if (!file_exists($fileXML)) {
        $root = $dom->createElement("storici_crediti");
        $dom->appendChild($root);
        $dom->save($fileXML);
    }

    // Carica il file XML
    if (!$dom->load($fileXML)) {
        return false;
    }

    $root = $dom->documentElement;

    // Crea un nuovo record (append-only)
    $record = $dom->createElement("record");
    $idNode = $dom->createElement("id_utente", $idUtente);
    $dataNode = $dom->createElement("data", date('Y-m-d'));
    $creditiNode = $dom->createElement("crediti", $nuoviCrediti);

    $record->appendChild($idNode);
    $record->appendChild($dataNode);
    $record->appendChild($creditiNode);

    $root->appendChild($record);

    $dom->save($fileXML);
    return true;
}
?>

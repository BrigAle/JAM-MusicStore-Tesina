<?php
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

    // ✅ Se applicazione_globale="true" → vale per tutti gli utenti
    $appGlobale = ((string)$sconto['applicazione_globale'] === 'true');
    if ($appGlobale) {
        // È sufficiente che sia nel periodo di validità e che il prodotto corrisponda
        return true;
    }

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

    $tipo        = (string)$condizione['tipo'];
    $valore      = (float)$condizione->valore;
    $dataRif     = (string)$condizione->data_riferimento;
    $idProdRif   = (string)$condizione->id_prodotto_rif;
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
            $xmlCrediti = simplexml_load_file("risorse/XML/storico_crediti.xml");
            if (!$xmlCrediti) return false;

            $idUtente = (string)$utente['id'];
            $creditiIniziali = null;
            $creditiFinali = null;
            $ultimaDataPrima = null;
            $ultimaDataDopo = null;

            foreach ($xmlCrediti->record as $r) {
                $id = (string)$r->id_utente;
                $data = (string)$r->data;
                $cred = (float)$r->crediti;

                if ($id === $idUtente) {
                    if ($data < $dataRif) {
                        if ($ultimaDataPrima === null || $data > $ultimaDataPrima) {
                            $ultimaDataPrima = $data;
                            $creditiIniziali = $cred;
                        }
                    }
                    if ($data >= $dataRif) {
                        if ($ultimaDataDopo === null || $data > $ultimaDataDopo) {
                            $ultimaDataDopo = $data;
                            $creditiFinali = $cred;
                        }
                    }
                }
            }

            if ($creditiFinali === null) return false;
            if ($creditiIniziali === null) $creditiIniziali = 0;

            $creditiAccumulati = $creditiFinali - $creditiIniziali;
            return ($creditiAccumulati >= $valore);

        case 'reputazione_minima':
            return $reputazione >= $valore;

        case 'acquisto_specifico':
            if ($xmlStorico) {
                foreach ($xmlStorico->storico as $storico) {
                    if ((string)$storico->id_utente === $idUtente) {
                        foreach ($storico->prodotti->prodotto as $p) {
                            if ((string)$p->id_prodotto === $idProdRif) {
                                return true;
                            }
                        }
                    }
                }
            }
            return false;

        // --- Condizione: offerta speciale ---
        case 'offerta_speciale':
            return true; // già validata dal periodo

        default:
            return false;
    }
}


function calcolaScontoUtente($xmlSconti, $utente, $idProdotto, $oggi, $xmlStorico = null)
{
    $scontoMassimo = 0;
    $condizioneScelta = "";

    if (!$xmlSconti) return ['sconto' => 0, 'condizione' => ""];

    foreach ($xmlSconti->sconto as $sconto) {
        // ✅ se lo sconto è globale, non serve verificare destinatari
        $appGlobale = ((string)$sconto['applicazione_globale'] === 'true');

        if ($appGlobale || verificaCondizione($sconto, $utente, $idProdotto, $oggi, $xmlStorico)) {
            $percentuale = (float)$sconto->percentuale;

            if ($percentuale > $scontoMassimo) {
                $scontoMassimo = $percentuale;

                $cond = $sconto->condizione;
                if ($appGlobale) {
                    // Offerta globale: descrizione generica
                    $evento = (string)$cond->evento;
                    $condizioneScelta = $evento ? "Offerta speciale: {$evento}" : "Offerta promozionale globale";
                } elseif ($cond) {
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
                            $condizioneScelta = "{$valore} crediti da {$dataRif}";
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

<?php
function verificaCondizione($sconto, $utente, $idProdotto, $oggi, $xmlStorico = null)
{
    $dataInizio = (string)$sconto->data_inizio;
    $dataFine   = (string)$sconto->data_fine;
    if ($oggi < $dataInizio || $oggi > $dataFine) return false;

    $appGlobale = ((string)$sconto['applicazione_globale'] === 'true');

    if (!$appGlobale) {
        $incluso = false;
        foreach ($sconto->id_prodotto as $idS) {
            if ((string)$idS === $idProdotto) {
                $incluso = true;
                break;
            }
        }
        if (!$incluso) return false;
    }

    $condizione = $sconto->condizione;
    if (!$condizione) return true; // se non ci sono condizioni → valido per tutti

    $tipo        = (string)$condizione['tipo'];
    $valore      = (float)$condizione->valore;
    $dataRif     = (string)$condizione->data_riferimento;
    $idProdRif   = (string)$condizione->id_prodotto_rif;

    // dati utenti
    $crediti     = (float)$utente->crediti;
    $reputazione = (float)$utente->reputazione;
    $dataIscr    = (string)$utente->data_iscrizione;

    switch ($tipo) {

        case 'mesi_iscrizione':
            // Verifica che l’utente sia iscritto da almeno X mesi
            $mesi = (strtotime($oggi) - strtotime($dataIscr)) / (60 * 60 * 24 * 30);
            return $mesi >= $valore;

        case 'crediti_minimi':
            // Verifica che l’utente abbia almeno X crediti
            return $crediti >= $valore;

        case 'crediti_da_data':
            // Controlla crediti accumulati da una certa data
            $xmlCrediti = simplexml_load_file("risorse/XML/storico_crediti.xml");
            if (!$xmlCrediti) return false;

            $idUtente = (string)$utente['id'];
            $creditiPrima = null;
            $creditiDopo  = null;

            foreach ($xmlCrediti->record as $r) {
                $id = (string)$r->id_utente;
                $data = (string)$r->data;
                $cred = (float)$r->crediti;

                if ($id === $idUtente) {
                    if ($data < $dataRif) {
                        $creditiPrima = $cred;
                    }
                    if ($data >= $dataRif) {
                        $creditiDopo = $cred;
                    }
                }
            }

            if ($creditiDopo === null) return false;
            if ($creditiPrima === null) $creditiPrima = 0;

            $accumulati = $creditiDopo - $creditiPrima;
            return $accumulati >= $valore;

        case 'reputazione_minima':
            // Verifica reputazione utente
            return $reputazione >= $valore;

        case 'acquisto_specifico':
            // Controlla se l’utente ha acquistato un prodotto specifico
            if (!$xmlStorico) return false;
            $idUtente = (string)$utente['id'];

            foreach ($xmlStorico->storico as $storico) {
                if ((string)$storico->id_utente === $idUtente) {
                    foreach ($storico->prodotti->prodotto as $p) {
                        if ((string)$p->id_prodotto === $idProdRif) {
                            return true;
                        }
                    }
                }
            }
            return false;

        case 'offerta_speciale':
            // Valida per tutti durante il periodo
            return true;

        default:
            return false;
    }
}


function calcolaScontoUtente($xmlSconti, $utente, $idProdotto, $oggi, $xmlStorico = null)
{
    $scontoMassimo = 0;
    $condizioneScelta = "";
    $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
    // se non ci sono sconti validi, ritorna $result['sconto'] = 0; $result['condizione'] = "";
    if (!$xmlSconti) return ['sconto' => 0, 'condizione' => ""];

    foreach ($xmlSconti->sconto as $sconto) {
        // verifico se sia applicato a tutti i prodotti
        $appGlobale = ((string)$sconto['applicazione_globale'] === 'true');

        // Verifica la condizione per l'utente e il prodotto corrente
        if ($appGlobale || verificaCondizione($sconto, $utente, $idProdotto, $oggi, $xmlStorico)) {
            $percentuale = (float)$sconto->percentuale;

            // Mantiene solo lo sconto più alto
            if ($percentuale > $scontoMassimo) {
                $scontoMassimo = $percentuale;

                $cond = $sconto->condizione;

                // Caso 1: sconto globale per tutti i prodotti
                //  se non ci sono condizioni o è un'offerta speciale
                // allora descrivo come offerta promozionale
                // altrimenti se e' un offerta speciale ma non e' stato scritto un evento
                // lo descrivo come promozione valida per tutti i prodotti
                if ($appGlobale && (!$cond || (string)$cond['tipo'] === 'offerta_speciale')) {
                    if ($cond) {
                        $evento = (string)$cond->evento;
                    } else {
                        $evento = "";
                    }
                    if (!empty($evento)) {
                        $condizioneScelta = "Offerta Promozionale: {$evento}";
                    } else {
                        $condizioneScelta = "Sconto valido su tutti i prodotti";
                    }
                }

                // Caso 2: sconto con condizione specifica
                // se il campo della condizione non e' vuoto
                // allora descrivo la condizione scelta in base alla tipologia
                elseif ($cond) {
                    $tipo = (string)$cond['tipo'];
                    $valore = (string)$cond->valore;
                    $dataRif = (string)$cond->data_riferimento;
                    $evento = (string)$cond->evento;
                    $idProdRif = (string)$cond->id_prodotto_rif;

                    switch ($tipo) {
                        case 'mesi_iscrizione':
                            $condizioneScelta = "Utenti iscritti da almeno {$valore} mesi";
                            break;

                        case 'crediti_minimi':
                            $condizioneScelta = "Utenti con almeno {$valore} crediti";
                            break;

                        case 'crediti_da_data':
                            $condizioneScelta = "Utenti con {$valore} crediti da {$dataRif}";
                            break;

                        case 'reputazione_minima':
                            $condizioneScelta = "Utenti con reputazione ≥ {$valore}";
                            break;

                        case 'acquisto_specifico':
                            $nomeProdRif = null;
                            if ($idProdRif !== "") {
                                foreach ($xmlProdotti->prodotto as $p) {
                                    if ((string)$p['id'] === $idProdRif) {
                                        $nomeProdRif = (string)$p->nome;
                                        break;
                                    }
                                }
                            }
                            if ($nomeProdRif) {
                                $condizioneScelta = "Clienti che hanno acquistato: {$nomeProdRif}";
                            } else {
                                $condizioneScelta = "Clienti che hanno acquistato il prodotto #{$idProdRif}";
                            }
                            break;

                        case 'offerta_speciale':
                            if (!empty($evento)) {
                                $condizioneScelta = "Offerta speciale: {$evento}";
                            } else {
                                $condizioneScelta = "Promozione valida per tutti";
                            }
                            break;

                        default:
                            $condizioneScelta = "Sconto valido per utenti che soddisfano la condizione: {$tipo}";
                    }
                }

                //Caso 3: sconto senza condizione
                else {
                    if ($appGlobale) {
                        $condizioneScelta = "Sconto applicato a tutti i prodotti";
                    } else {
                        $condizioneScelta = "Sconto valido per il prodotto selezionato";
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

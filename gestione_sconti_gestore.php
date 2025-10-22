<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    // L'utente non è loggato o non è un gestore, reindirizza alla pagina di login
    header('Location: login.php');
    exit();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jam Music Store</title>
    <link rel="stylesheet" href="risorse/CSS/style.css" type="text/css" />
    <link rel="icon" href="risorse/IMG/jam.ico" type="image/x-icon" />
</head>

<body>
    <div class="header">
        <div class="logo">
            <a href="homepage.php"><img src="risorse/IMG/JAM_logo (2).png" alt="JAM Music Store" /></a>
        </div>

        <div class="navSearch">
            <form action="risorse/PHP/ricerca_catalogo.php" method="get">
                <div class="searchContainer">
                    <input type="text" name="query" placeholder="Cerca brani o categorie..." />
                    <button type="submit" name="tipo" value="nome">Per nome prodotto</button>
                    <button type="submit" name="tipo" value="categoria">Per categoria</button>
                </div>
            </form>
        </div>

        <div class="navLink">
            <!-- link admin -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
                <a href="amministrazione.php">admin</a>
            <?php endif; ?>
            <!-- link gestore -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'):
                echo "<a href=\"gestione.php\">gestore</a>";
            endif; ?>
            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
            <?php endif; ?>
            <!-- link cliente -->
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div>
    </div>


    <div class="content">
        <h2 style="text-align: left;">Gestione Sconti</h2>

        <?php

        $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
        $xmlSconti   = simplexml_load_file("risorse/XML/sconti.xml");

        if ($xmlSconti && count($xmlSconti->sconto) > 0) {
            $oggi = date('Y-m-d');

            foreach ($xmlSconti->sconto as $sconto) {
                $idSconto = (int)$sconto['id'];
                $percentuale = (float)$sconto->percentuale;
                $dataInizio = (string)$sconto->data_inizio;
                $dataFine = (string)$sconto->data_fine;
                $attivo = ($oggi >= $dataInizio && $oggi <= $dataFine);
                $stato = $attivo
                    ? "<span style='color:limegreen;'>Attivo</span>"
                    : "<span style='color:red;'>Scaduto</span>";

                // --- Lettura condizione ---
                $condizioneEl = $sconto->condizione;
                if ($condizioneEl) {
                    $tipo = (string)$condizioneEl['tipo'];
                    $valore = (string)$condizioneEl->valore;
                    $dataRif = (string)$condizioneEl->data_riferimento;
                    $evento = (string)$condizioneEl->evento;
                    $idProdRif = (string)$condizioneEl->id_prodotto_rif;

                    $descrizioneCond = "<strong>{$tipo}</strong>";
                    if ($valore !== "") $descrizioneCond .= " → valore: {$valore}";
                    if ($dataRif !== "") $descrizioneCond .= " | entro la data: {$dataRif}";
                    if ($evento !== "") $descrizioneCond .= " | evento: {$evento}";
                    if ($idProdRif !== "") $descrizioneCond .= " | prodotto rif: {$idProdRif}";
                } else {
                    $descrizioneCond = "(nessuna condizione)";
                }

                echo "
            <div style='margin-bottom:40px; position:relative;'>
                <div style='display:flex; justify-content:space-between; align-items:center;'>
                    <h3 style='margin-bottom:8px;'>
                        Sconto #{$idSconto} — {$descrizioneCond} ({$percentuale}%)
                        <small style='color:#aaa;'>[dal {$dataInizio} al {$dataFine}]</small> — {$stato}
                    </h3>

                    <form action='risorse/PHP/gestore/elimina_sconto.php' method='GET'
                          onsubmit=\"return confirm('Vuoi davvero annullare lo sconto #{$idSconto}?');\"
                          style='margin:0;'>
                        <input type='hidden' name='id_sconto' value='{$idSconto}'>
                        <button type='submit' style='
                            background-color:#b30000;
                            color:white;
                            border:none;
                            padding:6px 12px;
                            border-radius:6px;
                            cursor:pointer;
                            font-weight:600;
                            transition:0.2s;'>
                            Rimuovi Sconto
                        </button>
                    </form>
                </div>";

                // --- Tabella prodotti scontati ---
                echo "
                <table border='1' cellpadding='6' style='width:100%; margin-top:10px;'>
                    <tr>
                        <th>ID</th>
                        <th>Immagine</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Descrizione</th>
                        <th>Prezzo (€)</th>
                        <th>Prezzo Scontato (€)</th>
                        <th>Data Inserimento</th>
                    </tr>";

                foreach ($sconto->id_prodotto as $idProdottoScontato) {
                    $idProd = (string)$idProdottoScontato;

                    $prodotto = null;
                    foreach ($xmlProdotti->prodotto as $p) {
                        if ((string)$p['id'] === $idProd) {
                            $prodotto = $p;
                            break;
                        }
                    }

                    if ($prodotto) {
                        $nome = (string)$prodotto->nome;
                        $categoria = (string)$prodotto->categoria;
                        $descrizione = (string)$prodotto->descrizione;
                        $prezzo = (float)$prodotto->prezzo;
                        $data = (string)$prodotto->data_inserimento;
                        $immagine = "risorse/IMG/prodotti/" . (string)$prodotto->immagine;
                        $prezzoFinale = $prezzo - ($prezzo * $percentuale / 100);

                        echo "
                    <tr>
                        <td>{$idProd}</td>
                        <td style='text-align:center;'>
                            <img src='{$immagine}' alt='{$nome}'
                                 style='width:70px; height:70px; object-fit:contain; border-radius:6px; background:#111;'>
                        </td>
                        <td>{$nome}</td>
                        <td>{$categoria}</td>
                        <td style='max-width:320px; text-align:left;'>{$descrizione}</td>
                        <td>" . number_format($prezzo, 2, ',', '.') . "</td>
                        <td><strong>" . number_format($prezzoFinale, 2, ',', '.') . "</strong></td>
                        <td>{$data}</td>
                    </tr>";
                    }
                }

                echo "</table>";

                // --- Mostra destinatari ---
                if (isset($sconto->destinatari->id_utente)) {
                    echo "<p style='margin-top:10px;'><strong>Destinatari:</strong> ";

                    // Crea array con tutti gli ID destinatari
                    $idUtenti = [];
                    foreach ($sconto->destinatari->id_utente as $idUtente) {
                        $idUtenti[] = (string)$idUtente;
                    }

                    // Connessione al database
                    require_once('risorse/PHP/connection.php');
                    $conn = new mysqli($host, $user, $password, $db);
                    if ($conn->connect_error) {
                        die("Connessione fallita: " . $conn->connect_error);
                    }

                    // Prepara la query per ottenere i nomi corrispondenti agli ID
                    $idList = implode(',', array_map('intval', $idUtenti)); // es: 3,4,5
                    $query = "SELECT username, id FROM utente WHERE id IN ($idList)";
                    $result = $conn->query($query);

                    $nomiUtenti = [];
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $nomiUtenti[$row['id']] = $row['username'];
                        }
                    }

                    // Sostituisci gli ID con i nomi utente
                    $outputUtenti = [];
                    foreach ($idUtenti as $id) {
                        $outputUtenti[] = isset($nomiUtenti[$id]) ? $nomiUtenti[$id] : "Utente ID $id";
                    }

                    echo implode(', ', $outputUtenti);
                    echo "</p>";

                    $conn->close();
                }


                echo "</div>"; // chiude blocco sconto
            }

            // --- Pulsante per aggiungere un nuovo sconto ---
            echo "<div style='margin-top:20px;'>
            <a href='aggiungi_sconti.php' style='
                display:inline-block;
                padding:10px 20px;
                background-color:#007BFF;
                color:white;
                border-radius:6px;
                text-decoration:none;
            '>➕ Inserisci Nuovo Sconto</a>
        </div>";
        } else {
            echo "<p>Nessuno sconto registrato.</p>
              <a href='aggiungi_sconti.php' style='color:#007BFF;'>Aggiungi il primo sconto</a>";
        }

        // --- Messaggi di successo / errore ---
        if (isset($_SESSION['successo_msg'])) {
            echo "<div class='sconto-msg sconto-success'>{$_SESSION['successo_msg']}</div>";
            unset($_SESSION['successo_msg']);
        } elseif (isset($_SESSION['errore_msg'])) {
            echo "<div class='sconto-msg sconto-error'>{$_SESSION['errore_msg']}</div>";
            unset($_SESSION['errore_msg']);
        }
        ?>
    </div>



    <div class="pdp">
        <div class="pdp-center">
            <p>&copy; 2025 JAM Music Store</p>
        </div>
        <div class="pdp-right">
            <a href="FAQs.php">FAQs</a>
        </div>
    </div>
</body>

</html>
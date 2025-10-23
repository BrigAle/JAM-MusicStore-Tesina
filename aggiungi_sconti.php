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
        <h2 style="text-align:left;">Aggiungi Sconto</h2>

        <?php
        // Mostra messaggi di successo/errore
        if (isset($_SESSION['successo_msg'])) {
            echo "<div class='msg success'>{$_SESSION['successo_msg']}</div>";
            unset($_SESSION['successo_msg']);
        } elseif (isset($_SESSION['errore_msg'])) {
            echo "<div class='msg error'>{$_SESSION['errore_msg']}</div>";
            unset($_SESSION['errore_msg']);
        }

        // Caricamento XML prodotti e utenti
        $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
        $xmlUtenti = simplexml_load_file("risorse/XML/utenti.xml");

        $tipo_condizione = $_POST['tipo_condizione'] ?? '';

        if (!$xmlProdotti || count($xmlProdotti->prodotto) == 0) {
            echo "<p>Nessun prodotto trovato.</p>";
        } else {
        ?>
            <form class="sconto-form" action="" method="post">
                <div class="sconto-field">
                    <label for="tipo_condizione">Tipo di condizione:</label>
                    <select id="tipo_condizione" name="tipo_condizione" class="sconto-input" required onchange="this.form.submit()">
                        <option value="">-- Seleziona condizione --</option>
                        <option value="mesi_iscrizione" <?php if ($tipo_condizione == 'mesi_iscrizione') echo 'selected'; ?>>Clienti iscritti da almeno X mesi</option>
                        <option value="crediti_da_data" <?php if ($tipo_condizione == 'crediti_da_data') echo 'selected'; ?>>Clienti con almeno M crediti da una data</option>
                        <option value="crediti_minimi" <?php if ($tipo_condizione == 'crediti_minimi') echo 'selected'; ?>>Clienti con almeno N crediti complessivi</option>
                        <option value="acquisto_specifico" <?php if ($tipo_condizione == 'acquisto_specifico') echo 'selected'; ?>>Clienti che hanno acquistato prodotti specifici</option>
                        <option value="reputazione_minima" <?php if ($tipo_condizione == 'reputazione_minima') echo 'selected'; ?>>Clienti con reputazione minima</option>
                        <option value="offerta_speciale" <?php if ($tipo_condizione == 'offerta_speciale') echo 'selected'; ?>>Offerta promozionale senza vincoli</option>
                    </select>
                    <noscript><input type="submit" value="Seleziona"></noscript>
                </div>

                <div class="sconto-field">
                    <label>Seleziona Prodotti:</label>
                    <div class="sconto-checkbox-group">
                        <?php
                        foreach ($xmlProdotti->prodotto as $p) {
                            $id = (string)$p['id'];
                            $nome = (string)$p->nome;
                            echo "<label class='sconto-checkbox-item'>
                            <input type='checkbox' name='prodotti[]' value='{$id}'> {$nome}
                          </label>";
                        }
                        ?>
                    </div>
                </div>

                <?php
                // Campi dinamici lato PHP in base alla condizione
                if ($tipo_condizione) {
                    echo "<fieldset class='sconto-condizione'>";
                    switch ($tipo_condizione) {
                        case 'mesi_iscrizione':
                            echo "<label>Numero di mesi:</label>
                          <input type='number' name='valore' class='sconto-input' min='1' required>";
                            break;

                        case 'crediti_minimi':
                            echo "<label>Crediti minimi complessivi:</label>
                          <input type='number' name='valore' class='sconto-input' min='1' required>";
                            break;

                        case 'crediti_da_data':
                            echo "<label>Crediti minimi:</label>
                          <input type='number' name='valore' class='sconto-input' min='1' required>
                          <label>Da data:</label>
                          <input type='date' name='data_riferimento' class='sconto-input' required>";
                            break;

                        case 'reputazione_minima':
                            echo "<label>Reputazione minima:</label>
                          <input type='number' name='valore' class='sconto-input' min='0' max='1000' step='0.1' required>";
                            break;

                        case 'offerta_speciale':
                            echo "<label>Nome evento (es. Black Friday):</label>
                          <input type='text' name='evento' class='sconto-input' required>
                          <input type='hidden' name='applicazione_globale' value='1'>"; // ✅ nuovo flag per salvataggio
                            break;

                        case 'acquisto_specifico':
                            echo "<label>ID prodotto già acquistato (rif):</label>
                          <input type='number' name='id_prodotto_rif' class='sconto-input' min='1' required>";
                            break;
                    }
                    echo "</fieldset>";
                }
                ?>

                <div class="sconto-field">
                    <label for="percentuale">Percentuale di Sconto (%):</label>
                    <input type="number" id="percentuale" name="percentuale" class="sconto-input" min="1" max="100" step="1" required>
                </div>

                <div class="sconto-date-group">
                    <div class="sconto-field">
                        <label for="data_inizio">Data Inizio:</label>
                        <input type="date" id="data_inizio" name="data_inizio" class="sconto-input" required>
                    </div>
                    <div class="sconto-field">
                        <label for="data_fine">Data Fine:</label>
                        <input type="date" id="data_fine" name="data_fine" class="sconto-input" required>
                    </div>
                </div>

                <?php
                require_once('risorse/PHP/connection.php');
                $conn = new mysqli($host, $user, $password, $db);
                if ($conn->connect_error) {
                    die("Connessione fallita: " . $conn->connect_error);
                }

                // Recupera solo gli ID degli utenti con ruolo = 'cliente'
                $query = "SELECT id FROM utente WHERE ruolo = 'cliente'";
                $result = $conn->query($query);

                $idClienti = [];
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $idClienti[] = $row['id'];
                    }
                }
                ?>

                <?php
                // ✅ Mostra la sezione destinatari SOLO se NON è offerta_speciale
                if ($tipo_condizione != 'offerta_speciale') {
                ?>
                    <div class="sconto-field">
                        <label>Seleziona Utenti Destinatari:</label>
                        <div class="sconto-checkbox-group" style="max-height:200px; overflow-y:auto; border:1px solid #aaa; padding:8px;">
                            <?php
                            if ($xmlUtenti && count($xmlUtenti->utente) > 0 && !empty($idClienti)) {
                                $trovato = false;
                                foreach ($xmlUtenti->utente as $u) {
                                    $idU = (string)$u['id'];
                                    if (in_array($idU, $idClienti)) {
                                        $trovato = true;
                                        $nomeU = (string)$u->nome;
                                        $cognomeU = (string)$u->cognome;
                                        echo "<label class='sconto-checkbox-item'>
                                        <input type='checkbox' name='utenti[]' value='{$idU}'> {$nomeU} {$cognomeU}
                                        </label>";
                                    }
                                }

                                if (!$trovato) {
                                    echo "<p>Nessun cliente trovato nel file XML corrispondente agli utenti nel database.</p>";
                                }
                            } else {
                                echo "<p>Nessun utente disponibile o nessun cliente trovato.</p>";
                            }
                            ?>
                        </div>
                    </div>
                <?php
                } else {
                    // Messaggio informativo in caso di offerta speciale
                    echo "<div class='sconto-field info'>
                        <p><strong>Nota:</strong> L'offerta speciale verrà applicata automaticamente a tutti gli utenti registrati (presenti e futuri).</p>
                      </div>";
                }
                ?>

                <?php
                // Mostra pulsante di submit solo dopo selezione condizione
                if ($tipo_condizione) {
                    echo '<button type="submit" formaction="risorse/PHP/gestore/salva_sconto.php" class="sconto-btn">Salva Sconto</button>';
                } else {
                    echo '<input type="submit" value="Continua" class="sconto-btn">';
                }
                ?>
            </form>

            <p><a href="gestione_sconti_gestore.php" class="sconto-back">Torna alla gestione sconti</a></p>
        <?php
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
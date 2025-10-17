<?php
session_start();
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
            <!-- admin links -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
                <a href="amministrazione.php">admin</a>
            <?php endif; ?>
            <!-- gestore links -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'):
                echo "<a href=\"gestione.php\">gestore</a>";
            endif; ?>
            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
            <?php endif; ?>
            <!-- cliente links -->
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div>
    </div>

    <!-- div presentazione sito -->
    <div class="content">

        <?php
        // Controlla se l'utente è loggato
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }
        ?>
        <!-- connessione al database per recuperare i dati dell'utente -->
        <?php
        require_once 'risorse/PHP/connection.php';
        $connection = new mysqli($host, $user, $password, $db);
        // Controlla la connessione
        if ($connection->connect_error) {
            die("Connessione fallita: " . $connection->connect_error);
        }
        $id_utente = $_SESSION['id_utente'];
        $query = "SELECT * FROM utente WHERE id='$id_utente'";
        $result = $connection->query($query);
        if ($result) {
            $record = $result->fetch_array(MYSQLI_ASSOC);
            $password_hash = $record['password'];
            $email = $record['email'];

        ?>

            <!-- carico il filme xml e recupero i dati con dom -->
            <?php
            $xmlFile = 'risorse/XML/utenti.xml';
            if (!file_exists($xmlFile)) {
                die("Errore: il file XML degli utenti non esiste");
            }
            $nome = $cognome = $telefono = $indirizzo = $reputazione = $stato = $portafoglio = $crediti = $data_iscrizione = "";
            $xml = simplexml_load_file($xmlFile);

            foreach ($xml->utente as $utente) {
                if ((int)$utente['id'] === (int)$id_utente) {
                    $nome = (string)$utente->nome;
                    $cognome = (string)$utente->cognome;
                    $telefono = (string)$utente->telefono;
                    $indirizzo = (string)$utente->indirizzo;
                    $reputazione = (string)$utente->reputazione;

                    $portafoglio = (float)$utente->portafoglio;
                    $crediti = (int)$utente->crediti;
                    $data_iscrizione = (string)$utente->data_iscrizione;
                    break; // Esci dal ciclo una volta trovato l'utente
                }
            }
            ?>
            <!-- visualizzo i dati dell'utente -->
            <h2>Profilo di <?php echo htmlspecialchars($record['username']); ?></h2>
            <div class="profile_info">
                <p><strong>id: <?php echo htmlspecialchars($id_utente); ?></strong></p>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
                <p><strong>Cognome:</strong> <?php echo htmlspecialchars($cognome); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($record['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Telefono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <p><strong>Indirizzo:</strong> <?php echo htmlspecialchars($indirizzo); ?></p>
                <p><strong>Reputazione:</strong> <?php echo htmlspecialchars($reputazione); ?> <a href="risorse/PHP/aggiorna_reputazione.php">Aggiorna reputazione</a></p>
                <p><strong>Stato:</strong> <?= $record['stato'] ? "Attivo" : "Disabilitato" ?></p>
                <form action="risorse/PHP/ricarica_portafoglio.php" method="post" style="display:flex; align-items:center; gap:8px;">
                    <p style="margin:0;">
                        <strong>Portafoglio:</strong>
                        €<?php echo number_format($portafoglio, 2, ',', '.'); ?>
                    </p>
                    <input type="number" name="importo" min="1" step="0.01" placeholder="€" required
                        style="width:80px; text-align:center; border:1px solid #aaa; border-radius:4px; padding:4px;">
                    <button type="submit"
                        style="background-color:#32CD32; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer;">
                        Ricarica
                    </button>
                </form>

                <?php
                if (isset($_SESSION['successo_msg'])) {
                    echo '<p style="color:green;">' . htmlspecialchars($_SESSION['successo_msg']) . '</p>';
                    unset($_SESSION['successo_msg']);
                }
                if (isset($_SESSION['errore_msg'])) {
                    echo '<p style="color:red;">' . htmlspecialchars($_SESSION['errore_msg']) . '</p>';
                    unset($_SESSION['errore_msg']);
                }
                ?>
                <p><strong>Crediti:</strong> <?php echo htmlspecialchars($crediti); ?><a href="richiesta_crediti.php"> Richiedi altri crediti</a></p>
                <p><strong>Data di Iscrizione:</strong> <?php echo htmlspecialchars($data_iscrizione); ?></p>
                <p><strong>Ruolo:</strong> <?php echo htmlspecialchars($record['ruolo']); ?></p>
                <!-- Aggiungi altre informazioni se necessario -->
                <a href="aggiorna_profilo.php">Modifica Profilo</a>
                <a href="cambia_password.php">Cambia Password</a>
                <a href="storico_acquisti.php">Vai allo storico degli acquisti</a>
                <?php if (isset($_SESSION['successo_msg']) && !empty($_SESSION['successo_msg'])): ?>
                    <p style="color: green;"><?php
                                                echo htmlspecialchars($_SESSION['successo_msg']);
                                                // Pulisci il messaggio dopo averlo mostrato
                                                $_SESSION['successo_msg'] = "";
                                                ?></p>
                <?php endif; ?>
                <?php if (isset($_SESSION['errore_msg']) && !empty($_SESSION['errore_msg'])): ?>
                    <p style="color: red;"><?php
                                            echo htmlspecialchars($_SESSION['errore_msg']);
                                            // Pulisci il messaggio dopo averlo mostrato
                                            $_SESSION['errore_msg'] = "";
                                            ?></p>
                <?php endif; ?>
                <?php
                if (isset($_SESSION['pwd_change_message']) && !empty($_SESSION['pwd_change_message'])) {
                    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['pwd_change_message']) . '</p>';
                    // Pulisci il messaggio dopo averlo mostrato
                    $_SESSION['pwd_change_message'] = "";
                }
                ?>
            </div>
        <?php
        } else {
            echo "<p>Errore nel recupero dei dati dell'utente.</p>";
        }
        // Chiudi la connessione
        $connection->close();
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
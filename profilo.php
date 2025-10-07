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
            <form action="homepage.php" method="get">
                <div class="searchContainer">

                    <input type="text" name="query" placeholder="Cerca brani, artisti, album..." />
                    <button type="submit"><img src="risorse/IMG/search.png" alt="Cerca"></button>

                    <!-- Checkbox nascosto -->
                    <input type="checkbox" id="advanced_commutator" style="display: none;" />
                    <label for="advanced_commutator" class="label_commutator">Ricerca avanzata</label>

                    <!-- Questo deve essere subito dopo il checkbox -->
                    <div class="advanced_filters">
                        <div class="filters_title">
                            <h4>Filtri avanzati</h4>
                        </div>
                        <div class="filters_container">
                            <h4>tamburi</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>chitarre</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>frochoni</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>vincenzo ferrara</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>vincenzo ferrara</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>vincenzo ferrara</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                        <div class="filters_container">
                            <h4>vincenzo ferrara</h4>
                            <label><input type="checkbox" name="formato[]" value="CD" /> CD</label>
                            <label><input type="checkbox" name="formato[]" value="Vinile" /> Vinile</label>
                            <label><input type="checkbox" name="scontati" value="1" /> Solo in sconto</label>
                        </div>
                    </div>

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
        <!-- mi connetto al db e prendo i dati dell'utente -->
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
        $id_utente = $_SESSION['id'];
        $query = "SELECT * FROM utente WHERE id='$id_utente'";
        $result = $connection->query($query);
        if ($result) {
            $record = $result->fetch_array(MYSQLI_ASSOC);
            $password_hash = $record['password'];
            $email = $record['email'];
        ?>
            <!-- converto la password da hash a normale -->
            <?php
            // Non è possibile convertire un hash in testo normale in modo sicuro.
            // La password hashata è progettata per essere unidirezionale.
            // Pertanto, non c'è modo di "decodificare" una password hashata.
            // Invece, quando un utente tenta di accedere, 
            // si confronta la password inserita con l'hash memorizzato usando password_verify().
            ?>

            <!-- carico il filme xml e recupero i dati con dom -->
            <?php
            $xmlFile = 'risorse/XML/utenti.xml';
            if (!file_exists($xmlFile)) {
                die("Errore: il file XML degli utenti non esiste");
            }
            $xml = simplexml_load_file($xmlFile);
            
            foreach ($xml->utente as $user) {
                if ((int)$user['id'] === (int)$id_utente) {
                    $nome = (string)$user->nome;
                    $cognome = (string)$user->cognome;
                    $telefono = (string)$user->telefono;
                    $indirizzo = (string)$user->indirizzo;
                    $reputazione = (string)$user->reputazione;
                    $stato = ((string)$user->stato === '1') ? true : false;
                    $portafoglio = (float)$user->portafoglio;
                    $crediti = (int)$user->crediti;
                    $data_iscrizione = (string)$user->data_iscrizione;
                    break; // Esci dal ciclo una volta trovato l'utente
                }
    
            }
            ?>
            <!-- visualizzo i dati dell'utente -->
            <h2>Profilo di <?php echo htmlspecialchars($record['username']); ?></h2>
            <div class="profile_info">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
                <p><strong>Cognome:</strong> <?php echo htmlspecialchars($cognome); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Telefono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <p><strong>Indirizzo:</strong> <?php echo htmlspecialchars($indirizzo); ?></p>
                <p><strong>Reputazione:</strong> <?php echo htmlspecialchars($reputazione); ?></p>
                <p><strong>Stato:</strong> <?php echo $stato ? 'Attivo' : 'Disabilitato'; ?></p>
                <p><strong>Portafoglio:</strong> €<?php echo number_format($portafoglio, 2); ?></p>
                <p><strong>Crediti:</strong> <?php echo htmlspecialchars($crediti); ?></p>
                <p><strong>Data di Iscrizione:</strong> <?php echo htmlspecialchars($data_iscrizione); ?></p>
                <p><strong>Ruolo:</strong> <?php echo htmlspecialchars($record['ruolo']); ?></p>
                <!-- Aggiungi altre informazioni se necessario -->
                <a href="modifica_profilo.php" class="edit_profile_button">Modifica Profilo</a>
                <a href="cambia_password.php" class="change_password_button">Cambia Password</a>
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
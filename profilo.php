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
    <div class="content user-profile">
        <?php
        if (!isset($_SESSION['username'])) {
            header("Location: login.php");
            exit();
        }

        require_once 'risorse/PHP/connection.php';
        $connection = new mysqli($host, $user, $password, $db);
        if ($connection->connect_error) {
            die("Connessione fallita: " . $connection->connect_error);
        }

        $id_utente = $_SESSION['id_utente'];
        $query = "SELECT * FROM utente WHERE id='$id_utente'";
        $result = $connection->query($query);

        if ($result) {
            $record = $result->fetch_array(MYSQLI_ASSOC);
            $email = $record['email'];

            $xmlFile = 'risorse/XML/utenti.xml';
            if (!file_exists($xmlFile)) die("Errore: il file XML degli utenti non esiste");
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
                    break;
                }
            }
        ?>
            <div class="profile-card">
                <h2 class="profile-title">Profilo di <?= htmlspecialchars($record['username']); ?></h2>
                <div class="profile-details">
                    <div class="profile-row"><strong>ID:</strong> <?= htmlspecialchars($id_utente); ?></div>
                    <div class="profile-row"><strong>Nome:</strong> <?= htmlspecialchars($nome); ?></div>
                    <div class="profile-row"><strong>Cognome:</strong> <?= htmlspecialchars($cognome); ?></div>
                    <div class="profile-row"><strong>Email:</strong> <?= htmlspecialchars($email); ?></div>
                    <div class="profile-row"><strong>Telefono:</strong> <?= htmlspecialchars($telefono); ?></div>
                    <div class="profile-row"><strong>Indirizzo:</strong> <?= htmlspecialchars($indirizzo); ?></div>
                    <div class="profile-row"><strong>Reputazione:</strong> <?= htmlspecialchars($reputazione); ?> <a href="risorse/PHP/aggiorna_reputazione.php" class="profile-link">Aggiorna</a></div>
                    <div class="profile-row"><strong>Stato:</strong> <?= $record['stato'] ? "Attivo" : "Disabilitato"; ?></div>

                    <?php if ($_SESSION['ruolo'] !== 'amministratore'&& $_SESSION['ruolo'] !== 'gestore'): ?>
                        <form action="risorse/PHP/ricarica_portafoglio.php" method="post" class="wallet-form">
                            <p class="profile-row wallet-info">
                                <strong>Portafoglio:</strong> €<?= number_format($portafoglio, 2, ',', '.'); ?>
                            </p>
                            <input type="number" name="importo" min="1" step="0.01" placeholder="€" required class="wallet-input">
                            <button type="submit" class="wallet-btn">Ricarica</button>
                        </form>
                    <?php endif; ?>


                    <?php if (isset($_SESSION['successo_msg'])): ?>
                        <p class="msg success"><?= htmlspecialchars($_SESSION['successo_msg']); ?></p>
                        <?php unset($_SESSION['successo_msg']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errore_msg'])): ?>
                        <p class="msg error"><?= htmlspecialchars($_SESSION['errore_msg']); ?></p>
                        <?php unset($_SESSION['errore_msg']); ?>
                    <?php endif; ?>
                    <?php if ($_SESSION['ruolo'] !== 'amministratore' && $_SESSION['ruolo'] !== 'gestore'): ?>
                        <div class="profile-row">
                            <strong>Crediti:</strong> <?= htmlspecialchars($crediti); ?>
                            <a href="richiesta_crediti.php" class="profile-link">Richiedi altri crediti</a>
                        </div>
                    <?php endif; ?>
                    <div class="profile-row"><strong>Data di Iscrizione:</strong> <?= htmlspecialchars($data_iscrizione); ?></div>
                    <div class="profile-row"><strong>Ruolo:</strong> <?= htmlspecialchars($record['ruolo']); ?></div>
                    <div class="profile-actions">
                        <a href="aggiorna_profilo.php" class="profile-btn">Modifica Profilo</a>
                        <a href="cambia_password.php" class="profile-btn">Cambia Password</a>
                        <?php if ($_SESSION['ruolo'] === 'cliente'): ?>
                            <a href="storico_acquisti.php" class="profile-btn">Storico Acquisti</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        } else {
            echo "<p class='msg error'>Errore nel recupero dei dati dell'utente.</p>";
        }
        $connection->close();
        ?>
        <?php if (isset($_SESSION['pwd_change_message']) && !empty($_SESSION['pwd_change_message'])): ?>
                <p class="msg success" style="margin-top:15px;">
                    <?= htmlspecialchars($_SESSION['pwd_change_message']); ?>
                </p>
                <?php unset($_SESSION['pwd_change_message']); ?>
            <?php endif; ?>
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
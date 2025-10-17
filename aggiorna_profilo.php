<?php
session_start();

// ✅ Solo utenti loggati
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// ✅ Connessione DB
require_once 'risorse/PHP/connection.php';
$connection = new mysqli($host, $user, $password, $db);
if ($connection->connect_error) {
    die("Connessione fallita: " . $connection->connect_error);
}

$id_utente = $_SESSION['id_utente'];
$query = "SELECT * FROM utente WHERE id='$id_utente'";
$result = $connection->query($query);
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
        <div class="profile-card">
            <h2 class="profile-title">Aggiorna Profilo</h2>

            <?php
            if ($result) {
                $record = $result->fetch_array(MYSQLI_ASSOC);
                $email = $record['email'];
                $username = $record['username'];

                // Carica XML utente
                $xmlFile = 'risorse/XML/utenti.xml';
                if (!file_exists($xmlFile)) {
                    die("<p class='msg error'>Errore: il file XML degli utenti non esiste.</p>");
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
                        break;
                    }
                }
            ?>
                <div class="profile-details">
                    <h3 style="color:#ffeb00; margin-bottom:10px;">Dati attuali</h3>
                    <div class="profile-row"><strong>ID:</strong> <?= htmlspecialchars($id_utente); ?></div>
                    <div class="profile-row"><strong>Nome:</strong> <?= htmlspecialchars($nome); ?></div>
                    <div class="profile-row"><strong>Cognome:</strong> <?= htmlspecialchars($cognome); ?></div>
                    <div class="profile-row"><strong>Username:</strong> <?= htmlspecialchars($username); ?></div>
                    <div class="profile-row"><strong>Email:</strong> <?= htmlspecialchars($email); ?></div>
                    <div class="profile-row"><strong>Telefono:</strong> <?= htmlspecialchars($telefono); ?></div>
                    <div class="profile-row"><strong>Indirizzo:</strong> <?= htmlspecialchars($indirizzo); ?></div>
                    <div class="profile-row"><strong>Reputazione:</strong> <?= htmlspecialchars($reputazione); ?></div>
                    <div class="profile-row"><strong>Stato:</strong> <?= $record['stato'] ? "Attivo " : "Disabilitato "; ?></div>
                    <?php if ($_SESSION['ruolo'] !== 'amministratore' && $_SESSION['ruolo'] !== 'gestore'): ?>
                        <div class="profile-row"><strong>Portafoglio:</strong> €<?= number_format($portafoglio, 2, ',', '.'); ?></div>
                        <div class="profile-row"><strong>Crediti:</strong> <?= htmlspecialchars($crediti); ?></div>
                    <?php endif; ?>
                    <div class="profile-row"><strong>Data di iscrizione:</strong> <?= htmlspecialchars($data_iscrizione); ?></div>
                </div>

                <form action="risorse/PHP/aggiorna_profilo.php" method="POST" class="profile-form" style="margin-top:30px;">
                    <h3 style="color:#ffeb00; margin-bottom:10px;">Modifica i tuoi dati</h3>
                    <p style="color:#aaa;">Lascia un campo vuoto per non modificare quel dato.</p>

                    <div class="profile-details">
                        <label for="nome"><strong>Nome:</strong></label>
                        <input type="text" id="nome" name="nome" class="wallet-input" value="" />

                        <label for="cognome"><strong>Cognome:</strong></label>
                        <input type="text" id="cognome" name="cognome" class="wallet-input" value="" />

                        <label for="username"><strong>Username:</strong></label>
                        <input type="text" id="username" name="username" class="wallet-input" value="" />

                        <label for="email"><strong>Email:</strong></label>
                        <input type="email" id="email" name="email" class="wallet-input" value="" />

                        <label for="telefono"><strong>Telefono:</strong></label>
                        <input type="text" id="telefono" name="telefono" class="wallet-input" value="" />

                        <label for="indirizzo"><strong>Indirizzo:</strong></label>
                        <input type="text" id="indirizzo" name="indirizzo" class="wallet-input" value="" />

                        <div style="text-align:center; margin-top:20px;">
                            <button type="submit" class="wallet-btn">Aggiorna Profilo</button>
                            <a href="profilo.php" class="profile-btn">Annulla</a>
                        </div>
                    </div>
                </form>

            <?php
            } else {
                echo "<p class='msg error'>Errore nel recupero dei dati dell'utente.</p>";
            }
            $connection->close();
            ?>
        </div>
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
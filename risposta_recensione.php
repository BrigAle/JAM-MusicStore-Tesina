<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
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
        <!-- Mostro la recensione a cui sto rispondendo -->
        <?php
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
            header("Location: login.php");
            exit();
        }

        // Ricevi i dati dal form precedente
        $id_prodotto   = $_POST['id_prodotto'];
        $id_utente     = $_POST['id_utente'];
        $id_recensione = $_POST['id_recensione'];

        // Se manca qualcosa, errore
        if (!$id_prodotto || !$id_utente || !$id_recensione) {
            die("Errore: dati mancanti nella richiesta.");
        }

        // Carica il file XML delle recensioni per trovare la recensione giusta
        $xmlRecensioni = simplexml_load_file('risorse/XML/recensioni.xml');
        $recensioneDaRispondere = null;
        foreach ($xmlRecensioni->recensione as $recensione) {
            if ((int)$recensione['id'] === (int)$id_recensione) {
                $recensioneDaRispondere = $recensione;
                break;
            }
        }
        if (!$recensioneDaRispondere) {
            die("Recensione non trovata.");
        }

        // Ricava il nome utente dal database
        require_once 'risorse/PHP/connection.php';
        $conn = new mysqli($host, $user, $password, $db);
        if ($conn->connect_error) {
            die("Connessione fallita: " . $conn->connect_error);
        }
        $query = "SELECT username FROM utente WHERE id = '$id_utente'";
        $result = $conn->query($query);
        if ($result) {
            $nome_utente = $result->fetch_array(MYSQLI_ASSOC)['username'];
        }
        $conn->close();
        ?>

        <h2>Rispondi alla recensione di <?= htmlspecialchars($nome_utente) ?></h2>
        <p>
            <strong>Recensione:</strong> <?= htmlspecialchars($recensione->commento) ?><br />
            <strong>Data:</strong> <?= htmlspecialchars($recensione->data) ?>
        </p>

        <form method="POST" action="risorse/PHP/inserisci_risposta.php">
            <input type="hidden" name="id_recensione" value="<?= htmlspecialchars($id_recensione) ?>" />
            <input type="hidden" name="id_utente" value="<?= htmlspecialchars($id_utente) ?>" />
            <input type="hidden" name="id_prodotto" value="<?= htmlspecialchars($id_prodotto) ?>" />
            <label for="risposta">La tua risposta:</label><br />
            <textarea name="risposta" required></textarea><br />
            <input type="submit" value="Invia risposta" />
        </form>
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
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
        
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
            header("Location: login.php");
            exit();
        }
        $id_prodotto        = isset($_POST['id_prodotto']) ? (string)$_POST['id_prodotto'] : '';
        $id_recensione      = isset($_POST['id_recensione']) ? (string)$_POST['id_recensione'] : '';
        // Questo è l'ID dell'autore della RECENSIONE a cui stai rispondendo (può arrivare dal form precedente)
        $id_autore_recensione = isset($_POST['id_utente']) ? (string)$_POST['id_utente'] : '';

        // ID dell'utente che RISPONDE (quello loggato ora)
        $id_utente_rispondente = isset($_SESSION['id_utente']) ? (string)$_SESSION['id_utente'] : '';

        // Flag per eventuali errori da mostrare (senza interrompere brutalmente con die)
        $errore = '';

        if ($id_prodotto === '' || $id_recensione === '') {
            $errore = 'Errore: dati mancanti (id_prodotto o id_recensione).';
        }

        $recensioneDaRispondere = null;
        if ($errore === '') {
            $xmlRecensioni = @simplexml_load_file('risorse/XML/recensioni.xml');
            if (!$xmlRecensioni) {
                $errore = 'Errore: impossibile caricare il file delle recensioni.';
            } else {
                foreach ($xmlRecensioni->recensione as $rec) {
                    if ((string)$rec['id'] === $id_recensione) {
                        $recensioneDaRispondere = $rec;
                        // Se dal form non è arrivato l'id dell'autore, lo recupero dalla recensione
                        if ($id_autore_recensione === '' && isset($rec->id_utente)) {
                            $id_autore_recensione = (string)$rec->id_utente;
                        }
                        break;
                    }
                }
                if (!$recensioneDaRispondere) {
                    $errore = 'Recensione non trovata.';
                }
            }
        }

        $nome_utente_autore = 'Utente';
        if ($errore === '' && $id_autore_recensione !== '') {
            require_once 'risorse/PHP/connection.php';
            $conn = @new mysqli($host, $user, $password, $db);
            if ($conn && !$conn->connect_error) {
                // uso prepared statement per sicurezza
                if ($stmt = $conn->prepare("SELECT username FROM utente WHERE id = ?")) {
                    $stmt->bind_param("s", $id_autore_recensione);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res && $res->num_rows > 0) {
                        $row = $res->fetch_assoc();
                        $nome_utente_autore = $row['username'];
                    }
                    $stmt->close();
                }
                $conn->close();
            }
        }
        ?>

        <div class="profile-card" style="max-width:700px; margin:auto; padding:40px;">
            <h2 class="profile-title" style="text-align:center;">Rispondi alla recensione</h2>

            <?php if ($errore !== ''): ?>
                <p class="msg error" style="margin-top:15px;"><?php echo htmlspecialchars($errore); ?></p>
            <?php else: ?>
                <div class="profile-details" style="margin-top:20px;">
                    <p style="color:#ffeb00; font-size:17px;">
                        <strong>Recensione di <?php echo htmlspecialchars($nome_utente_autore); ?>:</strong>
                    </p>
                    <div style="background-color:#1c1c1c; border:1px solid #2c2c2c; border-radius:6px; padding:12px; color:#ddd; margin-bottom:20px;">
                        <p style="margin:0;">
                            <?php echo htmlspecialchars((string)$recensioneDaRispondere->commento); ?>
                        </p>
                        <p style="margin:6px 0 0; color:#aaa; font-size:13px;">
                            Data: <?php echo htmlspecialchars((string)$recensioneDaRispondere->data); ?>
                        </p>
                    </div>

                    <form method="POST" action="risorse/PHP/inserisci_risposta.php" class="profile-form" style="display:flex; flex-direction:column; gap:14px;">
                        <!-- Hidden necessari -->
                        <input type="hidden" name="id_recensione" value="<?php echo htmlspecialchars($id_recensione); ?>" />
                        <input type="hidden" name="id_utente" value="<?php echo htmlspecialchars($id_utente_rispondente); ?>" />
                        <input type="hidden" name="id_prodotto" value="<?php echo htmlspecialchars($id_prodotto); ?>" />

                        <label for="risposta" style="font-weight:bold; color:#ffeb00;">La tua risposta:</label>
                        <textarea id="risposta" name="risposta" rows="4" required
                            style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2c2c2c; color:#fff; font-size:15px; resize:vertical;"></textarea>

                        <div style="text-align:center; margin-top:20px;">
                            <button type="submit"
                                style="background-color:#32CD32; color:white; border:none; padding:10px 22px; border-radius:6px; cursor:pointer; font-size:15px; font-weight:bold; margin-right:8px;">
                                Invia risposta
                            </button>
                            <a href="recensioni.php?id_prodotto=<?php echo htmlspecialchars($id_prodotto); ?>"
                                style="background-color:#2c2c2c; color:#1E90FF; border:none; padding:10px 22px; border-radius:6px; font-size:15px; font-weight:bold; text-decoration:none;">
                                Annulla
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
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
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
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'): ?>
                <a href="gestione.php">gestore</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
            <?php endif; ?>

            <!-- cliente links -->
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div> <!-- fine navLink -->
    </div> <!-- ✅ QUI chiudiamo la header -->

    <div class="content">
        <?php
        $id_utente_recensione = $_POST['id_utente_recensione'] ?? null;
        $id_utente_risposta = $_POST['id_utente_risposta'] ?? null;
        $id_risposta = $_POST['id_risposta'] ?? null;
        $id_recensione = $_POST['id_recensione'] ?? null;
        ?>

        <h2>Segnala <?php echo ($id_risposta !== null) ? 'risposta' : 'recensione'; ?></h2>
        <form action="risorse/PHP/segnala_recensione_risposta.php" method="POST">
            <input type="hidden" name="id_utente_recensione" value="<?php echo htmlspecialchars($id_utente_recensione); ?>" />
            <input type="hidden" name="id_utente_risposta" value="<?php echo htmlspecialchars($id_utente_risposta); ?>" />
            <input type="hidden" name="id_risposta" value="<?php echo htmlspecialchars($id_risposta); ?>" />
            <input type="hidden" name="id_recensione" value="<?php echo htmlspecialchars($id_recensione); ?>" />
            <input type="hidden" name="id_prodotto" value="<?php echo htmlspecialchars($_POST['id_prodotto']); ?>" />
            <label for="motivo">Motivo della segnalazione:</label><br />
            <textarea id="motivo" name="motivo" rows="4" cols="50" placeholder="Inserisci il motivo della segnalazione..." required></textarea><br /><br />
            <input type="submit" value="Invia segnalazione" />
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

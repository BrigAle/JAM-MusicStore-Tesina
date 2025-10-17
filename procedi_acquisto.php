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
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div>

    </div>



    <div class="content">
        <?php  

        
        if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
            $_SESSION['errore'] = 'Effettua il login per procedere.';
            header("Location: login.php");
            exit();
        }

        $id_utente = $_SESSION['id_utente'] ?? '';
        if (empty($id_utente)) {
            $_SESSION['errore'] = 'Utente non riconosciuto.';
            header("Location: catalogo.php");
            exit();
        }

        // Totale carrello aggiornato (viene da POST)
        $totaleOrdine = isset($_POST['totale_carrello']) ? floatval($_POST['totale_carrello']) : 0;

        if ($totaleOrdine <= 0) {
            $_SESSION['errore'] = 'Errore: il totale carrello non è valido o mancante.';
            header("Location: cart.php");
            exit();
        }
        ?>

        <h2>Procedi all'Acquisto</h2>
        <p>Totale ordine (con eventuali sconti):
            <strong style="color:#ffcc00;">€<?= number_format($totaleOrdine, 2, ',', '.') ?></strong>
        </p>

        <form action="risorse/PHP/conferma_acquisto.php" method="post">
            <input type="hidden" name="id_utente" value="<?= htmlspecialchars($id_utente) ?>">
            <input type="hidden" name="totale_carrello" value="<?= number_format($totaleOrdine, 2, '.', '') ?>">

            <h3>Scegli il metodo di pagamento:</h3>

            <label style="margin-right:15px;">
                <input type="radio" name="metodo_pagamento" value="Portafoglio" required> Portafoglio
            </label>
            <label>
                <input type="radio" name="metodo_pagamento" value="Carta di credito" required> Carta di credito
            </label>

            <br><br>
            <button type="submit" style="
            padding:10px 20px; 
            background-color:#1E90FF; 
            color:white; 
            border:none; 
            border-radius:4px; 
            cursor:pointer;">
                Conferma Acquisto
            </button>
        </form>

        <p style="margin-top:15px;">
            <a href="cart.php" style="color:#1E90FF;">⬅ Torna al carrello</a>
        </p>
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
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

            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
                <a href="amministrazione.php">admin</a>
            <?php endif; ?>


            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'): ?>
                <a href="gestione.php">gestore</a>
            <?php endif; ?>

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
        <div class="user-profile">
            <?php
            $id_utente_recensione = $_POST['id_utente_recensione'] ?? null;
            $id_utente_risposta = $_POST['id_utente_risposta'] ?? null;
            $id_risposta = $_POST['id_risposta'] ?? null;
            $id_recensione = $_POST['id_recensione'] ?? null;
            ?>

            <h2>Segnala <?php echo ($id_risposta !== null) ? 'risposta' : 'recensione'; ?></h2>
            <div class="profile-card">
                <form action="risorse/PHP/segnala_recensione_risposta.php" method="POST" style="display:flex; flex-direction:column; gap:16px;">
                    <input type="hidden" name="id_utente_recensione" value="<?= htmlspecialchars($id_utente_recensione); ?>" />
                    <input type="hidden" name="id_utente_risposta" value="<?= htmlspecialchars($id_utente_risposta); ?>" />
                    <input type="hidden" name="id_risposta" value="<?= htmlspecialchars($id_risposta); ?>" />
                    <input type="hidden" name="id_recensione" value="<?= htmlspecialchars($id_recensione); ?>" />
                    <input type="hidden" name="id_prodotto" value="<?= htmlspecialchars($_POST['id_prodotto']); ?>" />

                    <label for="motivo" style="color:#ffeb00; font-weight:bold; font-size:15px;">
                        Motivo della segnalazione:
                    </label>
                    <textarea id="motivo" name="motivo" rows="5" cols="50" required
                        placeholder="Inserisci il motivo della segnalazione..."
                        style="background:#111; color:#f5f5f5; border:1px solid #333; border-radius:8px; padding:12px; font-size:14px; resize:none; outline:none; transition:0.3s;">
                    </textarea>

                    <input type="submit" value="Invia segnalazione"
                        style="background-color:#ffeb00; color:#111; font-weight:bold; border:none; border-radius:8px; padding:10px 16px; cursor:pointer; font-size:15px; transition:0.3s; box-shadow:0 0 8px rgba(255,235,0,0.3);"
                        onmouseover="this.style.backgroundColor='#ffee33';"
                        onmouseout="this.style.backgroundColor='#ffeb00';" />
                </form>
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
<?php
session_start();
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true') {
    header("Location: login.php");
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
            <h2 class="profile-title">Cambia Password</h2>

            <form action="risorse/PHP/cambia_password.php" method="post" class="profile-form">
                <div class="profile-details">
                    <label for="current_password"><strong>Password Attuale:</strong></label>
                    <input type="password" id="current_password" name="current_password" required class="wallet-input" />

                    <label for="new_password"><strong>Nuova Password:</strong></label>
                    <input type="password" id="new_password" name="new_password" required class="wallet-input" />

                    <label for="confirm_password"><strong>Conferma Nuova Password:</strong></label>
                    <input type="password" id="confirm_password" name="confirm_password" required class="wallet-input" />

                    <div style="text-align:center; margin-top:20px;">
                        <button type="submit" class="wallet-btn">Aggiorna Password</button>
                        <a href="profilo.php" class="profile-btn">Annulla</a>
                    </div>
                </div>
            </form>

            <?php if (isset($_SESSION['pwd_change_message']) && !empty($_SESSION['pwd_change_message'])): ?>
                <p class="msg error" style="margin-top:15px;">
                    <?= htmlspecialchars($_SESSION['pwd_change_message']); ?>
                </p>
                <?php $_SESSION['pwd_change_message'] = ""; ?>
            <?php endif; ?>
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
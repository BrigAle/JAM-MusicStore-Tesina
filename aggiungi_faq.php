<?php
session_start();
if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] != 'amministratore') {
    header("Location: homepage.php");
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

    <div class="content">
        <div class="faq_container">
            <h2>Aggiungi una nuova FAQ</h2>
            <form action="risorse\PHP\amministratore\aggiungi_faq.php" method="post">
                <label for="domanda">Domanda:</label><br />
                <input type="text" id="domanda" name="domanda" required /><br /><br />
                <label for="risposta">Risposta:</label><br />
                <textarea id="risposta" name="risposta" rows="4" cols="50" required></textarea><br /><br />
                <label>Categoria:</label><br/>
                <label><input type="radio" name="categoria" value="Pagamenti" required> Pagamenti</label><br />
                <label><input type="radio" name="categoria" value="Ordini" required> Ordini</label><br />
                <label><input type="radio" name="categoria" value="Prodotti" required> Prodotti</label><br />
                <label><input type="radio" name="categoria" value="Bonus" required> Bonus</label><br />
                <label><input type="radio" name="categoria" value="Registrazione" required> Registrazione</label><br />
                <input type="submit" value="Aggiungi FAQ" />
            </form>
        </div>
    </div>

</body>

</html> 
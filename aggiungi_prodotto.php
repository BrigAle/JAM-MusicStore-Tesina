<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] != 'gestore') {
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

    <div class="content">
        <div class="form_container">
            <h2>Aggiungi un nuovo prodotto al catalogo</h2>
            <form action="risorse\PHP\gestore\aggiungi_prodotto.php" method="post" enctype="multipart/form-data">

                <label for="nome">Nome prodotto:</label>
                <input type="text" id="nome" name="nome" required />

                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" required />

                <label for="descrizione">Descrizione:</label>
                <textarea id="descrizione" name="descrizione" required></textarea>

                <label for="prezzo">Prezzo (€):</label>
                <input type="number" step="1.00" id="prezzo" name="prezzo" required />

                <label for="bonus">Punti bonus:</label>
                <input type="number" id="bonus" name="bonus" required />

                <label for="immagine">Immagine del prodotto:</label>
                <input type="file" id="immagine" name="immagine" accept="image/*" required />

                <button type="submit">Aggiungi Prodotto</button>

        </div>
    </div>

</body>

</html> 
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
    <?php
    if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
        header("Location: login.php");
        exit();
    }

    $id_prodotto = $_GET['id_prodotto'] ?? '';
    if (empty($id_prodotto)) {
        echo "<p>Errore: nessun prodotto selezionato.</p>";
        exit();
    }

    // Carico i dati del prodotto
    $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
    $prodotto = null;
    foreach ($xmlProdotti->prodotto as $p) {
        if ((string)$p['id'] === (string)$id_prodotto) {
            $prodotto = $p;
            break;
        }
    }

    if (!$prodotto) {
        echo "<p>Prodotto non trovato.</p>";
        exit();
    }

    $nome = (string)$prodotto->nome;
    $immagine = "risorse/IMG/prodotti/" . (string)$prodotto->immagine;
    ?>
    <div class="content"
        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:70vh; text-align:center;">

        <div style="background-color:#111; border:1px solid #333; border-radius:10px; padding:30px 40px; width:90%; max-width:600px; box-shadow:0 0 10px rgba(0,0,0,0.4);">

            <h2 style="margin-bottom:20px;">Scrivi una recensione</h2>

            <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:25px;">
                <img src="<?= $immagine ?>" alt="<?= htmlspecialchars($nome) ?>"
                    style="width:140px; height:140px; object-fit:contain; border-radius:6px; background:#222; padding:10px; margin-bottom:12px;">
                <h3 style="color:#fff;"><?= htmlspecialchars($nome) ?></h3>
            </div>

            <form action="risorse/PHP/salva_recensione.php" method="post"
                style="display:flex; flex-direction:column; gap:14px; align-items:center; color:#ddd;">

                <input type="hidden" name="id_prodotto" value="<?= htmlspecialchars($id_prodotto) ?>">

                <label for="valutazione" style="font-weight:bold;">Valutazione:</label>
                <select name="valutazione" id="valutazione" required
                    style="padding:8px; border-radius:6px; border:1px solid #555; width:130px; background-color:#222; color:#fff;">
                    <option value="">--</option>
                    <option value="1">⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                </select>

                <label for="commento" style="font-weight:bold;">Commento:</label>
                <textarea id="commento" name="commento" rows="5" required
                    placeholder="Scrivi qui la tua opinione..."
                    style="width:100%; max-width:500px; padding:10px; border-radius:8px; border:1px solid #555; background-color:#222; color:#fff; resize:none;"></textarea>

                <button type="submit"
                    style="background-color:#1E90FF; color:white; border:none; padding:10px 22px; border-radius:8px; cursor:pointer; font-weight:bold; margin-top:10px; transition:0.2s;">
                    Invia recensione
                </button>

                <a href="recensioni.php?id_prodotto=<?= $id_prodotto ?>"
                    style="margin-top:10px; text-decoration:none; color:#1E90FF;">← Torna alle recensioni</a>
            </form>
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
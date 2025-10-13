<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
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
    <?php
    // Caricamento XML prodotti
    $xmlFile = 'risorse/XML/prodotti.xml';
    $xml = simplexml_load_file($xmlFile);

    // Ottieni ID prodotto da GET
    if (isset($_GET['id_prodotto'])) {
        $id_prodotto = $_GET['id_prodotto'];
    } else {
        die("<p>ID prodotto non specificato.</p>");
    }

    // Trova il prodotto corrispondente
    $prodottoTrovato = null;
    foreach ($xml->prodotto as $p) {
        if ((int)$p['id'] === (int)$id_prodotto) {
            $prodottoTrovato = $p;
            break;
        }
    }

    if ($prodottoTrovato):
        $nome = (string)$prodottoTrovato->nome;
        $categoria = (string)$prodottoTrovato->categoria;
        $descrizione = (string)$prodottoTrovato->descrizione;
        $prezzo = (float)$prodottoTrovato->prezzo;
        $bonus = (float)$prodottoTrovato->bonus;
        $data_inserimento = (string)$prodottoTrovato->data_inserimento;
        $immagine = (string)$prodottoTrovato->immagine;
    ?>

        <h2>Modifica prodotto: <?php echo htmlspecialchars($nome); ?></h2>

        <div class="profile_info">
            <!-- multipart form ddata per caricare immagini -->
            <form action="risorse/PHP/gestore/aggiorna_prodotto.php" method="POST" enctype="multipart/form-data">
                <p>Dati attuali:</p>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($id_prodotto); ?></p>
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?></p>
                <p><strong>Categoria:</strong> <?php echo htmlspecialchars($categoria); ?></p>
                <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($descrizione); ?></p>
                <p><strong>Prezzo:</strong> €<?php echo number_format($prezzo, 2, ',', '.'); ?></p>
                <p><strong>Bonus:</strong> <?php echo number_format($bonus, 2, ',', '.'); ?> punti</p>
                <p><strong>Data di inserimento:</strong> <?php echo htmlspecialchars($data_inserimento); ?></p>
                <p><strong>Immagine:</strong></p>
                <img src="risorse/IMG/prodotti/<?php echo htmlspecialchars($immagine); ?>" alt="<?php echo htmlspecialchars($nome); ?>" style="width:120px; height:120px; object-fit:contain; border-radius:8px; background:#111; margin-bottom:10px;" />

                <br><br>
                <p>Modifica i dati (lascia vuoto per non modificare):</p>

                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_prodotto); ?>" />

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Nuovo nome prodotto" />

                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria" placeholder="Nuova categoria" />

                <label for="descrizione">Descrizione:</label>
                <textarea id="descrizione" name="descrizione" rows="4" style="width:100%; background:#2A2A2A; color:#F5F5F5; border:1px solid #555; border-radius:4px; padding:10px;" placeholder="Nuova descrizione"></textarea>

                <label for="prezzo">Prezzo (€):</label>
                <input type="number" step="0.01" id="prezzo" name="prezzo" placeholder="Es. 999.99" />

                <label for="bonus">Bonus punti:</label>
                <input type="number" step="0.01" id="bonus" name="bonus" placeholder="Es. 50.00" />

                <label for="immagine">Nuova immagine:</label>
                <input type="file" id="immagine" name="immagine" accept="image/*" />

                <br>
                <button type="submit">Aggiorna Prodotto</button>
            </form>
        </div>

    <?php
    else:
        echo "<p>Prodotto non trovato.</p>";
    endif;
    ?>
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
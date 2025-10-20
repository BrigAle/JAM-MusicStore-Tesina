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

    < class="content user-profile">
    <div class="profile-card">
        <h2 class="profile-title">Modifica Prodotto</h2>

        <?php
        // Caricamento XML prodotti
        $xmlFile = 'risorse/XML/prodotti.xml';
        $xml = simplexml_load_file($xmlFile);

        // Ottieni ID prodotto da GET
        if (!isset($_GET['id_prodotto'])) {
            die("<p class='msg error'>ID prodotto non specificato.</p>");
        }

        $id_prodotto = $_GET['id_prodotto'];
        $prodottoTrovato = null;

        // Trova prodotto nel file XML
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

            <!-- DATI ATTUALI -->
            <div class="profile-details">
                <h3 style="color:#ffeb00; margin-bottom:10px;">Dati attuali</h3>
                <div class="profile-row"><strong>ID:</strong> <?= htmlspecialchars($id_prodotto); ?></div>
                <div class="profile-row"><strong>Nome:</strong> <?= htmlspecialchars($nome); ?></div>
                <div class="profile-row"><strong>Categoria:</strong> <?= htmlspecialchars($categoria); ?></div>
                <div class="profile-row"><strong>Descrizione:</strong> <?= htmlspecialchars($descrizione); ?></div>
                <div class="profile-row"><strong>Prezzo:</strong> €<?= number_format($prezzo, 2, ',', '.'); ?></div>
                <div class="profile-row"><strong>Bonus:</strong> <?= number_format($bonus, 2, ',', '.'); ?> punti</div>
                <div class="profile-row"><strong>Data inserimento:</strong> <?= htmlspecialchars($data_inserimento); ?></div>
                <div class="profile-row"><strong>Immagine attuale:</strong></div>
                <img src="risorse/IMG/prodotti/<?= htmlspecialchars($immagine); ?>"
                     alt="<?= htmlspecialchars($nome); ?>"
                     style="width:150px; height:150px; object-fit:contain; border-radius:8px;
                            background:#111; margin-bottom:15px; box-shadow:0 0 8px rgba(255,235,0,0.4);" />
            </div>

            <!-- FORM DI MODIFICA -->
            <form action="risorse/PHP/gestore/aggiorna_prodotto.php" method="POST" enctype="multipart/form-data"
                  class="profile-form" style="margin-top:30px;">
                <h3 style="color:#ffeb00; margin-bottom:10px;">Modifica dati prodotto</h3>
                <p style="color:#aaa;">Lascia un campo vuoto per non modificarlo.</p>

                <input type="hidden" name="id" value="<?= htmlspecialchars($id_prodotto); ?>" />

                <div class="profile-details">
                    <label for="nome"><strong>Nome:</strong></label>
                    <input type="text" id="nome" name="nome" class="wallet-input" placeholder="Nuovo nome prodotto" />

                    <label for="categoria"><strong>Categoria:</strong></label>
                    <input type="text" id="categoria" name="categoria" class="wallet-input" placeholder="Nuova categoria" />

                    <label for="descrizione"><strong>Descrizione:</strong></label>
                    <textarea id="descrizione" name="descrizione" rows="4" class="wallet-input"
                              placeholder="Nuova descrizione"></textarea>

                    <label for="prezzo"><strong>Prezzo (€):</strong></label>
                    <input type="number" step="0.01" id="prezzo" name="prezzo" class="wallet-input" placeholder="Es. 999.99" />

                    <label for="bonus"><strong>Bonus punti:</strong></label>
                    <input type="number" step="0.01" id="bonus" name="bonus" class="wallet-input" placeholder="Es. 50.00" />

                    <label for="immagine"><strong>Nuova immagine:</strong></label>
                    <input type="file" id="immagine" name="immagine" class="wallet-input" accept="image/*" />

                    <div style="text-align:center; margin-top:20px;">
                        <button type="submit" class="wallet-btn">Aggiorna Prodotto</button>
                        <a href="gestione_prodotti_gestore.php" class="profile-btn">Annulla</a>
                    </div>
                </div>
            </form>

        <?php else: ?>
            <p class="msg error"> Prodotto non trovato.</p>
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
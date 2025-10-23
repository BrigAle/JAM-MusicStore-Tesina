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

    <div class="content user-profile">
        <?php
        // recupero i dati precedenti se ci sono
        $old = $_SESSION['old_data'] ?? [
            'nome' => '',
            'categoria' => '',
            'descrizione' => '',
            'prezzo' => '',
            'bonus' => ''
        ];
        $old['nome'] = $old['nome'] ?? '';
        $old['categoria'] = $old['categoria'] ?? '';
        $old['descrizione'] = $old['descrizione'] ?? '';
        $old['prezzo'] = $old['prezzo'] ?? '';
        $old['bonus'] = $old['bonus'] ?? '';
        
        ?>

        <div class="profile-card" style="max-width:700px; margin:auto; padding:40px;">
            <h2 class="profile-title" style="text-align:center; color:#ffeb00; margin-bottom:25px; text-shadow:0 0 4px #E50914;">
                Aggiungi un nuovo prodotto
            </h2>

            <form action="risorse/PHP/gestore/aggiungi_prodotto.php" method="post" enctype="multipart/form-data" class="profile-form">
                <div class="profile-details" style="display:flex; flex-direction:column; gap:14px;">

                    <label for="nome" style="font-weight:bold; color:#ffeb00;">Nome prodotto:</label>
                    <input type="text" id="nome" name="nome" required
                        value="<?= htmlspecialchars($old['nome']) ?>"
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444;
                           background:#2c2c2c; color:#fff; font-size:15px;" />

                    <label for="categoria" style="font-weight:bold; color:#ffeb00;">Categoria:</label>
                    <input type="text" id="categoria" name="categoria" required
                        value="<?= htmlspecialchars($old['categoria']) ?>"
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444;
                           background:#2c2c2c; color:#fff; font-size:15px;" />

                    <label for="descrizione" style="font-weight:bold; color:#ffeb00;">Descrizione:</label>
                    <textarea id="descrizione" name="descrizione" required rows="4"
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444;
                           background:#2c2c2c; color:#fff; font-size:15px; resize:vertical;"><?= htmlspecialchars($old['descrizione']) ?></textarea>

                    <label for="prezzo" style="font-weight:bold; color:#ffeb00;">Prezzo (€):</label>
                    <input type="number" step="0.01" id="prezzo" name="prezzo" required
                        value="<?= htmlspecialchars($old['prezzo']) ?>"
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444;
                           background:#2c2c2c; color:#fff; font-size:15px;" />

                    <label for="bonus" style="font-weight:bold; color:#ffeb00;">Punti bonus:</label>
                    <input type="number" id="bonus" name="bonus" required
                        value="<?= htmlspecialchars($old['bonus']) ?>"
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444;
                           background:#2c2c2c; color:#fff; font-size:15px;" />

                    <label for="immagine" style="font-weight:bold; color:#ffeb00;">Immagine del prodotto:</label>
                    <input type="file" id="immagine" name="immagine" accept="image/*" required
                        style="width:100%; padding:8px; border-radius:6px; background:#1c1c1c;
                           color:#fff; border:1px solid #444;" />

                    <?php if (isset($_SESSION['errore_msg'])): ?>
                        <p style="color:red; font-weight:bold; text-align:center;">
                            <?= $_SESSION['errore_msg'] ?>
                        </p>
                        <?php unset($_SESSION['errore_msg']); ?>
                    <?php endif; ?>

                    <div style="text-align:center; margin-top:25px;">
                        <button type="submit"
                            style="background-color:#32CD32; color:white; border:none; padding:10px 22px;
                               border-radius:6px; cursor:pointer; font-size:15px; font-weight:bold;
                               margin-right:8px;">
                            Aggiungi Prodotto
                        </button>
                        <a href="gestione.php"
                            style="background-color:#2c2c2c; color:#1E90FF; border:none; padding:10px 22px;
                               border-radius:6px; font-size:15px; font-weight:bold; text-decoration:none;">
                            Annulla
                        </a>
                    </div>
                </div>
            </form>
            
        </div>

        <?php unset($_SESSION['old_data']); ?>

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
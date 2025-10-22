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
    <?php
    if (!isset($_SESSION['ruolo']) || $_SESSION['ruolo'] != 'amministratore') {
        header("Location: homepage.php");
        exit();
    }

    // leggo i dati salvati dal backend (se ci sono)
    $old = $_SESSION['old_data'] ?? [
        'domanda' => '',
        'risposta' => '',
        'categoria' => ''
    ];
    ?>
    <?php

    // Se esistono dati precedenti in sessione (es. dopo errore di validazione)
    $old = $_SESSION['old_data'] ?? [];

    // Inizializza i campi mancanti per evitare warning
    $old['domanda'] = $old['domanda'] ?? '';
    $old['risposta'] = $old['risposta'] ?? '';
    $old['categoria'] = $old['categoria'] ?? '';
    ?>
    <div class="content user-profile">
        <div class="profile-card" style="max-width:700px; margin:auto; padding:40px;">
            <h2 class="profile-title" style="text-align:center; color:#ffeb00; margin-bottom:25px;">Aggiungi una nuova FAQ</h2>

            <form action="risorse/PHP/amministratore/aggiungi_faq.php" method="post" class="profile-form">
                <div class="profile-details" style="display:flex; flex-direction:column; gap:14px;">

                    <label for="domanda" style="font-weight:bold; color:#ffeb00;">Domanda:</label>
                    <input type="text" id="domanda" name="domanda" required
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2c2c2c; color:#fff; font-size:15px;"
                        value="<?= $old['domanda'] ?>" />

                    <label for="risposta" style="font-weight:bold; color:#ffeb00;">Risposta:</label>
                    <textarea id="risposta" name="risposta" rows="4" required
                        style="width:100%; padding:10px; border-radius:6px; border:1px solid #444; background:#2c2c2c; color:#fff; font-size:15px; resize:vertical;">
                        <?= htmlspecialchars($old['risposta']) ?></textarea>

                    <label style="font-weight:bold; color:#ffeb00;">Categoria:</label>
                    <div style="display:flex; flex-direction:column; gap:6px; margin-left:10px;">
                        <label style="color:#ddd;">
                            <input type="radio" name="categoria" value="Pagamenti"
                                <?= ($old['categoria'] ?? '') === 'Pagamenti' ? 'checked' : '' ?> /> Pagamenti
                        </label>
                        <label style="color:#ddd;">
                            <input type="radio" name="categoria" value="Ordini"
                                <?= ($old['categoria'] ?? '') === 'Ordini' ? 'checked' : '' ?> /> Ordini
                        </label>
                        <label style="color:#ddd;">
                            <input type="radio" name="categoria" value="Prodotti"
                                <?= ($old['categoria'] ?? '') === 'Prodotti' ? 'checked' : '' ?> /> Prodotti
                        </label>
                        <label style="color:#ddd;">
                            <input type="radio" name="categoria" value="Bonus"
                                <?= ($old['categoria'] ?? '') === 'Bonus' ? 'checked' : '' ?> /> Bonus
                        </label>
                        <label style="color:#ddd;">
                            <input type="radio" name="categoria" value="Registrazione"
                                <?= ($old['categoria'] ?? '') === 'Registrazione' ? 'checked' : '' ?> /> Registrazione
                        </label>
                    </div>

                    <div style="text-align:center; margin-top:25px;">
                        <button type="submit"
                            style="background-color:#32CD32; color:white; border:none; padding:10px 22px; border-radius:6px; cursor:pointer; font-size:15px; font-weight:bold; margin-right:8px;">
                            Aggiungi FAQ
                        </button>
                        <a href="gestione_contenuti_admin.php"
                            style="background-color:#2c2c2c; color:#1E90FF; border:none; padding:10px 22px; border-radius:6px; font-size:15px; font-weight:bold; text-decoration:none;">
                            Annulla
                        </a>
                    </div>
                </div>
            </form>

            <?php
            // Messaggi di feedback
            if (isset($_SESSION['successo_msg'])) {
                echo "<p style='color:#32CD32; text-align:center; margin-top:20px; font-weight:bold;'>"
                    . htmlspecialchars($_SESSION['successo_msg']) . "</p>";
                unset($_SESSION['successo_msg']);
            } elseif (isset($_SESSION['errore_msg'])) {
                echo "<p style='color:#ff4040; text-align:center; margin-top:20px; font-weight:bold;'>"
                    . htmlspecialchars($_SESSION['errore_msg']) . "</p>";
                unset($_SESSION['errore_msg']);
            }
            unset($_SESSION['old_data']);
            ?>

</body>

</html>
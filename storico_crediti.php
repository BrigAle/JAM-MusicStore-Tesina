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


    <div class="content">
        <h2 style="text-align: left;">Storico Crediti</h2>
        <?php
        $xmlStoricoCrediti = simplexml_load_file("risorse/XML/storico_crediti.xml");

        if ($xmlStoricoCrediti) {
            $totale = 0;
            $numero = 0;
            echo "
        <table border='1' cellpadding='6'>
            <tr>
                <th>N°</th> 
                <th>Data</th>
                <th>Crediti registrati</th>
                <th>Totale aggiornato</th>
            </tr>";

            $idUtente = $_SESSION['id_utente'] ?? null;

            foreach ($xmlStoricoCrediti->record as $record) {
                if ((string)$record->id_utente === (string)$idUtente) {
                    $totale = (float)$record->crediti;
                    $data = (string)$record->data;
                    $numero++;
                    echo "
                <tr>
                    <td style='text-align:center;'>{$numero}</td>
                    <td style='text-align:center;'>{$data}</td>
                    <td style='text-align:center;'>{$record->crediti}</td>
                    <td style='text-align:center; font-weight:bold; color:#ffcc00;'>
                        {$totale}
                    </td>
                </tr>";
                }
            }

            echo "</table>";
        } else {
            echo "<p>Nessun dato di storico crediti disponibile.</p>";
        }
        ?>
        <div style="text-align:center; margin-top:20px;">
            <a href="profilo.php" class="profile-btn">Torna indietro</a>
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
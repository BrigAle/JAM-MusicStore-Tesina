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
        <?php
        if (!isset($_GET['id_utente'])) {
            die("ID utente non specificato.");
        }
        $idUtente = (int)$_GET['id_utente'];

        $xmlFile = "risorse/XML/storico_acquisti.xml";
        if (!file_exists($xmlFile)) {
            die("File storico non trovato.");
        }

        $xml = simplexml_load_file($xmlFile);
        $storiciUtente = [];
        foreach ($xml->storico as $s) {
            if ((int)$s->id_utente === $idUtente) {
                $storiciUtente[] = $s;
            }
        }

        echo "<div class='content'>";
        echo "<h2>Storico Acquisti Utente ID: $idUtente</h2>";

        if (count($storiciUtente) === 0) {
            echo "<p>Nessun acquisto registrato per questo utente.</p>";
        } else {
            foreach ($storiciUtente as $storico) {
                $idStorico = (int)$storico['id'];
                $data = (string)$storico->data;
                $prezzoTotale = (string)$storico->prezzo_totale_ordine;

                echo "<div style='margin:15px 0; border:1px solid #aaa; padding:10px;'>";
                echo "<h3>Ordine #$idStorico – Data: $data</h3>";
                echo "<table border='1' cellpadding='6' style='width:100%;'>
                <tr>
                    <th>ID Prodotto</th>
                    <th>Quantità</th>
                    <th>Prezzo Unitario (€)</th>
                    <th>Prezzo Totale (€)</th>
                </tr>";
                foreach ($storico->prodotti->prodotto as $p) {
                    $idProdotto = (string)$p->id_prodotto;
                    $qta = (string)$p->quantita;
                    $prezzoU = (string)$p->prezzo_unitario;
                    $prezzoT = (string)$p->prezzo_totale;
                    echo "<tr>
                    <td>$idProdotto</td>
                    <td>$qta</td>
                    <td>$prezzoU</td>
                    <td>$prezzoT</td>
                </tr>";
                }
                echo "</table>";
                echo "<p style='text-align:right; font-weight:bold;'>Totale ordine: €$prezzoTotale</p>";
                echo "</div>";
            }
        }

        echo "<p><a href='gestione_utenti.php'>← Torna alla gestione utenti</a></p>";
        echo "</div>";
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
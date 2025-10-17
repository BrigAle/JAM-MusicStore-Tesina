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
        <h2 style="text-align: left;">Storico Acquisti</h2>

        <?php
        $xmlStorici = simplexml_load_file("risorse/XML/storico_acquisti.xml");
        $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
        $idUtente = $_SESSION['id_utente'] ?? null;

        if ($xmlStorici && $idUtente) {
            // Filtra solo gli ordini dell'utente loggato
            $ordiniUtente = [];
            foreach ($xmlStorici->storico as $storico) {
                if ((string)$storico->id_utente === (string)$idUtente) {
                    $ordiniUtente[] = $storico;
                }
            }

            if (count($ordiniUtente) > 0) {
                echo "
          <table border='1' cellpadding='6'>
            <tr>
              <th>ID Ordine</th>
              <th>Data</th>
              <th>Metodo di Pagamento</th>
              <th>Prodotti</th>
              <th>Totale (€)</th>
            </tr>";

                foreach ($ordiniUtente as $ordine) {
                    $idOrdine = (string)$ordine['id'];
                    $data = (string)$ordine->data;
                    $metodo = isset($ordine->metodo_pagamento) ? (string)$ordine->metodo_pagamento : '-';
                    $totale = (float)$ordine->prezzo_totale_ordine;

                    // Sezione prodotti
                    $listaProdotti = "";

                    foreach ($ordine->prodotti->prodotto as $p) {
                        $idProd = (string)$p->id_prodotto;
                        $quantita = (int)$p->quantita;
                        $prezzoUnitario = (float)$p->prezzo_unitario;

                        // Recupera info prodotto
                        $nome = "Prodotto non trovato";
                        $immagine = "risorse/IMG/prodotti/placeholder.jpg";
                        foreach ($xmlProdotti->prodotto as $item) {
                            if ((string)$item['id'] === $idProd) {
                                $nome = (string)$item->nome;
                                $immagine = "risorse/IMG/prodotti/" . (string)$item->immagine;
                                break;
                            }
                        }

                        $listaProdotti .= "
                    <div style='display:flex; align-items:center; margin-bottom:6px;'>
                      <img src='{$immagine}' alt='{$nome}' 
                           style='width:60px; height:60px; object-fit:contain; border-radius:6px; background:#111; margin-right:10px;'>
                      <div style='text-align:left;'>
                        <strong>{$nome}</strong><br>
                        Quantità: {$quantita}<br>
                        Prezzo unitario: €" . number_format($prezzoUnitario, 2, ',', '.') . "
                      </div>
                    </div>";
                    }

                    echo "
              <tr>
                <td style='text-align:center;'>{$idOrdine}</td>
                <td style='text-align:center;'>{$data}</td>
                <td style='text-align:center;'>{$metodo}</td>
                <td>{$listaProdotti}</td>
                <td style='text-align:center; font-weight:bold; color:#ffcc00;'>
                  €" . number_format($totale, 2, ',', '.') . "
                </td>
              </tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Non hai ancora effettuato acquisti.</p>";
            }
        } else {
            echo "<p>Errore nel caricamento dello storico acquisti.</p>";
        }
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
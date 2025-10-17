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
        <h2 style="text-align: left;">Gestione Prodotti</h2>

        <?php
        // Carica file XML prodotti e recensioni
        $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
        $xmlRecensioni = simplexml_load_file("risorse/XML/recensioni.xml");

        if ($xmlProdotti && count($xmlProdotti->prodotto) > 0) {
            echo "
      <table border='1' cellpadding='6'>
        <tr>
          <th>ID</th>
          <th>Immagine</th>
          <th>Nome</th>
          <th>Categoria</th>
          <th>Descrizione</th>
          <th>Prezzo (€)</th>
          <th>Bonus</th>
          <th>Data Inserimento</th>
          <th>Valutazione Media</th>
          <th>Azioni</th>
        </tr>";

            // Cicla i prodotti
            foreach ($xmlProdotti->prodotto as $prodotto) {
                $id = (string)$prodotto['id'];
                $nome = (string)$prodotto->nome;
                $categoria = (string)$prodotto->categoria;
                $descrizione = (string)$prodotto->descrizione;
                $prezzo = (float)$prodotto->prezzo;
                $bonus = (float)$prodotto->bonus;
                $data = (string)$prodotto->data_inserimento;
                $immagine = "risorse/IMG/prodotti/" . (string)$prodotto->immagine;

                // Calcolo valutazione media
                $valutazioneTotale = 0;
                $countValutazioni = 0;

                foreach ($xmlRecensioni->recensione as $recensione) {
                    if ((string)$recensione->id_prodotto === $id) {
                        $valutazioneTotale += (float)$recensione->valutazione;
                        $countValutazioni++;
                    }
                }
                $valutazioneMedia = $countValutazioni > 0 ? round($valutazioneTotale / $countValutazioni, 1) : 0;

                // Azioni gestore
                $azioni = "
              <a href='recensioni.php?id_prodotto={$id}'>Recensioni</a> |
              <a href='gestore_modifica_prodotto.php?id_prodotto={$id}'>Modifica</a> |
              <a href='risorse/PHP/gestore/elimina_prodotto.php?id_prodotto={$id}'
                 onclick=\"return confirm('Sei sicuro di voler eliminare il prodotto &quot;{$nome}&quot;?');\">
                 Elimina
              </a>
          ";

                echo "
          <tr>
            <td>{$id}</td>
            <td style='text-align:center;'>
              <img src='{$immagine}' alt='{$nome}' style='width:70px; height:70px; object-fit:contain; border-radius:6px; background:#111;'>
            </td>
            <td>{$nome}</td>
            <td>{$categoria}</td>
            <td style='max-width:320px; text-align:left;'>{$descrizione}</td>
            <td>" . number_format($prezzo, 2, ',', '.') . "</td>
            <td>" . ($bonus > 0 ? number_format($bonus, 2, ',', '.') : '-') . "</td>
            <td>{$data}</td>
            <td style='text-align:center;'>{$valutazioneMedia}
              <img src='risorse/IMG/stella.png' alt='★' style='    margin-bottom: 3px;width: 25px;height: 25px; vertical-align: middle;'>
            </td>
            <td>{$azioni}</td>
          </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Nessun prodotto trovato nel catalogo.</p>";
        }
        ?>
        <a class="aggiungi-faq" href="aggiungi_prodotto.php"><h2 style="color:#1E90FF">Aggiungi Prodotti</h2></a>
        <?php
        if (isset($_SESSION['aggiungi_prodotto_successo'])) {
            if ($_SESSION['aggiungi_prodotto_successo']) {
                echo "<p style='color: green;'> Prodotto aggiunto con successo!</p>";
            } else {
                echo "<p style='color: red;'> Errore durante l'aggiunta del prodotto. Riprova.</p>";
            }
            unset($_SESSION['aggiungi_prodotto_successo']);
        }
        if (isset($_SESSION['elimina_prodotto_successo'])) {
            if ($_SESSION['elimina_prodotto_successo']) {
                echo "<p style='color: green;'> Prodotto eliminato con successo!</p>";
            } else {
                echo "<p style='color: red;'> Errore durante l'eliminazione del prodotto. Riprova.</p>";
            }
            unset($_SESSION['elimina_prodotto_successo']);
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
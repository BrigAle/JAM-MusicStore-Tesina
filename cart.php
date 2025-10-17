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
      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
      <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
      <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
    </div>

  </div>



  <div class="content">
    <h2 style="text-align:left;">Il Mio Carrello</h2>

    <?php
    $xmlCarrelli = simplexml_load_file("risorse/XML/carrelli.xml");
    $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
    $xmlSconti   = @simplexml_load_file("risorse/XML/sconti.xml");

    $oggi = date('Y-m-d');
    $id_utente = $_SESSION['id_utente'] ?? null;
    $carrelloUtente = null;

    // Trova carrello dell’utente
    if ($xmlCarrelli && $id_utente) {
      foreach ($xmlCarrelli->carrello as $carrello) {
        if ((string)$carrello->id_utente === (string)$id_utente) {
          $carrelloUtente = $carrello;
          break;
        }
      }
    }

    if ($carrelloUtente && count($carrelloUtente->prodotti->prodotto) > 0) {
      echo "
    <table border='1' cellpadding='6'>
      <tr>
        <th>ID</th>
        <th>Immagine</th>
        <th>Nome</th>
        <th>Quantità</th>
        <th>Prezzo Unitario (€)</th>
        <th>Sconto (%)</th>
        <th>Totale (€)</th>
        <th>Azioni</th>
      </tr>";

      $totaleCarrelloAggiornato = 0;

      foreach ($carrelloUtente->prodotti->prodotto as $prodottoCarrello) {
        $idProd = (string)$prodottoCarrello->id_prodotto;
        $quantita = (int)$prodottoCarrello->quantita;
        $prezzoUnitario = (float)$prodottoCarrello->prezzo_unitario;

        // 🔹 Cerca info prodotto
        $nome = "Prodotto non trovato";
        $immagine = "risorse/IMG/prodotti/placeholder.jpg";
        foreach ($xmlProdotti->prodotto as $p) {
          if ((string)$p['id'] === $idProd) {
            $nome = (string)$p->nome;
            $immagine = "risorse/IMG/prodotti/" . (string)$p->immagine;
            break;
          }
        }

        // 🔹 Sconto più alto attivo
        $percentualeScontoMax = 0;
        if ($xmlSconti && count($xmlSconti->sconto) > 0) {
          foreach ($xmlSconti->sconto as $sconto) {
            foreach ($sconto->id_prodotto as $idScontato) {
              if ((string)$idScontato === $idProd) {
                $dataInizio = (string)$sconto->data_inizio;
                $dataFine = (string)$sconto->data_fine;
                if ($oggi >= $dataInizio && $oggi <= $dataFine) {
                  $perc = (float)$sconto->percentuale;
                  if ($perc > $percentualeScontoMax) $percentualeScontoMax = $perc;
                }
              }
            }
          }
        }

        // 🔹 Prezzo finale scontato
        $prezzoUnitarioScontato = $prezzoUnitario;
        if ($percentualeScontoMax > 0) {
          $prezzoUnitarioScontato -= ($prezzoUnitario * $percentualeScontoMax / 100);
        }
        $prezzoTotaleScontato = $prezzoUnitarioScontato * $quantita;
        $totaleCarrelloAggiornato += $prezzoTotaleScontato;

        echo "
      <tr>
        <td>{$idProd}</td>
        <td style='text-align:center;'>
          <img src='{$immagine}' alt='{$nome}' style='width:70px; height:70px; object-fit:contain; border-radius:6px; background:#111;'>
        </td>
        <td>{$nome}</td>
        <td style='text-align:center;'>{$quantita}</td>
        <td style='text-align:center;'>
          " . ($percentualeScontoMax > 0
          ? "<span style='text-decoration:line-through;color:#888;'>" . number_format($prezzoUnitario, 2, ',', '.') . "</span><br>
               <span style='color:#ffcc00;font-weight:bold;'>" . number_format($prezzoUnitarioScontato, 2, ',', '.') . "</span>"
          : number_format($prezzoUnitario, 2, ',', '.')) . "
        </td>
        <td style='text-align:center;'>" . ($percentualeScontoMax > 0 ? number_format($percentualeScontoMax, 1, ',', '.') . '%' : '-') . "</td>
        <td style='text-align:center;font-weight:bold;'>" . number_format($prezzoTotaleScontato, 2, ',', '.') . "</td>
        <td style='text-align:center;'>
          <form action='risorse/PHP/rimuovi_dal_carrello.php' method='post' onsubmit='return confirm(\"Rimuovere il prodotto?\");'>
            <input type='hidden' name='id_prodotto' value='{$idProd}'>
            <button type='submit' style='padding:6px 12px; background-color:#FF6347; color:white; border:none; border-radius:4px; cursor:pointer;'>Rimuovi</button>
          </form>
        </td>
      </tr>";
      }

      echo "
      <tr style='background:#000; font-weight:bold; color:white;'>
        <td colspan='6' style='text-align:right;'>Totale Carrello (con sconti):</td>
        <td style='text-align:center; color:#ffcc00;'>" . number_format($totaleCarrelloAggiornato, 2, ',', '.') . " €</td>
      </tr>
    </table>
    <br>
    <form action='procedi_acquisto.php' method='post' style='text-align:center;'>
      <input type='hidden' name='id_utente' value='{$id_utente}'>
      <input type='hidden' name='totale_carrello' value='" . number_format($totaleCarrelloAggiornato, 2, '.', '') . "'>
      <button type='submit' style='padding:10px 20px; background-color:#1E90FF; color:white; border:none; border-radius:4px; cursor:pointer; font-size:16px;'>
        Procedi all'acquisto 🛒
      </button>
    </form>";
    } else {
      echo "<p>Il tuo carrello è vuoto. <a href='catalogo.php'>Torna al catalogo</a></p>";
    }
    ?>
    <?php 
    if(isset($_SESSION['successo_msg'])) {
      echo "<p style='color:green; font-weight:bold;'>" . $_SESSION['successo_msg'] . "</p>";
      unset($_SESSION['successo_msg']);
    }
    else if(isset($_SESSION['errore_msg'])) {
      echo "<p style='color:red; font-weight:bold;'>" . $_SESSION['errore_msg'] . "</p>";
      unset($_SESSION['errore_msg']);
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
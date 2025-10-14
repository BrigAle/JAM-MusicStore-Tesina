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
      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
      <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
      <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
    </div>

  </div>



  <div class="content">
    <div class="content">
      <h2 style="text-align: left;">Il Mio Carrello</h2>

      <?php
      // Carica XML del carrello e prodotti
      $xmlCarrelli = simplexml_load_file("risorse/XML/carrelli.xml");
      $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");

      $idUtente = $_SESSION['id_utente'] ?? null;
      $carrelloUtente = null;

      // Trova il carrello dell'utente loggato
      if ($xmlCarrelli && $idUtente) {
        foreach ($xmlCarrelli->carrello as $carrello) {
          if ((string)$carrello->id_utente === (string)$idUtente) {
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
          <th>Totale (€)</th>
          <th>Azioni</th>
        </tr>";

        // Cicla i prodotti nel carrello
        foreach ($carrelloUtente->prodotti->prodotto as $prodottoCarrello) {
          $idProd = (string)$prodottoCarrello->id_prodotto;
          $quantita = (int)$prodottoCarrello->quantita;
          $prezzoUnitario = (float)$prodottoCarrello->prezzo_unitario;
          $prezzoTotale = (float)$prodottoCarrello->prezzo_totale;
          // Trova il nome e l'immagine in prodotti.xml
          $nome = "Prodotto non trovato";
          $immagine = "risorse/IMG/prodotti/placeholder.jpg";

          foreach ($xmlProdotti->prodotto as $p) {
            if ((string)$p['id'] === $idProd) {
              $nome = (string)$p->nome;
              $immagine = "risorse/IMG/prodotti/" . (string)$p->immagine;
              break;
            }
          }

          echo "
          <tr>
            <td>{$idProd}</td>
            <td style='text-align:center;'>
              <img src='{$immagine}' alt='{$nome}' style='width:70px; height:70px; object-fit:contain; border-radius:6px; background:#111;'>
            </td>
            <td>{$nome}</td>
            <td style='text-align:center;'>{$quantita}</td>
            <td style='text-align:center;'>" . number_format($prezzoUnitario, 2, ',', '.') . "</td>
            <td style='text-align:center; font-weight:bold;'>" . number_format($prezzoTotale, 2, ',', '.') . "</td>
            <td style='text-align:center;'>
              <form action='risorse/PHP/rimuovi_dal_carrello.php' method='post' onsubmit='return confirm(\"Sei sicuro di voler rimuovere questo prodotto dal carrello?\");'>
                <input type='hidden' name='id_prodotto' value='{$idProd}'>
                <button type='submit' style='padding:6px 12px; background-color:#FF6347; color:white; border:none; border-radius:4px; cursor:pointer;'>Rimuovi</button>
                <input type='number' name='quantita' value='{$quantita}' min='1' style='width:60px; text-align:center;'>
                <button type='submit' formaction='risorse/PHP/aggiorna_quantita_carrello.php' style='padding:6px 12px; background-color:#1E90FF; color:white; border:none; border-radius:4px; cursor:pointer;'>Aggiorna</button>
              </form>
            </td>
          </tr>";
        }

        $totaleCarrello = (float)$carrelloUtente->prezzo_totale_carrello;
        echo "
        <tr style='background:#000; font-weight:bold;'>
          <td colspan='5' style='text-align:right;'>Totale Carrello:</td>
          <td style='text-align:center;'>" . number_format($totaleCarrello, 2, ',', '.') . " €</td>
        </tr>
      </table>
      <br>
      <form action='risorse/PHP/procedi_acquisto.php' method='post' style='text-align:center;'>
        <input type='hidden' name='id_utente' value='{$idUtente}'>
        <button type='submit' style='padding:10px 20px; background-color:#1E90FF; color:white; border:none; border-radius:4px; cursor:pointer; font-size:16px;'>
          Procedi all'acquisto 🛒
        </button>
      </form>
      ";
      } else {
        echo "<p>Il tuo carrello è vuoto.</p>";
      }
      ?>
      <?php
      // Mostra messaggi di errore o successo
      if (isset($_SESSION['errore'])) {
        echo "<p class='error_message' style='color:red;'>❌ " . $_SESSION['errore'] . "</p>";
        unset($_SESSION['errore']);
      }
      if (isset($_SESSION['successo'])) {
        echo "<p class='success_message' style='color:green;'>✅ " . $_SESSION['successo'] . "</p>";
        unset($_SESSION['successo']);
      }
      if (isset($_SESSION['successo_rimozione'])) {
        echo "<p class='success_message' style='color:green;'>✅ " . $_SESSION['successo_rimozione'] . "</p>";
        unset($_SESSION['successo_rimozione']);
      }
      ?>
    </div>
  </div>
  <div class="prodotti">
    <!-- Qui andranno i prodotti aggiunti al carrello -->

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
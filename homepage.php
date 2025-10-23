<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">

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
      <a href="homepage.php"><img src="risorse/IMG/JAM_logo%20(2).png" alt="JAM Music Store" /></a>
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
      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'amministratore') : ?>
        <a href="amministrazione.php">admin</a>
      <?php endif; ?>

      <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'gestore') :
        echo '<a href="gestione.php">gestore</a>';
      endif; ?>

      <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true') : ?>
        <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo" /></a>
      <?php endif; ?>

      <a href="catalogo.php">Catalogo</a>
      <a href="homepage.php"><img src="risorse/IMG/home.png" alt="Home" /></a>
      <a href="cart.php"><img src="risorse/IMG/cart.png" alt="Carrello" /></a>

      <?php if (!isset($_SESSION['username'])) {
        echo '<a href="login.php">Accedi</a>';
      } ?>
      <?php if (isset($_SESSION['username'])) {
        echo '<a href="risorse/PHP/logout.php">Esci</a>';
      } ?>
    </div>
  </div>

  <div class="content" style="text-align:center; padding:40px 20px; color:white; background-color:#111;">
    <div style="
      max-width:800px;
      margin:auto;
      background-color:#1b1b1b;
      border:1px solid #2e2e2e;
      border-radius:10px;
      padding:30px; margin-top:60px;
      box-shadow:0 0 15px rgba(255,255,0,0.1);">
      <h1 style="color:#ffeb00; margin-bottom:15px;">Benvenuto su JAM Music Store</h1>
      <p style="font-size:18px; line-height:1.6; margin-bottom:70px;">
        Il tuo negozio di musica online, dove puoi trovare strumenti, accessori e tanto altro!
      </p>

      <p style="text-align:left; font-weight:bold; color:#ccc;">Con JAM Music Store puoi:</p>
      <ol style="text-align:left; list-style-type:disc; margin-left:20px; color:#ccc; font-size:17px;">
        <li>Consultare il catalogo prodotti</li>
        <li>Aggiungere articoli al carrello</li>
        <li>Accumulare punti bonus per ogni acquisto</li>
        <li>Usare i punti per ottenere sconti speciali</li>
      </ol>
    </div>

    <h2 style="margin:20px 0 20px;">Prodotti recenti</h2>

    <div class="box_prodotto">
      <?php
      $xmlProdotti = simplexml_load_file('risorse/XML/prodotti.xml');
      $xmlRecensioni = simplexml_load_file('risorse/XML/recensioni.xml');
      $count = 0;

      foreach ($xmlProdotti->prodotto as $prodotto) :
        if ($count >= 4) {
          break;
        }

        $nome = htmlspecialchars((string)$prodotto->nome, ENT_QUOTES, 'UTF-8');
        $descrizione = htmlspecialchars((string)$prodotto->descrizione, ENT_QUOTES, 'UTF-8');
        $prezzo = htmlspecialchars((string)$prodotto->prezzo, ENT_QUOTES, 'UTF-8');
        $bonus = htmlspecialchars((string)$prodotto->bonus, ENT_QUOTES, 'UTF-8');
        $datainserimento = htmlspecialchars((string)$prodotto->data_inserimento, ENT_QUOTES, 'UTF-8');
        $immagine = 'risorse/IMG/prodotti/' . htmlspecialchars((string)$prodotto->immagine, ENT_QUOTES, 'UTF-8');
        $id = (string)$prodotto['id'];

        // Calcolo valutazione media
        $valutazioneTotale = 0.0;
        $countValutazioni = 0;
        foreach ($xmlRecensioni->recensione as $recensione) :
          if ((string)$recensione->id_prodotto === (string)$id) {
            $valutazioneTotale += (float)$recensione->valutazione;
            $countValutazioni++;
          }
        endforeach;
        $valutazioneMedia = ($countValutazioni > 0) ? ($valutazioneTotale / $countValutazioni) : 0.0;

        // ID DOM locale sicuro
        $domId = 'quantitaP' . $count;

        // ID reale per il form
        $id_value = htmlspecialchars(trim($id), ENT_QUOTES, 'UTF-8');

        echo '<div class="contenuto_prodotto">';
        echo '<div class="immagine_box">';
        echo '<img src="' . $immagine . '" alt="' . $nome . '" />';
        echo '</div>';
        echo '<div class="dettagli_box">';
        echo '<h3>' . $nome . '</h3>';
        echo '<p>' . $descrizione . '</p>';
        echo '<p>Prezzo non scontato: €' . $prezzo . '</p>';
        if ((float)$bonus > 0) {
          echo '<p>Bonus: ' . $bonus . ' punti</p>';
        }
        echo '<p>Data di inserimento: ' . $datainserimento . '</p>';
        echo '<p class="valutazione">Valutazione: ' . number_format((float)$valutazioneMedia, 1, ',', '') .
          ' <img src="risorse/IMG/stella.png" alt="Stella valutazione" /></p>';

      

        echo '</div></div>';
        $count++;
      endforeach;
      ?>
    </div>

    <div style="text-align:center; margin:30px 0;">
      <h2 style="margin:0; font-weight:bold;">
        <a href="catalogo.php"
          style="display:inline-block; background-color:#2c2c2c; color:#ffeb00; text-decoration:none; padding:12px 60px; border-radius:4px;">
          Vai al catalogo completo
        </a>
      </h2>
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
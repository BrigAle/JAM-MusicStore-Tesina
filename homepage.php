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
    <div style="padding: 20px; max-width: 800px;">
      <h1>Benvenuto su JAM Music Store</h1>
      <p>Il tuo negozio di musica online, dove puoi trovare strumenti, accessori e tanto altro!</p>
      <p>Con JAM Music Store puoi:</p>
      <ol>
        <li>Consultare il catalogo prodotti</li>
        <li>Aggiungere articoli al carrello</li>
        <li>Accumulare punti bonus per ogni acquisto</li>
        <li>Usare i punti per ottenere sconti speciali</li>
      </ol>
      <p>Registrati oggi stesso e inizia a esplorare il mondo della musica con noi!</p>
    </div>
    <h2>Prodotti recenti</h2>
    <div class="box_prodotto">
      <?php
      $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
      $xmlRecensioni = simplexml_load_file("risorse/XML/recensioni.xml");
      $count = 0;
      foreach ($xmlProdotti->prodotto as $prodotto):
        if ($count >= 4) break; // Mostra solo i primi 4 prodotti
        $nome = $prodotto->nome;
        $descrizione = $prodotto->descrizione;
        $prezzo = $prodotto->prezzo;
        $bonus = $prodotto->bonus;
        $datainserimento = $prodotto->data_inserimento;
        $immagine = "risorse/IMG/prodotti/" . $prodotto->immagine;
        $id = $prodotto['id']; // Ottieni l'ID del prodotto
        $valutazioneTotale = 0;
        $countValutazioni = 0; // Per evitare divisione per zero

        foreach ($xmlRecensioni->recensione as $recensione):
          if ((string)$recensione->id_prodotto == (string)$id) {
            $valutazioneTotale += (float)$recensione->valutazione;
            $countValutazioni++;     
          }
        endforeach;

        if ($countValutazioni > 0) {
          $valutazioneMedia = $valutazioneTotale / $countValutazioni;
        } else {
          $valutazioneMedia = 0;
        }
      ?>
        <div class="contenuto_prodotto">
          <div class="immagine_box">
            <img src="<?= $immagine ?>" alt="<?= $nome ?>" />
          </div>
          <div class="dettagli_box">
            <h3><?= $nome ?></h3>
            <p><?= $descrizione ?></p>
            <p>Prezzo: €<?= $prezzo ?></p>
            <?php if ($bonus > 0): ?>
              <p>Bonus: <?= $bonus ?> punti</p>
            <?php endif; ?>
            <p>Data di inserimento: <?= $datainserimento ?></p>
            <p class="valutazione">
              Valutazione: <?= $valutazioneMedia ?> 
              <img src="risorse/IMG/stella.png" alt="">
            </p>
            <!-- form carrello -->
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && $_SESSION['ruolo'] === 'cliente'): ?>
              <form action="carrello.php" method="post">
                <input type="hidden" name="id" value="<?= $id ?>" />
                <button type="submit">Aggiungi al carrello</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      <?php $count++;
      endforeach; ?>
    </div>
    <a href="catalogo.php">
      <h2>Vai al catalogo completo</h2>
    </a>
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
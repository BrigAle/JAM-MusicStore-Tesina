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

  <?php

  $id_prodotto = $_GET['id_prodotto'];
  $xmlRecensioni = simplexml_load_file('risorse/XML/recensioni.xml');
  $xmlRisposte = simplexml_load_file('risorse/XML/risposte.xml');
  $xmlProdotti = simplexml_load_file('risorse/XML/prodotti.xml');
  $recensioniProdotto = [];
  $risposteRecensioni = [];

  $nomeProdotto = "Prodotto non trovato";
  $immagineProdotto = "risorse/IMG/prodotti/default.png";


  foreach ($xmlRecensioni->recensione as $recensione) {
    if ($recensione->id_prodotto == $id_prodotto) {
      $recensioniProdotto[] = $recensione;
    }
  }
  if (empty($recensioniProdotto)) {
    echo "<div class=\"content\"><h2>Nessuna recensione trovata per questo prodotto.</h2></div>";
    exit;
  }

  foreach ($xmlRisposte->risposta as $risposta) {
    $risposteRecensioni[] = $risposta;
  }
  foreach ($xmlProdotti->prodotto as $prodotto) {
    if ($prodotto['id'] == $id_prodotto) {
      $nomeProdotto = (string)$prodotto->nome;
      $immagineProdotto = "risorse/IMG/prodotti/" . (string)$prodotto->immagine;
      break;
    }
  }

  ?>


  <div class="content" style="align-items: normal;">
    <h1>Recensioni del prodotto: <?= $nomeProdotto ?></h1>
    <div class="immagine_prodotto">
      <img src="<?= htmlspecialchars($immagineProdotto) ?>" alt="<?= htmlspecialchars($nomeProdotto) ?>" style="max-width:300px; max-height:200px;" />
    </div>
    <div class="box_prodotto" style="flex-direction: column;">
      <?php if (empty($recensioniProdotto)): ?>
        <p>Nessuna recensione trovata per questo prodotto.</p>
      <?php else: ?>
        <?php
        $id_utente = isset($_SESSION['id']) ? $_SESSION['id'] : null; // ID utente loggato o null se non loggato
        foreach ($recensioniProdotto as $recensione):
          $id_recensione = (int)$recensione['id'];
          $votoUtente = null;
          $id_utente_recensione = (int)$recensione->id_utente;

          // connessione al database per ricavare l'username dell'utente che ha scritto la recensione
          require_once 'risorse/PHP/connection.php';
          $connection = new mysqli($host, $user, $password, $db);
          $queryU = "SELECT username FROM utente WHERE id = $id_utente_recensione";
          $resultU = $connection->query($queryU);
          if ($resultU) {
            $rowU = $resultU->fetch_assoc();
            $username_recensione = $rowU['username'];
          }

          // ✅ controlla correttamente il nodo "voto_utenti"
          if (isset($recensione->voto_utenti)) {
            foreach ($recensione->voto_utenti->voto as $v) {
              if ((int)$v['id_utente'] === (int)$id_utente) {
                $votoUtente = (string)$v['tipo']; // "like" o "dislike"
                break;
              }
            }
          }
        ?>
          <div class="recensione">
            <h3>Recensione di <?= htmlspecialchars($username_recensione) ?></h3>
            <p class="valutazione">
              Valutazione: <?= $recensione->valutazione ?>
              <img src="risorse/IMG/stella.png" alt="">
            </p>
            <p><strong>Commento:</strong> <?= nl2br(htmlspecialchars($recensione->commento)) ?></p>

            <p><strong>Likes:</strong> <?= htmlspecialchars($recensione->voti_like) ?> |
              <strong>Dislikes:</strong> <?= htmlspecialchars($recensione->voti_dislike) ?>
            </p>

            <p><strong>Data:</strong> <?= htmlspecialchars($recensione->data) ?></p>
            <?php if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true'): ?>
              <p style="font-size: smaller;"><em>Devi essere loggato come cliente, gestore o amministratore per votare o rispondere a questa recensione.</em></p>
            <?php endif; ?>

            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && (($_SESSION['ruolo'] == 'cliente') ||
              ($_SESSION['ruolo'] == 'gestore') || ($_SESSION['ruolo'] == 'amministratore'))): ?>

              <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <div class="voti">

                  <!-- Pulsante Like -->
                  <form action="risorse/PHP/aggiorna_voto.php" method="GET" style="display:inline;">
                    <input type="hidden" name="id_recensione" value="<?= $id_recensione ?>"> <!-- ✅ ora funziona -->
                    <input type="hidden" name="id_prodotto" value="<?= $_GET['id_prodotto'] ?>">
                    <button type="submit" name="voto" value="like"
                      class="<?= $votoUtente === 'like' ? 'voto-attivo' : 'voto-passivo' ?>">
                      <img src="risorse/IMG/thumbs_up.png" alt="Voto utile" width="25" height="25">
                    </button>
                  </form>

                  <!-- Pulsante Dislike -->
                  <form action="risorse/PHP/aggiorna_voto.php" method="GET" style="display:inline;">
                    <input type="hidden" name="id_recensione" value="<?= $id_recensione ?>">
                    <input type="hidden" name="id_prodotto" value="<?= $_GET['id_prodotto'] ?>">
                    <button type="submit" name="voto" value="dislike"
                      class="<?= $votoUtente === 'dislike' ? 'voto-attivo' : 'voto-passivo' ?>">
                      <img src="risorse/IMG/thumbs_down.png" alt="Voto inutile" width="25" height="25">
                    </button>
                  </form>
                </div>

              <?php endif; ?>
              <form action="risposta_recensione.php" method="POST">
                <input type="hidden" name="id_prodotto" value="<?= $_GET['id_prodotto'] ?>" />
                <input type="hidden" name="id_utente" value="<?= $_SESSION['id'] ?>" />
                <input type="hidden" name="id_recensione" value="<?= $recensione['id'] ?>" />
                <button type="submit">Rispondi</button>
              </form>
            <?php endif; ?>

            <?php
            // se utente loggato e' un gestore, mostra pulsante segnala
            if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && $_SESSION['ruolo'] == 'gestore'):
              echo '<form action="segnala_recensione_risposta.php" method="POST">
                      <input type="hidden" name="id_prodotto" value="' . htmlspecialchars($_GET['id_prodotto']) . '" />
                      <input type="hidden" name="id_utente_recensione" value="' . htmlspecialchars((int)$recensione->id_utente) . '" />
                      <input type="hidden" name="id_recensione" value="' . htmlspecialchars((int)$recensione['id']) . '" />
                      <button type="submit">Segnala</button>
                    </form>';
            endif;

            ?>

            <!-- RISPOSTE -->
            <?php
            $risposteTrovate = false;
            foreach ($risposteRecensioni as $risposta):
              if ((int)$risposta->id_recensione == (int)$recensione['id']):
                $risposteTrovate = true;
            ?>
                <?php
                // connessione al database per ricavare l'username dell'utente che ha scritto la risposta
                $id_utente_risposta = (int)$risposta->id_utente;
                $queryU = "SELECT username,ruolo FROM utente WHERE id = $id_utente_risposta";
                $resultU = $connection->query($queryU);
                if ($resultU) {
                  $rowU = $resultU->fetch_assoc();
                  $username_risposta = $rowU['username'];
                  $ruolo_risposta = $rowU['ruolo'];
                }
                ?>
                <div class="risposta">

                  <?php
                  if (isset($username_risposta)) {
                    $ruoloLower = strtolower($ruolo_risposta);
                    $ruoloTesto = '';

                    if ($ruoloLower === 'amministratore') {
                      $ruoloTesto = ' <span style="color:red;">(Amministratore)</span>';
                    } elseif ($ruoloLower === 'gestore') {
                      $ruoloTesto = ' <span style="color:orange;">(Gestore)</span>';
                    }

                    echo '<h4>Risposta di ' . htmlspecialchars($username_risposta) . $ruoloTesto . ':</h4>';
                  }
                  ?>

                  <p><?= nl2br(htmlspecialchars($risposta->commento)) ?></p>
                  <p style="text-align: end;"><strong>Data:</strong> <?= htmlspecialchars($risposta->data) ?><strong> Ora:</strong> <?= htmlspecialchars($risposta->ora) ?></p>
                  <?php
                  // se utente loggato e' un gestore, mostra pulsante segnala
                  if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && $_SESSION['ruolo'] == 'gestore'):
                    echo '<form action="segnala_recensione_risposta.php" method="POST">
                            <input type="hidden" name="id_prodotto" value="' . htmlspecialchars($_GET['id_prodotto']) . '" />
                            <input type="hidden" name="id_utente_risposta" value="' . htmlspecialchars((int)$risposta->id_utente) . '" />
                            <input type="hidden" name="id_risposta" value="' . htmlspecialchars((int)$risposta['id']) . '" />
                            <button type="submit">Segnala</button>
                          </form>';
                  endif;
                  ?>
                </div>
            <?php endif;
            endforeach; ?>

            <?php if (!$risposteTrovate): ?>
              <p><em>Nessuna risposta trovata per questa recensione.</em></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <a href="catalogo.php">
      <h2>Torna al catalogo</h2>
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
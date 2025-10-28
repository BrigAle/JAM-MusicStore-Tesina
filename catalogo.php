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

  $nessunRisultato = false;
  $query = '';
  $tipo = '';

  // Carico le funzioni per calcolare e assegnare lo sconto più alto tra le promozioni attive del cliente
  require_once("risorse/PHP/funzioni_sconti.php");

  // Carica XML
  $xmlProdotti   = simplexml_load_file("risorse/XML/prodotti.xml");
  $xmlUtenti     = simplexml_load_file("risorse/XML/utenti.xml");
  $xmlSconti     = simplexml_load_file("risorse/XML/sconti.xml");
  $xmlRecensioni = simplexml_load_file("risorse/XML/recensioni.xml");
  $xmlStorico    = simplexml_load_file("risorse/XML/storico_acquisti.xml");

  $oggi = date('Y-m-d');

  // Legge eventuale ricerca dalla sessione
  if (isset($_SESSION['search_query'])) {
    $query = strtolower(trim($_SESSION['search_query']));
  }
  if (isset($_SESSION['search_tipo'])) {
    $tipo = $_SESSION['search_tipo'];
  }

  $prodottiFiltrati = [];

  if ($query !== '') {
    foreach ($xmlProdotti->prodotto as $p) {
      $nome = strtolower((string)$p->nome);
      $categoria = strtolower((string)$p->categoria);

      if ($tipo === 'nome' && strpos($nome, $query) !== false) {
        $prodottiFiltrati[] = $p;
      } elseif ($tipo === 'categoria' && strpos($categoria, $query) !== false) {
        $prodottiFiltrati[] = $p;
      }
    }

    $nessunRisultato = false;
    if (empty($prodottiFiltrati)) {
      $nessunRisultato = true;
    }

    unset($_SESSION['search_query'], $_SESSION['search_tipo']);
  } else {
    $prodottiFiltrati = $xmlProdotti->prodotto;
  }
  ?>

  <div class="content">
    <h1>Catalogo Prodotti</h1>
    <?php
    // Messaggi di sessione
    if (isset($_SESSION['successo_msg'])) {
      echo "<p style='color:green; font-weight:bold;'>" . $_SESSION['successo_msg'] . "</p>";
      unset($_SESSION['successo_msg']);
    } elseif (isset($_SESSION['errore_msg'])) {
      echo "<p style='color:red; font-weight:bold;'>" . $_SESSION['errore_msg'] . "</p>";
      unset($_SESSION['errore_msg']);
    }
    ?>
    <?php if ($nessunRisultato): ?>
      <p style="color:red; text-align:center; margin-top:30px; font-size:18px;">
        Nessun prodotto trovato per "<strong><?= htmlspecialchars($query) ?></strong>"
        <?php
        if ($tipo === 'nome') echo '(ricerca per nome)';
        elseif ($tipo === 'categoria') echo '(ricerca per categoria)';
        ?>
      </p>

    <?php else: ?>
      <?php if ($query !== ''): ?>
        <p style="color:#ffeb00; text-align:center; margin-bottom:15px;">
          Risultati per "<strong><?= htmlspecialchars($query) ?></strong>"
          <?php
          if ($tipo === 'nome') echo '(ricerca per nome)';
          elseif ($tipo === 'categoria') echo '(ricerca per categoria)';
          ?>
        </p>
      <?php endif; ?>

      <div class="box_prodotto">
        <?php
        $oggi = date('Y-m-d');

        foreach ($prodottiFiltrati as $prodotto):
          $idProdotto = (string)$prodotto['id'];
          $nome = (string)$prodotto->nome;
          $descrizione = (string)$prodotto->descrizione;
          $prezzo = (float)$prodotto->prezzo;
          $bonus = (float)$prodotto->bonus;
          $dataInserimento = (string)$prodotto->data_inserimento;
          $immagine = "risorse/IMG/prodotti/" . (string)$prodotto->immagine;

          $percentualeScontoPromo = 0;
          $descrizioneCondizione = "";
          $utenteLoggato = null;

          // --- Se l’utente è loggato, recupera il nodo XML utente ---
          if (
            isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' &&
            isset($_SESSION['id_utente']) && $_SESSION['ruolo'] === 'cliente'
          ) {
            foreach ($xmlUtenti->utente as $u) {
              if ((string)$u['id'] === (string)$_SESSION['id_utente']) {
                $utenteLoggato = $u;
                break;
              }
            }
          }

          // Applica sconti
          if ($xmlSconti) {

            if ($utenteLoggato) {
              $result = calcolaScontoUtente($xmlSconti, $utenteLoggato, $idProdotto, $oggi, $xmlStorico);
              $percentualeScontoPromo = $result['sconto'];
              $descrizioneCondizione = $result['condizione'];
            } else {
              foreach ($xmlSconti->sconto as $s) {
                $appGlobale = ((string)$s['applicazione_globale'] === 'true');
                $dataInizio = (string)$s->data_inizio;
                $dataFine   = (string)$s->data_fine;

                if ($oggi >= $dataInizio && $oggi <= $dataFine) {
                  // verifica che il prodotto sia incluso
                  foreach ($s->id_prodotto as $idS) {
                    if ((string)$idS === $idProdotto && $appGlobale) {
                      $perc = (float)$s->percentuale;
                      if ($perc > $percentualeScontoPromo) {
                        $percentualeScontoPromo = $perc;
                        $evento = (string)$s->condizione->evento;
                        if (!empty($evento)) {
                          $descrizioneCondizione = "Offerta speciale: {$evento}";
                        } else {
                          $descrizioneCondizione = "Offerta promozionale globale";
                        }
                      }
                    }
                  }
                }
              }
            }
          }

          // Sconto da crediti (solo se loggato)
          $percentualeScontoCrediti = 0;
          if ($utenteLoggato) {
            $crediti = (float)$utenteLoggato->crediti;
            if ($crediti >= 100) {
              $percentualeScontoCrediti = 1.5 * floor($crediti / 100);
              if ($percentualeScontoCrediti > 20) $percentualeScontoCrediti = 10;
            }
          }

          // Calcolo prezzo finale
          $prezzoFinale = $prezzo;
          if ($percentualeScontoPromo > 0)
            $prezzoFinale -= ($prezzoFinale * $percentualeScontoPromo / 100);
          if ($percentualeScontoCrediti > 0)
            $prezzoFinale -= ($prezzoFinale * $percentualeScontoCrediti / 100);


          // Calcolo valutazione media
          $valutazioneTotale = 0;
          $countValutazioni = 0;
          foreach ($xmlRecensioni->recensione as $rec) {
            if ((string)$rec->id_prodotto === $idProdotto) {
              $valutazioneTotale += (float)$rec->valutazione;
              $countValutazioni++;
            }
          }
          $valutazioneMedia = $countValutazioni > 0 ? round($valutazioneTotale / $countValutazioni, 1) : 0;
        ?>

          <div class="contenuto_prodotto">
            <div class="immagine_box">
              <img src="<?= $immagine ?>" alt="<?= htmlspecialchars($nome) ?>" />
              <?php if ($percentualeScontoPromo > 0): ?>
                <div style="position:absolute; top:10px; left:10px; background-color:#ffcc00; color:#111; padding:4px 8px; border-radius:6px; font-weight:700; font-size:13px;">
                  -<?= number_format($percentualeScontoPromo, 0) ?>%
                </div>
              <?php endif; ?>
            </div>

            <div class="dettagli_box">
              <h3><?= htmlspecialchars($nome) ?></h3>
              <p><?= htmlspecialchars($descrizione) ?></p>

              <?php if ($percentualeScontoPromo > 0 || $percentualeScontoCrediti > 0): ?>
                <p>
                  <span style="text-decoration: line-through; color: #888;">Prezzo: €<?= number_format($prezzo, 2, ',', '.') ?></span><br />
                  <span style="color:#ffcc00; font-weight:bold;">Prezzo scontato: €<?= number_format($prezzoFinale, 2, ',', '.') ?></span><br />
                  <?php if ($percentualeScontoPromo > 0): ?>
                    <small style="color:#aaa;">
                      (<?= $percentualeScontoPromo ?>% promozionale)
                      <?php if (!empty($descrizioneCondizione)): ?>
                        — <?= htmlspecialchars($descrizioneCondizione) ?>
                      <?php endif; ?>
                    </small><br />
                  <?php endif; ?>
                  <?php if ($percentualeScontoCrediti > 0): ?>
                    <small style="color:#aaa;">(+<?= $percentualeScontoCrediti ?>% sconto da crediti)</small>
                  <?php endif; ?>
                </p>
              <?php else: ?>
                <p>Prezzo: €<?= number_format($prezzo, 2, ',', '.') ?></p>
              <?php endif; ?>

              <?php if ($bonus > 0): ?>
                <p>Bonus: <?= number_format($bonus, 2, ',', '.') ?> punti</p>
              <?php endif; ?>
              <p>Data di inserimento: <?= htmlspecialchars($dataInserimento) ?></p>

              <p class="valutazione" style="text-align:center;">
                Valutazione: <?= $valutazioneMedia ?>
                <img src="risorse/IMG/stella.png" alt="★" style="width:22px;height:22px;vertical-align:middle;" />
              </p>

              <?php
              $haAcquistato = false;
              if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && $_SESSION['ruolo'] === 'cliente') {
                $idUtente = $_SESSION['id_utente'] ?? '';
                if ($xmlStorico && $idUtente !== '') {
                  foreach ($xmlStorico->storico as $storico) {
                    if ((string)$storico->id_utente === (string)$idUtente) {
                      foreach ($storico->prodotti->prodotto as $p) {
                        if ((string)$p->id_prodotto === (string)$idProdotto) {
                          $haAcquistato = true;
                          break 2;
                        }
                      }
                    }
                  }
                }
              }
              ?>

              <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;">
                <form action="recensioni.php" method="GET" style="margin:0;">
                  <input type="hidden" name="id_prodotto" value="<?= $idProdotto ?>" />
                  <button type="submit" style="background-color:#1E90FF; color:white; border:none; padding:6px 14px; border-radius:6px; cursor:pointer;">Leggi recensioni</button>
                </form>

                <?php if ($haAcquistato): ?>
                  <form action="scrivi_recensione.php" method="GET" style="margin:0;">
                    <input type="hidden" name="id_prodotto" value="<?= $idProdotto ?>" />
                    <button type="submit" style="background-color:#32CD32; color:white; border:none; padding:6px 14px; border-radius:6px; cursor:pointer;">Scrivi recensione</button>
                  </form>
                <?php endif; ?>
              </div>

              <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true' && $_SESSION['ruolo'] === 'cliente'): ?>
                <form action="risorse/PHP/aggiungi_nel_carrello.php" method="post"
                  style="margin-top:10px; display:flex; flex-direction:column; align-items:center; gap:6px;">
                  <input type="hidden" name="id" value="<?= $idProdotto ?>" />
                  <input type="hidden" name="prezzo_finale" value="<?= number_format($prezzoFinale, 2, '.', '') ?>" />

                  <label for="quantita_<?= $idProdotto ?>" style="font-weight:500;">Quantità:</label>
                  <input type="number" id="quantita_<?= $idProdotto ?>" name="quantita" min="1" value="1" step="1" required
                    style="width:60px; text-align:center; border-radius:4px; border:1px solid #aaa; padding:4px;" />

                  <button type="submit"
                    style="background-color:#FF8C00; color:white; border:none; padding:6px 14px; border-radius:6px; cursor:pointer; font-size:14px;">
                    Aggiungi nel carrello
                  </button>
                </form>
              <?php endif; ?>
            </div> <!-- fine .dettagli_box -->
          </div> <!-- fine .contenuto_prodotto -->

        <?php endforeach; ?>
      </div> <!-- fine .box_prodotto -->
    <?php endif; ?>
  </div> <!-- fine .content -->



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
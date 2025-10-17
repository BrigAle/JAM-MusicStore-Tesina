<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestione Recensioni — JAM Music Store</title>
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
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
                <a href="amministrazione.php">admin</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'): ?>
                <a href="gestione.php">gestore</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo" /></a>
            <?php endif; ?>
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="Home" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="Carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div>
    </div>

    <div class="content">
        <h2 style="text-align:left;">Gestione Recensioni</h2>

        <?php
        $xmlRecensioni = simplexml_load_file("risorse/XML/recensioni.xml");
        $xmlProdotti = simplexml_load_file("risorse/XML/prodotti.xml");
        $xmlUtenti = simplexml_load_file("risorse/XML/utenti.xml");

        if ($xmlRecensioni && count($xmlRecensioni->recensione) > 0) {
            echo "<table border='1' cellpadding='6'>
                    <tr>
                        <th>ID</th>
                        <th>Prodotto</th>
                        <th>Utente</th>
                        <th>Commento</th>
                        <th style='text-align:center;'>Valutazione</th>
                        <th style='text-align:center;'>Like 👍</th>
                        <th style='text-align:center;'>Dislike 👎</th>
                        <th style='text-align:center;'>Data</th>
                        <th style='text-align:center;'>Azioni</th>
                    </tr>";

            foreach ($xmlRecensioni->recensione as $recensione) {
                $id = (string)$recensione['id'];
                $idProdotto = (string)$recensione->id_prodotto;
                $idUtente = (string)$recensione->id_utente;
                $commento = (string)$recensione->commento;
                $valutazione = (float)$recensione->valutazione;
                $votiLike = (int)$recensione->voti_like;
                $votiDislike = (int)$recensione->voti_dislike;
                $dataInserimento = (string)$recensione->data;

                // Recupera nome prodotto
                $nomeProdotto = "Sconosciuto";
                foreach ($xmlProdotti->prodotto as $p) {
                    if ((string)$p['id'] === $idProdotto) {
                        $nomeProdotto = (string)$p->nome;
                        break;
                    }
                }

                // Recupera nome utente
                $usernameUtente = "Utente eliminato";
                foreach ($xmlUtenti->utente as $u) {
                    if ((string)$u['id'] === $idUtente) {
                        $usernameUtente = (string)$u->username;
                        break;
                    }
                }

                echo "<tr>
                        <td>{$id}</td>
                        <td>{$nomeProdotto} (ID {$idProdotto})</td>
                        <td>{$usernameUtente} (ID {$idUtente})</td>
                        <td style='max-width:300px; text-align:left;'>{$commento}</td>
                        <td style='text-align:center;'>{$valutazione} 
                            <img src='risorse/IMG/stella.png' alt='★' style='width:18px;height:18px;vertical-align:middle;margin-bottom:3px;'>
                        </td>
                        <td style='text-align:center;'>{$votiLike}</td>
                        <td style='text-align:center;'>{$votiDislike}</td>
                        <td>{$dataInserimento}</td>
                        <td>
                            <a href='risorse/PHP/gestore/elimina_recensione.php?id_recensione={$id}' onclick=\"return confirm('Sei sicuro di voler eliminare questa recensione?');\">Elimina</a>                          
                        </td>
                    </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Nessuna recensione trovata.</p>";
        }
        if (isset($_SESSION['elimina_recensione_successo'])) {
            if ($_SESSION['elimina_recensione_successo']) {
                echo "<p style='color:green;'>Recensione eliminata con successo.</p>";
            } else {
                echo "<p style='color:red;'>Errore durante l'eliminazione della recensione.</p>";
            }
            unset($_SESSION['elimina_recensione_successo']);
        }
        ?>
        <h2 style="text-align:left; margin: 20px 0;">Gestione Risposte alle Recensioni</h2>

        <?php
        
        // RISPOSTE
        
        $xmlRisposte = simplexml_load_file("risorse/XML/risposte.xml");

        if ($xmlRisposte && count($xmlRisposte->risposta) > 0) {
            echo "<table border='1' cellpadding='6'>
                    <tr>
                        <th>ID Risposta</th>
                        <th>ID Recensione</th>
                        <th>ID Utente</th>
                        <th>Commento</th>
                        <th>Data</th>
                        <th>Ora</th>
                        <th>Azioni</th>
                    </tr>";

            foreach ($xmlRisposte->risposta as $risposta) {
                $idRisposta = (string)$risposta['id'];
                $idRecensione = (string)$risposta->id_recensione;
                $idUtente = (string)$risposta->id_utente;
                $commento = (string)$risposta->commento;
                $data = (string)$risposta->data;
                $ora = (string)$risposta->ora;

                // Nome utente
                $usernameUtente = "Utente eliminato";
                foreach ($xmlUtenti->utente as $u) {
                    if ((string)$u['id'] === $idUtente) {
                        $usernameUtente = (string)$u->username;
                        break;
                    }
                }

                echo "<tr>
                        <td>{$idRisposta}</td>
                        <td>{$idRecensione}</td>
                        <td>{$usernameUtente} (ID {$idUtente})</td>
                        <td style='max-width:300px; text-align:left;'>{$commento}</td>
                        <td>{$data}</td>
                        <td>{$ora}</td>
                        <td>
                            <a href='risorse/PHP/gestore/elimina_risposta.php?id_risposta={$idRisposta}' onclick=\"return confirm('Sei sicuro di voler eliminare questa risposta?');\">Elimina</a>
                        </td>
                    </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Nessuna risposta trovata.</p>";
        }
        if (isset($_SESSION['elimina_risposta_successo'])) {
            if ($_SESSION['elimina_risposta_successo']) {
                echo "<p style='color:green;'>Risposta eliminata con successo.</p>";
            } else {
                echo "<p style='color:red;'>Errore durante l'eliminazione della risposta.</p>";
            }
            unset($_SESSION['elimina_risposta_successo']);
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
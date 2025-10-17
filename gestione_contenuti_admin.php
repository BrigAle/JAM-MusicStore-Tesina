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
        require_once('risorse/PHP/connection.php');
        $connection = new mysqli($host, $user, $password, $db);
        if ($connection->connect_error) {
            die("Connessione fallita: " . $connection->connect_error);
        }
        // SEGNALAZIONI

        $xmlFile = "risorse/XML/segnalazioni.xml";
        $xml = simplexml_load_file($xmlFile);

        if ($xml) {
            echo "<h2 style='text-align: left;'>Segnalazioni Utenti</h2>";
            echo "<table border='1' cellpadding='6'>
                <tr>
                    <th>ID Segnalazione</th>
                    <th>ID Utente</th>
                    <th>Username</th>
                    <th>Motivo</th>
                    <th>Tipo</th>
                    <th>Data Segnalazione</th>
                    <th>Azioni</th>
                </tr>";
            foreach ($xml->segnalazione as $segnalazione) {
                $id_segnalazione = (int)$segnalazione['id'];
                $id_utente = (int)$segnalazione->id_utente;
                $motivo = (string)$segnalazione->motivo;
                $id_contenuto_elem = $segnalazione->id_contenuto;
                $id_contenuto = (string)$id_contenuto_elem;
                $tipo = (string)$id_contenuto_elem['tipo'];
                $data_segnalazione = (string)$segnalazione->data;

                // Ottengo lo username dall'ID utente
                $username = '-';
                $sql = "SELECT username FROM utente WHERE id = $id_utente";
                $result = $connection->query($sql);
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $username = $row['username'];
                }

               
                $azioni = "<a href='risorse/PHP/amministratore/elimina_segnalazione.php?id_segnalazione={$id_segnalazione}' 
                onclick=\"return confirm('Sei sicuro di voler eliminare questa segnalazione?');\">
                Elimina
                    </a>";

                echo "
                    <tr>
                        <td>{$id_segnalazione}</td>
                        <td>{$id_utente}</td>
                        <td>{$username}</td>
                        <td>{$motivo}</td>
                        <td>{$tipo}</td>
                        <td>{$data_segnalazione}</td>
                        <td>{$azioni}</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Nessuna segnalazione trovata.";
        }
        echo "<br/><br/>";
        if (isset($_SESSION['elimina_segnalazione_successo'])) {
            if ($_SESSION['elimina_segnalazione_successo']) {
                echo "<p style='color: green;'>Segnalazione eliminata con successo.</p>";
            } else {
                echo "<p style='color: red;'>Errore nell'eliminazione della segnalazione.</p>";
            }
            unset($_SESSION['elimina_segnalazione_successo']);
        }
        $connection->close();
        ?>

        <h2 style="text-align: left;">FAQs</h2>
        <?php
        $xml = simplexml_load_file("risorse/XML/FAQs.xml");
        if ($xml) {
            echo "<table border='1' cellpadding='6'>
                <tr>
                    <th>ID FAQ</th>
                    <th>Domanda</th>
                    <th>Risposta</th>
                    <th>Azioni</th>
                </tr>";
            foreach ($xml->faq as $faq) {
                $id_faq = (int)$faq['id'];
                $domanda = (string)$faq->domanda;
                $risposta = (string)$faq->risposta;

                echo "
                    <tr>
                        <td>{$id_faq}</td>
                        <td>{$domanda}</td>
                        <td>{$risposta}</td>
                        <td>
                            <a href='risorse/PHP/amministratore/elimina_faq.php?id_faq={$id_faq}' 
                            onclick=\"return confirm('Sei sicuro di voler eliminare questa FAQ?');\">
                            Elimina
                            </a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "Nessuna FAQ trovata.";
        }
        ?>
    <a class="aggiungi-faq" href="aggiungi_faq.php"><h2 style="color:#1E90FF">Aggiungi FAQs</h2></a>
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
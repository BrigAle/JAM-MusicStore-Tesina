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
    <?php
    // Controllo accesso
    if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'amministratore') {
        $_SESSION['errore'] = "Accesso non autorizzato.";
        header("Location: ../../login.php");
        exit();
    }

    $id_utente = $_SESSION['id_utente'] ?? '';
    ?>
    <div class="content" style="max-width:900px; margin:150px auto; text-align:center; background:#111; padding:25px; border-radius:10px;">
        <h2 style="color:#fff; margin-bottom:25px;">Gestione Richieste Crediti</h2>

        <?php
        $xmlFile = "risorse/XML/richiesteCrediti.xml";
        if (!file_exists($xmlFile)) {
            echo "<p style='color:#aaa;'>Nessuna richiesta trovata.</p>";
        } else {
            $xml = simplexml_load_file($xmlFile);

            if (count($xml->richiesta) === 0) {
                echo "<p style='color:#aaa;'>Non ci sono richieste di crediti.</p>";
            } else {
                echo "<table style='margin:auto; border-collapse:collapse; width:100%; background:#181818; border:1px solid #333;'>
                    <tr style='background-color:#1E90FF; color:#fff;'>
                        <th style='padding:8px;'>ID</th>
                        <th style='padding:8px;'>Utente</th>
                        <th style='padding:8px;'>Importo</th>
                        <th style='padding:8px;'>Data</th>
                        <th style='padding:8px;'>Stato</th>
                        <th style='padding:8px;'>Azioni</th>
                    </tr>";

                foreach ($xml->richiesta as $r) {
                    $id = (string)$r['id'];
                    $idUtente = (string)$r->id_utente;
                    $importo = (int)$r->importo;
                    $data = (string)$r->data;
                    $stato = strtolower((string)$r->stato);

                    $colore = match ($stato) {
                        'approvata' => 'lightgreen',
                        'rifiutata' => 'tomato',
                        default => '#f0ad4e'
                    };

                    echo "<tr style='color:#ddd; border-top:1px solid #333; text-align:center;'>
                        <td>{$id}</td>
                        <td>{$idUtente}</td>
                        <td>{$importo}</td>
                        <td>{$data}</td>
                        <td style='color:{$colore}; text-transform:capitalize;'>{$stato}</td>
                        <td>";

                    if ($stato === 'in attesa') {
                        echo "
                        <form action='risorse/PHP/amministratore/aggiorna_stato_crediti.php' method='post' style='display:inline;'>
                            <input type='hidden' name='id_richiesta' value='{$id}'>
                            <input type='hidden' name='azione' value='approvata'>
                            <button type='submit' style='background:green; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;'>Approva</button>
                        </form>
                        <form action='risorse/PHP/amministratore/aggiorna_stato_crediti.php' method='post' style='display:inline;'>
                            <input type='hidden' name='id_richiesta' value='{$id}'>
                            <input type='hidden' name='azione' value='rifiutata'>
                            <button type='submit' style='background:red; color:white; border:none; padding:5px 10px; border-radius:5px; cursor:pointer;'>Rifiuta</button>
                        </form>";
                    } else {
                        echo "<em style='color:#777;'>Nessuna azione</em>";
                    }

                    echo "</td></tr>";
                }

                echo "</table>";
            }
            if (isset($_SESSION['successo_msg'])) {
                echo "<p style='color:lightgreen; margin-top:15px;'>" . $_SESSION['successo_msg'] . "</p>";
                unset($_SESSION['successo_msg']);
            }
            if (isset($_SESSION['errore_msg'])) {
                echo "<p style='color:tomato; margin-top:15px;'>" . $_SESSION['errore_msg'] . "</p>";
                unset($_SESSION['errore_msg']);
            }
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
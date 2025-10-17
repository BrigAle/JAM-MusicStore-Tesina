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
    if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'true' || $_SESSION['ruolo'] !== 'cliente') {
        $_SESSION['errore'] = "Effettua il login per richiedere crediti.";
        header("Location: login.php");
        exit();
    }

    $id_utente = $_SESSION['id_utente'] ?? '';
    ?>
    <div class="content" style="max-width:700px; margin:170px auto; text-align:center; background-color:#111; padding:30px; border-radius:10px; box-shadow:0 0 12px rgba(255,255,255,0.05);">

        <h2 style="margin-bottom:25px; color:#fff;">Richiedi altri crediti</h2>

        <!-- FORM RICHIESTA CREDITI -->
        <form action="risorse/PHP/salva_richiesta_crediti.php" method="post"
            style="display:flex; flex-direction:column; gap:15px; align-items:center; background:#181818; padding:20px; border-radius:8px;">

            <label for="importo" style="color:#ddd;"><strong>Importo da richiedere (€):</strong></label>
            <input type="number" id="importo" name="importo" min="1" step="1" required
                style="padding:8px; width:200px; border:1px solid #333; border-radius:6px; text-align:center; background:#222; color:#fff;">

            <button type="submit"
                style="background-color:#1E90FF; color:white; border:none; padding:10px 22px; border-radius:6px; cursor:pointer; font-weight:bold;">
                Invia Richiesta
            </button>

            <a href="profilo.php" style="color:#1E90FF; text-decoration:none; margin-top:5px;">← Torna al profilo</a>
        </form>

        <!-- MESSAGGI -->
        <?php
        if (isset($_SESSION['successo_msg'])) {
            echo "<p style='color:lightgreen; margin-top:15px;'>✅ " . htmlspecialchars($_SESSION['successo_msg']) . "</p>";
            unset($_SESSION['successo_msg']);
        }

        if (isset($_SESSION['errore_msg'])) {
            echo "<p style='color:tomato; margin-top:15px;'>❌ " . htmlspecialchars($_SESSION['errore_msg']) . "</p>";
            unset($_SESSION['errore_msg']);
        }
        ?>

        <!-- STORICO RICHIESTE -->
        <h3 style="margin-top:40px; color:#fff;">Le tue richieste di crediti</h3>

        <?php
        $xmlFile = "risorse/XML/richiesteCrediti.xml";
        if (file_exists($xmlFile)) {
            $xml = simplexml_load_file($xmlFile);
            $richiesteUtente = [];

            foreach ($xml->richiesta as $r) {
                if ((string)$r->id_utente === (string)$id_utente) {
                    $richiesteUtente[] = $r;
                }
            }

            if (count($richiesteUtente) > 0) {
                echo "<table style='margin:auto; margin-top:15px; border-collapse:collapse; width:100%; max-width:550px; background:#181818; border:1px solid #333; border-radius:6px; overflow:hidden;'>
                    <tr style='background-color:#1E90FF; color:#fff;'>
                        <th style=\"padding:8px;\">ID</th>
                        <th style=\"padding:8px;\">Importo </th>
                        <th style=\"padding:8px;\">Data</th>
                        <th style=\"padding:8px;\">Stato</th>
                    </tr>";

                foreach ($richiesteUtente as $r) {
                    $id = (string)$r['id'];
                    $importo = (int)$r->importo;
                    $data = (string)$r->data;
                    $stato = strtolower((string)$r->stato);

                    $colore = match ($stato) {
                        'approvata' => 'lightgreen',
                        'rifiutata' => 'tomato',
                        default => '#f0ad4e'
                    };

                    echo "<tr style='color:#ddd; border-top:1px solid #333;'>
                        <td style='padding:8px;'>{$id}</td>
                        <td style='padding:8px;'>{$importo}</td>
                        <td style='padding:8px;'>{$data}</td>
                        <td style='padding:8px; color:{$colore}; text-transform:capitalize;'>{$stato}</td>
                      </tr>";
                }

                echo "</table>";
            } else {
                echo "<p style='margin-top:15px; color:#aaa;'>Non hai ancora richiesto crediti.</p>";
            }
        } else {
            echo "<p style='margin-top:15px; color:#aaa;'>Nessuna richiesta trovata.</p>";
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
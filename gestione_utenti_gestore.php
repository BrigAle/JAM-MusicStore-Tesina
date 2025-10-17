<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['ruolo'] !== 'gestore') {
    // L'utente non è loggato o non è un gestore, reindirizza alla pagina di login
    header('Location: login.php');
    exit();
}

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
            <!-- link admin -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'amministratore'): ?>
                <a href="amministrazione.php">admin</a>
            <?php endif; ?>
            <!-- link gestore -->
            <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'gestore'):
                echo "<a href=\"gestione.php\">gestore</a>";
            endif; ?>
            <?php if (isset($_SESSION['logged']) && $_SESSION['logged'] === 'true'): ?>
                <a href="profilo.php"><img src="risorse/IMG/user.png" alt="Profilo"></a>
            <?php endif; ?>
            <!-- link cliente -->
            <a href="catalogo.php">Catalogo</a>
            <a href="homepage.php"><img src="risorse/IMG/home.png" alt="casetta" /></a>
            <a href="cart.php"><img src="risorse/IMG/cart.png" alt="carrello" /></a>
            <?php if (!isset($_SESSION['username'])) echo '<a href="login.php">Accedi</a>'; ?>
            <?php if (isset($_SESSION['username'])) echo '<a href="risorse/PHP/logout.php">Esci</a>'; ?>
        </div>
    </div>

    
    <div class="content">
        <?php
        require_once('risorse/PHP/connection.php');
        $connection = new mysqli($host, $user, $password, $db);
        if ($connection->connect_error) {
            die("Connessione fallita: " . $connection->connect_error);
        }

        // ricerca in base agli id degli utenti nel database
        $xmlFile = "risorse/XML/utenti.xml";
        $xml = simplexml_load_file($xmlFile);

        $sql = "SELECT id, username, email, ruolo, stato FROM utente";
        $result = $connection->query($sql);

        if ($result) {
            echo "<h2 style='text-align: left;'>Gestione Utenti</h2>";
            echo "<table border='1' cellpadding='6'>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Ruolo</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Telefono</th>
                    <th style='padding: 0px 30px;text-align:center'>Indirizzo</th>
                    <th>Reputazione</th>
                    <th>Stato</th>
                    <th>Portafoglio</th>
                    <th>Crediti</th>
                    <th>Data Iscrizione</th>
                    
                </tr>";

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $id = $row['id'];

                // Cerco lo stesso utente nell'XML
                $utenteXML = null;
                foreach ($xml->utente as $u) {
                    if ((int)$u['id'] === (int)$id) {
                        $utenteXML = $u;
                        break;
                    }
                }

                // Estraggo i dati dall'XML
                if ($utenteXML) {
                    $nome = (string)$utenteXML->nome;
                    $cognome = (string)$utenteXML->cognome;
                    $telefono = (string)$utenteXML->telefono;
                    $indirizzo = (string)$utenteXML->indirizzo;
                    $reputazione = (string)$utenteXML->reputazione;
                    $portafoglio = (string)$utenteXML->portafoglio;
                    $crediti = (string)$utenteXML->crediti;
                    $data_iscrizione = (string)$utenteXML->data_iscrizione;
                } else {
                    $nome = $cognome = $telefono = $indirizzo = $reputazione = $portafoglio = $crediti = $data_iscrizione = '-';
                }
                $stato = $row['stato'] ?? '1'; // Default a '1' se non definito
                
            

                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['ruolo']}</td>
                    <td>$nome</td>
                    <td>$cognome</td>
                    <td>$telefono</td>
                    <td>$indirizzo</td>
                    <td>$reputazione</td>
                    <td>$stato</td>
                    <td>$portafoglio</td>
                    <td>$crediti</td>
                    <td>$data_iscrizione</td>                    
                </tr>";
            }
            echo "</table>";
        } else {
            echo "Nessun utente trovato.";
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